<?php
/**
 * ============================================================
 * LUỒNG: XỦ LÝ ĐẶT HÀNG (THANH TOÁN)
 *
 * GọI BỚI: Form submit từ thanhToan.php
 *   method="POST"
 *   action="ThanhToan/xuấtLyThanhToan.php"
 *
 * NHẬN VÀO (POST):
 *   hoten, sdt, email           — Thông tin người nhận
 *   loai_dia_chi                — 'da_luu' hoặc 'moi'
 *   ma_dia_chi                  — Nếu loai='da_luu': ID địa chỉ đã lưu
 *   dia_chi_moi                 — Nếu loai='moi': chuỗi địa chỉ mới
 *   phuong_thuc                 — 1=COD (tiền mặt), 2=QR (chuyển khoản)
 *
 * ĐIỀU KIỆN CHẠY:
 *   - REQUEST_METHOD == POST
 *   - $_SESSION['cart_temp'] có dữ liệu (do kiemTraGioHang.php đã set)
 *
 * QUY TRÌNH (7 bước trong 1 Transaction):
 *   1. Xác định người dùng (đã đăng nhập / khách vãng lai)
 *   2. Xác định mã địa chỉ giao hàng
 *   3. Tạo mã đơn hàng (duy nhất)
 *   4. FOR EACH sản phẩm: query giá từ DB + kiểm tra kho + trừ kho
 *   5. INSERT ĐonHang
 *   6. INSERT ChiTietDH (giá từ DB, không từ session)
 *   7. Commit + dọn dẹp giỏ hàng
 *
 * BẢO MẬT:
 *   - GIÁ được query lại từ DB bằng
     SELECT ... FOR UPDATE (lần xác thực thứ 2)
 *   - FOR UPDATE: khóa hàng trong khi transaction — tránh race condition
 *   - Địa chỉ được verify thuộc về user (chống IDOR)
 *   - Khách vãng lai: tìm hoặc tạo bản ghi NguoiDung (không có TaiKhoan)
 *
 * OUTPUT:
 *   COD (phuong_thuc=1) → redirect thanhCong.php?maDH=...
 *   QR  (phuong_thuc=2) → redirect quetMaQR.php?maDH=...&tien=...
 *   Lỗi                 → die() với <script>alert + history.back()
 * ============================================================
 */
session_start();
require_once "../../../KetNoi/config/db.php";

// Guard: Chỉ xử lý khi POST và cart_temp tồn tại
// cart_temp: giỏ hàng đã qua xác thực giá (do kiemTraGioHang.php đã set)
// Nếu trực tiếp vào đường dẫn này (GET) → chặn
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['cart_temp'])) {
    die("Lỗi: Dữ liệu không hợp lệ.");
}

// Lấy giỏ hàng đã XÁC THỰC từ session (giá từ DB, không phải từ form)
// GHI CHÚ: $gioHang này chỉ dùng để biết maSach + soLuong
// Giá sẽ được query LẠI từ DB trong transaction bước 4 (FOR UPDATE)
$gioHang    = $_SESSION['cart_temp'];

// Nhận dữ liệu form (trim — tránh khoảng trắng dẫn đến lỗi)
$hoTen      = trim($_POST['hoten']        ?? '');
$sdt        = trim($_POST['sdt']          ?? '');
$email      = trim($_POST['email']        ?? '');
$loaiDiaChi = trim($_POST['loai_dia_chi'] ?? 'moi'); // 'da_luu' hoặc 'moi'
$phuongThuc = (int)($_POST['phuong_thuc'] ?? 1);      // 1=COD, 2=QR

// —— BƯỚC 1: XÁC ĐỊNH NGƯỜI DÙNG ————————————————————————————————
// Trường hợp 1 (đã đăng nhập): Dùng maND từ session (không cần tìm DB)
// Trường hợp 2 (khách vãng lai): Tìm hoặc tạo NguoiDung theo email/SĐT
//   - Tìm: Tránh tạo bản ghi trùng lặp cho khách mua nhiều lần
//   - Tạo: Khách mới chưa có trong DB — tạo bản ghi NguoiDung (không có TaiKhoan)
$maND = null;
if (isset($_SESSION['nguoi_dung_id'])) {
    $maND = (int)$_SESSION['nguoi_dung_id']; // Tin session — đã xấc thực khi đăng nhập
} else {
    // Khách vãng lai: tìm theo email hoặc SĐT
    $stmtTimKH = $pdo->prepare("SELECT maND FROM NguoiDung WHERE email = ? OR sdt = ? LIMIT 1");
    $stmtTimKH->execute([$email, $sdt]);
    $khachHang = $stmtTimKH->fetch();

    if ($khachHang) {
        $maND = $khachHang['maND']; // Khách đã từng mua: dùng maND cũ
    } else {
        // Khách mới: tạo bản ghi NguoiDung (không có TaiKhoan)
        $stmtTaoKH = $pdo->prepare("INSERT INTO NguoiDung (tenND, sdt, email) VALUES (?, ?, ?)");
        $stmtTaoKH->execute([$hoTen, $sdt, $email]);
        $maND = (int)$pdo->lastInsertId();
    }
}

// —— BƯỚC 2: XÁC ĐỊNH MÃ ĐỊA CHỈ GIAO HÀNG —————————————————————————
$maDC = null;

if ($loaiDiaChi === 'da_luu') {
    // Với địa chỉ đã lưu: kiểm tra có thuộc về user không
    $maDCGui = (int)($_POST['ma_dia_chi'] ?? 0);
    $stmtKT  = $pdo->prepare("SELECT maDC FROM DiaChiGiaoHang WHERE maDC = ? AND maND = ? LIMIT 1");
    $stmtKT->execute([$maDCGui, $maND]);
    $kiemTra = $stmtKT->fetch();

    if ($kiemTra) {
        $maDC = $maDCGui; // Hợp lệ: địa chỉ thuộc đúng user
    } else {
        // Không hợp lệ: tấn công IDOR (gửi maDC của người khác)
        die("<script>alert('Địa chỉ giao hàng không hợp lệ.'); history.back();</script>");
    }
} else {
    // Địa chỉ MỚI: lấy từ POST → lưu vào DiaChiGiaoHang (laMacDinh=0)
    $diaChiMoi = trim($_POST['dia_chi_moi'] ?? '');
    if (empty($diaChiMoi)) {
        die("<script>alert('Vui lòng nhập địa chỉ giao hàng.'); history.back();</script>");
    }
    $stmtTaoDC = $pdo->prepare("INSERT INTO DiaChiGiaoHang (maND, diaChiChiTiet, laMacDinh) VALUES (?, ?, 0)");
    $stmtTaoDC->execute([$maND, $diaChiMoi]);
    $maDC = (int)$pdo->lastInsertId();
}

// Guard: đảm bảo $maDC hợp lệ trước khi vào transaction
if (!$maDC) {
    die("<script>alert('Địa chỉ giao hàng không hợp lệ.'); history.back();</script>");
}

// —— BƯỚC 3: TẠO MÃ ĐƠN HÀNG ————————————————————————————————
// 'DH' + timestamp + 2 chữ số random → khó đoán, khả năng trùng rất thấp
$maDonHang = 'DH' . time() . rand(10, 99);
$tongTien  = 0; // Tính lại từ DB trong transaction, không tin session

// =============================================================
// BẮT ĐẦU TRANSACTION — ĐẢM BẢO TOÀN VẸN DỮ LIỆU
// Nếu bất kỳ bước nào thất bại → rollBack() → không có gì lưu cả
// =============================================================
$pdo->beginTransaction();
try {
    // —— BƯỚC 4: KIỂM TRA KHO + LẤY GIÁ TỪ DB + TRỪ KHO ————————————————
    // ĐÂY LÀ LẦN XÁC THỰC GIÁ THỨ 2 (lần 1 là kiemTraGioHang.php)
    // FOR UPDATE: khóa dòng sách trong suốt transaction
    //   => Tránh race condition: 2 user cùng đặt 1 cuốn rách cuối cùng cùng lúc
    //   => Chỉ transaction này được đọc và ghi, transaction khác phải đợi commit
    foreach ($gioHang as $sanPham) {
        $maSach  = $sanPham['maSach'];
        $soLuong = (int)$sanPham['soLuong'];

        // BẢO MẬT: LẤY GIÁ TỪ DB (không tin $sanPham['giaBan'] từ session)
        // FOR UPDATE: khóa hàng này, các transaction khác phải đợi
        $stmtKho = $pdo->prepare("
            SELECT s.soLuongTon, s.tenSach, s.giaBan,
                   (
                       SELECT ROUND(s.giaBan * (1 - ckm.phanTramGiam / 100))
                       FROM ChiTietKhuyenMai ckm
                       JOIN KhuyenMai km ON km.maKM = ckm.maKM
                       WHERE ckm.maSach = s.maSach
                         AND NOW() BETWEEN km.ngayBatDau AND km.ngayKetThuc
                       ORDER BY ckm.phanTramGiam DESC
                       LIMIT 1
                   ) AS giaSau
            FROM Sach s
            WHERE s.maSach = ? FOR UPDATE
        ");
        $stmtKho->execute([$maSach]);
        $sachDB = $stmtKho->fetch();

        // Kiểm tra tồn kho: nếu không đủ → throw Exception → rollBack() toàn bộ
        if (!$sachDB || $sachDB['soLuongTon'] < $soLuong) {
            throw new Exception("Sản phẩm '{$sachDB['tenSach']}' không đủ số lượng trong kho.");
        }

        // Giá thực tế: ưu tiên flash sale, không thì giá gốc
        $giaChinh = ($sachDB['giaSau'] !== null)
            ? (float)$sachDB['giaSau']
            : (float)$sachDB['giaBan'];

        $tongTien += $giaChinh * $soLuong; // Tính tổng bằng GIÁ DB

        // Lưu giá đã xác thực vào bảng tạm — dùng nhật insert ChiTietDH (bước 6)
        $gioHang_validated[$maSach] = [
            'giaChinh' => $giaChinh,
            'soLuong'  => $soLuong,
            'tenSach'  => $sachDB['tenSach'],
        ];

        // TRỪ KHO: cập nhật soLuongTon — chạy sau khi đã kiểm tra đủ hàng
        // Nếu rollBack(): UPDATE này cũng bị huỷ (transaction đảm bảo)
        $stmtTruKho = $pdo->prepare("UPDATE Sach SET soLuongTon = soLuongTon - ? WHERE maSach = ?");
        $stmtTruKho->execute([$soLuong, $maSach]);
    }

    // —— BƯỚC 5: INSERT ĐƠN HÀNG ——————————————————————————————————
    // trangThai = 'ChoDuyet': Admin xem và xác nhận trước khi giao hàng
    // $tongTien: đã tính lại từ DB — không phải từ session
    $stmtDH = $pdo->prepare("INSERT INTO DonHang (maDH, maND, maDC, maPT, tongTien, trangThai) VALUES (?, ?, ?, ?, ?, 'ChoDuyet')");
    $stmtDH->execute([$maDonHang, $maND, $maDC, $phuongThuc, $tongTien]);

    // —— BƯỚC 6: INSERT CHI TIẮT ĐƠN HÀNG —————————————————————————————
    // GIÁ ĐƯỢC LẤY TỪ $gioHang_validated (query từ DB ở bước 4)
    // KHÔNG lấy từ $sanPham['giaBan'] (đến từ session/cart_temp)
    foreach ($gioHang as $sanPham) {
        $ms        = $sanPham['maSach'];
        $soLuong   = (int)$gioHang_validated[$ms]['soLuong'];
        $giaBan    = (float)$gioHang_validated[$ms]['giaChinh']; // ← GIÁ TỪ DB
        $thanhTien = $soLuong * $giaBan;
        $stmtCT    = $pdo->prepare("INSERT INTO ChiTietDH (maDH, maSach, soLuong, giaBan, thanhTien) VALUES (?, ?, ?, ?, ?)");
        $stmtCT->execute([$maDonHang, $ms, $soLuong, $giaBan, $thanhTien]);
    }

    // —— BƯỚC 7: COMMIT + DọN DẸP —————————————————————————————————
    $pdo->commit(); // Xác nhận toàn bộ transaction (trừ kho + tạo đơn)

    // Xóa giỏ hàng (session + DB + localStorage)
    unset($_SESSION['cart_temp'], $_SESSION['cart']); // Xóa khỏi session
    if (isset($_SESSION['nguoi_dung_id'])) {
        $stmtXoaGio = $pdo->prepare("DELETE FROM GioHang WHERE maND = ?");
        $stmtXoaGio->execute([(int)$_SESSION['nguoi_dung_id']]);  // Xóa khỏi DB
    }
    $_SESSION['xoa_cart_local'] = true; // Báo JS xóa localStorage khi redirect

    // —— REDIRECT: Tùy phương thức thanh toán ————————————————————————————————
    // COD (1): chuyển thẳng đến trang thành công
    // QR  (2): chuyển đến trang QR code chờ thanh toán
    if ($phuongThuc === 2) {
        header("Location: quetMaQR.php?maDH=$maDonHang&tien=$tongTien");
    } else {
        header("Location: thanhCong.php?maDH=$maDonHang");
    }
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    die("<script>alert('Lỗi đặt hàng: " . addslashes($e->getMessage()) . "'); history.back();</script>");
}
