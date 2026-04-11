<?php
/**
 * xuly_huyDon.php — Cho phép khách tự hủy đơn đang ở trạng thái ChoDuyet.
 *
 * Điều kiện hủy hợp lệ:
 *   1. Khách phải đăng nhập (session nguoi_dung_id)
 *   2. Đơn hàng phải thuộc về khách này
 *   3. Trạng thái phải là 'ChoDuyet'
 *
 * Sau khi hủy: hoàn lại tồn kho (Transaction) → redirect về donHang/index.php
 * Thuần PHP — không AJAX.
 */
session_start();
require_once '../../../KetNoi/config/db.php';

// Hàm redirect kèm thông báo
function redirectVeDonHang(string $tab = 'cho-duyet', string $tb = ''): never {
    $url = 'index.php?tab=' . urlencode($tab);
    if ($tb !== '') $url .= '&tb=' . urlencode($tb);
    header('Location: ' . $url);
    exit;
}

// [1] Bắt buộc đăng nhập
if (!isset($_SESSION['nguoi_dung_id'])) {
    header('Location: ../../../index.php');
    exit;
}

// [2] Chỉ chấp nhận POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectVeDonHang('cho-duyet', 'Yêu cầu không hợp lệ.');
}

$maND = (int)$_SESSION['nguoi_dung_id'];
$maDH = trim($_POST['maDH'] ?? '');

if (empty($maDH)) {
    redirectVeDonHang('cho-duyet', 'Thiếu mã đơn hàng.');
}

try {
    $pdo->beginTransaction();

    // [3] Kiểm tra đơn hàng: phải thuộc về user này VÀ đang ChoDuyet
    $stmtCheck = $pdo->prepare("
        SELECT trangThai FROM DonHang
        WHERE maDH = ? AND maND = ?
        FOR UPDATE
    ");
    $stmtCheck->execute([$maDH, $maND]);
    $donHang = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$donHang) {
        $pdo->rollBack();
        redirectVeDonHang('cho-duyet', 'Không tìm thấy đơn hàng hoặc đơn không thuộc về bạn.');
    }

    if ($donHang['trangThai'] !== 'ChoDuyet') {
        $pdo->rollBack();
        redirectVeDonHang('cho-duyet', 'Chỉ có thể hủy đơn đang ở trạng thái Chờ Duyệt.');
    }

    // [4] Cập nhật trạng thái → DaHuy
    $pdo->prepare("UPDATE DonHang SET trangThai = 'DaHuy' WHERE maDH = ?")
        ->execute([$maDH]);

    // [5] Hoàn lại tồn kho
    $stmtCT = $pdo->prepare("SELECT maSach, soLuong FROM ChiTietDH WHERE maDH = ?");
    $stmtCT->execute([$maDH]);
    $chiTiet = $stmtCT->fetchAll(PDO::FETCH_ASSOC);

    $stmtHoanKho = $pdo->prepare("UPDATE Sach SET soLuongTon = soLuongTon + ? WHERE maSach = ?");
    foreach ($chiTiet as $ct) {
        $stmtHoanKho->execute([(int)$ct['soLuong'], $ct['maSach']]);
    }

    $pdo->commit();
    redirectVeDonHang('da-huy', 'ok_huy');

} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    redirectVeDonHang('cho-duyet', 'Lỗi hệ thống: ' . $e->getMessage());
}
