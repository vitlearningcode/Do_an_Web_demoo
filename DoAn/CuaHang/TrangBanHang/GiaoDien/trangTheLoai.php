<?php
/**
 * File: DoAn-Web/DoAn/CuaHang/TrangBanHang/GiaoDien/trangTheLoai.php
 * Trang hiển thị danh sách các sách theo Thể loại
 */

session_start();
$duong_dan_goc = '/DoAn-Web/DoAn/';

// Đường dẫn lùi về thư mục gốc để gọi database và bookCard
require_once '../../../KetNoi/config/db.php';
require_once '../Components/bookCard.php';

function hienThiAn($chuoi, $macc = '') {
    return htmlspecialchars((string)($chuoi ?? $macc), ENT_QUOTES, 'UTF-8');
}

$tenTheLoai = trim($_GET['theloai'] ?? '');
$ds_sach = [];
$isLoggedIn = isset($_SESSION['nguoi_dung_id']);

if ($tenTheLoai !== '') {
    try {
        $truyVan = $pdo->prepare("
            SELECT
                s.maSach, s.tenSach, s.giaBan, s.soLuongTon,
                (SELECT urlAnh FROM HinhAnhSach WHERE maSach = s.maSach LIMIT 1) AS hinhAnh,
                (SELECT GROUP_CONCAT(tg.tenTG SEPARATOR ', ')
                 FROM Sach_TacGia stg JOIN TacGia tg ON tg.maTG = stg.maTG
                 WHERE stg.maSach = s.maSach) AS tacGia,
                tl.tenTL AS theLoai,
                (SELECT ROUND(AVG(diemDG), 1) FROM DanhGiaSach WHERE maSach = s.maSach) AS diemTB,
                (SELECT COUNT(*) FROM DanhGiaSach WHERE maSach = s.maSach) AS soReview,
                IFNULL((
                    SELECT SUM(ct.soLuong) FROM ChiTietDH ct
                    JOIN DonHang dh ON dh.maDH = ct.maDH
                    WHERE ct.maSach = s.maSach AND dh.trangThai = 'HoanThanh'
                ), 0) AS tongBan,
                (SELECT ckm.phanTramGiam FROM ChiTietKhuyenMai ckm
                 JOIN KhuyenMai km ON km.maKM = ckm.maKM
                 WHERE ckm.maSach = s.maSach
                   AND NOW() BETWEEN km.ngayBatDau AND km.ngayKetThuc
                 LIMIT 1) AS phanTramGiam
            FROM Sach s
            JOIN Sach_TheLoai stl ON stl.maSach = s.maSach
            JOIN TheLoai tl ON tl.maTL = stl.maTL
            WHERE tl.tenTL = ? AND s.trangThai = 'DangKD'
            ORDER BY s.maSach DESC
        ");
        
        $truyVan->execute([$tenTheLoai]);
        $ds_sach = $truyVan->fetchAll(PDO::FETCH_ASSOC);

        foreach ($ds_sach as &$sl) {
            if (!empty($sl['phanTramGiam'])) {
                $sl['giaSau'] = (int) round($sl['giaBan'] * (1 - $sl['phanTramGiam'] / 100));
            } else {
                $sl['giaSau'] = null;
            }
        }
        unset($sl);

    } catch (PDOException $e) {
        error_log('[TrangLocSach] Lỗi: ' . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $tenTheLoai !== '' ? "Sách thể loại: " . hienThiAn($tenTheLoai) : "Tất cả sách" ?> — Book Sales</title>
    
    <link rel="stylesheet" href="<?= $duong_dan_goc ?>GiaoDien/style.css">
    <link rel="stylesheet" href="<?= $duong_dan_goc ?>GiaoDien/books.css">
    <link rel="stylesheet" href="<?= $duong_dan_goc ?>GiaoDien/trangTheLoai.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script>const dangDangNhap = <?= $isLoggedIn ? 'true' : 'false' ?>;</script>
    <?php if ($isLoggedIn): ?>
    <script>var cartServerData = <?= json_encode($_SESSION['cart'] ?? [], JSON_UNESCAPED_UNICODE) ?>;</script>
    <?php else: ?>
    <script>var cartServerData = null;</script>
    <?php endif; ?>
</head>
<body>

<?php include_once 'header.php'; ?>

<div class="loc-sach-container">
    <?php if ($tenTheLoai === ''): ?>
        <div class="loc-sach-empty">
            <i class="fas fa-question-circle"></i>
            <h3>Chưa chọn thể loại</h3>
            <p>Vui lòng chọn một thể loại từ Danh mục sách để bắt đầu khám phá.</p>
        </div>
    <?php else: ?>
        
        <h1 class="loc-sach-title">
            <i class="fas fa-tags"></i> Sách thuộc thể loại: <?= hienThiAn($tenTheLoai) ?>
        </h1>

        <?php if (empty($ds_sach)): ?>
            <div class="loc-sach-empty">
                <i class="fas fa-box-open"></i>
                <h3>Opps! Tạm thời hết sách</h3>
                <p>Hiện tại cửa hàng chưa có cuốn sách nào thuộc thể loại "<strong><?= hienThiAn($tenTheLoai) ?></strong>".</p>
            </div>
        <?php else: ?>
            <div class="books-grid" style="--columns:5">
                <?php foreach ($ds_sach as $sl):
                    $nhanLQ = [];
                    if (!empty($sl['phanTramGiam'])) {
                        $nhanLQ[] = ['class' => 'label-discount', 'label' => '-' . (int)$sl['phanTramGiam'] . '%'];
                    }
                    echo hienThiTheSach($sl, $nhanLQ);
                endforeach; ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>

<?php include_once 'footer.php'; ?>
<?php include_once '../ChiTietSach/formXemNhanhSach.php'; ?>
<?php include_once '../GioHang/formGioHang.php'; ?>

<script src="<?= $duong_dan_goc ?>PhuongThuc/components/thongBao.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/cart.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/components/bookCard.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/components/xacThuc.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/components/xacNhanDangXuat.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/components/chatbot.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/btnThemGioHang.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/xemNhanhSach.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/app.js"></script>

</body>
</html>