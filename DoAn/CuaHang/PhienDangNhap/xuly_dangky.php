<?php
/**
 * ============================================================
 * LUỒNG ĐĂNG KÝ TÀI KHOẢN
 *
 * GỌI BỞI: Form HTML trong modal đăng ký (modalDangNhap.php)
 *   method="POST"
 *   action="/CuaHang/PhienDangNhap/xuly_dangky.php"
 *
 * NHẬN VÀO (POST):
 *   hoten, tendangnhap, email, sodienthoai, matkhau
 *
 * QUY TRÌNH (5 bước, có Transaction):
 *   1. Kiểm tra tên đăng nhập chưa tồn tại
 *   2. Kiểm tra email / SĐT chưa được dùng
 *   3. Lấy maVT của vai trò "Khách hàng" từ DB
 *   4. Transaction: INSERT NguoiDung → lấy maND → INSERT TaiKhoan
 *   5. Commit hoặc Rollback nếu lỗi
 *
 * TẠI SAO CẦN 2 BẢNG (NguoiDung + TaiKhoan)?
 *   - NguoiDung: Lưu thông tin cá nhân (tên, SĐT, email)
 *   - TaiKhoan:  Lưu thông tin đăng nhập (tenDN, matKhau, maVT)
 *   - Tách biệt để 1 người có thể có nhiều tài khoản (hoặc login mạng xã hội sau này)
 *   - NguoiDung phải INSERT trước để có maND → dùng cho TaiKhoan
 *
 * BẢO MẬT:
 *   - MẬT KHẨU được hash MD5 trước khi lưu (md5 là 1 chiều)
 *   - PDO Prepared Statement → chống SQL Injection
 *   - Transaction: nếu INSERT TaiKhoan lỗi → rollBack() → NguoiDung cũng không được tạo
 *
 * OUTPUT:
 *   Thành công → alert + redirect index.php
 *   Thất bại   → alert + history.back() (giữ nguyên form)
 * ============================================================
 */

// Phải gọi trước bất kỳ output nào
session_start();

// Nạp kết nối PDO ($pdo)
require_once '../../KetNoi/config/db.php';

// Chỉ xử lý khi form submit có nút 'btn_dangky'
if (isset($_POST['btn_dangky'])) {

    // -----------------------------------------------------------
    // BƯỚC 1: Nhận dữ liệu từ POST form
    // trim() không dùng ở đây — PDO prepared statement đã an toàn
    // md5() hash mật khẩu một chiều (khớp với xuly_dangnhap.php)
    // -----------------------------------------------------------
    $hoten       = $_POST['hoten'];
    $tendangnhap = $_POST['tendangnhap'];
    $email       = $_POST['email'];
    $sodienthoai = $_POST['sodienthoai'];
    $matkhau     = md5($_POST['matkhau']); // Hash MD5 — lưu dạng chuỗi 32 ký tự hex

    try {
        // -----------------------------------------------------------
        // BƯỚC 2: Kiểm tra trùng Tên đăng nhập
        // TaiKhoan.tenDN nên có UNIQUE constraint ở DB
        // Kiểm tra thủ công để trả lỗi thân thiện hơn
        // -----------------------------------------------------------
        $stmt_tk = $pdo->prepare("SELECT tenDN FROM TaiKhoan WHERE tenDN = :tendangnhap");
        $stmt_tk->execute(['tendangnhap' => $tendangnhap]);
        if ($stmt_tk->fetch()) {
            // Tên đã tồn tại → dừng, trả lỗi, giữ form
            echo "<script>alert('Tên đăng nhập đã tồn tại!'); window.history.back();</script>";
            exit();
        }

        // -----------------------------------------------------------
        // BƯỚC 3: Kiểm tra trùng Email HOẶC SĐT
        // Dùng OR: chỉ cần 1 trong 2 trùng là từ chối
        // (Email và SĐT là định danh định dạng trong NguoiDung)
        // -----------------------------------------------------------
        $stmt_nd = $pdo->prepare("SELECT maND FROM NguoiDung WHERE email = :email OR sdt = :sodienthoai");
        $stmt_nd->execute(['email' => $email, 'sodienthoai' => $sodienthoai]);
        if ($stmt_nd->fetch()) {
            echo "<script>alert('Email hoặc Số điện thoại đã được sử dụng!'); window.history.back();</script>";
            exit();
        }

        // -----------------------------------------------------------
        // BƯỚC 4: Lấy maVT của vai trò "Khách hàng"
        // Không hardcode ID để linh hoạt khi DB thay đổi
        // Fallback: ID = 2 nếu bảng VaiTro không có bản ghi phù hợp
        // -----------------------------------------------------------
        $stmt_vt = $pdo->query("SELECT maVT FROM VaiTro WHERE tenVT LIKE '%Khách hàng%' OR tenVT LIKE '%Khach hang%' LIMIT 1");
        $row_vt = $stmt_vt->fetch();
        $maVT = $row_vt ? $row_vt['maVT'] : 2; // Dự phòng ID 2

        // -----------------------------------------------------------
        // BƯỚC 5: Transaction — INSERT 2 bảng theo thứ tự
        //
        // TẠI SAO PHẢI DÙNG TRANSACTION?
        //   Nếu INSERT NguoiDung thành công nhưng INSERT TaiKhoan lỗi
        //   → Không dùng transaction: NguoiDung bị lưu nhưng không có TaiKhoan
        //   → Dùng transaction: rollBack() xóa luôn cả NguoiDung → dữ liệu nhất quán
        // -----------------------------------------------------------
        $pdo->beginTransaction();

        // --- Bước A: INSERT vào NguoiDung TRƯỚC ---
        // Phải INSERT NguoiDung trước để AUTO_INCREMENT sinh ra maND
        $sql_nd = "INSERT INTO NguoiDung (tenND, sdt, email) VALUES (:hoten, :sodienthoai, :email)";
        $stmt_insert_nd = $pdo->prepare($sql_nd);
        $stmt_insert_nd->execute([
            'hoten'       => $hoten,
            'sodienthoai' => $sodienthoai,
            'email'       => $email
        ]);

        // lastInsertId(): lấy maND vừa được AUTO_INCREMENT tạo ra
        // PHẢI gọi ngay sau INSERT, trước bất kỳ query nào khác
        $maND = $pdo->lastInsertId();

        // --- Bước B: INSERT vào TaiKhoan với maND vừa lấy ---
        // trangThai = 'on': tài khoản hoạt động ngay sau đăng ký
        $sql_tk = "INSERT INTO TaiKhoan (tenDN, matKhau, maND, maVT, trangThai)
                   VALUES (:tendangnhap, :matkhau, :maND, :maVT, 'on')";
        $stmt_insert_tk = $pdo->prepare($sql_tk);
        $stmt_insert_tk->execute([
            'tendangnhap' => $tendangnhap,
            'matkhau'     => $matkhau, // Đã hash MD5 từ bước 1
            'maND'        => $maND,    // maND từ lastInsertId() ở bước A
            'maVT'        => $maVT    // maVT = "Khách hàng" từ bước 4
        ]);

        // --- Commit: xác nhận cả 2 INSERT ---
        $pdo->commit();

        // Thành công: chuyển về index.php (user tự đăng nhập)
        // Không tự đăng nhập vì: cần user chủ động xác nhận thông tin
        echo "<script>alert('Đăng ký thành công! Vui lòng đăng nhập.'); window.location.href='../../index.php';</script>";

    } catch (Exception $e) {
        // -----------------------------------------------------------
        // XỬ LÝ LỖI: Rollback toàn bộ transaction
        // Các lỗi có thể xảy ra:
        //   - Mất kết nối DB giữa chừng
        //   - Vi phạm UNIQUE constraint (chạy đồng thời)
        //   - Lỗi SQL syntax
        // addslashes() tránh lỗi JavaScript khi message có dấu '
        // -----------------------------------------------------------
        $pdo->rollBack();
        echo "<script>alert('Lỗi hệ thống: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}
?>