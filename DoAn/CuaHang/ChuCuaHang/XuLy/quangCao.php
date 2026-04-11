<?php
// ══════════════════════════════════════════════════════
//  quangCao.php — Xử lý CRUD banner / quảng cáo
//  Hành động: them | sua | xoa | doi_trang_thai
// ══════════════════════════════════════════════════════
require_once __DIR__ . '/../../_kiemTraQuyen.php';
require_once '../../../KetNoi/config/db.php';

function redirectQC(string $msg, string $loai = 'success'): never {
    header('Location: ../index.php?trang=khuyenMai&tab=quangcao&thongbao=' . urlencode($msg) . '&loai=' . $loai);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirectQC('Yêu cầu không hợp lệ.', 'error');

$hanhDong = trim($_POST['hanh_dong'] ?? '');
$maQC     = (int)($_POST['maQC'] ?? 0);

// ── Xử lý upload ảnh ──────────────────────────────────────────────────────
function xuLyUploadAnh(): string {
    if (!empty($_FILES['hinhAnh_file']['name']) && $_FILES['hinhAnh_file']['error'] === UPLOAD_ERR_OK) {
        $file       = $_FILES['hinhAnh_file'];
        $ext        = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $extChophep = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (!in_array($ext, $extChophep)) return '';
        $tenFile = 'banner_' . time() . '.' . $ext;
        $thuMuc  = __DIR__ . '/../../../HinhAnh/banner/';
        if (!is_dir($thuMuc)) mkdir($thuMuc, 0755, true);
        if (move_uploaded_file($file['tmp_name'], $thuMuc . $tenFile)) {
            return '/DoAn/HinhAnh/banner/' . $tenFile;
        }
    }
    return trim($_POST['hinhAnh_url'] ?? '');
}

try {
    switch ($hanhDong) {

        case 'them':
            $hinhAnh = xuLyUploadAnh();
            $nhan    = trim($_POST['nhan']    ?? '');
            $tieuDe  = trim($_POST['tieuDe']  ?? '');
            $moTa    = trim($_POST['moTa']    ?? '');
            $chuNut  = trim($_POST['chuNut']  ?? 'Xem thêm');
            $mauNen  = trim($_POST['mauNen']  ?? 'blue');
            if (empty($hinhAnh) || empty($nhan) || empty($tieuDe)) {
                redirectQC('Vui lòng nhập đầy đủ hình ảnh, nhãn và tiêu đề.', 'error');
            }
            $pdo->prepare("
                INSERT INTO QuangCao (hinhAnh, nhan, tieuDe, moTa, chuNut, mauNen, trangThai)
                VALUES (?, ?, ?, ?, ?, ?, 1)
            ")->execute([$hinhAnh, $nhan, $tieuDe, $moTa, $chuNut, $mauNen]);
            redirectQC('Đã thêm banner quảng cáo thành công.');

        case 'sua':
            if ($maQC <= 0) redirectQC('Thiếu mã quảng cáo.', 'error');
            $hinhAnhMoi = xuLyUploadAnh();
            $nhan    = trim($_POST['nhan']   ?? '');
            $tieuDe  = trim($_POST['tieuDe'] ?? '');
            $moTa    = trim($_POST['moTa']   ?? '');
            $chuNut  = trim($_POST['chuNut'] ?? 'Xem thêm');
            $mauNen  = trim($_POST['mauNen'] ?? 'blue');
            if (empty($nhan) || empty($tieuDe)) {
                redirectQC('Vui lòng nhập đủ nhãn và tiêu đề.', 'error');
            }
            if ($hinhAnhMoi !== '') {
                $pdo->prepare("
                    UPDATE QuangCao SET hinhAnh=?, nhan=?, tieuDe=?, moTa=?, chuNut=?, mauNen=? WHERE maQC=?
                ")->execute([$hinhAnhMoi, $nhan, $tieuDe, $moTa, $chuNut, $mauNen, $maQC]);
            } else {
                $pdo->prepare("
                    UPDATE QuangCao SET nhan=?, tieuDe=?, moTa=?, chuNut=?, mauNen=? WHERE maQC=?
                ")->execute([$nhan, $tieuDe, $moTa, $chuNut, $mauNen, $maQC]);
            }
            redirectQC("Đã cập nhật banner #$maQC.");

        case 'xoa':
            if ($maQC <= 0) redirectQC('Thiếu mã quảng cáo.', 'error');
            $pdo->prepare("DELETE FROM QuangCao WHERE maQC = ?")->execute([$maQC]);
            redirectQC("Đã xóa banner #$maQC.");

        case 'doi_trang_thai':
            if ($maQC <= 0) redirectQC('Thiếu mã quảng cáo.', 'error');
            $stmt = $pdo->prepare("SELECT trangThai FROM QuangCao WHERE maQC = ?");
            $stmt->execute([$maQC]);
            $tt = (int)$stmt->fetchColumn();
            $ttMoi = $tt === 1 ? 0 : 1;
            $pdo->prepare("UPDATE QuangCao SET trangThai = ? WHERE maQC = ?")->execute([$ttMoi, $maQC]);
            redirectQC($ttMoi === 1 ? "Đã bật hiển thị banner #$maQC." : "Đã tắt banner #$maQC.");

        default:
            redirectQC('Hành động không hợp lệ.', 'error');
    }
} catch (Throwable $e) {
    redirectQC('Lỗi: ' . $e->getMessage(), 'error');
}
