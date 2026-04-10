<?php
session_start();
require_once "../../../KetNoi/config/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['cart_temp'])) {
    die("Lỗi: Dữ liệu không hợp lệ.");
}

$cart = $_SESSION['cart_temp'];
$hoTen = trim($_POST['hoten'] ?? '');
$sdt = trim($_POST['sdt'] ?? '');
$email = trim($_POST['email'] ?? '');
$diaChi = trim($_POST['diachi'] ?? '');
$phuongThuc = (int)($_POST['phuong_thuc'] ?? 1);

// 1. CHỐT NGƯỜI DÙNG (GUEST / LOGGED IN)
$maND = null;
if (isset($_SESSION['nguoi_dung_id'])) {
    $maND = $_SESSION['nguoi_dung_id'];
} else {
    // Tìm hoặc tạo khách vãng lai
    $stmt = $pdo->prepare("SELECT maND FROM NguoiDung WHERE email = ? OR sdt = ? LIMIT 1");
    $stmt->execute([$email, $sdt]);
    $user = $stmt->fetch();
    
    if ($user) {
        $maND = $user['maND'];
    } else {
        $stmtIns = $pdo->prepare("INSERT INTO NguoiDung (tenND, sdt, email) VALUES (?, ?, ?)");
        $stmtIns->execute([$hoTen, $sdt, $email]);
        $maND = $pdo->lastInsertId();
    }
}

// 2. KHỞI TẠO ĐỊA CHỈ GIAO HÀNG (Nếu chưa có, hoặc cứ tạo mới)
$stmtDC = $pdo->prepare("INSERT INTO DiaChiGiaoHang (maND, diaChiChiTiet) VALUES (?, ?)");
$stmtDC->execute([$maND, $diaChi]);
$maDC = $pdo->lastInsertId();

// 3. TẠO MÃ ĐƠN HÀNG
$maDH = 'DH' . time() . rand(10, 99);
$tongTien = 0;

$pdo->beginTransaction();
try {
    // 4. KIỂM TRA & TRỪ KHO TẬP TRUNG
    foreach ($cart as $item) {
        $maSach = $item['maSach'];
        $sl = (int)$item['soLuong'];
        
        // Lock Row for update
        $checkStmt = $pdo->prepare("SELECT soLuongTon, tenSach FROM Sach WHERE maSach = ? FOR UPDATE");
        $checkStmt->execute([$maSach]);
        $sachDB = $checkStmt->fetch();
        
        if (!$sachDB || $sachDB['soLuongTon'] < $sl) {
            throw new Exception("Sản phẩm '{$item['tenSach']}' không đủ số lượng trong kho.");
        }
        
        $tongTien += ($item['giaBan'] * $sl);
        
        // Trừ kho luôn (Shopee Model: Trừ tại checkout form)
        $updKho = $pdo->prepare("UPDATE Sach SET soLuongTon = soLuongTon - ? WHERE maSach = ?");
        $updKho->execute([$sl, $maSach]);
    }
    
    // 5. LƯU ĐƠN HÀNG VÀO DB
    $stmtDH = $pdo->prepare("INSERT INTO DonHang (maDH, maND, maDC, maPT, tongTien, trangThai) VALUES (?, ?, ?, ?, ?, 'ChoDuyet')");
    $stmtDH->execute([$maDH, $maND, $maDC, $phuongThuc, $tongTien]);
    
    // Lưu Chi Tiết
    foreach ($cart as $item) {
        $sl = (int)$item['soLuong'];
        $gia = (float)$item['giaBan'];
        $thanhTien = $sl * $gia;
        $stmtCT = $pdo->prepare("INSERT INTO ChiTietDH (maDH, maSach, soLuong, giaBan, thanhTien) VALUES (?, ?, ?, ?, ?)");
        $stmtCT->execute([$maDH, $item['maSach'], $sl, $gia, $thanhTien]);
    }
    
    $pdo->commit();
    
    // Dọn dẹp
    unset($_SESSION['cart_temp']);
    unset($_SESSION['cart']);
    // Xóa giỏ hàng trong DB sau khi đặt hàng thành công
    if (isset($_SESSION['nguoi_dung_id'])) {
        $stmtXoaGio = $pdo->prepare("DELETE FROM GioHang WHERE maND = ?");
        $stmtXoaGio->execute([(int)$_SESSION['nguoi_dung_id']]);
    }
    // Cần 1 cờ để JS xóa localstorage khi quay lại trang chủ.
    $_SESSION['xoa_cart_local'] = true;
    
    // XỬ LÝ CHUYỂN HƯỚNG
    if ($phuongThuc === 2) { // QR Bank
        header("Location: quetMaQR.php?maDH=$maDH&tien=$tongTien");
    } else {
        header("Location: thanhCong.php?maDH=$maDH");
    }
    exit;
    
} catch (Exception $e) {
    $pdo->rollBack();
    die("<script>alert('Lỗi đặt hàng: " . $e->getMessage() . "'); history.back();</script>");
}
