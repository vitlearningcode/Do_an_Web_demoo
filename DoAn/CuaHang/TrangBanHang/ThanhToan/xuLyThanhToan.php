<?php
/**
 * ThanhToan/xuLyThanhToan.php — Xử lý đặt hàng
 * Hỗ trợ 2 kiểu địa chỉ:
 *   - loai_dia_chi = 'da_luu'  → dùng maDC từ dropdown
 *   - loai_dia_chi = 'moi'     → nhập địa chỉ mới (tạo bản ghi DiaChiGiaoHang)
 * Thuần PHP — không AJAX.
 */
session_start();
require_once "../../../KetNoi/config/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['cart_temp'])) {
    die("Lỗi: Dữ liệu không hợp lệ.");
}

$gioHang    = $_SESSION['cart_temp'];
$hoTen      = trim($_POST['hoten']       ?? '');
$sdt        = trim($_POST['sdt']         ?? '');
$email      = trim($_POST['email']       ?? '');
$loaiDiaChi = trim($_POST['loai_dia_chi'] ?? 'moi');
$phuongThuc = (int)($_POST['phuong_thuc'] ?? 1);

// ── 1. XÁC ĐỊNH NGƯỜI DÙNG (đã đăng nhập / khách vãng lai) ──────────────────
$maND = null;
if (isset($_SESSION['nguoi_dung_id'])) {
    $maND = (int)$_SESSION['nguoi_dung_id'];
} else {
    // Tìm hoặc tạo khách vãng lai theo email / SĐT
    $stmtTimKH = $pdo->prepare("SELECT maND FROM NguoiDung WHERE email = ? OR sdt = ? LIMIT 1");
    $stmtTimKH->execute([$email, $sdt]);
    $khachHang = $stmtTimKH->fetch();

    if ($khachHang) {
        $maND = $khachHang['maND'];
    } else {
        $stmtTaoKH = $pdo->prepare("INSERT INTO NguoiDung (tenND, sdt, email) VALUES (?, ?, ?)");
        $stmtTaoKH->execute([$hoTen, $sdt, $email]);
        $maND = (int)$pdo->lastInsertId();
    }
}

// ── 2. XÁC ĐỊNH MÃ ĐỊA CHỈ GIAO HÀNG ───────────────────────────────────────
$maDC = null;

if ($loaiDiaChi === 'da_luu') {
    // Dùng địa chỉ đã lưu — xác nhận địa chỉ thuộc về user này
    $maDCGui = (int)($_POST['ma_dia_chi'] ?? 0);
    $stmtKT  = $pdo->prepare("SELECT maDC FROM DiaChiGiaoHang WHERE maDC = ? AND maND = ? LIMIT 1");
    $stmtKT->execute([$maDCGui, $maND]);
    $kiemTra = $stmtKT->fetch();

    if ($kiemTra) {
        $maDC = $maDCGui;
    } else {
        die("<script>alert('Địa chỉ giao hàng không hợp lệ.'); history.back();</script>");
    }
} else {
    // Nhập địa chỉ mới → tạo bản ghi DiaChiGiaoHang (không mặc định)
    $diaChiMoi = trim($_POST['dia_chi_moi'] ?? '');
    if (empty($diaChiMoi)) {
        die("<script>alert('Vui lòng nhập địa chỉ giao hàng.'); history.back();</script>");
    }
    $stmtTaoDC = $pdo->prepare("INSERT INTO DiaChiGiaoHang (maND, diaChiChiTiet, laMacDinh) VALUES (?, ?, 0)");
    $stmtTaoDC->execute([$maND, $diaChiMoi]);
    $maDC = (int)$pdo->lastInsertId();
}

// ── 3. TẠO MÃ ĐƠN HÀNG ──────────────────────────────────────────────────────
$maDonHang = 'DH' . time() . rand(10, 99);
$tongTien  = 0;

$pdo->beginTransaction();
try {
    // 4. KIỂM TRA & TRỪ KHO
    foreach ($gioHang as $sanPham) {
        $maSach  = $sanPham['maSach'];
        $soLuong = (int)$sanPham['soLuong'];

        $stmtKho = $pdo->prepare("SELECT soLuongTon, tenSach FROM Sach WHERE maSach = ? FOR UPDATE");
        $stmtKho->execute([$maSach]);
        $sachDB = $stmtKho->fetch();

        if (!$sachDB || $sachDB['soLuongTon'] < $soLuong) {
            throw new Exception("Sản phẩm '{$sanPham['tenSach']}' không đủ số lượng trong kho.");
        }

        $tongTien += $sanPham['giaBan'] * $soLuong;

        $stmtTruKho = $pdo->prepare("UPDATE Sach SET soLuongTon = soLuongTon - ? WHERE maSach = ?");
        $stmtTruKho->execute([$soLuong, $maSach]);
    }

    // 5. LƯU ĐƠN HÀNG
    $stmtDH = $pdo->prepare("INSERT INTO DonHang (maDH, maND, maDC, maPT, tongTien, trangThai) VALUES (?, ?, ?, ?, ?, 'ChoDuyet')");
    $stmtDH->execute([$maDonHang, $maND, $maDC, $phuongThuc, $tongTien]);

    // 6. LƯU CHI TIẾT ĐƠN
    foreach ($gioHang as $sanPham) {
        $soLuong   = (int)$sanPham['soLuong'];
        $giaBan    = (float)$sanPham['giaBan'];
        $thanhTien = $soLuong * $giaBan;
        $stmtCT    = $pdo->prepare("INSERT INTO ChiTietDH (maDH, maSach, soLuong, giaBan, thanhTien) VALUES (?, ?, ?, ?, ?)");
        $stmtCT->execute([$maDonHang, $sanPham['maSach'], $soLuong, $giaBan, $thanhTien]);
    }

    $pdo->commit();

    // Dọn dẹp giỏ hàng
    unset($_SESSION['cart_temp'], $_SESSION['cart']);
    if (isset($_SESSION['nguoi_dung_id'])) {
        $stmtXoaGio = $pdo->prepare("DELETE FROM GioHang WHERE maND = ?");
        $stmtXoaGio->execute([(int)$_SESSION['nguoi_dung_id']]);
    }
    $_SESSION['xoa_cart_local'] = true;

    // Chuyển hướng
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
