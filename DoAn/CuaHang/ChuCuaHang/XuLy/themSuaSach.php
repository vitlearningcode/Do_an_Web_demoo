<?php
// ══════════════════════════════════════════════════════
//  themSuaSach.php — Xử lý thêm / sửa sách
//  - Mã sách tự sinh dạng SXXX (S001, S002..., S100...)
//  - Ảnh bìa: tên file s{maSach}_{timestamp}.{ext}
//  - Tác giả / Thể loại: checkbox + thêm mới inline
//  - Tồn kho KHÔNG nhập thủ công — chỉ qua phiếu nhập
// ══════════════════════════════════════════════════════
require_once __DIR__ . '/../_kiemTraQuyen.php';
require_once '../../../KetNoi/config/db.php';

function redirectSach(string $msg, string $loai = 'success'): never {
    header('Location: ../index.php?trang=sachVaTonKho&thongbao=' . urlencode($msg) . '&loai=' . $loai);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirectSach('Yêu cầu không hợp lệ.', 'error');

// ── Đọc dữ liệu form ─────────────────────────────────
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
$tenTGMoi   = trim($_POST['tenTG_moi'] ?? '');   // Tác giả mới thêm
$tenTLMoi   = trim($_POST['tenTL_moi'] ?? '');   // Thể loại mới thêm

$isEdit = $maSach_cu !== '';

// ── Validate sớm ─────────────────────────────────────
if (empty($tenSach) || $giaBan <= 0 || $maNXB <= 0) {
    redirectSach('Vui lòng điền đầy đủ tên sách, NXB và giá bán.', 'error');
}

// ── Kiểm tra file upload (chưa move — cần biết maSach trước) ────────────
$coFileUpload = false;
$tmpFileAnh   = '';
$extAnh       = '';
if (!empty($_FILES['anhBia_file']['name']) && $_FILES['anhBia_file']['error'] === UPLOAD_ERR_OK) {
    $extChoPhep = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $extAnh = strtolower(pathinfo($_FILES['anhBia_file']['name'], PATHINFO_EXTENSION));
    if (!in_array($extAnh, $extChoPhep)) {
        redirectSach('Định dạng ảnh không hợp lệ. Chỉ chấp nhận JPG, PNG, WEBP, GIF.', 'error');
    }
    $tmpFileAnh   = $_FILES['anhBia_file']['tmp_name'];
    $coFileUpload = true;
}

try {
    $pdo->beginTransaction();

    // ── Tác giả mới (thêm vào DB trước) ─────────────
    if ($tenTGMoi !== '') {
        $pdo->prepare("INSERT IGNORE INTO TacGia (tenTG) VALUES (?)")->execute([$tenTGMoi]);
        $newId = (int)$pdo->lastInsertId();
        if ($newId === 0) {
            $r = $pdo->prepare("SELECT maTG FROM TacGia WHERE tenTG = ?");
            $r->execute([$tenTGMoi]); $newId = (int)$r->fetchColumn();
        }
        if ($newId > 0) $maTG_list[] = $newId;
    }

    // ── Thể loại mới (thêm vào DB trước) ────────────
    if ($tenTLMoi !== '') {
        $pdo->prepare("INSERT IGNORE INTO TheLoai (tenTL) VALUES (?)")->execute([$tenTLMoi]);
        $newId = (int)$pdo->lastInsertId();
        if ($newId === 0) {
            $r = $pdo->prepare("SELECT maTL FROM TheLoai WHERE tenTL = ?");
            $r->execute([$tenTLMoi]); $newId = (int)$r->fetchColumn();
        }
        if ($newId > 0) $maTL_list[] = $newId;
    }

    if ($isEdit) {
        // ── SỬA sách ────────────────────────────────────
        $maSachActive = $maSach_cu;

        $pdo->prepare("
            UPDATE Sach SET tenSach=?, maNXB=?, namSX=?, loaiBia=?, giaBan=?, moTa=?, trangThai=?
            WHERE maSach=?
        ")->execute([$tenSach, $maNXB, $namSX, $loaiBia, $giaBan, $moTa, $trangThai, $maSach_cu]);

        $pdo->prepare("DELETE FROM Sach_TacGia  WHERE maSach = ?")->execute([$maSach_cu]);
        $pdo->prepare("DELETE FROM Sach_TheLoai WHERE maSach = ?")->execute([$maSach_cu]);

        // Upload ảnh (nếu có) — tên: s{maSach}_{time}.{ext}
        if ($coFileUpload) {
            $tenFile    = 's' . strtolower($maSachActive) . '_' . time() . '.' . $extAnh;
            $thuMuc     = __DIR__ . '/../../../HinhAnh/sach/';
            if (!is_dir($thuMuc)) mkdir($thuMuc, 0755, true);
            if (!move_uploaded_file($tmpFileAnh, $thuMuc . $tenFile)) {
                $pdo->rollBack();
                redirectSach('Không thể lưu file ảnh.', 'error');
            }
            $urlAnh = '/DoAn-Web/DoAn/HinhAnh/sach/' . $tenFile;
        }

        if ($urlAnh !== '') {
            $pdo->prepare("DELETE FROM HinhAnhSach WHERE maSach = ?")->execute([$maSach_cu]);
            $pdo->prepare("INSERT INTO HinhAnhSach (maSach, urlAnh) VALUES (?, ?)")->execute([$maSach_cu, $urlAnh]);
        }

    } else {
        // ── THÊM mới — Tự sinh mã sách dạng SXXX ────────
        $stmtMax = $pdo->query("
            SELECT MAX(CAST(SUBSTRING(maSach, 2) AS UNSIGNED))
            FROM Sach
            WHERE maSach REGEXP '^S[0-9]+$'
        ");
        $soLonNhat = (int)($stmtMax->fetchColumn() ?? 0);
        $soMoi     = $soLonNhat + 1;
        $maSach    = 'S' . str_pad($soMoi, max(3, strlen((string)$soMoi)), '0', STR_PAD_LEFT);
        $maSachActive = $maSach;

        // Phòng ngừa race condition
        $dup = $pdo->prepare("SELECT 1 FROM Sach WHERE maSach = ?");
        $dup->execute([$maSach]);
        if ($dup->fetchColumn()) {
            $pdo->rollBack();
            redirectSach("Mã sách '$maSach' vừa được tạo bởi thao tác khác. Vui lòng thử lại.", 'error');
        }

        // Upload ảnh nếu có — tên: s{maSach}_{time}.{ext}
        if ($coFileUpload) {
            $tenFile = 's' . strtolower($maSach) . '_' . time() . '.' . $extAnh;
            $thuMuc  = __DIR__ . '/../../../HinhAnh/sach/';
            if (!is_dir($thuMuc)) mkdir($thuMuc, 0755, true);
            if (!move_uploaded_file($tmpFileAnh, $thuMuc . $tenFile)) {
                $pdo->rollBack();
                redirectSach('Không thể lưu file ảnh.', 'error');
            }
            $urlAnh = '/DoAn-Web/DoAn/HinhAnh/sach/' . $tenFile;
        }

        // soLuongTon luôn = 0 khi thêm mới — chỉ tăng qua phiếu nhập
        $pdo->prepare("
            INSERT INTO Sach (maSach, tenSach, maNXB, namSX, loaiBia, giaBan, soLuongTon, moTa, trangThai)
            VALUES (?, ?, ?, ?, ?, ?, 0, ?, ?)
        ")->execute([$maSach, $tenSach, $maNXB, $namSX, $loaiBia, $giaBan, $moTa, $trangThai]);

        if ($urlAnh !== '') {
            $pdo->prepare("INSERT INTO HinhAnhSach (maSach, urlAnh) VALUES (?, ?)")->execute([$maSach, $urlAnh]);
        }
    }

    // ── Liên kết Tác giả & Thể loại ─────────────────
    $stmtTG = $pdo->prepare("INSERT IGNORE INTO Sach_TacGia (maSach, maTG) VALUES (?, ?)");
    foreach ($maTG_list as $maTG) {
        if ((int)$maTG > 0) $stmtTG->execute([$maSachActive, (int)$maTG]);
    }

    $stmtTL = $pdo->prepare("INSERT IGNORE INTO Sach_TheLoai (maSach, maTL) VALUES (?, ?)");
    foreach ($maTL_list as $maTL) {
        if ((int)$maTL > 0) $stmtTL->execute([$maSachActive, (int)$maTL]);
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
