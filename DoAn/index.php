<?php
/**
 * index.php — Trang chủ cửa hàng (Entry Point)
 * File chỉ còn: session_start, require DB, load dữ liệu, render layout.
 * Mọi chức năng được tách thành file riêng trong KhuVucTrungBay/.
 */
session_start();
require_once "KetNoi/config/db.php";

// ── Tải các thành phần dữ liệu ─────────────────────────────────────────────
require_once "CuaHang/TrangBanHang/Components/bookCard.php";
require_once "CuaHang/TrangBanHang/KhuVucTrungBay/taiFlashSale.php";
require_once "CuaHang/TrangBanHang/KhuVucTrungBay/taiSachBanChay.php";
require_once "CuaHang/TrangBanHang/KhuVucTrungBay/taiSachMoi.php";
require_once "CuaHang/TrangBanHang/LoadDanhMuc/taiDanhSach_DanhMuc.php";
require_once "CuaHang/TrangBanHang/LoadDuLieu/taiQuangCao.php";

// ── Khởi tạo: kiểm tra đăng nhập + xử lý cờ xóa cart ─────────────────────
require_once "CuaHang/TrangBanHang/KhuVucTrungBay/khoiDauTrangChu.php";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Sales Management - Cửa hàng sách trực tuyến</title>
    <link rel="stylesheet" href="GiaoDien/style.css">
    <link rel="stylesheet" href="GiaoDien/xemNhanhSach.css">
    <link rel="stylesheet" href="GiaoDien/gioHang.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>const dangDangNhap = <?= $isLoggedIn ? 'true' : 'false' ?>;</script>
    <?php if ($isLoggedIn): ?>
    <script>
        // PHP render giỏ hàng từ Session vào biến JS (không AJAX, không fetch)
        var cartServerData = <?= json_encode($_SESSION['cart'] ?? [], JSON_UNESCAPED_UNICODE) ?>;
    </script>
    <?php else: ?>
    <script>var cartServerData = null;</script>
    <?php endif; ?>
    <?php if ($phai_xoa_cart): ?>
    <script>localStorage.removeItem('book_cart');</script>
    <?php endif; ?>
</head>
<body>

<?php include_once "CuaHang/TrangBanHang/GiaoDien/header.php"; ?>

<main class="main-content container">
<div id="home-content">

    <?php require_once "CuaHang/TrangBanHang/KhuVucTrungBay/khuVucHeroBanner.php"; ?>
    <?php require_once "CuaHang/TrangBanHang/KhuVucTrungBay/khuVucFlashSale.php"; ?>
    <?php require_once "CuaHang/TrangBanHang/KhuVucTrungBay/khuVucDanhMuc.php"; ?>
    <?php require_once "CuaHang/TrangBanHang/KhuVucTrungBay/khuVucSachBanChay.php"; ?>
    <?php require_once "CuaHang/TrangBanHang/KhuVucTrungBay/khuVucSachMoi.php"; ?>
    <!-- ===== HERO BANNER ===== -->
    <section class="hero-banner">
        <div class="hero-slider" id="hero-slider">
            <?php
            foreach ($ds_quangCao as $idx => $b):
                $mau = !empty($b['mauNen']) ? $b['mauNen'] : 'blue';
            ?>
            <div class="hero-slide <?= htmlspecialchars($mau) ?> <?= $idx === 0 ? 'active' : '' ?>">
                <div class="hero-slide-bg">
                    <img src="<?= htmlspecialchars($b['hinhAnh']) ?>" alt="Banner">
                    <div class="gradient-overlay"></div>
                </div>
                <div class="hero-content">
                    <span class="hero-badge"><?= htmlspecialchars($b['nhan']) ?></span>
                    <h2><?= $b['tieuDe'] ?></h2>
                    <p><?= htmlspecialchars($b['moTa']) ?></p>
                    <button class="hero-btn"><?= htmlspecialchars($b['chuNut']) ?> <i class="fas fa-arrow-right"></i></button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <button class="hero-nav prev" id="hero-prev"><i class="fas fa-chevron-left"></i></button>
        <button class="hero-nav next" id="hero-next"><i class="fas fa-chevron-right"></i></button>
        <div class="hero-indicators" id="hero-indicators"></div>
    </section>

    <!-- ===== FLASH SALE ===== -->
    <?php if (!empty($ds_flashsale)): ?>
    <section class="flash-sale">
        <div class="flash-sale-inner">
            <div class="flash-sale-header">
                <div class="flash-sale-title">
                    <div class="flash-icon"><i class="fas fa-fire"></i></div>
                    <div>
                        <h3>Flash Sale <span>Giá Sốc</span></h3>
                        <p>Kết thúc trong</p>
                    </div>
                </div>
                <div class="flash-timer">
                    <div class="timer-block"><div class="timer-value" id="hours">00</div><span>Giờ</span></div>
                    <span class="timer-sep">:</span>
                    <div class="timer-block"><div class="timer-value" id="minutes">00</div><span>Phút</span></div>
                    <span class="timer-sep">:</span>
                    <div class="timer-block"><div class="timer-value timer-seconds" id="seconds">00</div><span>Giây</span></div>
                </div>
            </div>
            <div class="books-grid flash-sale-grid">
                <?php foreach ($ds_flashsale as $sach):
                    // ── Tính toán thanh tiến độ kiểu Shopee ────────────────
                    $tongBan   = (int)($sach['tongBan'] ?? 0);
                    $soLuongKM = (int)($sach['soLuongKhuyenMai'] ?? 0);
                    $conLai    = (int)($sach['soLuongTon'] ?? 0);
                    $tongBanDau = ($soLuongKM > 0) ? $soLuongKM : max(1, $tongBan + $conLai);
                    $pctDaBan   = min(100, round($tongBan / $tongBanDau * 100));
                    $pctConLai  = 100 - $pctDaBan;

                    $gapHet  = ($conLai <= 5 && $conLai > 0);
                    $sapHet  = (!$gapHet && $pctDaBan >= 80);
                    $isPopular = (!$gapHet && !$sapHet);

                    // Chuẩn bị HTML label cho thanh
                    $progressLabelHtml = '';
                    if ($isPopular) {
                        $progressLabelHtml = '<div class="nhan-chu vi-tri-tuyet-doi">ĐANG BÁN CHẠY</div>';
                    } elseif ($gapHet) {
                        $progressLabelHtml = '<div class="khoang-dem vi-tri-tuyet-doi"></div><div class="nhan-chu vi-tri-tuyet-doi">CHỈ CÒN ' . $conLai . '</div>';
                    } else {
                        $progressLabelHtml = '<div class="nhan-chu vi-tri-tuyet-doi">GẦN HẾT HÀNG</div>';
                    }

                    $borderRadiusDaBan = $pctDaBan >= 100 ? '8px' : '8px 0 0 8px';

                    // Khối HTML customBottom sẽ được chèn vào trong bookCard
                    $customHtmlBottom = "
                    <div class=\"thanh-tien-do\">
                        <div class=\"thanh-nen\">
                            {$progressLabelHtml}
                            <div class=\"phan-da-ban\" style=\"width: {$pctDaBan}%; border-radius: {$borderRadiusDaBan};\"></div>
                            <div class=\"phan-con-lai\"></div>
                        </div>
                    </div>";

                    /* Badge 1 (cam): nhãn "Flash Sale"
                       Badge 2 (đỏ): "-XX%" — phanTramGiam thực từ ChiTietKhuyenMai */
                    echo hienThiTheSach($sach, [
                        ['class' => 'label-type',    'label' => 'Flash Sale'],
                        ['class' => 'label-discount', 'label' => '-' . $sach['phanTramGiam'] . '%'],
                    ], $customHtmlBottom);
                endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ===== DANH MỤC ===== -->
    <section class="categories-section">
        <div class="section-header">
            <h3>Khám Phá Theo Danh Mục</h3>
            <a href="#">Xem tất cả <i class="fas fa-chevron-right"></i></a>
        </div>
        <div class="categories-grid">
            <?php
            $viTri = 0;
            foreach ($ds_danhmuc as $tl): ?>
            <a href="#" class="category-card">
                <div class="category-icon"><?= $bieu_tuong[$viTri++ % 6] ?></div>
                <span><?= htmlspecialchars($tl['tenTL']) ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- ===== SÁCH BÁN CHẠY NHẤT ===== -->
    <section class="featured-books">
        <div class="section-header">
            <div>
                <h3><i class="fas fa-fire-alt" style="color:#f97316"></i> Sách Bán Chạy Nhất</h3>
                <p>Top 10 bán chạy nhất tháng <?= date('n/Y') ?></p>
            </div>
            <a href="CuaHang/TrangBanHang/danhSachSach.php" class="view-all-btn">Xem tất cả <i class="fas fa-chevron-right"></i></a>
        </div>
        <div class="books-grid" id="banchay-grid">
            <?php foreach ($ds_banchay as $sach):
                /*
                 * Badges:
                 *  1. Luôn có nhãn "Hot" màu cam
                 *  2. Nếu đang trong Flash Sale → thêm nhãn "-XX%" màu đỏ
                 *     và tính giaSau để hiển thị giá gạch ngang
                 */
                $nhanHieu = [['class' => 'label-type', 'label' => '🔥 Hot']];
                if (!empty($sach['phanTramGiam'])) {
                    $nhanHieu[] = ['class' => 'label-discount', 'label' => '-' . $sach['phanTramGiam'] . '%'];
                    $sach['giaSau'] = round($sach['giaBan'] * (1 - $sach['phanTramGiam'] / 100));
                }
                echo hienThiTheSach($sach, $nhanHieu);
            endforeach; ?>
        </div>
    </section>

    <!-- ===== SÁCH MỚI PHÁT HÀNH ===== -->
    <section class="new-releases">
        <div class="section-header">
            <div>
                <h3><i class="fas fa-sparkles"></i> Sách Mới Phát Hành</h3>
                <p>Cập nhật những tựa sách mới nhất</p>
            </div>
            <a href="#" class="view-all-btn light">Xem tất cả <i class="fas fa-chevron-right"></i></a>
        </div>
        <div class="books-grid">
            <?php foreach ($ds_sachmoi as $sach):
                /* Badge cam: "Mới" */
                echo hienThiTheSach($sach, [
                    ['class' => 'label-type', 'label' => 'Mới'],
                ]);
            endforeach; ?>
        </div>
    </section>

</div>
</main>

<?php include_once "CuaHang/TrangBanHang/GiaoDien/footer.php"; ?>

<?php include_once "CuaHang/TrangBanHang/ChiTietSach/formXemNhanhSach.php"; ?>
<?php include_once "CuaHang/TrangBanHang/GioHang/formGioHang.php"; ?>

<script src="PhuongThuc/components/thongBao.js"></script>
<script src="PhuongThuc/trinhChieuBanner.js"></script>
<script src="PhuongThuc/components/bookCard.js"></script>
<script src="PhuongThuc/cart.js"></script>
<script src="PhuongThuc/components/xacThuc.js"></script>
<script src="PhuongThuc/components/xacNhanDangXuat.js"></script>
<script src="PhuongThuc/components/chatbot.js"></script>
<script src="PhuongThuc/btnDanhMuc.js"></script>
<script src="PhuongThuc/btnThemGioHang.js"></script>
<script src="PhuongThuc/app.js"></script>
<script src="PhuongThuc/xemNhanhSach.js"></script>

<?php require_once "CuaHang/TrangBanHang/KhuVucTrungBay/scriptTrangChu.php"; ?>

</body>
</html>
