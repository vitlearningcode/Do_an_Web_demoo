<?php
// ═══════════════════════════════════════════════════════════════
//  index.php — Entry Point & Router cho Admin (ChuCuaHang)
//  URL: /DoAn/CuaHang/ChuCuaHang/index.php?trang=<tenTrang>
// ═══════════════════════════════════════════════════════════════
session_start();
require_once '../../KetNoi/config/db.php';

// ── [1] BẢO VỆ QUYỀN TRUY CẬP ──────────────────────────────
// TODO: Bỏ comment dòng dưới khi đồng đội hoàn thành phần login/session.
// Hiện tại tạm bỏ qua để có thể build & kiểm tra giao diện trước.
/*
if (!isset($_SESSION['maVT']) || $_SESSION['maVT'] != 1) {
    header('Location: ../../index.php?thongbao=' . urlencode('Bạn không có quyền truy cập trang này.') . '&loai=error');
    exit;
}
*/

// ── [2] LẤY THÔNG TIN ADMIN (tạm thời dùng giá trị mặc định) ──
// Sau khi login xong: $tenAdmin = $_SESSION['tenND'];
$tenAdmin = $_SESSION['tenND'] ?? 'Admin';

// ── [3] ROUTING: đọc ?trang= ──────────────────────────────────
$cacTrang = [
    'tongQuan'     => 'TrangQuanLy/tongQuan.php',
    'donHang'      => 'TrangQuanLy/donHang.php',
    'sachVaTonKho' => 'TrangQuanLy/sachVaTonKho.php',
    'nhapHang'     => 'TrangQuanLy/nhapHang.php',
    'khuyenMai'    => 'TrangQuanLy/khuyenMai.php',
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
