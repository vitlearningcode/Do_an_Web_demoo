<?php
session_start();
require_once "../../../KetNoi/config/db.php";

$maDH = $_GET['maDH'] ?? '';
$isTimeout = isset($_GET['timeout']) && $_GET['timeout'] == '1';

if (!$maDH) {
    die("Không tìm thấy đơn hàng để hủy.");
}

$pdo->beginTransaction();
try {
    // Lấy thông tin đơn hàng
    $stmt = $pdo->prepare("SELECT trangThai FROM DonHang WHERE maDH = ? FOR UPDATE");
    $stmt->execute([$maDH]);
    $dh = $stmt->fetch();
    
    if ($dh && $dh['trangThai'] === 'ChoDuyet') {
        // Đơn hàng đang ở trạng thái chờ duyệt (Hoặc chờ thanh toán QR), ta tiến hành hủy
        $updDH = $pdo->prepare("UPDATE DonHang SET trangThai = 'DaHuy' WHERE maDH = ?");
        $updDH->execute([$maDH]);
        
        // Hoàn kho
        $stmtCT = $pdo->prepare("SELECT maSach, soLuong FROM ChiTietDH WHERE maDH = ?");
        $stmtCT->execute([$maDH]);
        $chiTiets = $stmtCT->fetchAll();
        
        $updKho = $pdo->prepare("UPDATE Sach SET soLuongTon = soLuongTon + ? WHERE maSach = ?");
        foreach ($chiTiets as $ct) {
            $updKho->execute([$ct['soLuong'], $ct['maSach']]);
        }
    }
    
    $pdo->commit();
    
    $msg = $isTimeout ? "Đã hết thời gian thanh toán (7 phút). Đơn hàng đã tự động bị hủy và hoàn trả sách vào kho!" : "Bạn đã hủy thanh toán. Đơn hàng đã được hoàn kho!";
    echo "<script>alert('$msg'); window.location.href='../../../index.php';</script>";
} catch (Exception $e) {
    $pdo->rollBack();
    die("Lỗi hủy đơn: " . $e->getMessage());
}
