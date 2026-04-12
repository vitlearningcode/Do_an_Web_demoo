<?php
// ═══════════════════════════════════════════════════════════════
//  index.php — Entry Point & Router cho Admin (ChuCuaHang)
//  URL: /DoAn/CuaHang/ChuCuaHang/index.php?trang=<tenTrang>
// ═══════════════════════════════════════════════════════════════
session_start();
require_once '../../KetNoi/config/db.php';

// ── [1] BẢO VỆ QUYỀN TRUY CẬP ──────────────────────────────
// Kiểm tra session từ xuly_dangnhap.php (vaitro lưu dạng chuỗi tenVT)
if (!isset($_SESSION['vaitro']) || strtolower($_SESSION['vaitro']) !== 'admin') {
    // Chưa đăng nhập hoặc không phải Admin → về trang chủ
    header('Location: ../../index.php');
    exit;
}

// ── [2] LẤY THÔNG TIN ADMIN TỪ SESSION ──
$tenAdmin = $_SESSION['ten_nguoi_dung'] ?? ($_SESSION['tendangnhap'] ?? 'Admin');

// ── [3] ROUTING: đọc ?trang= ──────────────────────────────────
$cacTrang = [
    'tongQuan'     => 'TrangQuanLy/tongQuan.php',
    'donHang'      => 'TrangQuanLy/donHang.php',
    'sachVaTonKho' => 'TrangQuanLy/sachVaTonKho.php',
    'nhapHang'     => 'TrangQuanLy/nhapHang.php',
    'khuyenMai'    => 'TrangQuanLy/khuyenMai.php',
    'nhaCungCap'   => 'TrangQuanLy/nhaCungCap.php',
    'taiKhoan'     => 'TrangQuanLy/taiKhoan.php',
];

$trangHienTai = $_GET['trang'] ?? 'tongQuan';

// Nếu trang không hợp lệ → về mặc định
if (!array_key_exists($trangHienTai, $cacTrang)) {
    $trangHienTai = 'tongQuan';
}

$fileTrang = $cacTrang[$trangHienTai];

// ── [4] RENDER ─────────────────────────────────────────────────
// Header (mở HTML, sidebar, topbar, <main>)
require_once 'GiaoDien/header.php';

// Nội dung trang
if (file_exists($fileTrang)) {
    require_once $fileTrang;
} else {
    echo '<div class="adm-alert adm-alert-warning"><i class="fas fa-tools"></i> Trang <strong>'
        . htmlspecialchars($trangHienTai)
        . '</strong> đang được xây dựng...</div>';
}

// Footer (đóng </main>, toast, JS)
require_once 'GiaoDien/footer.php';
