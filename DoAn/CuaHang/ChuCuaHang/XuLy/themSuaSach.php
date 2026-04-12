<?php
// ══════════════════════════════════════════════════════
//  themSuaSach.php — Xử lý thêm / sửa sách
//  Mã sách tự sinh dạng SXXX (S001, S002..., S100...)
//  Tồn kho KHÔNG nhập thủ công — chỉ qua phiếu nhập
// ══════════════════════════════════════════════════════
// [BẢO MẬT] Kiểm tra quyền Admin trước tiên
require_once __DIR__ . '/../_kiemTraQuyen.php';
require_once '../../../KetNoi/config/db.php';

function redirectSach(string $msg, string $loai = 'success'): never {
    header('Location: ../index.php?trang=sachVaTonKho&thongbao=' . urlencode($msg) . '&loai=' . $loai);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirectSach('Yêu cầu không hợp lệ.', 'error');

$maSach_cu  = trim($_POST['maSach_cu']  ?? '');   // rỗng = thêm mới
$tenSach    = trim($_POST['tenSach']    ?? '');
$maNXB      = (int)($_POST['maNXB']    ?? 0);
$namSX      = (int)($_POST['namSX']    ?? date('Y'));
$giaBan     = (float)($_POST['giaBan'] ?? 0);
$loaiBia    = in_array($_POST['loaiBia'] ?? '', ['Bìa Mềm','Bìa Cứng']) ? $_POST['loaiBia'] : 'Bìa Mềm';
$trangThai  = in_array($_POST['trangThai'] ?? '', ['DangKD','NgungKD']) ? $_POST['trangThai'] : 'DangKD';
$moTa       = trim($_POST['moTa']      ?? '');
$urlAnh     = trim($_POST['urlAnh']    ?? '');
$maTG_list  = $_POST['maTG'] ?? [];
$maTL_list  = $_POST['maTL'] ?? [];

// ── Xử lý upload ảnh bìa (ưu tiên file upload hơn URL text) ─────────────
$anhDaUpload = '';
if (!empty($_FILES['anhBia_file']['name']) && $_FILES['anhBia_file']['error'] === UPLOAD_ERR_OK) {
    $file       = $_FILES['anhBia_file'];
    $ext        = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $extChoPhep = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    if (!in_array($ext, $extChoPhep)) {
        redirectSach('Định dạng ảnh không hợp lệ. Chỉ chấp nhận JPG, PNG, WEBP, GIF.', 'error');
    }

    $tenGocSach = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($file['name']));
    $tenFileMoi = time() . '_' . $tenGocSach;
    $thuMuc     = __DIR__ . '/../../../HinhAnh/sach/';

    if (!is_dir($thuMuc)) mkdir($thuMuc, 0755, true);
    if (!move_uploaded_file($file['tmp_name'], $thuMuc . $tenFileMoi)) {
        redirectSach('Không thể lưu file ảnh. Kiểm tra quyền ghi thư mục HinhAnh/sach/.', 'error');
    }
    $anhDaUpload = '/DoAn-Web/DoAn/HinhAnh/sach/' . $tenFileMoi;
}
if ($anhDaUpload !== '') $urlAnh = $anhDaUpload;

$isEdit = $maSach_cu !== '';

// ── Validate cơ bản ──────────────────────────────────────────────────────
if (empty($tenSach) || $giaBan <= 0 || $maNXB <= 0) {
    redirectSach('Vui lòng điền đầy đủ tên sách, NXB và giá bán.', 'error');
}

try {
    $pdo->beginTransaction();

    if ($isEdit) {
        // ── SỬA sách (KHÔNG cập nhật soLuongTon) ──────────────────────
        $pdo->prepare("
            UPDATE Sach SET tenSach=?, maNXB=?, namSX=?, loaiBia=?, giaBan=?, moTa=?, trangThai=?
            WHERE maSach=?
        ")->execute([$tenSach, $maNXB, $namSX, $loaiBia, $giaBan, $moTa, $trangThai, $maSach_cu]);

        $pdo->prepare("DELETE FROM Sach_TacGia  WHERE maSach = ?")->execute([$maSach_cu]);
        $pdo->prepare("DELETE FROM Sach_TheLoai WHERE maSach = ?")->execute([$maSach_cu]);

        if ($urlAnh !== '') {
            $pdo->prepare("DELETE FROM HinhAnhSach WHERE maSach = ?")->execute([$maSach_cu]);
            $pdo->prepare("INSERT INTO HinhAnhSach (maSach, urlAnh) VALUES (?, ?)")->execute([$maSach_cu, $urlAnh]);
        }
        $maSachActive = $maSach_cu;

    } else {
        // ── THÊM mới — Tự sinh mã sách dạng SXXX ──────────────────────
        $stmtMax = $pdo->query("
            SELECT MAX(CAST(SUBSTRING(maSach, 2) AS UNSIGNED))
            FROM Sach
            WHERE maSach REGEXP '^S[0-9]+$'
        ");
        $soLonNhat = (int)($stmtMax->fetchColumn() ?? 0);
        $soMoi     = $soLonNhat + 1;
        // Pad: S001...S099..S100..S999 (tự mở rộng chữ số nếu vượt 999)
        $maSach    = 'S' . str_pad($soMoi, max(3, strlen((string)$soMoi)), '0', STR_PAD_LEFT);

        // Phòng ngừa race condition
        $dup = $pdo->prepare("SELECT 1 FROM Sach WHERE maSach = ?");
        $dup->execute([$maSach]);
        if ($dup->fetchColumn()) {
            $pdo->rollBack();
            redirectSach("Mã sách '$maSach' vừa được tạo bởi thao tác khác. Vui lòng thử lại.", 'error');
        }

        // soLuongTon luôn = 0 khi thêm mới — chỉ tăng qua phiếu nhập
        $pdo->prepare("
            INSERT INTO Sach (maSach, tenSach, maNXB, namSX, loaiBia, giaBan, soLuongTon, moTa, trangThai)
            VALUES (?, ?, ?, ?, ?, ?, 0, ?, ?)
        ")->execute([$maSach, $tenSach, $maNXB, $namSX, $loaiBia, $giaBan, $moTa, $trangThai]);

        if ($urlAnh !== '') {
            $pdo->prepare("INSERT INTO HinhAnhSach (maSach, urlAnh) VALUES (?, ?)")->execute([$maSach, $urlAnh]);
        }
        $maSachActive = $maSach;
    }

    // ── Tác giả & Thể loại ────────────────────────────────────────────────
    $stmtTG = $pdo->prepare("INSERT IGNORE INTO Sach_TacGia (maSach, maTG) VALUES (?, ?)");
    foreach ($maTG_list as $maTG) {
        $stmtTG->execute([$maSachActive, (int)$maTG]);
    }

    $stmtTL = $pdo->prepare("INSERT IGNORE INTO Sach_TheLoai (maSach, maTL) VALUES (?, ?)");
    foreach ($maTL_list as $maTL) {
        $stmtTL->execute([$maSachActive, (int)$maTL]);
    }

    $pdo->commit();

    $msg = $isEdit
        ? "Đã cập nhật sách '$tenSach'."
        : "Đã thêm '$tenSach' (mã: $maSachActive). Tồn kho = 0, vui lòng tạo phiếu nhập để nhập hàng.";
    redirectSach($msg);

} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    redirectSach('Lỗi: ' . $e->getMessage(), 'error');
}
