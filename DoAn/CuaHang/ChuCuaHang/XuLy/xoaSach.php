<?php
// ══════════════════════════════════════════════════════
//  xoaSach.php — Xóa hoặc toggle trạng thái sách
// ══════════════════════════════════════════════════════
session_start();
require_once '../../../KetNoi/config/db.php';

function redirectSach(string $msg, string $loai = 'success'): never {
    header('Location: ../index.php?trang=sachVaTonKho&thongbao=' . urlencode($msg) . '&loai=' . $loai);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirectSach('Yêu cầu không hợp lệ.', 'error');

$maSach   = trim($_POST['maSach']    ?? '');
$hanhDong = trim($_POST['hanh_dong'] ?? '');

if (empty($maSach)) redirectSach('Thiếu mã sách.', 'error');

try {
    if ($hanhDong === 'xoa') {
        // Kiểm tra có đơn hàng đã sử dụng sách này chưa
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM ChiTietDH WHERE maSach = ?");
        $stmtCheck->execute([$maSach]);
        if ($stmtCheck->fetchColumn() > 0) {
            redirectSach("Không thể xóa '$maSach' vì đã có trong đơn hàng. Hãy dùng Ngưng KD thay thế.", 'error');
        }
        $pdo->prepare("DELETE FROM Sach WHERE maSach = ?")->execute([$maSach]);
        redirectSach("Đã xóa sách $maSach.");

    } elseif ($hanhDong === 'toggle_tt') {
        $stmtGet = $pdo->prepare("SELECT trangThai FROM Sach WHERE maSach = ?");
        $stmtGet->execute([$maSach]);
        $tt = $stmtGet->fetchColumn();

        $ttMoi = $tt === 'DangKD' ? 'NgungKD' : 'DangKD';
        $pdo->prepare("UPDATE Sach SET trangThai = ? WHERE maSach = ?")->execute([$ttMoi, $maSach]);

        $msg = $ttMoi === 'NgungKD' ? "Đã ngưng kinh doanh sách $maSach." : "Đã kích hoạt lại sách $maSach.";
        redirectSach($msg);

    } else {
        redirectSach('Hành động không hợp lệ.', 'error');
    }
} catch (Throwable $e) {
    redirectSach('Lỗi: ' . $e->getMessage(), 'error');
}
