<?php
// ══════════════════════════════════════════════════════
//  themSuaSach.php — Xử lý thêm / sửa sách
// ══════════════════════════════════════════════════════
session_start();
require_once '../../../KetNoi/config/db.php';

function redirectSach(string $msg, string $loai = 'success'): never {
    header('Location: ../index.php?trang=sachVaTonKho&thongbao=' . urlencode($msg) . '&loai=' . $loai);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirectSach('Yêu cầu không hợp lệ.', 'error');

$maSach_cu  = trim($_POST['maSach_cu']  ?? '');   // rỗng = thêm mới
$maSach     = trim($_POST['maSach']     ?? '');
$tenSach    = trim($_POST['tenSach']    ?? '');
$maNXB      = (int)($_POST['maNXB']    ?? 0);
$namSX      = (int)($_POST['namSX']    ?? date('Y'));
$giaBan     = (float)($_POST['giaBan'] ?? 0);
$soLuong    = max(0, (int)($_POST['soLuongTon'] ?? 0));
$loaiBia    = in_array($_POST['loaiBia'] ?? '', ['Bìa Mềm','Bìa Cứng']) ? $_POST['loaiBia'] : 'Bìa Mềm';
$trangThai  = in_array($_POST['trangThai'] ?? '', ['DangKD','NgungKD']) ? $_POST['trangThai'] : 'DangKD';
$moTa       = trim($_POST['moTa']      ?? '');
$urlAnh     = trim($_POST['urlAnh']    ?? '');
$maTG_list  = $_POST['maTG'] ?? [];
$maTL_list  = $_POST['maTL'] ?? [];

$isEdit = $maSach_cu !== '';

// Validate
if (empty($tenSach) || $giaBan <= 0 || $maNXB <= 0) {
    redirectSach('Vui lòng điền đầy đủ tên sách, NXB và giá bán.', 'error');
}
if (!$isEdit && empty($maSach)) {
    redirectSach('Vui lòng nhập mã sách.', 'error');
}

try {
    $pdo->beginTransaction();

    if ($isEdit) {
        // Sửa
        $pdo->prepare("
            UPDATE Sach SET tenSach=?, maNXB=?, namSX=?, loaiBia=?, giaBan=?, soLuongTon=?, moTa=?, trangThai=?
            WHERE maSach=?
        ")->execute([$tenSach, $maNXB, $namSX, $loaiBia, $giaBan, $soLuong, $moTa, $trangThai, $maSach_cu]);

        // Cập nhật tác giả & thể loại
        $pdo->prepare("DELETE FROM Sach_TacGia  WHERE maSach = ?")->execute([$maSach_cu]);
        $pdo->prepare("DELETE FROM Sach_TheLoai WHERE maSach = ?")->execute([$maSach_cu]);

        // Cập nhật ảnh bìa (nếu nhập URL mới)
        if ($urlAnh !== '') {
            $pdo->prepare("DELETE FROM HinhAnhSach WHERE maSach = ?")->execute([$maSach_cu]);
            $pdo->prepare("INSERT INTO HinhAnhSach (maSach, urlAnh) VALUES (?, ?)")->execute([$maSach_cu, $urlAnh]);
        }

        $maSachActive = $maSach_cu;
    } else {
        // Kiểm tra mã sách không trùng
        $dup = $pdo->prepare("SELECT 1 FROM Sach WHERE maSach = ?");
        $dup->execute([$maSach]);
        if ($dup->fetchColumn()) {
            $pdo->rollBack();
            redirectSach("Mã sách '$maSach' đã tồn tại.", 'error');
        }

        // Thêm mới
        $pdo->prepare("
            INSERT INTO Sach (maSach, tenSach, maNXB, namSX, loaiBia, giaBan, soLuongTon, moTa, trangThai)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ")->execute([$maSach, $tenSach, $maNXB, $namSX, $loaiBia, $giaBan, $soLuong, $moTa, $trangThai]);

        if ($urlAnh !== '') {
            $pdo->prepare("INSERT INTO HinhAnhSach (maSach, urlAnh) VALUES (?, ?)")->execute([$maSach, $urlAnh]);
        }

        $maSachActive = $maSach;
    }

    // Thêm tác giả
    $stmtTG = $pdo->prepare("INSERT IGNORE INTO Sach_TacGia (maSach, maTG) VALUES (?, ?)");
    foreach ($maTG_list as $maTG) {
        $stmtTG->execute([$maSachActive, (int)$maTG]);
    }

    // Thêm thể loại
    $stmtTL = $pdo->prepare("INSERT IGNORE INTO Sach_TheLoai (maSach, maTL) VALUES (?, ?)");
    foreach ($maTL_list as $maTL) {
        $stmtTL->execute([$maSachActive, (int)$maTL]);
    }

    $pdo->commit();
    redirectSach($isEdit ? "Đã cập nhật sách '$tenSach'." : "Đã thêm sách '$tenSach' thành công.");

} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    redirectSach('Lỗi: ' . $e->getMessage(), 'error');
}
