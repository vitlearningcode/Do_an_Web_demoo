<?php
/**
 * donHang/xuly_danhGia.php — Lưu đánh giá sản phẩm vào DanhGiaSach
 *
 * Nhận POST từ form đánh giá trong trang donHang/index.php.
 * Kiểm tra: đã đăng nhập, đã mua sách, dữ liệu hợp lệ.
 * Thuần PHP form POST — không AJAX.
 */
session_start();
require_once '../../../KetNoi/config/db.php';

// Bắt buộc đăng nhập
if (!isset($_SESSION['nguoi_dung_id'])) {
    header('Location: ../../../index.php');
    exit;
}

// Chỉ chấp nhận POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$maND   = (int)$_SESSION['nguoi_dung_id'];
$maDH   = trim($_POST['maDH']   ?? '');
$maSach = trim($_POST['maSach'] ?? '');
$diem   = (int)($_POST['diem']  ?? 0);
$nhanXet = trim($_POST['nhanXet'] ?? '');

// Validate
if (empty($maDH) || empty($maSach) || $diem < 1 || $diem > 5) {
    echo "<script>alert('Dữ liệu không hợp lệ!'); history.back();</script>";
    exit;
}

// Kiểm tra: user đã mua sách này trong đơn này chưa?
$stmtCheck = $pdo->prepare("
    SELECT ct.maSach
    FROM ChiTietDH ct
    JOIN DonHang dh ON ct.maDH = dh.maDH
    WHERE ct.maDH = ? AND ct.maSach = ? AND dh.maND = ? AND dh.trangThai = 'HoanThanh'
    LIMIT 1
");
$stmtCheck->execute([$maDH, $maSach, $maND]);

if (!$stmtCheck->fetch()) {
    echo "<script>alert('Bạn chưa mua sản phẩm này hoặc đơn hàng chưa hoàn thành!'); history.back();</script>";
    exit;
}

// Kiểm tra: user này đã đánh giá sách này chưa? (nếu có → cập nhật)
$stmtExist = $pdo->prepare("SELECT maDG FROM DanhGiaSach WHERE maSach = ? AND maND = ? LIMIT 1");
$stmtExist->execute([$maSach, $maND]);
$existing = $stmtExist->fetch();

if ($existing) {
    // Cập nhật đánh giá cũ
    $stmtUpsert = $pdo->prepare("
        UPDATE DanhGiaSach SET diemDG = ?, nhanXet = ?, ngayDG = NOW()
        WHERE maDG = ?
    ");
    $stmtUpsert->execute([$diem, $nhanXet ?: null, $existing['maDG']]);
} else {
    // Thêm mới
    $stmtInsert = $pdo->prepare("
        INSERT INTO DanhGiaSach (maSach, maND, diemDG, nhanXet, ngayDG)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmtInsert->execute([$maSach, $maND, $diem, $nhanXet ?: null]);
}

// Redirect về trang đơn hàng với thông báo thành công
header('Location: index.php?tab=da-giao&tb=ok');
exit;
?>
