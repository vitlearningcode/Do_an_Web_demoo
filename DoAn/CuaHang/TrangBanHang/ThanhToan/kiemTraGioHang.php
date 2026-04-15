<?php
/**
 * ============================================================
 * LUỒNG: KIỂM TRA GIỏ HÀNG TRƯỚC THANH TOÁN
 *
 * GọI BỚI: thanhToan.php (require_once ở đầu trang)
 *   + luồng phụ: trang chiết khấu, đổi mã (nếu có sau này)
 *
 * YÊU CẦU:
 *   - session_start() đã gọi
 *   - $pdo đã có (từ require db.php)
 *   - $_SESSION['cart'] hoặc $_SESSION['cart_temp'] có dữ liệu
 *
 * XEM NẠY LÀ TUỒNG CƠ SỞNG: TIVI và QUAN TRọNG thường thất bại nhất
 *
 * OUTPUT:
 *   $gioHang  — mảng đã xác thực: [{maSach, tenSach, giaBan←DB, soLuong, hinhAnh, tacGia}]
 *   $tongTien — tổng tiền tính bằng giá từ DB
 *   $_SESSION['cart_temp'] = $gioHang (lưu cho xuất LýThanhToan.php)
 *
 * TẠI SAO PHẢI QUERY GIÁ LẠI?
 *   - $_SESSION['cart'] chỉ có maSach + soLuong (không có giá)
 *   - Ngay cả nếu có giá trong session, cũng phải query lại
 *     vì giá có thể thay đổi sau khi user đã đưa vào giỏ (flash sale kết thúc, admin sửa giá)
 * ============================================================
 */

// —— 1. Lấy danh sách maSach + soLuong từ session ——————————————————————
// Uu tiên $_SESSION['cart'] (giỏ hiện tại)
// Fallback $_SESSION['cart_temp'] (nếu user reload trang thanh toán)
$cartRaw = [];
if (!empty($_SESSION['cart'])) {
    $cartRaw = $_SESSION['cart'];
} elseif (!empty($_SESSION['cart_temp'])) {
    // cart_temp: giỏ đã qua xác thực (từ lần load thanhToan.php trước)
    // Trường hợp: user reload trang thanh toán mà không có cart trong session
    $cartRaw = $_SESSION['cart_temp'];
}

// Nếu cả hai đều rỗng: giỏ hàng trống / session hết hạn
// Không redirect bằng header() vì file này được include giữa trang — dùng JS
if (empty($cartRaw)) {
    echo "<script>alert('Giỏ hàng trống hoặc phiên giao dịch đã hết hạn!'); window.location.href='/DoAn-Web/DoAn/index.php';</script>";
    exit;
}

// —— 2. Trích xuất maSach và soLuong ———————————————————————————
// CHỈ TIN soLuong từ session (do user đặt)
// GIÁ hoàn toàn không được lấy từ đây — bước 3 sẽ query DB
$dsMaSach   = [];          // Mảng mà sách — dùng cho IN (?)
$mapSoLuong = [];          // maSach => soLuong
foreach ($cartRaw as $item) {
    $ms = trim($item['maSach'] ?? '');
    $sl = max(1, (int)($item['soLuong'] ?? 1)); // Đảm bảo tối thiểu là 1
    if ($ms !== '') {
        $dsMaSach[]       = $ms;
        $mapSoLuong[$ms]  = $sl;
    }
}

if (empty($dsMaSach)) {
    echo "<script>alert('Giỏ hàng trống hoặc phiên giao dịch đã hết hạn!'); window.location.href='/DoAn-Web/DoAn/index.php';</script>";
    exit;
}

// —— 3. QUERY DB: LẤY THÔNG TIN SÁCH + GIÁ THẬT (Có FLASH SALE) ————————————
// Đây là lần XÁC THỰC GIÁ LẦN 1 (lần 2 sẽ trong xuất LýThanhToan.php)
// IN ($inPlaceholders): PDO tự thoát các giá trị — an toàn SQL
$inPlaceholders = implode(',', array_fill(0, count($dsMaSach), '?'));

$sqlLayGia = "
    SELECT
        s.maSach,
        s.tenSach,
        s.giaBan,
        COALESCE(ha.urlAnh, '') AS hinhAnh,
        COALESCE(
            GROUP_CONCAT(DISTINCT tg.tenTG ORDER BY tg.maTG SEPARATOR ', '),
            'Đang cập nhật'
        ) AS tacGia,
        -- Áp dụng flash sale nếu còn hiệu lực
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
    LEFT JOIN (
        SELECT maSach, MIN(urlAnh) AS urlAnh
        FROM HinhAnhSach
        GROUP BY maSach
    ) ha ON ha.maSach = s.maSach
    LEFT JOIN Sach_TacGia stg ON stg.maSach = s.maSach
    LEFT JOIN TacGia tg       ON tg.maTG   = stg.maTG
    WHERE s.maSach IN ($inPlaceholders)
    GROUP BY s.maSach, s.tenSach, s.giaBan, ha.urlAnh
";

$stmtGia = $pdo->prepare($sqlLayGia);
$stmtGia->execute($dsMaSach); // Truyền mảng maSach làm tham số bấo mật
$sachDB = $stmtGia->fetchAll(PDO::FETCH_ASSOC);

// Build lookup map: maSach => dong_DB (để tìm nhanh bước xây dựng $gioHang)
$mapSachDB = [];
foreach ($sachDB as $row) {
    $mapSachDB[$row['maSach']] = $row;
}

// —— 4. XÂY DỰNG $gioHang VỚI GIÁ TỪ DB —————————————————————————————
// Giế giảm bảo mật: $gioHang không có giá từ client, chỉ từ DB
$gioHang = [];
foreach ($dsMaSach as $ms) {
    if (!isset($mapSachDB[$ms])) {
        // Sách bị xóa khỏi DB sau khi user bỏ vào giỏ — bỏ qua
        continue;
    }
    $dbRow = $mapSachDB[$ms];

    // THỨC TẤ: ƯU TIÊN giá flash sale nếu còn hiệu lực
    $giaChinh = ($dbRow['giaSau'] !== null)
        ? (float)$dbRow['giaSau']  // Giá sau flash sale
        : (float)$dbRow['giaBan']; // Giá gốc

    $gioHang[] = [
        'maSach'  => $ms,
        'tenSach' => $dbRow['tenSach'],
        'giaBan'  => $giaChinh,          // ← GIÁ TỪ DB, không từ session/client
        'hinhAnh' => $dbRow['hinhAnh'],
        'tacGia'  => $dbRow['tacGia'],
        'soLuong' => $mapSoLuong[$ms],   // ← số lượng từ session
    ];
}

if (empty($gioHang)) {
    echo "<script>alert('Không tìm thấy sản phẩm hợp lệ trong giỏ hàng!'); window.location.href='/DoAn-Web/DoAn/index.php';</script>";
    exit;
}

// —— 5. LƯŲ TẠM VÀO SESSION ———————————————————————————————————————
// cart_temp — giỏ đã XÁC THỰC: giá từ DB, tên, ảnh,... đủ dùng để hiển thị + đặt hàng
// xuất LýThanhToan.php: $gioHang = $_SESSION['cart_temp']
// (không query được cart_temp, chỉ dùng mơi checkout thanhCong)
$_SESSION['cart_temp'] = $gioHang;

// —— 6. TÍNH TỔNG TIỀN (DÙNG GIÁ TỪ DB) ————————————————————————————
// $tongTien được dùng bởi thanhToan.php để hiển thị
// xuấtLyđoat.php sẽ tính lại từ DB (không tin $tongTien từ session)
$tongTien = 0;
foreach ($gioHang as $sanPham) {
    $tongTien += $sanPham['giaBan'] * $sanPham['soLuong'];
}
