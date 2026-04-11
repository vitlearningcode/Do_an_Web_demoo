<?php
/**
 * demDonChoDuyet.php — Đếm số đơn hàng đang ở trạng thái ChoDuyet
 * Kết quả: $soDonChoDuyet (int)
 * Yêu cầu: $pdo đã được khai báo
 */
$soDonChoDuyet = 0;
if (isset($pdo)) {
    try {
        $stmtDemDon = $pdo->query("SELECT COUNT(*) FROM DonHang WHERE trangThai = 'ChoDuyet'");
        $soDonChoDuyet = (int)$stmtDemDon->fetchColumn();
    } catch (Throwable $e) {
        $soDonChoDuyet = 0;
    }
}
