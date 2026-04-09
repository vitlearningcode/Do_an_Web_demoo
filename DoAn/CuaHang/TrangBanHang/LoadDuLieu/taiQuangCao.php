<?php
/**
 * taiQuangCao.php
 * Tải danh sách banner quảng cáo đang hoạt động.
 *
 * Yêu cầu: $pdo đã được khởi tạo từ file gọi (index.php).
 * Kết quả:
 *   $ds_quangCao — Mảng các banner quảng cáo (trangThai = 1), sắp xếp theo maQC ASC
 */

$ds_quangCao = $pdo->query("SELECT * FROM QuangCao WHERE trangThai = 1 ORDER BY maQC ASC")->fetchAll(PDO::FETCH_ASSOC);
