<?php
/**
 * layChiTietSach.php — v2 (Premium)
 * Trang chi tiết sách — render HTML thuần PHP (KHÔNG phải JSON API).
 *
 * Tham số GET:
 *   maSach (string) — mã định danh sách cần truy vấn
 *
 * Layout: ảnh bìa | thông tin đầy đủ + sách liên quan
 */

session_start();
require_once '../../../KetNoi/config/db.php';
require_once '../../TrangBanHang/GiaoDien/thanhPhan/bookCard.php';

// ── Hàm tiện ích ──────────────────────────────────────────────────────────────

/** Lấy văn bản hiển thị an toàn (tránh XSS) */
function hienThiAn($chuoi, $macc = '') {
    return htmlspecialchars((string)($chuoi ?? $macc), ENT_QUOTES, 'UTF-8');
}

/** Định dạng giá tiền kiểu Việt Nam */
function dinhDangGia($soTien) {
    return number_format((float)$soTien, 0, ',', '.') . ' ₫';
}

// ── Kiểm tra tham số đầu vào ──────────────────────────────────────────────────
$maSach = trim($_GET['maSach'] ?? '');
$sach   = null;
$ds_lienQuan = [];

if ($maSach !== '') {
    try {
        $truyVan = $pdo->prepare("
            SELECT
                s.maSach,
                s.tenSach,
                s.giaBan,
                s.namSX,
                s.moTa,
                s.loaiBia     AS hinhThucBia,
                s.soLuongTon,
                (SELECT tenNXB FROM NhaXuatBan nxb WHERE nxb.maNXB = s.maNXB) AS nhaXuatBan,

                (SELECT urlAnh FROM HinhAnhSach
                 WHERE maSach = s.maSach LIMIT 1)                                  AS hinhAnh,

                (SELECT GROUP_CONCAT(tg.tenTG ORDER BY tg.tenTG SEPARATOR ', ')
                 FROM Sach_TacGia stg
                 JOIN TacGia tg ON tg.maTG = stg.maTG
                 WHERE stg.maSach = s.maSach)                                      AS tacGia,

                (SELECT tl.tenTL
                 FROM Sach_TheLoai stl
                 JOIN TheLoai tl ON tl.maTL = stl.maTL
                 WHERE stl.maSach = s.maSach LIMIT 1)                              AS theLoai,

                (SELECT stl.maTL
                 FROM Sach_TheLoai stl
                 WHERE stl.maSach = s.maSach LIMIT 1)                              AS maTL,

                (SELECT ROUND(AVG(diemDG), 1)
                 FROM DanhGiaSach WHERE maSach = s.maSach)                         AS diemTB,

                (SELECT COUNT(*)
                 FROM DanhGiaSach WHERE maSach = s.maSach)                         AS soReview,

                IFNULL((
                    SELECT SUM(ct.soLuong)
                    FROM ChiTietDH ct
                    JOIN DonHang dh ON dh.maDH = ct.maDH
                    WHERE ct.maSach = s.maSach AND dh.trangThai = 'HoanThanh'
                ), 0)                                                               AS tongBan,

                (SELECT ckm.phanTramGiam
                 FROM ChiTietKhuyenMai ckm
                 JOIN KhuyenMai km ON km.maKM = ckm.maKM
                 WHERE ckm.maSach = s.maSach
                   AND NOW() BETWEEN km.ngayBatDau AND km.ngayKetThuc
                 LIMIT 1)                                                           AS phanTramGiam

            FROM Sach s
            WHERE s.maSach = ?
        ");
        $truyVan->execute([$maSach]);
        $sach = $truyVan->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $loi) {
        // DEV: ghi lỗi để debug
        error_log('[layChiTietSach] PDOException: ' . $loi->getMessage());
        $sach = null;
    }

    // Tính giá sau khuyến mãi
    if ($sach) {
        if (!empty($sach['phanTramGiam'])) {
            $sach['giaSau'] = (int) round($sach['giaBan'] * (1 - $sach['phanTramGiam'] / 100));
        } else {
            $sach['giaSau'] = null;
        }

        // ── Sách liên quan (cùng thể loại, loại trừ sách hiện tại) ────────
        if (!empty($sach['maTL'])) {
            try {
                $truyVanLQ = $pdo->prepare("
                    SELECT
                        s.maSach,
                        s.tenSach,
                        s.giaBan,
                        s.soLuongTon,
                        (SELECT urlAnh FROM HinhAnhSach WHERE maSach = s.maSach LIMIT 1)     AS hinhAnh,
                        (SELECT GROUP_CONCAT(tg.tenTG SEPARATOR ', ')
                         FROM Sach_TacGia stg JOIN TacGia tg ON tg.maTG = stg.maTG
                         WHERE stg.maSach = s.maSach)                                        AS tacGia,
                        (SELECT tl.tenTL FROM Sach_TheLoai stl
                         JOIN TheLoai tl ON tl.maTL = stl.maTL
                         WHERE stl.maSach = s.maSach LIMIT 1)                               AS theLoai,
                        (SELECT ROUND(AVG(diemDG), 1) FROM DanhGiaSach
                         WHERE maSach = s.maSach)                                            AS diemTB,
                        (SELECT COUNT(*) FROM DanhGiaSach WHERE maSach = s.maSach)           AS soReview,
                        IFNULL((
                            SELECT SUM(ct.soLuong) FROM ChiTietDH ct
                            JOIN DonHang dh ON dh.maDH = ct.maDH
                            WHERE ct.maSach = s.maSach AND dh.trangThai = 'HoanThanh'
                        ), 0)                                                                AS tongBan,
                        (SELECT ckm.phanTramGiam FROM ChiTietKhuyenMai ckm
                         JOIN KhuyenMai km ON km.maKM = ckm.maKM
                         WHERE ckm.maSach = s.maSach
                           AND NOW() BETWEEN km.ngayBatDau AND km.ngayKetThuc
                         LIMIT 1)                                                            AS phanTramGiam
                    FROM Sach s
                    JOIN Sach_TheLoai stl ON stl.maSach = s.maSach
                    WHERE stl.maTL = ?
                      AND s.maSach <> ?
                    ORDER BY RAND()
                    LIMIT 8
                ");
                $truyVanLQ->execute([$sach['maTL'], $maSach]);
                $ds_lienQuan = $truyVanLQ->fetchAll(PDO::FETCH_ASSOC);

                // Tính giá sau giảm cho từng sách liên quan
                foreach ($ds_lienQuan as &$sl) {
                    if (!empty($sl['phanTramGiam'])) {
                        $sl['giaSau'] = (int) round($sl['giaBan'] * (1 - $sl['phanTramGiam'] / 100));
                    } else {
                        $sl['giaSau'] = null;
                    }
                }
                unset($sl);

            } catch (PDOException $e) {
                $ds_lienQuan = [];
            }
        }

        // ── Đánh giá của độc giả ────────────────────────────────
        $dsDanhGia   = [];
        $tongDanhGia = 0;
        $diemTBDanhGia = 0;
        try {
            $stmtDG = $pdo->prepare("
                SELECT dg.diemDG, dg.nhanXet, dg.ngayDG, nd.tenND
                FROM DanhGiaSach dg
                JOIN NguoiDung nd ON dg.maND = nd.maND
                WHERE dg.maSach = ?
                ORDER BY dg.ngayDG DESC
                LIMIT 20
            ");
            $stmtDG->execute([$maSach]);
            $dsDanhGia   = $stmtDG->fetchAll(PDO::FETCH_ASSOC);
            $tongDanhGia = count($dsDanhGia);
            if ($tongDanhGia > 0) {
                $diemTBDanhGia = round(array_sum(array_column($dsDanhGia, 'diemDG')) / $tongDanhGia, 1);
            }
        } catch (PDOException $e) {
            $dsDanhGia = [];
        }
    }
}

// ── Kiểm tra đăng nhập ────────────────────────────────────────────────────────
$isLoggedIn    = isset($_SESSION['nguoi_dung_id']);
$phai_xoa_cart = false;
$duong_dan_goc = '/DoAn-Web/DoAn/';

// ── Biến $tonKho luôn có giá trị mặc định ────────────────────────────────────
$tonKho = 0;

// Giá hiển thị
$giaHienTai = $sach ? ($sach['giaSau'] ?? $sach['giaBan']) : 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $sach ? hienThiAn($sach['tenSach']) . ' — Book Sales' : 'Không tìm thấy sách' ?></title>
    <meta name="description" content="<?= $sach ? hienThiAn(mb_substr($sach['moTa'] ?? '', 0, 160)) : 'Trang chi tiết sách' ?>">
    <link rel="stylesheet" href="../../../GiaoDien/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../GiaoDien/chiTietSach.css">
    <?php
    // BẢO MẬT: Lấy cartServerData + price map từ DB
    require_once '../../../PhuongThuc/layGioHangCoGia.php';
    ?>
    <script>const dangDangNhap = <?= $isLoggedIn ? 'true' : 'false' ?>;</script>
    <?php if ($isLoggedIn): ?>
    <script>
        var cartServerData = <?= $cartServerDataJson ?? '[]' ?>;
        var __giaSach      = <?= $giaSachMapJson ?? '{}' ?>;
        var __tonKhoMap    = <?= $tonKhoMapJson  ?? '{}' ?>;
    </script>
    <?php else: ?>
    <script>
        var cartServerData = null;
        var __giaSach      = <?= $giaSachMapJson ?? '{}' ?>;
        var __tonKhoMap    = <?= $tonKhoMapJson  ?? '{}' ?>;
    </script>
    <?php endif; ?>
    
</head>
<body>

<?php include_once '../../../CuaHang/TrangBanHang/GiaoDien/header.php'; ?>

<!-- ── Breadcrumb ── -->
<nav class="ct-breadcrumb" aria-label="Breadcrumb">
    <a href="<?= $duong_dan_goc ?>index.php"><i class="fas fa-home"></i> Trang chủ</a>
    <span class="sep"><i class="fas fa-chevron-right" style="font-size:10px"></i></span>
    <?php if ($sach && !empty($sach['theLoai'])): ?>
    <a href="<?= $duong_dan_goc ?>index.php"><?= hienThiAn($sach['theLoai']) ?></a>
    <span class="sep"><i class="fas fa-chevron-right" style="font-size:10px"></i></span>
    <?php endif; ?>
    <span class="current"><?= $sach ? hienThiAn(mb_substr($sach['tenSach'], 0, 50)) : 'Không tìm thấy' ?></span>
</nav>

<div class="ct-wrap">

<?php if (!$sach): ?>
    <!-- ── Lỗi: Không tìm thấy sách ── -->
    <div class="ct-error-card">
        <div class="ct-error-icon"><i class="fas fa-book-open"></i></div>
        <h2>Không tìm thấy sách</h2>
        <p>Mã sách <strong><?= hienThiAn($maSach ?: '(trống)') ?></strong> không tồn tại hoặc đã bị xóa.</p>
        <a href="<?= $duong_dan_goc ?>index.php" class="ct-btn-back">
            <i class="fas fa-home"></i> Về trang chủ
        </a>
    </div>

<?php else:
    $giaBan     = (float)$sach['giaBan'];
    $giaSau     = $sach['giaSau'];
    $giaHienTai = $giaSau ?? $giaBan;
    $giam       = (int)($sach['phanTramGiam'] ?? 0);
    $diem       = (float)($sach['diemTB'] ?? 0);
    $soReview   = (int)($sach['soReview'] ?? 0);
    $tongBan    = (int)($sach['tongBan'] ?? 0);
    $tonKho     = (int)($sach['soLuongTon'] ?? 0);
    $hinhAnh    = anhSach($sach['hinhAnh'] ?? null);
?>

    <!-- ── Layout 2 cột: ảnh | thông tin ── -->
    <div class="ct-layout-wrap">

        <!-- Cột trái: ảnh bìa (sticky riêng) -->
        <div class="ct-col-left">
            <div class="ct-cover-wrap">
                <img src="<?= $hinhAnh ?>" alt="<?= hienThiAn($sach['tenSach']) ?>" id="ct-anh-bia">
                <?php if ($giam > 0): ?>
                <div class="ct-badge-km">-<?= $giam ?>%</div>
                <?php endif; ?>
            </div>
        </div><!-- /ct-col-left -->

        <!-- Cột phải: thông tin -->
        <div class="ct-col-right">

            <!-- Thể loại chip -->
            <?php if (!empty($sach['theLoai'])): ?>
            <div class="ct-theloai-chip">
                <i class="fas fa-tag"></i>
                <?= hienThiAn($sach['theLoai']) ?>
            </div>
            <?php endif; ?>

            <!-- Tên sách -->
            <h1 class="ct-ten-sach"><?= hienThiAn($sach['tenSach']) ?></h1>

            <!-- Tác giả -->
            <?php if (!empty($sach['tacGia'])): ?>
            <p class="ct-tac-gia">
                <i class="fas fa-user-edit fa-sm" style="color:#6366f1;margin-right:5px"></i>
                Tác giả: <a href="#"><?= hienThiAn($sach['tacGia']) ?></a>
            </p>
            <?php endif; ?>

            <!-- Rating -->
            <div class="ct-rating-bar">
                <?php if ($diem > 0): ?>
                <div class="ct-sao">
                    <?php for ($i = 1; $i <= 5; $i++):
                        if ($i <= floor($diem)) echo '<i class="fas fa-star"></i>';
                        elseif ($i < $diem + 1) echo '<i class="fas fa-star-half-alt"></i>';
                        else echo '<i class="far fa-star" style="color:#d1d5db"></i>';
                    endfor; ?>
                </div>
                <span class="ct-diem"><?= number_format($diem, 1) ?></span>
                <span class="ct-sep"></span>
                <?php endif; ?>
                <?php if ($soReview > 0): ?>
                <span class="ct-so-review"><?= number_format($soReview, 0, ',', '.') ?> đánh giá</span>
                <span class="ct-sep"></span>
                <?php endif; ?>
                <?php if ($tongBan > 0): ?>
                <span class="ct-da-ban">Đã bán <?= $tongBan >= 1000 ? number_format($tongBan/1000, 1) . 'k+' : $tongBan . '+' ?></span>
                <?php endif; ?>
            </div>

            <hr class="ct-divider">

            <!-- Giá -->
            <div class="ct-gia-block">
                <span class="ct-gia-moi"><?= dinhDangGia($giaHienTai) ?></span>
                <?php if ($giaSau !== null && $giaBan > $giaHienTai): ?>
                <span class="ct-gia-cu"><?= dinhDangGia($giaBan) ?></span>
                <span class="ct-pct-giam">-<?= $giam ?>%</span>
                <?php endif; ?>
            </div>

            <!-- Tồn kho -->
            <?php if ($tonKho <= 0): ?>
            <div class="ct-ton-kho-tag het-hang"><i class="fas fa-times-circle"></i> Hết hàng</div>
            <?php elseif ($tonKho <= 5): ?>
            <div class="ct-ton-kho-tag sap-het"><i class="fas fa-exclamation-triangle"></i> Sắp hết — còn <?= $tonKho ?> cuốn</div>
            <?php else: ?>
            <div class="ct-ton-kho-tag con-hang"><i class="fas fa-check-circle"></i> Còn hàng</div>
            <?php endif; ?>

            <hr class="ct-divider">

            <!-- Số lượng + Nút mua -->
            <?php if ($tonKho > 0): ?>
            <div class="ct-mua-wrap">
                <!-- Số lượng -->
                <div class="ct-so-luong">
                    <button class="ct-sl-btn" id="ct-giam-sl" type="button">−</button>
                    <input class="ct-sl-input" id="ct-so-luong" type="number" value="1" min="1" max="<?= $tonKho ?>">
                    <button class="ct-sl-btn" id="ct-tang-sl" type="button">+</button>
                </div>

                <!-- Thêm vào giỏ -->
                <button class="ct-btn-them-gio" id="ct-btn-them-gio"
                    data-id="<?= hienThiAn($sach['maSach']) ?>"
                    data-name="<?= hienThiAn($sach['tenSach']) ?>"
                    data-image="<?= $hinhAnh ?>"
                    data-tac-gia="<?= hienThiAn($sach['tacGia'] ?? '') ?>">
                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                </button>

                <!-- Mua ngay -->
                <button class="ct-btn-mua-ngay" id="ct-btn-mua-ngay"
                    data-id="<?= hienThiAn($sach['maSach']) ?>"
                    data-name="<?= hienThiAn($sach['tenSach']) ?>"
                    data-image="<?= $hinhAnh ?>"
                    data-tac-gia="<?= hienThiAn($sach['tacGia'] ?? '') ?>">
                    <i class="fas fa-bolt"></i> Mua ngay
                </button>
            </div>
            <?php else: ?>
            <p style="color:#dc2626;font-weight:600;font-size:14px;margin-bottom:20px">
                <i class="fas fa-bell" style="margin-right:6px"></i>
                Sản phẩm tạm hết hàng, vui lòng quay lại sau.
            </p>
            <?php endif; ?>

            <!-- Meta thông tin -->
            <div class="ct-meta-grid">
                <div class="ct-meta-row">
                    <span class="ct-meta-label">Nhà xuất bản</span>
                    <span class="ct-meta-value"><?= hienThiAn($sach['nhaXuatBan'] ?? 'Đang cập nhật') ?></span>
                </div>
                <div class="ct-meta-row">
                    <span class="ct-meta-label">Năm xuất bản</span>
                    <span class="ct-meta-value"><?= hienThiAn($sach['namSX'] ?? 'Đang cập nhật') ?></span>
                </div>
                <div class="ct-meta-row">
                    <span class="ct-meta-label">Hình thức bìa</span>
                    <span class="ct-meta-value"><?= hienThiAn($sach['hinhThucBia'] ?? 'Đang cập nhật') ?></span>
                </div>

                <div class="ct-meta-row">
                    <span class="ct-meta-label">Mã sách</span>
                    <span class="ct-meta-value"><?= hienThiAn($sach['maSach']) ?></span>
                </div>
            </div>

        </div><!-- /ct-col-right -->
    </div><!-- /ct-layout-wrap -->

    <!-- ── Mô tả sách ── -->
    <?php if (!empty($sach['moTa'])): ?>
    <div class="ct-mota-section">
        <h2 class="ct-section-title">Mô tả sản phẩm</h2>
        <div class="ct-mota-text" id="ct-mota-text">
            <?= nl2br(hienThiAn($sach['moTa'])) ?>
            <br/>
            <div class="ct-mota-fade" id="ct-mota-fade"></div>
        </div>
        <?php if (mb_strlen($sach['moTa']) > 300): ?>
        <button class="ct-btn-xem-them" id="ct-xem-them">
            <i class="fas fa-chevron-down" id="ct-icon-xem-them"></i>
            <span id="ct-text-xem-them">Xem thêm</span>
        </button>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- ── Đánh giá từ độc giả ── -->
    <?php if (!empty($dsDanhGia) || $tongDanhGia === 0): ?>
    <div class="ct-danh-gia-section">
        <h2 class="ct-section-title">
            <i class="fas fa-star" style="color:#f59e0b;margin-right:8px"></i>
            Đánh giá từ độc giả
        </h2>

        <?php if ($tongDanhGia === 0): ?>
        <div class="ct-dg-rong">
            <i class="far fa-comment-alt"></i>
            Chưa có đánh giá nào cho cuốn sách này.
        </div>
        <?php else: ?>

        <!-- Tổng điểm -->
        <div class="ct-dg-tong">
            <div class="ct-dg-diem-lon"><?= $diemTBDanhGia ?></div>
            <div>
                <div class="ct-dg-sao">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="<?= $i <= round($diemTBDanhGia) ? 'fas' : 'far' ?> fa-star"></i>
                    <?php endfor; ?>
                </div>
                <div class="ct-dg-tong-so"><?= $tongDanhGia ?> lượt đánh giá</div>
            </div>
        </div>

        <!-- Danh sách đánh giá -->
        <div class="ct-dg-list">
        <?php foreach ($dsDanhGia as $dg):
            $kyTuDau = mb_strtoupper(mb_substr($dg['tenND'], 0, 1, 'UTF-8'), 'UTF-8');
            $ngay    = date('d/m/Y', strtotime($dg['ngayDG']));
        ?>
        <div class="ct-dg-item">
            <div class="ct-dg-avatar"><?= hienThiAn($kyTuDau) ?></div>
            <div class="ct-dg-body">
                <div class="ct-dg-ten"><?= hienThiAn($dg['tenND']) ?></div>
                <div class="ct-dg-sao">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="<?= $i <= $dg['diemDG'] ? 'fas' : 'far' ?> fa-star"></i>
                    <?php endfor; ?>
                </div>
                <?php if (!empty($dg['nhanXet'])): ?>
                <div class="ct-dg-nd"><?= hienThiAn($dg['nhanXet']) ?></div>
                <?php endif; ?>
                <div class="ct-dg-ngay"><?= $ngay ?></div>
            </div>
        </div>
        <?php endforeach; ?>
        </div>

        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- ── Sách liên quan ── -->
    <?php if (!empty($ds_lienQuan)): ?>
    <div class="ct-lien-quan-section">
        <h2 class="ct-section-title">Có thể bạn quan tâm</h2>
        <div class="books-grid" style="--columns:4">
            <?php foreach ($ds_lienQuan as $sl):
                $nhanLQ = [];
                if (!empty($sl['phanTramGiam'])) {
                    $nhanLQ[] = ['class' => 'label-discount', 'label' => '-' . (int)$sl['phanTramGiam'] . '%'];
                }
                echo hienThiTheSach($sl, $nhanLQ);
            endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

<?php endif; ?>

</div><!-- /ct-wrap -->

<?php include_once '../../../CuaHang/TrangBanHang/GiaoDien/footer.php'; ?>
<?php include_once '../../../CuaHang/TrangBanHang/ChiTietSach/formXemNhanhSach.php'; ?>
<?php include_once '../../../CuaHang/TrangBanHang/GioHang/formGioHang.php'; ?>

<script src="<?= $duong_dan_goc ?>PhuongThuc/components/thongBao.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/cart.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/components/bookCard.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/components/xacThuc.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/components/xacNhanDangXuat.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/components/chatbot.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/btnThemGioHang.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/xemNhanhSach.js"></script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/app.js"></script>

<script>
(function () {
  'use strict';

  // ── Số lượng ─────────────────────────────────────────────────────────
  var slInput = document.getElementById('ct-so-luong');
  var tonKho  = <?= $tonKho ?>;

  var nutGiam = document.getElementById('ct-giam-sl');
  var nutTang = document.getElementById('ct-tang-sl');

  if (nutGiam && slInput) {
    nutGiam.addEventListener('click', function () {
      var v = parseInt(slInput.value) || 1;
      if (v > 1) { slInput.value = v - 1; }
    });
  }
  if (nutTang && slInput) {
    nutTang.addEventListener('click', function () {
      var v = parseInt(slInput.value) || 1;
      if (v < tonKho) { slInput.value = v + 1; }
    });
  }
  if (slInput) {
    slInput.addEventListener('change', function () {
      var v = parseInt(slInput.value) || 1;
      if (v < 1) v = 1;
      if (v > tonKho) v = tonKho;
      slInput.value = v;
    });
  }

  // ── Thêm vào giỏ từ trang chi tiết ──────────────────────────────────
  // BẢO MẬT: Giá lấy từ __giaSach (PHP inject từ DB), không từ data-price button
  function layThongTinSach(btn) {
    var maSach = btn.getAttribute('data-id') || '';
    return {
      maSach : maSach,
      tenSach: btn.getAttribute('data-name')    || '',
      giaBan : (window.__giaSach && window.__giaSach[maSach]) ? window.__giaSach[maSach] : 0,
      hinhAnh: btn.getAttribute('data-image')   || '',
      tacGia : btn.getAttribute('data-tac-gia') || '',
    };
  }

  var nutThemGio = document.getElementById('ct-btn-them-gio');
  if (nutThemGio) {
    nutThemGio.addEventListener('click', function () {
      if (typeof dangDangNhap === 'undefined' || !dangDangNhap) {
        alert('Bạn cần đăng nhập để thêm sách vào giỏ hàng!');
        if (typeof openLogin === 'function') openLogin();
        return;
      }
      var sl = parseInt(slInput ? slInput.value : 1) || 1;
      if (typeof cartDrawer !== 'undefined') {
        cartDrawer.addItem(layThongTinSach(nutThemGio), sl);
      }
    });
  }

  var nutMuaNgay = document.getElementById('ct-btn-mua-ngay');
  if (nutMuaNgay) {
    nutMuaNgay.addEventListener('click', function () {
      if (typeof dangDangNhap === 'undefined' || !dangDangNhap) {
        alert('Bạn cần đăng nhập để mua hàng!');
        if (typeof openLogin === 'function') openLogin();
        return;
      }
      var sl = parseInt(slInput ? slInput.value : 1) || 1;
      if (typeof cartDrawer !== 'undefined') {
        cartDrawer.addItem(layThongTinSach(nutMuaNgay), sl);
        // Chuyển thẳng vào giỏ hàng (mở drawer)
        cartDrawer.open();
      }
    });
  }

  // ── Xem thêm / Thu gọn mô tả ─────────────────────────────────────────
  var elMota     = document.getElementById('ct-mota-text');
  var elFade     = document.getElementById('ct-mota-fade');
  var btnXemThem = document.getElementById('ct-xem-them');
  var iconArrow  = document.getElementById('ct-icon-xem-them');
  var textSpan   = document.getElementById('ct-text-xem-them');

  if (btnXemThem && elMota) {
    btnXemThem.addEventListener('click', function () {
      var expanded = elMota.classList.toggle('expanded');
      if (iconArrow) {
        iconArrow.className = expanded ? 'fas fa-chevron-up' : 'fas fa-chevron-down';
      }
      if (textSpan) {
        textSpan.textContent = expanded ? 'Thu gọn' : 'Xem thêm';
      }
    });
  }

})();
</script>

</body>
</html>
