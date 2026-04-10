<?php
session_start();
require_once 'KetNoi/config/db.php'; // File này cung cấp biến $pdo

if (isset($_POST['btn_dangnhap'])) {
    $tendangnhap = $_POST['tendangnhap'];
    $matkhau = $_POST['matkhau']; 

    // Truy vấn kết hợp bảng TaiKhoan, NguoiDung và VaiTro bằng PDO
    $sql = "SELECT tk.tenDN, tk.maND, tk.maVT, vt.tenVT, nd.tenND 
            FROM TaiKhoan tk
            JOIN NguoiDung nd ON tk.maND = nd.maND
            JOIN VaiTro vt ON tk.maVT = vt.maVT
            WHERE tk.tenDN = :tendangnhap 
            AND tk.matKhau = :matkhau 
            AND tk.trangThai = 'on' LIMIT 1";
            
    // Sử dụng Prepare Statement để bảo mật
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'tendangnhap' => $tendangnhap,
        'matkhau' => $matkhau
    ]);
    
    // fetch() sẽ lấy ra 1 dòng dữ liệu dưới dạng mảng
    $user = $stmt->fetch();

    if ($user) {
        // Lưu thông tin vào session
        $_SESSION['nguoi_dung_id'] = $user['maND']; 
        $_SESSION['tendangnhap'] = $user['tenDN'];
        $_SESSION['ten_nguoi_dung'] = $user['tenND'];
        $_SESSION['vaitro'] = $user['tenVT'];

        // Kiểm tra phân quyền dựa theo tên vai trò
        if (strtolower($user['tenVT']) == 'admin') {
            header("Location: CuaHang/ChuCuaHang/index.php");
        } else {
            // Khách hàng -> Về trang chủ
            header("Location: index.php"); 
        }
        exit();
    } else {
        echo "<script>alert('Tên đăng nhập / mật khẩu không đúng, hoặc tài khoản đang bị khóa!'); window.history.back();</script>";
    }
}
?>