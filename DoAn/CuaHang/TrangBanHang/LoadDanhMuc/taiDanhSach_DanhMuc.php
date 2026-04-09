<?php
/**
 * taiDanhSach_DanhMuc.php
 * Tải danh sách thể loại (danh mục) và bộ biểu tượng tương ứng.
 *
 * Yêu cầu: $pdo đã được khởi tạo từ file gọi (index.php).
 * Kết quả:
 *   $ds_danhmuc  — Mảng tối đa 6 thể loại từ bảng TheLoai
 *   $bieu_tuong  — Mảng emoji tương ứng cho từng danh mục
 */

$ds_danhmuc = $pdo->query("SELECT * FROM TheLoai LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);

$bieu_tuong = ['📚', '📈', '🧠', '🧸', '🔬', '🌍'];
