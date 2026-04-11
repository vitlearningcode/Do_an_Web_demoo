<?php
session_start();
// Lùi về gốc để lấy db và các file cần thiết
require_once "../../../KetNoi/config/db.php"; 

if (!isset($_SESSION['nguoi_dung_id'])) {
    header("Location: ../../../index.php");
    exit();
}

require_once "../Components/bookCard.php";

$maNguoiDung = $_SESSION['nguoi_dung_id'];
$isLoggedIn = true;

// Lấy danh sách sách yêu thích của người dùng này
$cauTruyVan = "
    SELECT 
        s.maSach, s.tenSach, s.giaBan, 
        (SELECT ha.urlAnh FROM HinhAnhSach ha WHERE ha.maSach = s.maSach LIMIT 1) AS hinhAnh,
        (SELECT GROUP_CONCAT(tg.tenTG SEPARATOR ', ') FROM TacGia tg JOIN Sach_TacGia st ON tg.maTG = st.maTG WHERE st.maSach = s.maSach) AS tacGia,
        (SELECT tl.tenTL FROM TheLoai tl JOIN Sach_TheLoai stl ON tl.maTL = stl.maTL WHERE stl.maSach = s.maSach LIMIT 1) AS theLoai,
        (SELECT ROUND(AVG(diemDG), 1) FROM DanhGiaSach WHERE maSach = s.maSach) AS diemTB,
        (SELECT COUNT(*) FROM DanhGiaSach WHERE maSach = s.maSach) AS soReview
    FROM SachYeuThich syt
    JOIN Sach s ON syt.maSach = s.maSach
    WHERE syt.maND = :maND AND s.trangThai = 'DangKD'
    ORDER BY syt.ngayThem DESC
";

$lenhThucThi = $pdo->prepare($cauTruyVan);
$lenhThucThi->execute(['maND' => $maNguoiDung]);
$danhSachYeuThich = $lenhThucThi->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sách yêu thích của bạn</title>
    <link rel="stylesheet" href="../../../GiaoDien/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>const dangDangNhap = true;</script>
</head>
<body>

<?php include_once "../GiaoDien/header.php"; ?>

<main class="main-content container" style="margin-top: 40px; min-height: 50vh;">
    <div class="section-header">
        <h3><i class="fas fa-heart" style="color:#ef4444"></i> Tủ Sách Yêu Thích Của Bạn</h3>
    </div>
    
    <?php if (count($danhSachYeuThich) > 0): ?>
        <div class="books-grid">
            <?php 
            foreach ($danhSachYeuThich as $sach) {
                // Hiển thị sách bằng hàm đã có sẵn
                echo hienThiTheSach($sach);
            }
            ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 50px 0;">
            <i class="far fa-folder-open" style="font-size: 40px; color: #ccc; margin-bottom: 15px;"></i>
            <p style="color: #666;">Bạn chưa có cuốn sách yêu thích nào. Hãy khám phá và thả tim ngay nhé!</p>
            <a href="../../../index.php" class="btn-primary" style="display: inline-block; padding: 10px 20px; width: auto; margin-top: 15px; text-decoration: none;">Tiếp tục mua sắm</a>
        </div>
    <?php endif; ?>
</main>

<?php include_once "../../../CuaHang/TrangBanHang/GioHang/formGioHang.php"; ?>
<?php include_once "../ChiTietSach/formXemNhanhSach.php"; ?>

<?php include_once "../GiaoDien/footer.php"; ?>

<script src="<?= $duong_dan_goc ?>PhuongThuc/app.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/cart.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/btnThemGioHang.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/xemNhanhSach.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/components/chatbot.js" defer></script>

</body>
</html>