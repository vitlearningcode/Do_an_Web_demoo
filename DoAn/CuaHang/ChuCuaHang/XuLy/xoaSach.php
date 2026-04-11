<?php
// ══════════════════════════════════════════════════════
//  xoaSach.php — Xóa hoặc toggle trạng thái sách
//  Logic xóa an toàn 2 lớp:
//    1. Đã có trong ChiTietDH → KHÔNG xóa, chuyển NgưngKD
//    2. Tồn kho < tổng nhập (đã bán) → KHÔNG xóa, chuyển NgưngKD
//    3. Chưa bán, chưa trong đơn → xóa thật
// ══════════════════════════════════════════════════════
// [BẢO MẬT] Kiểm tra quyền Admin trước tiên
require_once __DIR__ . '/../../_kiemTraQuyen.php';
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
        // ── Lớp 1: Kiểm tra đơn hàng ─────────────────────────────────
        $stmtDH = $pdo->prepare("SELECT COUNT(*) FROM ChiTietDH WHERE maSach = ?");
        $stmtDH->execute([$maSach]);
        $coDonHang = $stmtDH->fetchColumn() > 0;

        // ── Lớp 2: Tồn kho đã giảm (đã bán qua phiếu nhập) ─────────
        $stmtTon = $pdo->prepare("SELECT soLuongTon FROM Sach WHERE maSach = ?");
        $stmtTon->execute([$maSach]);
        $tonHienTai = (int)($stmtTon->fetchColumn() ?? 0);

        $stmtNhap = $pdo->prepare("SELECT COALESCE(SUM(soLuongNhap), 0) FROM ChiTietPN WHERE maSach = ?");
        $stmtNhap->execute([$maSach]);
        $tongNhap = (int)($stmtNhap->fetchColumn() ?? 0);

        // Tồn còn nguyên nếu: chưa nhập lần nào (= 0) hoặc tồn = tổng nhập
        $tonConNguyen = ($tongNhap === 0) ? true : ($tonHienTai >= $tongNhap);

        if ($coDonHang) {
            // Đã có trong đơn hàng → chỉ ngưng KD
            $pdo->prepare("UPDATE Sach SET trangThai = 'NgungKD' WHERE maSach = ?")
                ->execute([$maSach]);
            redirectSach("Sách '$maSach' đã có mặt trong đơn hàng — không thể xóa. Đã chuyển sang Ngưng kinh doanh.", 'info');
        }

        if (!$tonConNguyen) {
            // Tồn đã giảm, có người đã mua qua phiếu nhập → chỉ ngưng KD
            $pdo->prepare("UPDATE Sach SET trangThai = 'NgungKD' WHERE maSach = ?")
                ->execute([$maSach]);
            redirectSach("Sách '$maSach' đã từng được bán (tồn kho đã giảm) — không thể xóa. Đã chuyển sang Ngưng kinh doanh.", 'info');
        }

        // Chưa bán, chưa trong đơn, tồn còn nguyên → xóa thật
        $pdo->prepare("DELETE FROM Sach WHERE maSach = ?")->execute([$maSach]);
        redirectSach("Đã xóa vĩnh viễn sách $maSach.");

    } elseif ($hanhDong === 'toggle_tt') {
        $stmtGet = $pdo->prepare("SELECT trangThai FROM Sach WHERE maSach = ?");
        $stmtGet->execute([$maSach]);
        $tt = $stmtGet->fetchColumn();

        if ($tt === false) redirectSach("Không tìm thấy sách '$maSach'.", 'error');

        $ttMoi = ($tt === 'DangKD') ? 'NgungKD' : 'DangKD';
        $pdo->prepare("UPDATE Sach SET trangThai = ? WHERE maSach = ?")
            ->execute([$ttMoi, $maSach]);

        $msg = $ttMoi === 'NgungKD'
            ? "Đã ngưng kinh doanh sách $maSach."
            : "Đã kích hoạt lại sách $maSach.";
        redirectSach($msg);

    } else {
        redirectSach('Hành động không hợp lệ.', 'error');
    }
} catch (Throwable $e) {
    redirectSach('Lỗi: ' . $e->getMessage(), 'error');
}
