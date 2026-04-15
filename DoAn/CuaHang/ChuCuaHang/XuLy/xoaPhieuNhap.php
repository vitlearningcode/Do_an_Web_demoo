<?php
// ══════════════════════════════════════════════════════
//  xoaPhieuNhap.php — Xóa phiếu nhập (chỉ khi chưa thanh toán)
//
//  Điều kiện xóa:
//    1. soTienDaThanhToan = 0
//    2. Không có record trong LichSuThanhToanPN
//
//  Khi xóa (transaction):
//    1. Trừ lại tồn kho từng sách trong ChiTietPN
//    2. Trừ CongNo NCC tương ứng
//    3. Xóa ChiTietPN
//    4. Xóa PhieuNhap
// ══════════════════════════════════════════════════════
require_once __DIR__ . '/../_kiemTraQuyen.php';
require_once '../../../KetNoi/config/db.php';

function redirectNhap(string $msg, string $loai = 'success'): never {
    header('Location: ../index.php?trang=nhapHang&tab=phieu&thongbao=' . urlencode($msg) . '&loai=' . $loai);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirectNhap('Yêu cầu không hợp lệ.', 'error');

$maPN = trim($_POST['maPN'] ?? '');
if (empty($maPN)) redirectNhap('Thiếu mã phiếu.', 'error');

try {
    $pdo->beginTransaction();

    // ── [1] Lấy thông tin phiếu nhập ──────────────────────────
    $stmtPN = $pdo->prepare("SELECT * FROM PhieuNhap WHERE maPN = ? FOR UPDATE");
    $stmtPN->execute([$maPN]);
    $pn = $stmtPN->fetch(PDO::FETCH_ASSOC);

    if (!$pn) {
        $pdo->rollBack();
        redirectNhap("Không tìm thấy phiếu $maPN.", 'error');
    }

    // ── [2] Kiểm tra điều kiện: chưa thanh toán ───────────────
    if ((float)$pn['soTienDaThanhToan'] > 0) {
        $pdo->rollBack();
        redirectNhap("Không thể xóa phiếu $maPN: đã có thanh toán {$pn['soTienDaThanhToan']}₫.", 'error');
    }

    // ── [3] Kiểm tra không có lịch sử thanh toán ──────────────
    $stmtLS = $pdo->prepare("SELECT COUNT(*) FROM LichSuThanhToanPN WHERE maPN = ?");
    $stmtLS->execute([$maPN]);
    if ((int)$stmtLS->fetchColumn() > 0) {
        $pdo->rollBack();
        redirectNhap("Không thể xóa phiếu $maPN: đã phát sinh lịch sử thanh toán.", 'error');
    }

    // ── [4] Lấy chi tiết phiếu để trừ kho ─────────────────────
    $stmtCT = $pdo->prepare("SELECT maSach, soLuongNhap FROM ChiTietPN WHERE maPN = ?");
    $stmtCT->execute([$maPN]);
    $dsChiTiet = $stmtCT->fetchAll(PDO::FETCH_ASSOC);

    // ── [5] Trừ lại tồn kho từng sách ─────────────────────────
    $stmtTruKho = $pdo->prepare("UPDATE Sach SET soLuongTon = GREATEST(0, soLuongTon - ?) WHERE maSach = ?");
    foreach ($dsChiTiet as $ct) {
        $stmtTruKho->execute([(int)$ct['soLuongNhap'], $ct['maSach']]);
    }

    // ── [6] Trừ công nợ NCC ────────────────────────────────────
    $pdo->prepare("
        UPDATE CongNo
        SET tongNo = GREATEST(0, tongNo - ?)
        WHERE maNCC = ?
    ")->execute([(float)$pn['tongTien'], (int)$pn['maNCC']]);

    // ── [7] Xóa chi tiết phiếu nhập ───────────────────────────
    $pdo->prepare("DELETE FROM ChiTietPN WHERE maPN = ?")->execute([$maPN]);

    // ── [8] Xóa phiếu nhập ────────────────────────────────────
    $pdo->prepare("DELETE FROM PhieuNhap WHERE maPN = ?")->execute([$maPN]);

    $pdo->commit();
    redirectNhap("Đã xóa phiếu nhập $maPN và hoàn lại tồn kho.");

} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    redirectNhap('Lỗi hệ thống: ' . $e->getMessage(), 'error');
}
