<?php
/**
 * xuLyCapNhatThongTin.php — Xử lý POST form cập nhật thông tin cá nhân + đổi mật khẩu
 * Kích hoạt khi: $_POST['hanh_dong_thong_tin'] được gửi
 *
 * Input:  $_POST (hoten, sdt, email, mk_cu, mk_moi, mk_xn)
 * Output: $thongBao (string), $loaiThongBao (string: 'success'|'error')
 * Yêu cầu: $pdo, $maND đã khai báo
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hanh_dong_thong_tin'])) {
    $hoTen    = trim($_POST['hoten']  ?? '');
    $soDienThoai = trim($_POST['sdt'] ?? '');
    $email    = trim($_POST['email']  ?? '');
    $matKhauCu  = trim($_POST['mk_cu']  ?? '');
    $matKhauMoi = trim($_POST['mk_moi'] ?? '');
    $matKhauXacNhan = trim($_POST['mk_xn'] ?? '');

    $danhSachLoi = [];
    if (empty($hoTen)) $danhSachLoi[] = 'Họ tên không được để trống.';

    if (empty($danhSachLoi)) {
        $stmtCapNhat = $pdo->prepare("UPDATE NguoiDung SET tenND = ?, sdt = ?, email = ? WHERE maND = ?");
        $stmtCapNhat->execute([$hoTen, $soDienThoai ?: null, $email ?: null, $maND]);
        $_SESSION['ten_nguoi_dung'] = $hoTen;

        // Đổi mật khẩu nếu người dùng điền mật khẩu cũ
        if (!empty($matKhauCu)) {
            $stmtLayTK = $pdo->prepare("SELECT matKhau FROM TaiKhoan WHERE maND = ? LIMIT 1");
            $stmtLayTK->execute([$maND]);
            $taiKhoan = $stmtLayTK->fetch();

            if (!$taiKhoan || $taiKhoan['matKhau'] !== $matKhauCu) {
                $danhSachLoi[] = 'Mật khẩu cũ không đúng!';
            } elseif ($matKhauMoi !== $matKhauXacNhan) {
                $danhSachLoi[] = 'Mật khẩu mới và xác nhận không khớp!';
            } elseif (strlen($matKhauMoi) < 1) {
                $danhSachLoi[] = 'Mật khẩu mới không được để trống!';
            } else {
                $stmtDoiMK = $pdo->prepare("UPDATE TaiKhoan SET matKhau = ? WHERE maND = ?");
                $stmtDoiMK->execute([$matKhauMoi, $maND]);
            }
        }
    }

    if (empty($danhSachLoi)) {
        $thongBao     = 'Cập nhật thông tin thành công!';
        $loaiThongBao = 'success';
    } else {
        $thongBao     = implode(' ', $danhSachLoi);
        $loaiThongBao = 'error';
    }
}
