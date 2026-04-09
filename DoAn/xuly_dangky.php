<?php
session_start();
require_once 'KetNoi/config/db.php'; // Cung cấp biến $pdo

if (isset($_POST['btn_dangky'])) {
    $hoten = $_POST['hoten'];
    $tendangnhap = $_POST['tendangnhap'];
    $email = $_POST['email'];
    $sodienthoai = $_POST['sodienthoai'];
    $matkhau = $_POST['matkhau'];

    try {
        // 1. Kiểm tra trùng lặp Tên đăng nhập
        $stmt_tk = $pdo->prepare("SELECT tenDN FROM TaiKhoan WHERE tenDN = :tendangnhap");
        $stmt_tk->execute(['tendangnhap' => $tendangnhap]);
        if ($stmt_tk->fetch()) {
            echo "<script>alert('Tên đăng nhập đã tồn tại!'); window.history.back();</script>";
            exit();
        }

        // 2. Kiểm tra trùng lặp Email hoặc SĐT (Bảng NguoiDung)
        $stmt_nd = $pdo->prepare("SELECT maND FROM NguoiDung WHERE email = :email OR sdt = :sodienthoai");
        $stmt_nd->execute(['email' => $email, 'sodienthoai' => $sodienthoai]);
        if ($stmt_nd->fetch()) {
            echo "<script>alert('Email hoặc Số điện thoại đã được sử dụng!'); window.history.back();</script>";
            exit();
        }

        // 3. Lấy mã Vai Trò của "Khách hàng" để gán mặc định
        $stmt_vt = $pdo->query("SELECT maVT FROM VaiTro WHERE tenVT LIKE '%Khách hàng%' OR tenVT LIKE '%Khach hang%' LIMIT 1");
        $row_vt = $stmt_vt->fetch();
        $maVT = $row_vt ? $row_vt['maVT'] : 2; // Dự phòng ID 2

        // 4. Bắt đầu lưu vào CSDL (Dùng Transaction trong PDO)
        $pdo->beginTransaction();
        
        // Bước A: Thêm vào bảng NguoiDung trước
        $sql_nd = "INSERT INTO NguoiDung (tenND, sdt, email) VALUES (:hoten, :sodienthoai, :email)";
        $stmt_insert_nd = $pdo->prepare($sql_nd);
        $stmt_insert_nd->execute([
            'hoten' => $hoten,
            'sodienthoai' => $sodienthoai,
            'email' => $email
        ]);
        
        // Lấy ID (maND) vừa tự động tăng sinh ra
        $maND = $pdo->lastInsertId();

        // Bước B: Dùng mã maND đó thêm vào bảng TaiKhoan
        $sql_tk = "INSERT INTO TaiKhoan (tenDN, matKhau, maND, maVT, trangThai) 
                   VALUES (:tendangnhap, :matkhau, :maND, :maVT, 'on')";
        $stmt_insert_tk = $pdo->prepare($sql_tk);
        $stmt_insert_tk->execute([
            'tendangnhap' => $tendangnhap,
            'matkhau' => $matkhau,
            'maND' => $maND,
            'maVT' => $maVT
        ]);

        // Xác nhận lưu dữ liệu (Commit)
        $pdo->commit();
        echo "<script>alert('Đăng ký thành công! Vui lòng đăng nhập.'); window.location.href='index.php';</script>";

    } catch (Exception $e) {
        // Nếu có lỗi, hoàn tác toàn bộ (Rollback)
        $pdo->rollBack();
        echo "<script>alert('Lỗi hệ thống: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}
?>