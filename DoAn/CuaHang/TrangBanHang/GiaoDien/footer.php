<?php
/**
 * footer.php — Cuối trang cửa hàng (TrangBanHang)
 * File này chỉ gọi require_once các thành phần con.
 * Yêu cầu: $duong_dan_goc (string) đã khai báo trong header.php
 *
 * Lưu ý: Modal đăng xuất (id="logout-modal") đã nằm trong modalDangNhap.php
 * → KHÔNG include modalXacNhanDangXuat.php để tránh trùng id.
 */
require_once __DIR__ . '/thanhPhan/khuVucCuoiTrang.php';
require_once __DIR__ . '/thanhPhan/khungChatbot.php';
require_once __DIR__ . '/thanhPhan/thanhPhanToast.php';
?>
