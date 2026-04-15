<?php
/**
 * ============================================================
 * LUỒNG ĐĂNG NHẬP
 *
 * GọI BỚI: Form HTML trong modal (modalDangNhap.php → thông qua header.php)
 *   method="POST"
 *   action="/CuaHang/PhienDangNhap/xuly_dangnhap.php"
 *
 * NHẬN VÀO (POST): tendangnhap, matkhau
 *
 * Xử LÝ: Kiểm tra DB → Set session → Khôi phục giỏ hàng → Redirect
 *
 * OUTPUT SESSION (sau khi thành công):
 *   $_SESSION['nguoi_dung_id']  = maND (int)
 *   $_SESSION['tendangnhap']    = tenDN (string)
 *   $_SESSION['ten_nguoi_dung'] = tenND (string)
 *   $_SESSION['vaitro']         = tenVT (string: 'admin' | 'Khách hàng')
 *   $_SESSION['cart']           = [{maSach, soLuong}] (GIÁ KHÔNG lưu — bảo mật)
 *
 * REDIRECT:
 *   Admin → ChuCuaHang/index.php
 *   Khách hàng → index.php (trang chủ)
 * ============================================================
 */

// Phải gọi trước bất kỳ output nào; khởi động quản lý phiên ($PHPSESSID cookie)
session_start();

// Nạp kết nối PDO ($pdo) và helper ảnh
require_once '../../KetNoi/config/db.php';

// Chỉ xử lý khi form submit có nút 'btn_dangnhap'
if (isset($_POST['btn_dangnhap'])) {

    // -----------------------------------------------------------
    // BƯỚC 1: Lấy dữ liệu từ form (chưa validate — PDO prepared statement bảo vệ)
    // -----------------------------------------------------------
    $tendangnhap = $_POST['tendangnhap'];
    $matkhau     = md5($_POST['matkhau']); // Hash MD5 một chiều — khớp với lúc lưu

    // -----------------------------------------------------------
    // BƯỚC 2: Query xác thực
    // JOIN 3 bảng: TaiKhoan → NguoiDung → VaiTro
    // Điều kiện: tên đăng nhập ĐÚNG + mật khẩu ĐÚNG + tài khoản đang HOẠT ĐỘNG
    // LIMIT 1 — chỉ lấy 1 kết quả (tenDN unique)
    // -----------------------------------------------------------
    $sql = "SELECT tk.tenDN, tk.maND, tk.maVT, vt.tenVT, nd.tenND 
            FROM TaiKhoan tk
            JOIN NguoiDung nd ON tk.maND = nd.maND
            JOIN VaiTro vt    ON tk.maVT = vt.maVT
            WHERE tk.tenDN   = :tendangnhap 
              AND tk.matKhau = :matkhau 
              AND tk.trangThai = 'on' LIMIT 1";

    // Prepared statement: PDO tự escape từ đồng → chống SQL Injection
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['tendangnhap' => $tendangnhap, 'matkhau' => $matkhau]);
    $user = $stmt->fetch(); // Kết quả: mảng hoặc false

    if ($user) {
        // -----------------------------------------------------------
        // BƯỚC 3: Lưu thông tin phên (đặt cookie PHPSESSID trên trình duyệt)
        // Đây là những session key mà các trang khác sẽ đọc để kiểm tra đăng nhập
        // -----------------------------------------------------------
        $_SESSION['nguoi_dung_id']  = $user['maND'];  // Dùng để JOIN DB (giỏ hàng, đơn hàng)
        $_SESSION['tendangnhap']    = $user['tenDN'];  // Hiển thị trên giao diện
        $_SESSION['ten_nguoi_dung'] = $user['tenND'];  // Hiển thị trên giao diện
        $_SESSION['vaitro']         = $user['tenVT'];  // Kiểm tra quyền admin

        // -----------------------------------------------------------
        // BƯỚC 4: Khôi phục giỏ hàng từ DB (PHIÊN TRUY VẤN QUAN TRỌNG)
        // Mục đích: merge giỏ hàng DB vào session sau khi đăng nhập
        // cart.js sẽ đọc cartServerData (từ layGioHangCoGia.php) thay vì localStorage
        //
        // BẢO MẬT GIÁ:
        //   - Chỉ lưu {maSach + soLuong} vào session cart
        //   - GIÁ (giaBan) KHÔNG lưu — sẽ được query lại từ DB ở kiemTraGioHang.php
        // -----------------------------------------------------------
        $maND = (int)$user['maND'];

        // JOIN Sach để lấy tên, ảnh, tác giả (chỉ dùng cho hiển thị, không tin giá)
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

        // -----------------------------------------------------------
        // Xây dựng mảng cart theo cấu trúc tối giản (chỉ maSach + soLuong)
        // BẢO MẬT: giaBan KHÔNG lưu vào session — sẽ được query DB ở kiemTraGioHang.php
        // -----------------------------------------------------------
        $cartArr = [];
        foreach ($rows as $row) {
            $cartArr[] = [
                'maSach'  => $row['maSach'],
                'soLuong' => (int)$row['soLuong'],
                // GIÁ CỐ ĨNH KHÔNG ĐƯỢC LƯĂU TẠI ĐÂY
            ];
        }

        // Lưu vào session — cart.js sẽ đọc cartServerData (do layGioHangCoGia.php tạo ra)
        $_SESSION['cart'] = $cartArr;

        // -----------------------------------------------------------
        // BƯỚC 5: Phân luồng theo vai trò
        // header() đặt HTTP Location và exit() dừng script ngay
        // -----------------------------------------------------------
        if (strtolower($user['tenVT']) === 'admin') {
            // Admin → trang quản lý (_kiemTraQuyen.php sẽ kiểm tra lại với mọi trang admin)
            header("Location: ../ChuCuaHang/index.php");
        } else {
            // Khách hàng → trang chủ
            // khoiDauTrangChu.php sẽ đọc session và set $isLoggedIn = true
            header("Location: ../../index.php");
        }
        exit();

    } else {
        // -----------------------------------------------------------
        // THẤT BẠI: Sai thông tin hoặc tài khoản bị khóa (trangThai != 'on')
        // Dùng echo <script> vì không dùng AJAX — lỗi trả về cùng trang (history.back)
        // -----------------------------------------------------------
        echo "<script>alert('Tên đăng nhập / mật khẩu không đúng, hoặc tài khoản đang bị khóa!'); window.history.back();</script>";
    }
}
?>