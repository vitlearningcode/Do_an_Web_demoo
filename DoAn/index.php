<?php
// Khởi động Session và Kết nối Database
session_start();
require_once "KetNoi/config/db.php"; 
$isLoggedIn = isset($_SESSION['nguoi_dung_id']);

// ================================================================
// HÀM TIỆN ÍCH: Render sao đánh giá (★★★★☆)
// ================================================================
function renderSao(float $diem): string {
    $html = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($diem >= $i) {
            $html .= '<i class="fas fa-star"></i>';
        } elseif ($diem >= $i - 0.5) {
            $html .= '<i class="fas fa-star-half-alt"></i>';
        } else {
            $html .= '<i class="far fa-star"></i>';
        }
    }
    return $html;
}

// ================================================================
// THUẬT TOÁN: Lấy sách đang trong Flash Sale hợp lệ (Real-time)
// Logic: Chỉ hiển thị nếu NOW() nằm trong [ngayBatDau, ngayKetThuc]
// và khuyến mãi tồn tại trong ChiTietKhuyenMai
// Ref README: "Hiển thị Real-time: giá Flash Sale tự động gạch ngang
//              giá gốc vào đúng khung giờ đó"
// ================================================================
$sql_flashsale = "
    SELECT 
        s.maSach, s.tenSach, s.giaBan, s.moTa,
        ckm.phanTramGiam,
        ROUND(s.giaBan * (1 - ckm.phanTramGiam / 100)) AS giaSau,
        (SELECT urlAnh FROM HinhAnhSach WHERE maSach = s.maSach LIMIT 1) AS hinhAnh,
        (SELECT GROUP_CONCAT(tg.tenTG SEPARATOR ', ')
         FROM Sach_TacGia stg JOIN TacGia tg ON stg.maTG = tg.maTG
         WHERE stg.maSach = s.maSach) AS tacGia,
        (SELECT ROUND(AVG(diemDG), 1) FROM DanhGiaSach WHERE maSach = s.maSach) AS diemTB,
        (SELECT COUNT(*) FROM DanhGiaSach WHERE maSach = s.maSach) AS soReview
    FROM Sach s
    JOIN ChiTietKhuyenMai ckm ON ckm.maSach = s.maSach
    JOIN KhuyenMai km ON km.maKM = ckm.maKM
    WHERE s.trangThai = 'DangKD'
      AND NOW() BETWEEN km.ngayBatDau AND km.ngayKetThuc
    LIMIT 6
";
$stmt_fs = $pdo->query($sql_flashsale);
$ds_flashsale = $stmt_fs->fetchAll(PDO::FETCH_ASSOC);

// Tính thời gian còn lại của Flash Sale (lấy khung giờ gần nhất đang active)
$sql_thoigian = "
    SELECT ngayKetThuc FROM KhuyenMai
    WHERE NOW() BETWEEN ngayBatDau AND ngayKetThuc
    ORDER BY ngayKetThuc ASC LIMIT 1
";
$stmt_tg = $pdo->query($sql_thoigian);
$flashSaleEndTime = $stmt_tg->fetchColumn();

// ================================================================
// THUẬT TOÁN: Sách Bán Chạy Nhất
// Logic: SUM(soLuong) từ ChiTietDH của các đơn HoanThanh → sort DESC
// Ref README: "Quy trình Duyệt & Giao Hàng - HoanThanh"
// ================================================================
$sql_banchay = "
    SELECT 
        s.maSach, s.tenSach, s.giaBan, s.moTa,
        (SELECT urlAnh FROM HinhAnhSach WHERE maSach = s.maSach LIMIT 1) AS hinhAnh,
        (SELECT GROUP_CONCAT(tg.tenTG SEPARATOR ', ')
         FROM Sach_TacGia stg JOIN TacGia tg ON stg.maTG = tg.maTG
         WHERE stg.maSach = s.maSach) AS tacGia,
        (SELECT tenTL FROM TheLoai tl 
         JOIN Sach_TheLoai stl ON stl.maTL = tl.maTL 
         WHERE stl.maSach = s.maSach LIMIT 1) AS theLoai,
        (SELECT ROUND(AVG(diemDG), 1) FROM DanhGiaSach WHERE maSach = s.maSach) AS diemTB,
        (SELECT COUNT(*) FROM DanhGiaSach WHERE maSach = s.maSach) AS soReview,
        IFNULL(
            (SELECT SUM(ct.soLuong) FROM ChiTietDH ct
             JOIN DonHang dh ON dh.maDH = ct.maDH
             WHERE ct.maSach = s.maSach AND dh.trangThai = 'HoanThanh'),
        0) AS tongBan
    FROM Sach s
    WHERE s.trangThai = 'DangKD'
    ORDER BY tongBan DESC
    LIMIT 8
";
$stmt_bc = $pdo->query($sql_banchay);
$ds_banchay = $stmt_bc->fetchAll(PDO::FETCH_ASSOC);

// ================================================================
// THUẬT TOÁN: Sách Mới Phát Hành
// Logic: Sort theo namSX DESC rồi maSach DESC (mã mới hơn = nhập sau)
// ================================================================
$sql_sachmoi = "
    SELECT 
        s.maSach, s.tenSach, s.giaBan, s.moTa, s.namSX,
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
";
$stmt_sm = $pdo->query($sql_sachmoi);
$ds_sachmoi = $stmt_sm->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Sales Management - Cửa hàng sách trực tuyến</title>
    <link rel="stylesheet" href="GiaoDien/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>const dangDangNhap = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;</script>
    <?php if ($flashSaleEndTime): ?>
    <script>const flashSaleKetThuc = "<?php echo $flashSaleEndTime; ?>";</script>
    <?php endif; ?>
</head>
<body>

    <?php include_once "CuaHang/TrangBanHang/GiaoDien/header.php"; ?>

    <main class="main-content container">
        <div id="home-content">
            
            <!-- ===== HERO BANNER ===== -->
            <section class="hero-banner">
                <div class="hero-slider" id="hero-slider">
                    <?php
                    $stmt = $pdo->query("SELECT * FROM QuangCao WHERE trangThai = 1 ORDER BY maQC ASC");
                    $banners = $stmt->fetchAll();
                    foreach ($banners as $index => $banner):
                        $mauNen = !empty($banner['mauNen']) ? $banner['mauNen'] : 'blue';
                    ?>
                        <div class="hero-slide <?php echo htmlspecialchars($mauNen); ?> <?php echo ($index === 0) ? 'active' : ''; ?>">
                            <div class="hero-slide-bg">
                                <img src="<?php echo htmlspecialchars($banner['hinhAnh']); ?>" alt="Banner">
                                <div class="gradient-overlay"></div>
                            </div>
                            <div class="hero-content">
                                <span class="hero-badge"><?php echo htmlspecialchars($banner['nhan']); ?></span>
                                <h2><?php echo $banner['tieuDe']; ?></h2> 
                                <p><?php echo htmlspecialchars($banner['moTa']); ?></p>
                                <button class="hero-btn"><?php echo htmlspecialchars($banner['chuNut']); ?> <i class="fas fa-arrow-right"></i></button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="hero-nav prev" id="hero-prev"><i class="fas fa-chevron-left"></i></button>
                <button class="hero-nav next" id="hero-next"><i class="fas fa-chevron-right"></i></button>
                <div class="hero-indicators" id="hero-indicators"></div>
            </section>

            <!-- ===== FLASH SALE (Dữ liệu thật từ KhuyenMai + ChiTietKhuyenMai) ===== -->
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
                            $anh = !empty($sach['hinhAnh']) ? $sach['hinhAnh'] : 'https://via.placeholder.com/300x400/EFF6FF/2563EB?text=📚';
                            $tacGia = $sach['tacGia'] ?: 'Đang cập nhật';
                            $diem = $sach['diemTB'] ?: 0;
                            $soReview = $sach['soReview'] ?: 0;
                        ?>
                        <div class="book-card" 
                             data-id="<?php echo htmlspecialchars($sach['maSach']); ?>"
                             data-name="<?php echo htmlspecialchars($sach['tenSach']); ?>"
                             data-price="<?php echo $sach['giaSau']; ?>"
                             data-image="<?php echo htmlspecialchars($anh); ?>">
                            <div class="book-image">
                                <span class="book-badge discount">-<?php echo $sach['phanTramGiam']; ?>%</span>
                                <img src="<?php echo htmlspecialchars($anh); ?>" alt="<?php echo htmlspecialchars($sach['tenSach']); ?>" loading="lazy">
                                <div class="book-overlay">
                                    <button class="btn-icon btn-quick-view" title="Xem nhanh"><i class="fas fa-eye"></i></button>
                                    <button class="btn-icon btn-add-cart" title="Thêm vào giỏ" onclick="themVaoGioHang(this)"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="book-info">
                                <h3 class="book-title"><?php echo htmlspecialchars($sach['tenSach']); ?></h3>
                                <p class="book-author"><?php echo htmlspecialchars($tacGia); ?></p>
                                <div class="book-rating">
                                    <?php echo renderSao($diem); ?>
                                    <span class="rating-count">(<?php echo $soReview; ?>)</span>
                                </div>
                                <div class="book-price">
                                    <span class="current-price"><?php echo number_format($sach['giaSau'], 0, ',', '.'); ?>đ</span>
                                    <span class="original-price"><?php echo number_format($sach['giaBan'], 0, ',', '.'); ?>đ</span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
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
                    $stmt_tl = $pdo->query("SELECT * FROM TheLoai LIMIT 6");
                    $icons = ['📚', '📈', '🧠', '🧸', '🔬', '🌍']; $i = 0;
                    while($tl = $stmt_tl->fetch()):
                    ?>
                        <a href="#" class="category-card">
                            <div class="category-icon"><?php echo $icons[$i++ % 6]; ?></div>
                            <span><?php echo htmlspecialchars($tl['tenTL']); ?></span>
                        </a>
                    <?php endwhile; ?>
                </div>
            </section>

            <!-- ===== SÁCH BÁN CHẠY (Thuật toán SUM đơn HoanThanh) ===== -->
            <section class="featured-books">
                <div class="section-header">
                    <div>
                        <h3><i class="fas fa-fire-alt"></i> Sách Bán Chạy Nhất</h3>
                        <p>Được độc giả lựa chọn nhiều nhất</p>
                    </div>
                    <a href="#" class="view-all-btn">Xem tất cả <i class="fas fa-chevron-right"></i></a>
                </div>
                <div class="books-grid">
                    <?php foreach ($ds_banchay as $sach):
                        $anh = !empty($sach['hinhAnh']) ? $sach['hinhAnh'] : 'https://via.placeholder.com/300x400/EFF6FF/2563EB?text=📚';
                        $tacGia = $sach['tacGia'] ?: 'Đang cập nhật';
                        $theLoai = $sach['theLoai'] ?: '';
                        $diem = $sach['diemTB'] ?: 0;
                        $soReview = $sach['soReview'] ?: 0;
                    ?>
                    <div class="book-card"
                         data-id="<?php echo htmlspecialchars($sach['maSach']); ?>"
                         data-name="<?php echo htmlspecialchars($sach['tenSach']); ?>"
                         data-price="<?php echo $sach['giaBan']; ?>"
                         data-image="<?php echo htmlspecialchars($anh); ?>">
                        <div class="book-image">
                            <?php if ($sach['tongBan'] > 0): ?>
                            <span class="book-badge hot">🔥 Hot</span>
                            <?php endif; ?>
                            <img src="<?php echo htmlspecialchars($anh); ?>" alt="<?php echo htmlspecialchars($sach['tenSach']); ?>" loading="lazy">
                            <div class="book-overlay">
                                <button class="btn-icon btn-quick-view" title="Xem nhanh"><i class="fas fa-eye"></i></button>
                                <button class="btn-icon btn-add-cart" title="Thêm vào giỏ" onclick="themVaoGioHang(this)"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="book-info">
                            <?php if ($theLoai): ?>
                            <span class="book-category"><?php echo htmlspecialchars($theLoai); ?></span>
                            <?php endif; ?>
                            <h3 class="book-title"><?php echo htmlspecialchars($sach['tenSach']); ?></h3>
                            <p class="book-author"><?php echo htmlspecialchars($tacGia); ?></p>
                            <div class="book-rating">
                                <?php echo renderSao($diem); ?>
                                <span class="rating-count">(<?php echo $soReview; ?>)</span>
                            </div>
                            <div class="book-price">
                                <span class="current-price"><?php echo number_format($sach['giaBan'], 0, ',', '.'); ?>đ</span>
                            </div>
                            <?php if ($sach['tongBan'] > 0): ?>
                            <p class="sold-count">Đã bán: <?php echo number_format($sach['tongBan']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- ===== SÁCH MỚI PHÁT HÀNH (Sort namSX DESC) ===== -->
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
                        $anh = !empty($sach['hinhAnh']) ? $sach['hinhAnh'] : 'https://via.placeholder.com/300x400/EFF6FF/2563EB?text=📚';
                        $tacGia = $sach['tacGia'] ?: 'Đang cập nhật';
                        $theLoai = $sach['theLoai'] ?: '';
                        $diem = $sach['diemTB'] ?: 0;
                        $soReview = $sach['soReview'] ?: 0;
                    ?>
                    <div class="book-card"
                         data-id="<?php echo htmlspecialchars($sach['maSach']); ?>"
                         data-name="<?php echo htmlspecialchars($sach['tenSach']); ?>"
                         data-price="<?php echo $sach['giaBan']; ?>"
                         data-image="<?php echo htmlspecialchars($anh); ?>">
                        <div class="book-image">
                            <span class="book-badge new">Mới <?php echo $sach['namSX']; ?></span>
                            <img src="<?php echo htmlspecialchars($anh); ?>" alt="<?php echo htmlspecialchars($sach['tenSach']); ?>" loading="lazy">
                            <div class="book-overlay">
                                <button class="btn-icon btn-quick-view" title="Xem nhanh"><i class="fas fa-eye"></i></button>
                                <button class="btn-icon btn-add-cart" title="Thêm vào giỏ" onclick="themVaoGioHang(this)"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="book-info">
                            <?php if ($theLoai): ?>
                            <span class="book-category"><?php echo htmlspecialchars($theLoai); ?></span>
                            <?php endif; ?>
                            <h3 class="book-title"><?php echo htmlspecialchars($sach['tenSach']); ?></h3>
                            <p class="book-author"><?php echo htmlspecialchars($tacGia); ?></p>
                            <div class="book-rating">
                                <?php echo renderSao($diem); ?>
                                <span class="rating-count">(<?php echo $soReview; ?>)</span>
                            </div>
                            <div class="book-price">
                                <span class="current-price"><?php echo number_format($sach['giaBan'], 0, ',', '.'); ?>đ</span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
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
            // Khởi tạo Banner Slider
            if (document.getElementById('hero-slider') && typeof TrinhChieuBanner !== 'undefined') {
                new TrinhChieuBanner('hero-slider');
            }

            // Đồng hồ đếm ngược Flash Sale (dùng thời gian thực từ DB)
            <?php if ($flashSaleEndTime): ?>
            (function() {
                const ketThuc = new Date("<?php echo $flashSaleEndTime; ?>").getTime();

                function capNhatDongHo() {
                    const now = new Date().getTime();
                    const conLai = ketThuc - now;

                    if (conLai <= 0) {
                        ['hours', 'minutes', 'seconds'].forEach(id => {
                            const el = document.getElementById(id);
                            if (el) el.textContent = '00';
                        });
                        return;
                    }

                    const gio   = Math.floor(conLai / (1000 * 60 * 60));
                    const phut  = Math.floor((conLai % (1000 * 60 * 60)) / (1000 * 60));
                    const giay  = Math.floor((conLai % (1000 * 60)) / 1000);

                    const theGio   = document.getElementById('hours');
                    const thePhut  = document.getElementById('minutes');
                    const theGiay  = document.getElementById('seconds');

                    if (theGio)  theGio.textContent  = String(gio).padStart(2, '0');
                    if (thePhut) thePhut.textContent  = String(phut).padStart(2, '0');
                    if (theGiay) theGiay.textContent  = String(giay).padStart(2, '0');
                }

                capNhatDongHo();
                setInterval(capNhatDongHo, 1000);
            })();
            <?php endif; ?>
        });
    </script>
</body>
</html>
