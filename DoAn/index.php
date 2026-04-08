<?php
session_start();
require_once "KetNoi/config/db.php";
$isLoggedIn = isset($_SESSION['nguoi_dung_id']);

function renderBookCard(array $s, array $badges = []): string
{
    $anh     = !empty($s['hinhAnh']) ? htmlspecialchars($s['hinhAnh'])
                                     : 'https://placehold.co/300x400/eff6ff/2563eb?text=📚';
    $ten     = htmlspecialchars($s['tenSach']);
    $tacGia  = htmlspecialchars(!empty($s['tacGia'])  ? $s['tacGia']  : 'Đang cập nhật');
    $theLoai = htmlspecialchars(!empty($s['theLoai']) ? $s['theLoai'] : '');
    $giaBan  = (float)($s['giaBan'] ?? 0);
    $giaSau  = isset($s['giaSau'])  ? (float)$s['giaSau']  : null;
    $diem    = (float)($s['diemTB'] ?? 0);
    $soRV    = (int)($s['soReview'] ?? 0);
    $maS     = htmlspecialchars($s['maSach']);
    $hienGia = ($giaSau !== null) ? $giaSau : $giaBan;

    /* Badges góc trái dọc*/
    $badgeHtml = '';
    foreach ($badges as $b) {
        $badgeHtml .= "<span class=\"book-badge {$b['class']}\">{$b['label']}</span>\n";
    }

    //điểm dánh giá
    if ($diem > 0) {
        $ratingHtml = "
            <div class=\"book-rating\">
                <i class=\"fas fa-star star-icon\"></i>
                <span class=\"rating-score\">{$diem}</span>
                <span class=\"rating-dot\"></span>
                <span class=\"rating-count\">({$soRV})</span>
            </div>";
    } else {
        $ratingHtml = "
            <div class=\"book-rating\">
                <i class=\"far fa-star star-icon\"></i>
                <span class=\"rating-count\">Chưa có đánh giá</span>
            </div>";
    }

    /* Giá */
    $giaFmt     = number_format($hienGia, 0, ',', '.');
    $giaGocHTML = ($giaSau !== null)
        ? '<span class="original-price">' . number_format($giaBan, 0, ',', '.') . ' ₫</span>'
        : '';

    $categoryHTML = $theLoai ? "<span class=\"book-category\">{$theLoai}</span>" : '';

    return "
    <div class=\"book-card\"
     data-id=\"{$maS}\"
     data-name=\"{$ten}\"
     data-price=\"{$hienGia}\"
     data-image=\"{$anh}\">

    <div class=\"book-image\">
        " . ($badgeHtml ? "<div class=\"book-badges\">{$badgeHtml}</div>" : '') . "
        <img src=\"{$anh}\" alt=\"{$ten}\" loading=\"lazy\">

        <!-- Nút phải: tim + mắt — ẩn, slide-in khi hover -->
        <div class=\"book-actions-right\">
            <button class=\"btn-action-icon btn-wishlist\" title=\"Yêu thích\">
                <i class=\"far fa-heart\"></i>
            </button>
            <button class=\"btn-action-icon btn-quickview\" title=\"Xem nhanh\">
                <i class=\"fas fa-eye\"></i>
            </button>
        </div>

        <!-- Nút dưới: Thêm Nhanh — ẩn, slide-up khi hover -->
        <div class=\"book-add-quick\">
            <button class=\"btn-add-quick\" onclick=\"themVaoGioHang(this)\">
                <i class=\"fas fa-shopping-cart\"></i> Thêm Nhanh
            </button>
        </div>
    </div>

    <div class=\"book-info\">
        {$categoryHTML}
        <h4 class=\"book-title\">{$ten}</h4>
        <p class=\"book-author\">{$tacGia}</p>
        {$ratingHtml}
        <div class=\"book-footer\">
            <div class=\"book-price-block\">
                <span class=\"current-price\">{$giaFmt} ₫</span>
                {$giaGocHTML}
            </div>
            <button class=\"btn-add-to-cart\" onclick=\"themVaoGioHang(this)\" title=\"Thêm vào giỏ\">
                <i class=\"fas fa-shopping-cart\"></i>
            </button>
        </div>
    </div>
</div>";
}

// ================================================================
// THUẬT TOÁN FLASH SALE
// Chỉ hiển thị khi NOW() nằm trong [ngayBatDau, ngayKetThuc]
// phanTramGiam thực từ ChiTietKhuyenMai (10% | 22% | 33%)
// ================================================================
$ds_flashsale = $pdo->query("
    SELECT
        s.maSach, s.tenSach, s.giaBan,
        ckm.phanTramGiam,
        ROUND(s.giaBan * (1 - ckm.phanTramGiam / 100)) AS giaSau,
        (SELECT urlAnh FROM HinhAnhSach WHERE maSach = s.maSach LIMIT 1) AS hinhAnh,
        (SELECT GROUP_CONCAT(tg.tenTG SEPARATOR ', ')
         FROM Sach_TacGia stg JOIN TacGia tg ON stg.maTG = tg.maTG
         WHERE stg.maSach = s.maSach) AS tacGia,
        (SELECT tenTL FROM TheLoai tl
         JOIN Sach_TheLoai stl ON stl.maTL = tl.maTL
         WHERE stl.maSach = s.maSach LIMIT 1) AS theLoai,
        (SELECT ROUND(AVG(diemDG), 1) FROM DanhGiaSach WHERE maSach = s.maSach) AS diemTB,
        (SELECT COUNT(*) FROM DanhGiaSach WHERE maSach = s.maSach) AS soReview
    FROM Sach s
    JOIN ChiTietKhuyenMai ckm ON ckm.maSach = s.maSach
    JOIN KhuyenMai km ON km.maKM = ckm.maKM
    WHERE s.trangThai = 'DangKD'
      AND NOW() BETWEEN km.ngayBatDau AND km.ngayKetThuc
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);

// Thời gian kết thúc Flash Sale (cho đồng hồ đếm ngược)
$flashSaleEndTime = $pdo->query("
    SELECT ngayKetThuc FROM KhuyenMai
    WHERE NOW() BETWEEN ngayBatDau AND ngayKetThuc
    ORDER BY ngayKetThuc ASC LIMIT 1
")->fetchColumn();

// ================================================================
// THUẬT TOÁN SÁCH BÁN CHẠY
// Lọc theo tháng hiện tại: MONTH(ngayDat) = MONTH(NOW())
//   AND YEAR(ngayDat) = YEAR(NOW())
// SUM(soLuong) từ đơn HoanThanh trong tháng → sort DESC → top 10
// Kèm phanTramGiam Flash Sale real-time nếu cuốn sách đang giảm giá
// ================================================================
$ds_banchay = $pdo->query("
    SELECT
        s.maSach, s.tenSach, s.giaBan,
        (SELECT urlAnh FROM HinhAnhSach WHERE maSach = s.maSach LIMIT 1) AS hinhAnh,
        (SELECT GROUP_CONCAT(tg.tenTG SEPARATOR ', ')
         FROM Sach_TacGia stg JOIN TacGia tg ON stg.maTG = tg.maTG
         WHERE stg.maSach = s.maSach) AS tacGia,
        (SELECT tenTL FROM TheLoai tl
         JOIN Sach_TheLoai stl ON stl.maTL = tl.maTL
         WHERE stl.maSach = s.maSach LIMIT 1) AS theLoai,
        (SELECT ROUND(AVG(diemDG), 1) FROM DanhGiaSach WHERE maSach = s.maSach) AS diemTB,
        (SELECT COUNT(*) FROM DanhGiaSach WHERE maSach = s.maSach) AS soReview,
        -- Số lượng bán trong tháng hiện tại
        IFNULL((
            SELECT SUM(ct.soLuong)
            FROM ChiTietDH ct
            JOIN DonHang dh ON dh.maDH = ct.maDH
            WHERE ct.maSach = s.maSach
              AND dh.trangThai = 'HoanThanh'
              AND MONTH(dh.ngayDat) = MONTH(NOW())
              AND YEAR(dh.ngayDat)  = YEAR(NOW())
        ), 0) AS tongBanThang,
        -- Giảm giá Flash Sale real-time (nếu đang trong khung giờ)
        (SELECT ckm.phanTramGiam
         FROM ChiTietKhuyenMai ckm
         JOIN KhuyenMai km ON km.maKM = ckm.maKM
         WHERE ckm.maSach = s.maSach
           AND NOW() BETWEEN km.ngayBatDau AND km.ngayKetThuc
         LIMIT 1) AS phanTramGiam
    FROM Sach s
    WHERE s.trangThai = 'DangKD'
    ORDER BY tongBanThang DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

// ================================================================
// THUẬT TOÁN SÁCH MỚI 
// ORDER BY namSX DESC, maSach DESC
// ================================================================
$ds_sachmoi = $pdo->query("
    SELECT
        s.maSach, s.tenSach, s.giaBan, s.namSX,
        (SELECT urlAnh FROM HinhAnhSach WHERE maSach = s.maSach LIMIT 1) AS hinhAnh,
        (SELECT GROUP_CONCAT(tg.tenTG SEPARATOR ', ')
         FROM Sach_TacGia stg JOIN TacGia tg ON stg.maTG = tg.maTG
         WHERE stg.maSach = s.maSach) AS tacGia,
        (SELECT tenTL FROM TheLoai tl
         JOIN Sach_TheLoai stl ON stl.maTL = tl.maTL
         WHERE stl.maSach = s.maSach LIMIT 1) AS theLoai,
        (SELECT ROUND(AVG(diemDG), 1) FROM DanhGiaSach WHERE maSach = s.maSach) AS diemTB,
        (SELECT COUNT(*) FROM DanhGiaSach WHERE maSach = s.maSach) AS soReview
    FROM Sach s
    WHERE s.trangThai = 'DangKD'
    ORDER BY s.namSX DESC, s.maSach DESC
    LIMIT 8
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Sales Management - Cửa hàng sách trực tuyến</title>
    <link rel="stylesheet" href="GiaoDien/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>const dangDangNhap = <?= $isLoggedIn ? 'true' : 'false' ?>;</script>
</head>
<body>

<?php include_once "CuaHang/TrangBanHang/GiaoDien/header.php"; ?>

<main class="main-content container">
<div id="home-content">

    <!-- ===== HERO BANNER ===== -->
    <section class="hero-banner">
        <div class="hero-slider" id="hero-slider">
            <?php
            $banners = $pdo->query("SELECT * FROM QuangCao WHERE trangThai = 1 ORDER BY maQC ASC")->fetchAll();
            foreach ($banners as $idx => $b):
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
            <div class="books-grid">
                <?php foreach ($ds_flashsale as $sach):
                    /* Badge 1 (cam): nhãn "Flash Sale"
                       Badge 2 (đỏ): "-XX%" — phanTramGiam thực từ ChiTietKhuyenMai */
                    echo renderBookCard($sach, [
                        ['class' => 'label-type',     'label' => 'Flash Sale'],
                        ['class' => 'label-discount',  'label' => '-' . $sach['phanTramGiam'] . '%'],
                    ]);
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
            $icons = ['📚','📈','🧠','🧸','🔬','🌍']; $i = 0;
            foreach ($pdo->query("SELECT * FROM TheLoai LIMIT 6") as $tl): ?>
            <a href="#" class="category-card">
                <div class="category-icon"><?= $icons[$i++ % 6] ?></div>
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
                $badges = [['class' => 'label-type', 'label' => '🔥 Hot']];
                if (!empty($sach['phanTramGiam'])) {
                    $badges[] = ['class' => 'label-discount', 'label' => '-' . $sach['phanTramGiam'] . '%'];
                    $sach['giaSau'] = round($sach['giaBan'] * (1 - $sach['phanTramGiam'] / 100));
                }
                echo renderBookCard($sach, $badges);
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
                /* Badge cam: "Mới YYYY" */
                echo renderBookCard($sach, [
                    ['class' => 'label-type', 'label' => 'Mới'],
                ]);
            endforeach; ?>
        </div>
    </section>

</div>
</main>

<?php include_once "CuaHang/TrangBanHang/GiaoDien/footer.php"; ?>

<script src="PhuongThuc/thongBao.js"></script>
<script src="PhuongThuc/trinhChieuBanner.js"></script>
<script src="PhuongThuc/bookCard.js"></script>
<script src="PhuongThuc/cart.js"></script>
<script src="PhuongThuc/xacThuc.js"></script>
<script src="PhuongThuc/xacNhanDangXuat.js"></script>
<script src="PhuongThuc/chatbot.js"></script>
<script src="PhuongThuc/btnDanhMuc.js"></script>
<script src="PhuongThuc/btnThemGioHang.js"></script>
<script src="PhuongThuc/app.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {

    // Banner Slider
    if (document.getElementById('hero-slider') && typeof TrinhChieuBanner !== 'undefined') {
        new TrinhChieuBanner('hero-slider');
    }

    <?php if ($flashSaleEndTime): ?>
    // Đồng hồ đếm ngược Flash Sale — dùng ngayKetThuc thực từ DB
    (function () {
        const ketThuc = new Date("<?= $flashSaleEndTime ?>").getTime();
        function tick() {
            const d = ketThuc - Date.now();
            if (d <= 0) {
                ['hours','minutes','seconds'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = '00';
                });
                return;
            }
            const pad = v => String(v).padStart(2, '0');
            const set = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = pad(v); };
            set('hours',   Math.floor(d / 3600000));
            set('minutes', Math.floor((d % 3600000) / 60000));
            set('seconds', Math.floor((d % 60000) / 1000));
        }
        tick();
        setInterval(tick, 1000);
    })();
    <?php endif; ?>

});
</script>
</body>
</html>
