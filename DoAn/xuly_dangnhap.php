<?php
session_start();
require_once 'KetNoi/config/db.php';

if (isset($_POST['btn_dangnhap'])) {
    $tendangnhap = $_POST['tendangnhap'];
    $matkhau     = $_POST['matkhau'];

    $sql = "SELECT tk.tenDN, tk.maND, tk.maVT, vt.tenVT, nd.tenND 
            FROM TaiKhoan tk
            JOIN NguoiDung nd ON tk.maND = nd.maND
            JOIN VaiTro vt    ON tk.maVT = vt.maVT
            WHERE tk.tenDN   = :tendangnhap 
              AND tk.matKhau = :matkhau 
              AND tk.trangThai = 'on' LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['tendangnhap' => $tendangnhap, 'matkhau' => $matkhau]);
    $user = $stmt->fetch();

    if ($user) {
        // Lưu thông tin phiên
        $_SESSION['nguoi_dung_id']  = $user['maND'];
        $_SESSION['tendangnhap']    = $user['tenDN'];
        $_SESSION['ten_nguoi_dung'] = $user['tenND'];
        $_SESSION['vaitro']         = $user['tenVT'];

        // ── Khôi phục giỏ hàng từ bảng GioHang (thuần PHP PDO) ─────────────
        $maND = (int)$user['maND'];

        $sqlCart = "
            SELECT gh.maSach,
                   gh.soLuong,
                   s.tenSach,
                   s.giaBan,
                   COALESCE(ha.urlAnh, '') AS hinhAnh,
                   COALESCE(GROUP_CONCAT(tg.tenTG ORDER BY tg.maTG SEPARATOR ', '), 'Đang cập nhật') AS tacGia
            FROM GioHang gh
            JOIN Sach s ON gh.maSach = s.maSach
            LEFT JOIN (
                SELECT maSach, MIN(urlAnh) AS urlAnh
                FROM HinhAnhSach
                GROUP BY maSach
            ) ha ON ha.maSach = gh.maSach
            LEFT JOIN Sach_TacGia stg ON stg.maSach = gh.maSach
            LEFT JOIN TacGia tg        ON tg.maTG   = stg.maTG
            WHERE gh.maND = ?
            GROUP BY gh.maSach, gh.soLuong, s.tenSach, s.giaBan, ha.urlAnh
        ";

        $stmtCart = $pdo->prepare($sqlCart);
        $stmtCart->execute([$maND]);
        $rows = $stmtCart->fetchAll();

        // Xây dựng mảng cart theo đúng cấu trúc JS đang dùng
        $cartArr = [];
        foreach ($rows as $row) {
            $cartArr[] = [
                'maSach'  => $row['maSach'],
                'tenSach' => $row['tenSach'],
                'giaBan'  => (float)$row['giaBan'],
                'hinhAnh' => $row['hinhAnh'],
                'tacGia'  => $row['tacGia'],
                'soLuong' => (int)$row['soLuong'],
            ];
        }

        $_SESSION['cart'] = $cartArr;
        // ─────────────────────────────────────────────────────────────────────

        if (strtolower($user['tenVT']) === 'admin') {
            header("Location: CuaHang/ChuCuaHang/index.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        echo "<script>alert('Tên đăng nhập / mật khẩu không đúng, hoặc tài khoản đang bị khóa!'); window.history.back();</script>";
    }
}
?>