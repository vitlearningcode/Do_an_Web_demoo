<?php
// ══════════════════════════════════════════════════════
//  capNhatTaiKhoan.php — Bật / tắt tài khoản người dùng
// ══════════════════════════════════════════════════════
// [BẢO MẬT] Kiểm tra quyền Admin trước tiên
require_once __DIR__ . '/../../_kiemTraQuyen.php';
require_once '../../../KetNoi/config/db.php';

function redirectTK(string $msg, string $loai = 'success'): never {
    header('Location: ../index.php?trang=taiKhoan&thongbao=' . urlencode($msg) . '&loai=' . $loai);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirectTK('Yêu cầu không hợp lệ.', 'error');

$tenDN = trim($_POST['tenDN'] ?? '');
if (empty($tenDN)) redirectTK('Thiếu tên đăng nhập.', 'error');

try {
    // Lấy trạng thái + vai trò hiện tại
    $stmtGet = $pdo->prepare("SELECT trangThai, maVT FROM TaiKhoan WHERE tenDN = ?");
    $stmtGet->execute([$tenDN]);
    $tk = $stmtGet->fetch(PDO::FETCH_ASSOC);

    if (!$tk) redirectTK("Không tìm thấy tài khoản '$tenDN'.", 'error');

    // Không cho khóa Admin
    if ($tk['maVT'] == 1) redirectTK('Không thể thay đổi trạng thái tài khoản Admin.', 'error');

    $ttMoi = $tk['trangThai'] === 'on' ? 'off' : 'on';
    $pdo->prepare("UPDATE TaiKhoan SET trangThai = ? WHERE tenDN = ?")->execute([$ttMoi, $tenDN]);

    $msg = $ttMoi === 'off' ? "Đã khóa tài khoản '$tenDN'." : "Đã mở khóa tài khoản '$tenDN'.";
    redirectTK($msg);

} catch (Throwable $e) {
    redirectTK('Lỗi: ' . $e->getMessage(), 'error');
}
