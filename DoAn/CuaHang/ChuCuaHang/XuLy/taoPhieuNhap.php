<?php
// ══════════════════════════════════════════════════════
//  taoPhieuNhap.php — Xử lý tạo phiếu nhập hàng
// ══════════════════════════════════════════════════════
// [BẢO MẬT] Kiểm tra quyền Admin trước tiên
require_once __DIR__ . '/../_kiemTraQuyen.php';
require_once '../../../KetNoi/config/db.php';

function redirectNhap(string $msg, string $loai = 'success'): never {
    header('Location: ../index.php?trang=nhapHang&tab=phieu&thongbao=' . urlencode($msg) . '&loai=' . $loai);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirectNhap('Yêu cầu không hợp lệ.', 'error');

$maPN     = trim($_POST['maPN']  ?? '');
$maNCC    = (int)($_POST['maNCC'] ?? 0);
$dsMaSach = $_POST['maSach']    ?? [];
$dsSoL    = $_POST['soLuong']   ?? [];
$dsGia    = $_POST['giaNhap']   ?? [];
$dsCK     = $_POST['chietKhau'] ?? [];

if (empty($maPN) || $maNCC <= 0) redirectNhap('Thiếu mã phiếu hoặc nhà cung cấp.', 'error');

// Lọc các dòng không trống
$chiTiet = [];
for ($i = 0; $i < count($dsMaSach); $i++) {
    $ms = trim($dsMaSach[$i] ?? '');
    $sl = (int)($dsSoL[$i] ?? 0);
    $gn = (float)($dsGia[$i] ?? 0);
    $ck = max(0, min(100, (float)($dsCK[$i] ?? 0)));
    if ($ms === '' || $sl <= 0 || $gn <= 0) continue;
    $thanhTien = $gn * $sl * (1 - $ck / 100);
    $chiTiet[] = ['maSach' => $ms, 'soLuong' => $sl, 'giaNhap' => $gn, 'chietKhau' => $ck, 'thanhTien' => $thanhTien];
}

if (empty($chiTiet)) redirectNhap('Vui lòng nhập ít nhất 1 dòng sách hợp lệ.', 'error');

try {
    $pdo->beginTransaction();

    // Kiểm tra mã phiếu
    $dup = $pdo->prepare("SELECT 1 FROM PhieuNhap WHERE maPN = ?");
    $dup->execute([$maPN]);
    if ($dup->fetchColumn()) {
        $pdo->rollBack();
        redirectNhap("Mã phiếu '$maPN' đã tồn tại.", 'error');
    }

    $tongTien    = array_sum(array_column($chiTiet, 'thanhTien'));
    $tongLuong   = array_sum(array_column($chiTiet, 'soLuong'));

    // Tạo phiếu nhập
    $pdo->prepare("
        INSERT INTO PhieuNhap (maPN, tongLuongNhap, soTienDaThanhToan, tongTien, trangThai, maNCC)
        VALUES (?, ?, 0, ?, 'Waiting', ?)
    ")->execute([$maPN, $tongLuong, $tongTien, $maNCC]);

    // Chi tiết phiếu nhập + cập nhật tồn kho sách
    $stmtCT  = $pdo->prepare("INSERT INTO ChiTietPN (maPN, maSach, soLuongNhap, giaNhap, chietKhau, thanhTien) VALUES (?,?,?,?,?,?)");
    $stmtKho = $pdo->prepare("UPDATE Sach SET soLuongTon = soLuongTon + ? WHERE maSach = ?");
    foreach ($chiTiet as $ct) {
        $stmtCT->execute([$maPN, $ct['maSach'], $ct['soLuong'], $ct['giaNhap'], $ct['chietKhau'], $ct['thanhTien']]);
        $stmtKho->execute([$ct['soLuong'], $ct['maSach']]);
    }

    // Cập nhật công nợ NCC (INSERT hoặc UPDATE)
    $pdo->prepare("
        INSERT INTO CongNo (maNCC, tongNo) VALUES (?, ?)
        ON DUPLICATE KEY UPDATE tongNo = tongNo + VALUES(tongNo)
    ")->execute([$maNCC, $tongTien]);

    $pdo->commit();
    redirectNhap("Đã tạo phiếu nhập $maPN thành công. Tổng tiền: " . number_format($tongTien,0,',','.') . '₫');

} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    redirectNhap('Lỗi: ' . $e->getMessage(), 'error');
}
