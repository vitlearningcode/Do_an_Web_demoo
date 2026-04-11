<?php
// ══════════════════════════════════════════════════════
//  taoKhuyenMai.php — Tạo chiến dịch khuyến mãi mới
// ══════════════════════════════════════════════════════
// [BẢO MẬT] Kiểm tra quyền Admin trước tiên
require_once __DIR__ . '/../_kiemTraQuyen.php';
require_once '../../../KetNoi/config/db.php';

function redirectKM(string $msg, string $loai = 'success'): never {
    header('Location: ../index.php?trang=khuyenMai&thongbao=' . urlencode($msg) . '&loai=' . $loai);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirectKM('Yêu cầu không hợp lệ.', 'error');

$maKM       = trim($_POST['maKM']      ?? '');
$tenKM      = trim($_POST['tenKM']     ?? '');
$ngayBD     = trim($_POST['ngayBatDau']  ?? '');
$ngayKT     = trim($_POST['ngayKetThuc'] ?? '');
$dsMaSach   = $_POST['maSach']        ?? [];
$dsPhanTram = $_POST['phanTramGiam']  ?? [];
$dsSoLuong  = $_POST['soLuongKM']    ?? [];

if (empty($maKM) || empty($tenKM) || empty($ngayBD) || empty($ngayKT)) {
    redirectKM('Vui lòng điền đầy đủ thông tin chiến dịch.', 'error');
}

// Lọc dòng hợp lệ
$chiTiet = [];
for ($i = 0; $i < count($dsMaSach); $i++) {
    $ms = trim($dsMaSach[$i] ?? '');
    $pt = (int)($dsPhanTram[$i] ?? 0);
    $sl = max(1, (int)($dsSoLuong[$i] ?? 1));
    if ($ms === '') continue;
    $chiTiet[] = ['maSach' => $ms, 'phanTramGiam' => $pt, 'soLuong' => $sl];
}

if (empty($chiTiet)) redirectKM('Vui lòng chọn ít nhất 1 sách.', 'error');

try {
    $pdo->beginTransaction();

    // Kiểm tra mã KM
    $dup = $pdo->prepare("SELECT 1 FROM KhuyenMai WHERE maKM = ?");
    $dup->execute([$maKM]);
    if ($dup->fetchColumn()) {
        $pdo->rollBack();
        redirectKM("Mã chiến dịch '$maKM' đã tồn tại.", 'error');
    }

    $pdo->prepare("
        INSERT INTO KhuyenMai (maKM, tenKM, ngayBatDau, ngayKetThuc) VALUES (?,?,?,?)
    ")->execute([$maKM, $tenKM, $ngayBD, $ngayKT]);

    $stmtCT = $pdo->prepare("
        INSERT INTO ChiTietKhuyenMai (maKM, maSach, phanTramGiam, soLuongKhuyenMai) VALUES (?,?,?,?)
    ");
    foreach ($chiTiet as $ct) {
        $stmtCT->execute([$maKM, $ct['maSach'], $ct['phanTramGiam'], $ct['soLuong']]);
    }

    $pdo->commit();
    redirectKM("Đã tạo chiến dịch '$tenKM' thành công với " . count($chiTiet) . " sách.");

} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    redirectKM('Lỗi: ' . $e->getMessage(), 'error');
}
