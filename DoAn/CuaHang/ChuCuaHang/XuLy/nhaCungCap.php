<?php
// ══════════════════════════════════════════════════════
//  XuLy/nhaCungCap.php — Xử lý thêm, sửa, xóa Nhà Cung Cấp
//  Sử dụng PRG pattern + URL flash message (?thongbao=&loai=)
// ══════════════════════════════════════════════════════

session_start();
require_once '../../../KetNoi/config/db.php';

// Kiểm tra quyền Admin
if (!isset($_SESSION['vaitro']) || strtolower($_SESSION['vaitro']) !== 'admin') {
    header('Location: ../../../index.php');
    exit;
}

$redirect = '../index.php?trang=nhaCungCap';

function chuyenTrang(string $url, string $msg, string $loai = 'success'): never {
    header('Location: ' . $url . '&thongbao=' . rawurlencode($msg) . '&loai=' . $loai);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $redirect);
    exit;
}

$hanh_dong = trim($_POST['hanh_dong'] ?? '');

// ── Xóa ─────────────────────────────────────────────
if ($hanh_dong === 'xoa') {
    $maNCC = (int)($_POST['maNCC'] ?? 0);
    if ($maNCC <= 0) chuyenTrang($redirect, 'Mã nhà cung cấp không hợp lệ.', 'error');

    try {
        $stmt = $pdo->prepare("DELETE FROM NhaCungCap WHERE maNCC = ?");
        $stmt->execute([$maNCC]);
        chuyenTrang($redirect, 'Đã xóa nhà cung cấp thành công.');
    } catch (PDOException $e) {
        $thongBao = (str_contains($e->getMessage(), '1451') || $e->getCode() == 23000)
            ? 'Không thể xóa: Nhà cung cấp đã có phiếu nhập hàng liên kết.'
            : 'Lỗi hệ thống: ' . $e->getMessage();
        chuyenTrang($redirect, $thongBao, 'error');
    }
}

// ── Thêm / Sửa ───────────────────────────────────────
$tenNCC           = trim($_POST['tenNCC'] ?? '');
$sdt              = trim($_POST['sdt'] ?? '');
$email            = trim($_POST['email'] ?? '');
$chietKhauMacDinh = max(0, min(100, (float)($_POST['chietKhauMacDinh'] ?? 0)));

if (empty($tenNCC)) {
    chuyenTrang($redirect, 'Vui lòng nhập tên nhà cung cấp.', 'error');
}

if ($hanh_dong === 'them') {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO NhaCungCap (tenNCC, sdt, email, chietKhauMacDinh)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$tenNCC, $sdt ?: null, $email ?: null, $chietKhauMacDinh]);
        chuyenTrang($redirect, 'Đã thêm nhà cung cấp "' . $tenNCC . '" thành công.');
    } catch (PDOException $e) {
        chuyenTrang($redirect, 'Thêm thất bại: ' . $e->getMessage(), 'error');
    }
}

if ($hanh_dong === 'sua') {
    $maNCC = (int)($_POST['maNCC'] ?? 0);
    if ($maNCC <= 0) chuyenTrang($redirect, 'Mã không hợp lệ.', 'error');

    try {
        $stmt = $pdo->prepare("
            UPDATE NhaCungCap
            SET tenNCC = ?, sdt = ?, email = ?, chietKhauMacDinh = ?
            WHERE maNCC = ?
        ");
        $stmt->execute([$tenNCC, $sdt ?: null, $email ?: null, $chietKhauMacDinh, $maNCC]);
        chuyenTrang($redirect, "Đã cập nhật nhà cung cấp #{$maNCC}.");
    } catch (PDOException $e) {
        chuyenTrang($redirect, 'Cập nhật thất bại: ' . $e->getMessage(), 'error');
    }
}

// Fallback
header('Location: ' . $redirect);
exit;
