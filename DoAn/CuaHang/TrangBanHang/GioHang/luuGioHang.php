<?php
/**
 * GioHang/luuGioHang.php — Đồng bộ giỏ hàng vào bảng GioHang (DB-backed, thuần PHP)
 *
 * Nhận form POST từ hidden form + <iframe> (KHÔNG AJAX, KHÔNG fetch).
 * Đồng bộ giỏ hàng vào bảng GioHang trong DB.
 *
 * POST params:
 *   cart_json — chuỗi JSON mảng [{maSach, soLuong, tenSach, giaBan, hinhAnh, tacGia}]
 *   Nếu rỗng hoặc [] → xóa hết giỏ hàng của user trong DB.
 */
session_start();
require_once __DIR__ . '/../../../KetNoi/config/db.php';

// Bắt buộc đăng nhập
if (!isset($_SESSION['nguoi_dung_id'])) {
    http_response_code(403);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$maND     = (int)$_SESSION['nguoi_dung_id'];
$cartJson = trim($_POST['cart_json'] ?? '');

// Decode cart từ POST (đây là PHP decode, KHÔNG phải AJAX/fetch)
$cartArr = [];
if ($cartJson !== '' && $cartJson !== '[]') {
    $decoded = json_decode($cartJson, true);
    if (is_array($decoded)) {
        $cartArr = $decoded;
    }
}

// ── Đồng bộ vào bảng GioHang (DELETE + INSERT trong transaction) ──────────
$pdo->beginTransaction();
try {
    // Xóa hết giỏ cũ của user
    $stmtDel = $pdo->prepare("DELETE FROM GioHang WHERE maND = ?");
    $stmtDel->execute([$maND]);

    // Chèn từng item (chỉ cần maSach + soLuong; thông tin sách lấy từ DB khi load)
    if (!empty($cartArr)) {
        $stmtIns = $pdo->prepare(
            "INSERT INTO GioHang (maND, maSach, soLuong) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE soLuong = VALUES(soLuong)"
        );
        foreach ($cartArr as $item) {
            $ms = trim($item['maSach'] ?? '');
            $sl = max(1, (int)($item['soLuong'] ?? 1));
            if ($ms !== '') {
                $stmtIns->execute([$maND, $ms, $sl]);
            }
        }
    }

    $pdo->commit();

    // Cập nhật session để trang hiện tại cũng đồng bộ
    $_SESSION['cart'] = $cartArr;

} catch (Exception $e) {
    $pdo->rollBack();
    // Lỗi lặng — iframe ẩn, user không nhìn thấy
}

http_response_code(200);
exit;
?>
