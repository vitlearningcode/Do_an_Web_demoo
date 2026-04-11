<?php
/**
 * layThongTinTaiKhoan.php — Truy vấn DB lấy thông tin người dùng + danh sách địa chỉ
 * Output: $nguoiDung (array), $taiKhoan (array), $danhSachDiaChi (array)
 * Yêu cầu: $pdo, $maND đã khai báo
 */

$stmtNguoiDung = $pdo->prepare("SELECT tenND, sdt, email FROM NguoiDung WHERE maND = ? LIMIT 1");
$stmtNguoiDung->execute([$maND]);
$nguoiDung = $stmtNguoiDung->fetch();

$stmtTaiKhoan = $pdo->prepare("SELECT tenDN FROM TaiKhoan WHERE maND = ? LIMIT 1");
$stmtTaiKhoan->execute([$maND]);
$taiKhoan = $stmtTaiKhoan->fetch();

$stmtDiaChi = $pdo->prepare("SELECT maDC, diaChiChiTiet, laMacDinh FROM DiaChiGiaoHang WHERE maND = ? ORDER BY laMacDinh DESC, maDC ASC");
$stmtDiaChi->execute([$maND]);
$danhSachDiaChi = $stmtDiaChi->fetchAll();
