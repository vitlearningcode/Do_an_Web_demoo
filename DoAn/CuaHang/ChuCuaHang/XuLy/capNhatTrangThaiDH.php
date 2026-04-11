<?php
// ══════════════════════════════════════════════════════
//  capNhatTrangThaiDH.php — Xử lý duyệt / hủy đơn hàng
// ══════════════════════════════════════════════════════
// [BẢO MẬT] Kiểm tra quyền Admin trước tiên
require_once __DIR__ . '/../../_kiemTraQuyen.php';
require_once '../../../KetNoi/config/db.php';

// Hàm redirect về trang đơn hàng kèm thông báo
function redirectDonHang(string $msg, string $loai = 'success', string $loc = 'TatCa'): never {
    $url = '../index.php?trang=donHang&loc=' . urlencode($loc)
         . '&thongbao=' . urlencode($msg)
         . '&loai=' . $loai;
    header('Location: ' . $url);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectDonHang('Yêu cầu không hợp lệ.', 'error');
}

$maDH         = trim($_POST['maDH']         ?? '');
$trangThaiMoi = trim($_POST['trangThaiMoi'] ?? '');
$redirectLoc  = trim($_POST['redirect_loc'] ?? 'TatCa');

// Validate
$cacTrangThaiHopLe = ['ChoDuyet', 'DangGiao', 'HoanThanh', 'DaHuy'];
if (empty($maDH) || !in_array($trangThaiMoi, $cacTrangThaiHopLe)) {
    redirectDonHang('Dữ liệu không hợp lệ.', 'error', $redirectLoc);
}

try {
    $pdo->beginTransaction();

    // Kiểm tra đơn hàng tồn tại
    $stmtCheck = $pdo->prepare("SELECT trangThai FROM DonHang WHERE maDH = ?");
    $stmtCheck->execute([$maDH]);
    $donHang = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$donHang) {
        $pdo->rollBack();
        redirectDonHang("Không tìm thấy đơn hàng $maDH.", 'error', $redirectLoc);
    }

    $trangThaiCu = $donHang['trangThai'];

    // Kiểm tra chuyển trạng thái hợp lệ
    $luongHopLe = [
        'ChoDuyet'  => ['DangGiao', 'DaHuy'],
        'DangGiao'  => ['HoanThanh'],
        'HoanThanh' => [],
        'DaHuy'     => [],
    ];

    if (!in_array($trangThaiMoi, $luongHopLe[$trangThaiCu] ?? [])) {
        $pdo->rollBack();
        redirectDonHang("Không thể chuyển từ '$trangThaiCu' → '$trangThaiMoi'.", 'error', $redirectLoc);
    }

    // Cập nhật trạng thái
    $stmtUpdate = $pdo->prepare("UPDATE DonHang SET trangThai = ? WHERE maDH = ?");
    $stmtUpdate->execute([$trangThaiMoi, $maDH]);

    // Nếu HoanThanh → KHÔNG cần trừ tồn kho ở đây vì đã trừ lúc đặt hàng (tùy thiết kế)
    // Nếu DaHuy → hoàn lại tồn kho
    if ($trangThaiMoi === 'DaHuy') {
        $stmtChiTiet = $pdo->prepare("
            SELECT maSach, soLuong FROM ChiTietDH WHERE maDH = ?
        ");
        $stmtChiTiet->execute([$maDH]);
        $chiTiet = $stmtChiTiet->fetchAll(PDO::FETCH_ASSOC);

        foreach ($chiTiet as $ct) {
            $pdo->prepare("
                UPDATE Sach SET soLuongTon = soLuongTon + ? WHERE maSach = ?
            ")->execute([$ct['soLuong'], $ct['maSach']]);
        }
    }

    $pdo->commit();

    $tenTT = match($trangThaiMoi) {
        'DangGiao'  => 'Đang giao',
        'HoanThanh' => 'Hoàn thành',
        'DaHuy'     => 'Đã hủy',
        default     => $trangThaiMoi,
    };

    redirectDonHang("Đơn $maDH đã được cập nhật → $tenTT.", 'success', $redirectLoc);

} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    redirectDonHang('Lỗi hệ thống: ' . $e->getMessage(), 'error', $redirectLoc);
}
