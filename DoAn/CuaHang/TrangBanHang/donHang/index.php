<?php
/**
 * donHang/index.php — Trang theo dõi đơn hàng (Entry Point)
 * Tabs: Tất cả | Chờ Duyệt | Đang Giao | Đã Giao | Đã Hủy | Đánh Giá
 * Thuần PHP: không AJAX, không chèn HTML từ JS.
 * Mỗi chức năng được tách thành file riêng và gọi qua require_once.
 */
session_start();
require_once '../../../KetNoi/config/db.php';
require_once 'hamHoTroDonHang.php';

$isLoggedIn = isset($_SESSION['nguoi_dung_id']);

// Bắt buộc phải đăng nhập
if (!$isLoggedIn) {
    header('Location: ../../../index.php');
    exit;
}

$maND = (int)$_SESSION['nguoi_dung_id'];

// Bản đồ tab → trạng thái DB
$trangThaiMap = [
    'tat-ca'     => null,
    'cho-duyet'  => 'ChoDuyet',
    'dang-giao'  => 'DangGiao',
    'da-giao'    => 'HoanThanh',
    'da-huy'     => 'DaHuy',
    'danh-gia'   => 'HoanThanh', // Chỉ đơn HoanThanh mới có thể đánh giá
];

$tabHienTai = $_GET['tab'] ?? 'tat-ca';
if (!array_key_exists($tabHienTai, $trangThaiMap)) {
    $tabHienTai = 'tat-ca';
}

// Tải dữ liệu đơn hàng từ DB
require_once 'layDuLieuDonHang.php';

// Lọc riêng cho tab "Đánh giá"
require_once 'locTabDanhGia.php';

// Thông báo sau khi đánh giá thành công
$thongBaoSauDanhGia = $_GET['tb'] ?? '';
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

    <?php require_once 'khuVucTabDonHang.php'; ?>
    <?php require_once 'danhSachTheDonHang.php'; ?>

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

        <?php if (!empty($thongBao) && $thongBao === 'ok_huy'): ?>
        <div style="background:#fee2e2;color:#dc2626;padding:10px 18px;border-radius:8px;margin-bottom:14px;font-weight:500;">
            <i class="fas fa-times-circle"></i> Đơn hàng của bạn đã được hủy thành công. Hàng sẽ được hoàn lại kho.
        </div>
        <?php endif; ?>

        <?php if (empty($dsDonHang)): ?>
        <div class="dh-empty">
            <i class="fas fa-box-open"></i>
            Không có đơn hàng nào.
        </div>
        <?php else: ?>

        <?php foreach ($dsDonHang as $dh):
            [$badgeClass, $badgeLabel] = thongTinBadge($dh['trangThai']);
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
                <div class="dh-order-actions">
                    <div class="dh-order-total">
                        Tổng cộng: <strong><?= number_format($dh['tongTien'], 0, ',', '.') ?>đ</strong>
                    </div>
                    <?php if ($dh['trangThai'] === 'ChoDuyet'): ?>
                    <!-- Nút Hủy đơn: chỉ hiện khi đơn đang Chờ Duyệt -->
                    <form method="POST" action="xuly_huyDon.php"
                          onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng <?= htmlspecialchars($dh['maDH']) ?> không?')">
                        <input type="hidden" name="maDH" value="<?= htmlspecialchars($dh['maDH']) ?>">
                        <button type="submit" class="dh-btn-cancel-order">
                            <i class="fas fa-times"></i> Hủy đơn
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <?php endif; // empty dsDonHang ?>

    </div><!-- /dh-content -->
</div><!-- /dh-container -->
</div><!-- /dh-page -->

<?php require_once 'modalDanhGiaSanPham.php'; ?>
<?php include_once '../../../CuaHang/TrangBanHang/GioHang/formGioHang.php'; ?>

<script src="../../../PhuongThuc/cart.js"></script>
<script src="../../../PhuongThuc/components/xacNhanDangXuat.js"></script>
<script src="../../../PhuongThuc/components/xacThuc.js"></script>

<?php require_once 'scriptDonHang.php'; ?>

</body>
</html>
