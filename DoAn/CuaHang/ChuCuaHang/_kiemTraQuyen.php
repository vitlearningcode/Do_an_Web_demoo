<?php
/**
 * _kiemTraQuyen.php — Bảo vệ quyền truy cập Admin
 *
 * Include file này ở DÒNG ĐẦU TIÊN của mọi file trong:
 *   - ChuCuaHang/TrangQuanLy/
 *   - ChuCuaHang/XuLy/
 *
 * Nếu người dùng chưa đăng nhập hoặc không phải Admin
 * → redirect ngay về trang chủ, không render bất kỳ nội dung nào.
 *
 * Cách dùng:
 *   require_once __DIR__ . '/../_kiemTraQuyen.php';  // từ TrangQuanLy/ hoặc XuLy/
 */

// Khởi động session nếu chưa (an toàn khi gọi nhiều lần)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra role: phải là 'admin' (không phân biệt hoa thường)
if (
    !isset($_SESSION['vaitro']) ||
    strtolower(trim($_SESSION['vaitro'])) !== 'admin'
) {
    // Tính đường dẫn tương đối về trang chủ từ vị trí hiện tại
    // File này nằm tại ChuCuaHang/_kiemTraQuyen.php
    // Gọi từ TrangQuanLy/ → cần lên 2 cấp → ../../..
    // Gọi từ XuLy/         → cần lên 2 cấp → ../../..
    // Dùng đường dẫn tuyệt đối để an toàn hơn
    $rootUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
             . '://' . $_SERVER['HTTP_HOST'];

    // Tìm thư mục gốc dự án từ DOCUMENT_ROOT
    // URL trang chủ: /DoAn/index.php
    header('Location: ' . $rootUrl . '/DoAn/index.php');
    exit;
}
