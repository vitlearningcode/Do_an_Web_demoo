<?php
/**
 * footer.php — Cuối trang cửa hàng (TrangBanHang)
 * File này chỉ gọi require_once các thành phần con.
 * Yêu cầu: $duong_dan_goc (string) đã khai báo trong header.php
 */
require_once __DIR__ . '/thanhPhan/khuVucCuoiTrang.php';
require_once __DIR__ . '/thanhPhan/khungChatbot.php';
require_once __DIR__ . '/thanhPhan/modalXacNhanDangXuat.php';
require_once __DIR__ . '/thanhPhan/thanhPhanToast.php';
?>