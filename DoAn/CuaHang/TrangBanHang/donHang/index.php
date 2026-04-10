<?php
/**
 * donHang/index.php — Theo dõi đơn hàng kiểu Shopee
 *
 * Tabs: Tất cả | Chờ Duyệt | Đang Giao | Đã Giao | Đã Hủy | Đánh Giá
 * Mỗi sản phẩm trong đơn "Đã Giao" có nút Đánh giá → form submit về xuly_danhGia.php
 * Thuần PHP: không AJAX, không chèn HTML từ JS.
 */
session_start();
require_once '../../../KetNoi/config/db.php';

$isLoggedIn = isset($_SESSION['nguoi_dung_id']);

// Bắt buộc đăng nhập
if (!$isLoggedIn) {
    header('Location: ../../../index.php');
    exit;
}

$maND = (int)$_SESSION['nguoi_dung_id'];

// Tab hiện tại
$tabHienTai = $_GET['tab'] ?? 'tat-ca';

// Trạng thái hợp lệ từ DB
$trangThaiMap = [
    'tat-ca'     => null,
    'cho-duyet'  => 'ChoDuyet',
    'dang-giao'  => 'DangGiao',
    'da-giao'    => 'HoanThanh',
    'da-huy'     => 'DaHuy',
    'danh-gia'   => 'HoanThanh', // Chỉ đơn HoanThanh mới đánh giá được
];

if (!array_key_exists($tabHienTai, $trangThaiMap)) {
    $tabHienTai = 'tat-ca';
}

// ── Lấy danh sách đơn hàng ──────────────────────────────────────────────────
$where = 'WHERE dh.maND = :maND';
$params = [':maND' => $maND];

if ($tabHienTai !== 'tat-ca' && $trangThaiMap[$tabHienTai] !== null) {
    $where .= ' AND dh.trangThai = :trangThai';
    $params[':trangThai'] = $trangThaiMap[$tabHienTai];
}

$sqlDH = "
    SELECT dh.maDH, dh.ngayDat, dh.tongTien, dh.trangThai,
           pt.tenPT, dcgh.diaChiChiTiet
    FROM DonHang dh
    JOIN PhuongThucThanhToan pt ON dh.maPT = pt.maPT
    JOIN DiaChiGiaoHang dcgh ON dh.maDC = dcgh.maDC
    $where
    ORDER BY dh.ngayDat DESC
";

$stmtDH = $pdo->prepare($sqlDH);
$stmtDH->execute($params);
$dsDonHang = $stmtDH->fetchAll();

// ── Lấy chi tiết từng đơn (items) ─────────────────────────────────────────
$chiTietDH = [];
$daDanhGia  = []; // [maDH_maSach] => true

if (!empty($dsDonHang)) {
    $maDHList = array_column($dsDonHang, 'maDH');
    $inParams = implode(',', array_fill(0, count($maDHList), '?'));

    $sqlCT = "
        SELECT ct.maDH, ct.maSach, ct.soLuong, ct.giaBan, ct.thanhTien,
               s.tenSach,
               ha.urlAnh
        FROM ChiTietDH ct
        JOIN Sach s ON ct.maSach = s.maSach
        LEFT JOIN (
            SELECT maSach, MIN(urlAnh) AS urlAnh
            FROM HinhAnhSach
            GROUP BY maSach
        ) ha ON ha.maSach = ct.maSach
        WHERE ct.maDH IN ($inParams)
        ORDER BY ct.maDH, ct.maSach
    ";
    $stmtCT = $pdo->prepare($sqlCT);
    $stmtCT->execute($maDHList);
    $allItems = $stmtCT->fetchAll();

    foreach ($allItems as $item) {
        $chiTietDH[$item['maDH']][] = $item;
    }

    // Kiểm tra sách nào đã được đánh giá bởi user này
    $sqlDG = "SELECT maSach FROM DanhGiaSach WHERE maND = ?";
    $stmtDG = $pdo->prepare($sqlDG);
    $stmtDG->execute([$maND]);
    foreach ($stmtDG->fetchAll() as $dg) {
        $daDanhGia[$dg['maSach']] = true;
    }
}

// ── Tab "Đánh Giá": chỉ hiện đơn HoanThanh có sách chưa đánh giá ──────────
if ($tabHienTai === 'danh-gia') {
    $dsDonHang = array_filter($dsDonHang, function($dh) use ($chiTietDH, $daDanhGia) {
        if ($dh['trangThai'] !== 'HoanThanh') return false;
        $items = $chiTietDH[$dh['maDH']] ?? [];
        foreach ($items as $item) {
            if (empty($daDanhGia[$item['maSach']])) return true;
        }
        return false;
    });
}

// Thông báo sau khi đánh giá thành công
$thongBao = $_GET['tb'] ?? '';

// Badge trang thái → class + nhãn
function badgeInfo(string $tt): array {
    return match($tt) {
        'ChoDuyet'   => ['cho-duyet',  'Chờ Duyệt'],
        'DangGiao'   => ['dang-giao',  'Đang Giao'],
        'HoanThanh'  => ['hoan-thanh', 'Đã Giao'],
        'DaHuy'      => ['da-huy',     'Đã Hủy'],
        default      => ['cho-duyet',  $tt],
    };
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theo Dõi Đơn Hàng - Book Sales</title>
    <link rel="stylesheet" href="../../../GiaoDien/style.css">
    <link rel="stylesheet" href="../../../GiaoDien/donHang.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <script>const dangDangNhap = true;</script>
    <script>var cartServerData = <?= json_encode($_SESSION['cart'] ?? [], JSON_UNESCAPED_UNICODE) ?>;</script>
</head>
<body>

<?php include_once '../../../CuaHang/TrangBanHang/GiaoDien/header.php'; ?>

<div class="dh-page">
<div class="dh-container">

    <a href="../../../index.php" class="dh-back-link">
        <i class="fas fa-arrow-left"></i> Quay lại cửa hàng
    </a>

    <h1 class="dh-title"><i class="fas fa-box"></i> Đơn Hàng Của Tôi</h1>

    <!-- ═══ TABS ═══ -->
    <div class="dh-tabs" role="tablist">
        <?php
        $tabs = [
            'tat-ca'    => 'Tất Cả',
            'cho-duyet' => 'Chờ Duyệt',
            'dang-giao' => 'Đang Giao',
            'da-giao'   => 'Đã Giao',
            'da-huy'    => 'Đã Hủy',
            'danh-gia'  => 'Đánh Giá',
        ];
        foreach ($tabs as $slug => $label): ?>
        <a href="?tab=<?= $slug ?>"
           class="dh-tab <?= $tabHienTai === $slug ? 'active' : '' ?>"
           role="tab"
           aria-selected="<?= $tabHienTai === $slug ? 'true' : 'false' ?>">
            <?= $label ?>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- ═══ NỘI DUNG ═══ -->
    <div class="dh-content">

        <?php if (!empty($thongBao) && $thongBao === 'ok'): ?>
        <div style="background:#dcfce7;color:#15803d;padding:10px 18px;border-radius:8px;margin-bottom:14px;font-weight:500;">
            <i class="fas fa-check-circle"></i> Đánh giá của bạn đã được ghi nhận. Cảm ơn!
        </div>
        <?php endif; ?>

        <?php if (empty($dsDonHang)): ?>
        <div class="dh-empty">
            <i class="fas fa-box-open"></i>
            Không có đơn hàng nào.
        </div>
        <?php else: ?>

        <?php foreach ($dsDonHang as $dh):
            [$badgeClass, $badgeLabel] = badgeInfo($dh['trangThai']);
            $items = $chiTietDH[$dh['maDH']] ?? [];
        ?>
        <div class="dh-order-card">
            <!-- Header đơn -->
            <div class="dh-order-header">
                <div class="dh-order-id">
                    Mã đơn: <span><?= htmlspecialchars($dh['maDH']) ?></span>
                    &nbsp;·&nbsp;
                    <span><?= htmlspecialchars($dh['tenPT']) ?></span>
                </div>
                <span class="dh-status-badge <?= $badgeClass ?>"><?= $badgeLabel ?></span>
            </div>

            <!-- Danh sách sách -->
            <div class="dh-order-items">
            <?php foreach ($items as $item):
                $daReview = !empty($daDanhGia[$item['maSach']]);
                $hinhAnh  = htmlspecialchars($item['urlAnh'] ?? 'https://placehold.co/70x90?text=📚');
            ?>
            <div class="dh-order-item">
                <img class="dh-item-img"
                     src="<?= $hinhAnh ?>"
                     alt="<?= htmlspecialchars($item['tenSach']) ?>"
                     onerror="this.src='https://placehold.co/70x90?text=📚'">
                <div class="dh-item-info">
                    <h4><?= htmlspecialchars($item['tenSach']) ?></h4>
                    <p>x<?= (int)$item['soLuong'] ?></p>

                    <?php if ($dh['trangThai'] === 'HoanThanh'): ?>
                        <?php if ($daReview): ?>
                        <div class="dh-reviewed-tag"><i class="fas fa-check-circle"></i> Đã đánh giá</div>
                        <?php else: ?>
                        <!-- Nút đánh giá → mở modal (thuần PHP form, JS chỉ toggle display) -->
                        <button class="dh-btn-review"
                                type="button"
                                data-madh="<?= htmlspecialchars($dh['maDH']) ?>"
                                data-masach="<?= htmlspecialchars($item['maSach']) ?>"
                                data-ten="<?= htmlspecialchars($item['tenSach']) ?>"
                                onclick="moModalDanhGia(this)">
                            <i class="fas fa-star"></i> Đánh giá
                        </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="dh-item-price-col">
                    <div class="dh-item-price"><?= number_format($item['thanhTien'], 0, ',', '.') ?>đ</div>
                </div>
            </div>
            <?php endforeach; ?>
            </div>

            <!-- Footer đơn -->
            <div class="dh-order-footer">
                <div class="dh-order-date">
                    <i class="far fa-clock"></i>
                    <?= date('d/m/Y H:i', strtotime($dh['ngayDat'])) ?>
                    &nbsp;·&nbsp;
                    <i class="fas fa-map-marker-alt"></i>
                    <?= htmlspecialchars(mb_strimwidth($dh['diaChiChiTiet'], 0, 40, '…')) ?>
                </div>
                <div class="dh-order-total">
                    Tổng cộng: <strong><?= number_format($dh['tongTien'], 0, ',', '.') ?>đ</strong>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <?php endif; // empty dsDonHang ?>

    </div><!-- /dh-content -->
</div><!-- /dh-container -->
</div><!-- /dh-page -->

<!-- ═══ MODAL ĐÁNH GIÁ ═══ -->
<!-- PHP render sẵn modal, JS chỉ toggle class và điền data-* vào form -->
<div id="review-overlay" class="dh-review-overlay" role="dialog" aria-modal="true" aria-labelledby="review-title">
    <div class="dh-review-modal">
        <button class="dh-modal-close" type="button" onclick="dongModalDanhGia()" aria-label="Đóng">&times;</button>
        <h3 id="review-title">Đánh giá sản phẩm</h3>
        <p class="dh-book-name" id="review-ten-sach"></p>

        <!-- Form POST thuần PHP → xuly_danhGia.php -->
        <form action="xuly_danhGia.php" method="POST" id="form-danh-gia">
            <input type="hidden" name="maDH"   id="review-maDH"   value="">
            <input type="hidden" name="maSach" id="review-maSach" value="">
            <input type="hidden" name="diem"   id="review-diem"   value="0">

            <!-- 5 sao — JS chỉ thêm/xóa class 'selected', không tạo element mới -->
            <div class="dh-star-row" id="review-stars" role="group" aria-label="Chọn số sao">
                <span class="dh-star" data-vi-tri="1" onclick="chonSao(1)" title="1 sao">&#9733;</span>
                <span class="dh-star" data-vi-tri="2" onclick="chonSao(2)" title="2 sao">&#9733;</span>
                <span class="dh-star" data-vi-tri="3" onclick="chonSao(3)" title="3 sao">&#9733;</span>
                <span class="dh-star" data-vi-tri="4" onclick="chonSao(4)" title="4 sao">&#9733;</span>
                <span class="dh-star" data-vi-tri="5" onclick="chonSao(5)" title="5 sao">&#9733;</span>
            </div>

            <textarea class="dh-review-textarea"
                      name="nhanXet"
                      id="review-nhanXet"
                      placeholder="Chia sẻ cảm nhận của bạn về cuốn sách này..."
                      rows="4"></textarea>

            <div class="dh-review-actions">
                <button type="button" class="dh-btn-cancel-review" onclick="dongModalDanhGia()">Hủy</button>
                <button type="submit" class="dh-btn-submit-review">
                    <i class="fas fa-paper-plane"></i> Gửi đánh giá
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Toast thông báo -->
<div id="dh-toast" class="dh-toast"></div>

<?php include_once '../../../CuaHang/TrangBanHang/GioHang/formGioHang.php'; ?>

<!-- JS thuần: chỉ toggle class + điền textContent/value — không innerHTML, không AJAX -->
<script src="../../../PhuongThuc/cart.js"></script>
<script src="../../../PhuongThuc/components/xacNhanDangXuat.js"></script>
<script src="../../../PhuongThuc/components/xacThuc.js"></script>

<script>
// ── Mở modal đánh giá ────────────────────────────────────────────────────────
function moModalDanhGia(nutBam) {
    var overlay  = document.getElementById('review-overlay');
    var tenSach  = document.getElementById('review-ten-sach');
    var inpMaDH  = document.getElementById('review-maDH');
    var inpMaSach= document.getElementById('review-maSach');
    var inpDiem  = document.getElementById('review-diem');
    var textarea = document.getElementById('review-nhanXet');

    // Điền dữ liệu từ data-* (PHP đã render) — không innerHTML
    tenSach.textContent   = nutBam.dataset.ten    || '';
    inpMaDH.value         = nutBam.dataset.madh   || '';
    inpMaSach.value       = nutBam.dataset.masach || '';
    inpDiem.value         = '0';
    textarea.value        = '';

    // Reset sao
    chonSao(0);

    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// ── Đóng modal ───────────────────────────────────────────────────────────────
function dongModalDanhGia() {
    document.getElementById('review-overlay').classList.remove('active');
    document.body.style.overflow = '';
}

// Đóng khi click ngoài
document.getElementById('review-overlay').addEventListener('click', function(e) {
    if (e.target === this) dongModalDanhGia();
});

// ── Chọn sao ─────────────────────────────────────────────────────────────────
function chonSao(n) {
    var stars = document.querySelectorAll('#review-stars .dh-star');
    stars.forEach(function(star, idx) {
        if (idx < n) {
            star.classList.add('selected');
        } else {
            star.classList.remove('selected');
        }
    });
    var inp = document.getElementById('review-diem');
    if (inp) inp.value = n;
}

// ── Validate trước khi submit ─────────────────────────────────────────────
document.getElementById('form-danh-gia').addEventListener('submit', function(e) {
    var diem = parseInt(document.getElementById('review-diem').value) || 0;
    if (diem < 1) {
        e.preventDefault();
        alert('Vui lòng chọn ít nhất 1 sao!');
    }
});

// ── Toggle user menu ────────────────────────────────────────────────────────
function toggleUserMenu(e) {
    e.stopPropagation();
    var menu = document.getElementById('userDropdown');
    if (menu) menu.classList.toggle('open');
}
document.addEventListener('click', function() {
    var menu = document.getElementById('userDropdown');
    if (menu) menu.classList.remove('open');
});
</script>
</body>
</html>
