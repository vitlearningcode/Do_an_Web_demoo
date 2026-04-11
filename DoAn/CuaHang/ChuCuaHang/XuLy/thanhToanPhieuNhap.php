<?php
// ══════════════════════════════════════════════════════
//  thanhToanPhieuNhap.php — Ghi nhận thanh toán NCC
// ══════════════════════════════════════════════════════
// [BẢO MẬT] Kiểm tra quyền Admin trước tiên
require_once __DIR__ . '/../../_kiemTraQuyen.php';
require_once '../../../KetNoi/config/db.php';

function redirectNhap(string $msg, string $loai = 'success'): never {
    header('Location: ../index.php?trang=nhapHang&tab=phieu&thongbao=' . urlencode($msg) . '&loai=' . $loai);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirectNhap('Yêu cầu không hợp lệ.', 'error');

$maPN        = trim($_POST['maPN']        ?? '');
$soTienTra   = (float)($_POST['soTienTra'] ?? 0);
$hinhThuc    = trim($_POST['hinhThucTra']  ?? 'Chuyển khoản');
$ghiChu      = trim($_POST['ghiChu']       ?? '');

if (empty($maPN) || $soTienTra <= 0) redirectNhap('Số tiền không hợp lệ.', 'error');

try {
    $pdo->beginTransaction();

    // Lấy thông tin phiếu
    $stmtPN = $pdo->prepare("SELECT * FROM PhieuNhap WHERE maPN = ? FOR UPDATE");
    $stmtPN->execute([$maPN]);
    $pn = $stmtPN->fetch(PDO::FETCH_ASSOC);
    if (!$pn) {
        $pdo->rollBack();
        redirectNhap("Không tìm thấy phiếu $maPN.", 'error');
    }

    $conNo = (float)$pn['tongTien'] - (float)$pn['soTienDaThanhToan'];
    if ($soTienTra > $conNo) $soTienTra = $conNo; // Không cho trả thừa

    // Ghi lịch sử
    $pdo->prepare("
        INSERT INTO LichSuThanhToanPN (maPN, soTienTra, hinhThucTra, ghiChu)
        VALUES (?, ?, ?, ?)
    ")->execute([$maPN, $soTienTra, $hinhThuc, $ghiChu]);

    // Cập nhật số tiền đã trả
    $soTienMoi = (float)$pn['soTienDaThanhToan'] + $soTienTra;
    $ttMoi     = $soTienMoi >= (float)$pn['tongTien'] ? 'Completed' : 'Waiting';

    $pdo->prepare("
        UPDATE PhieuNhap SET soTienDaThanhToan = ?, trangThai = ? WHERE maPN = ?
    ")->execute([$soTienMoi, $ttMoi, $maPN]);

    // Cập nhật công nợ NCC
    $pdo->prepare("
        UPDATE CongNo SET tongNo = GREATEST(0, tongNo - ?) WHERE maNCC = ?
    ")->execute([$soTienTra, $pn['maNCC']]);

    $pdo->commit();
    redirectNhap("Đã ghi nhận thanh toán " . number_format($soTienTra,0,',','.') . "₫ cho phiếu $maPN.");

} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    redirectNhap('Lỗi: ' . $e->getMessage(), 'error');
}
