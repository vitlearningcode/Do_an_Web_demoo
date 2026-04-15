<?php
/**
 * ============================================================
 * LUỒNG: ĐỒNG BỘ GIỏ HÀNG VÀO DATABASE
 *
 * GọI BỚI: cart.js → hàm saveCart()
 *   Cơ chế: Hidden form + target iframe (KHÔNG AJAX, KHÔNG fetch)
 *   syncJson.value = JSON.stringify(cartArr)
 *   syncForm.submit()  ← form này target vào iframe ẩn trên trang
 *
 * ĐIỀU KIỆN GỌI:
 *   - User phải đã đăng nhập (cart.js kiểm tra 'dangDangNhap' trước)
 *   - Mọi thao tác giỏ hàng (đểm + xóa + thay đổi số lượng) đều trigger saveCart()
 *
 * NHẬN VÀO (POST):
 *   cart_json — chuỗi JSON mảng [{maSach, soLuong, ...}]
 *   Giá (giaBan) trong JSON sẽ bị Bỏ QUA — KHÔNG lưu vào DB
 *
 * OUTPUT:
 *   - DB: bảng GioHang được đồng bộ (DELETE old → INSERT new)
 *   - SESSION: $_SESSION['cart'] được cập nhật (chỉ maSach + soLuong)
 *   - HTTP: 200 OK (iframe nhận, user không thấy gì)
 *
 * BẢO MẬT:
 *   - Chỉ lưu maSach + soLuong — GIÁ luôn được lấy từ DB khi thanh toán
 *   - Kiểm tra session['nguoi_dung_id'] trước khi xử lý
 * ============================================================
 */
session_start();
require_once __DIR__ . '/../../../KetNoi/config/db.php';

// —— BẢO VỆ TRUY CẬP —————————————————————————————————————————————————
// cart.js kiểm tra 'dangDangNhap' trước khi gọi saveCart()
// Tuy nhiên vẫn cần kiểm tra lại phía server (không tin client)
if (!isset($_SESSION['nguoi_dung_id'])) {
    http_response_code(403); // Forbidden — iframe nhận, user không thấy
    exit;
}

// Chỉ xử lý POST (form submit từ cart.js)
// GET trực tiếp vào URL này — bỏ qua (405 Method Not Allowed)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

// —— NHẬN DỮ LIỆU —————————————————————————————————————————————————
// maND từ SESSION (không từ POST — chống giả mạo ND khác)
$maND     = (int)$_SESSION['nguoi_dung_id'];
// cart_json: chuỗi JSON bạng cart.js tạo ra:
//   JSON.stringify([{maSach:'S001', soLuong:2, giaBan:...}, ...])
// GIÁ (giaBan) trong JSON bị Bỏ QUA — bước dưới chỉ lưu maSach + soLuong
$cartJson = trim($_POST['cart_json'] ?? '');

// JSON decode — nếu rỗng hoặc lỗi thì cartArr = [] (xóa hết giỏ)
$cartArr = [];
if ($cartJson !== '' && $cartJson !== '[]') {
    $decoded = json_decode($cartJson, true); // true: decode thành mảng PHP
    if (is_array($decoded)) {
        $cartArr = $decoded; // Hợp lệ: dùng
    }
    // Nếu $decoded không phải mảng (JSON lỗi): giữ $cartArr = [] — xóa giỏ
}

// —— ĐỒNG BỘ VÀO BẢNG GioHang (DELETE + INSERT TRONG TRANSACTION) ——————————
// Chiến lược DELETE + INSERT (re-sync hoàn toàn):
// - Đơn giản: không cần diff giỏ cũ vs mới
// - Nhược: nếu có nhiều request cùng lúc — không ảnh hưởng vì saveCart() chạy tuần tự
// ON DUPLICATE KEY UPDATE: chống lỗi nếu PRIMARY KEY (maND, maSach) đã tồn tại
$pdo->beginTransaction();
try {
    // Xóa hết giỏ cũ của user — (mọi item dù có hay không có trong mảng mới)
    // Lý do dùng DELETE thày vì UPDATE:
    //   Cart có thể có các mễu xb hiệu khác nhau, rất khó diff chính xác
    $stmtDel = $pdo->prepare("DELETE FROM GioHang WHERE maND = ?");
    $stmtDel->execute([$maND]);

    // Chỉ INSERT nếu giỏ không rỗng
    if (!empty($cartArr)) {
        $stmtIns = $pdo->prepare(
            // ON DUPLICATE KEY UPDATE: dự phòng nếu logic DELETE ở trên bị race condition
            "INSERT INTO GioHang (maND, maSach, soLuong) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE soLuong = VALUES(soLuong)"
        );
        foreach ($cartArr as $item) {
            $ms = trim($item['maSach'] ?? '');      // trim: loại khoảng trắng ngẫu nhiên
            $sl = max(1, (int)($item['soLuong'] ?? 1)); // Đảm bảo soLuong >= 1
            if ($ms !== '') {
                $stmtIns->execute([$maND, $ms, $sl]);
                // Lưu ý: GIÁ (giaBan) trong $item Bị Bỏ QUA hoàn toàn
                // Bảng GioHang chỉ có (maND, maSach, soLuong) — không có cột giá
            }
        }
    }

    $pdo->commit(); // Xác nhận transaction

    // —— CẬP NHẬT SESSION['cart'] ——————————————————————————————————
    // BẢO MẬT: CHỈ lưu maSach + soLuong vào session.
    // GIÁ KHÔNG bỏ vào session — luuGioHang.php không phải nguồn chân lý về giá.
    // Giá sẽ được xác thực lại từ DB bởi: kiemTraGioHang.php khi thanh toán.
    $sessionCart = [];
    foreach ($cartArr as $item) {
        $ms = trim($item['maSach'] ?? '');
        $sl = max(1, (int)($item['soLuong'] ?? 1));
        if ($ms !== '') {
            $sessionCart[] = [
                'maSach'  => $ms,
                'soLuong' => $sl,
                // 'giaBan' không được lưu — cố ý bỏ qua
            ];
        }
    }
    $_SESSION['cart'] = $sessionCart; // Ghi đ è session hiện tại

} catch (Exception $e) {
    $pdo->rollBack();
    // Lỗi lặng (silent fail):
    // - iframe ẩn — user không thấy gì cả
    // - Giỏ hàng của user vẫn hiển thị được từ localStorage
    // - Lần saveCart() tiếp theo sẽ thử lại
    // Có thể log ra file error nếu cần debug: error_log($e->getMessage());
}

// Trả về 200 OK (iframe nhận, không hiển thị gì cho user)
http_response_code(200);
exit;
?>
