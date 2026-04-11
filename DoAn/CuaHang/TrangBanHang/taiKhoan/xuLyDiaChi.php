<?php
/**
 * xuLyDiaChi.php — Xử lý POST form quản lý địa chỉ giao hàng
 * Kích hoạt khi: $_POST['hanh_dong_dia_chi'] được gửi
 * Hành động: them_moi | dat_mac_dinh | xoa_dia_chi
 *
 * Input:  $_POST (hanh_dong_dia_chi, dia_chi_moi, la_mac_dinh, ma_dc)
 * Output: $thongBao (string), $loaiThongBao (string: 'success'|'error')
 * Yêu cầu: $pdo, $maND đã khai báo
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hanh_dong_dia_chi'])) {
    $hanhDong = $_POST['hanh_dong_dia_chi'];

    if ($hanhDong === 'them_moi') {
        $diaChiMoi  = trim($_POST['dia_chi_moi'] ?? '');
        $laMacDinh  = isset($_POST['la_mac_dinh']) ? 1 : 0;

        if (!empty($diaChiMoi)) {
            if ($laMacDinh) {
                $pdo->prepare("UPDATE DiaChiGiaoHang SET laMacDinh = 0 WHERE maND = ?")->execute([$maND]);
            }
            $stmtThemDiaChi = $pdo->prepare("INSERT INTO DiaChiGiaoHang (maND, diaChiChiTiet, laMacDinh) VALUES (?, ?, ?)");
            $stmtThemDiaChi->execute([$maND, $diaChiMoi, $laMacDinh]);
            $thongBao     = 'Đã thêm địa chỉ mới thành công!';
            $loaiThongBao = 'success';
        } else {
            $thongBao     = 'Địa chỉ không được để trống.';
            $loaiThongBao = 'error';
        }

    } elseif ($hanhDong === 'dat_mac_dinh') {
        $maDiaChi = (int)($_POST['ma_dc'] ?? 0);
        $stmtKiemTra = $pdo->prepare("SELECT maDC FROM DiaChiGiaoHang WHERE maDC = ? AND maND = ?");
        $stmtKiemTra->execute([$maDiaChi, $maND]);
        if ($stmtKiemTra->fetch()) {
            $pdo->prepare("UPDATE DiaChiGiaoHang SET laMacDinh = 0 WHERE maND = ?")->execute([$maND]);
            $pdo->prepare("UPDATE DiaChiGiaoHang SET laMacDinh = 1 WHERE maDC = ? AND maND = ?")->execute([$maDiaChi, $maND]);
            $thongBao     = 'Đã đặt làm địa chỉ mặc định.';
            $loaiThongBao = 'success';
        }

    } elseif ($hanhDong === 'xoa_dia_chi') {
        $maDiaChi = (int)($_POST['ma_dc'] ?? 0);
        $pdo->prepare("DELETE FROM DiaChiGiaoHang WHERE maDC = ? AND maND = ?")->execute([$maDiaChi, $maND]);
        $thongBao     = 'Đã xóa địa chỉ.';
        $loaiThongBao = 'success';
    }
}
