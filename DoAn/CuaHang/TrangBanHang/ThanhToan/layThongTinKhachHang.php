<?php
/**
 * layThongTinKhachHang.php — Lấy thông tin KH + danh sách địa chỉ (nếu đã đăng nhập)
 * Output: $thongTinKH (array|null), $danhSachDiaChi (array), $diaChiMacDinh (string)
 * Yêu cầu: $isLoggedIn (bool), $pdo đã khai báo
 */

$thongTinKH     = null;
$danhSachDiaChi = [];
$diaChiMacDinh  = '';

if ($isLoggedIn) {
    $maND = (int)$_SESSION['nguoi_dung_id'];

    $stmtKhachHang = $pdo->prepare("SELECT tenND, sdt, email FROM NguoiDung WHERE maND = ? LIMIT 1");
    $stmtKhachHang->execute([$maND]);
    $thongTinKH = $stmtKhachHang->fetch();

    $stmtDiaChi = $pdo->prepare("SELECT maDC, diaChiChiTiet, laMacDinh FROM DiaChiGiaoHang WHERE maND = ? ORDER BY laMacDinh DESC, maDC ASC");
    $stmtDiaChi->execute([$maND]);
    $danhSachDiaChi = $stmtDiaChi->fetchAll();

    // Lấy địa chỉ mặc định để pre-fill
    foreach ($danhSachDiaChi as $diaChi) {
        if ($diaChi['laMacDinh']) {
            $diaChiMacDinh = $diaChi['diaChiChiTiet'];
            break;
        }
    }
}
