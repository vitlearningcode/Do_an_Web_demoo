<?php
/**
 * ThanhToan/thanhToan.php — Trang thanh toán
 * - Tự điền thông tin KH từ session/DB
 * - Cho chọn địa chỉ đã lưu hoặc nhập địa chỉ mới (Phương án B)
 * Thuần PHP — không AJAX, không JSON.
 */
session_start();
require_once '../../../KetNoi/config/db.php';

$isLoggedIn = isset($_SESSION['nguoi_dung_id']);

// ── Giỏ hàng ────────────────────────────────────────────────────────────────
$gioHang = [];
if (!empty($_SESSION['cart'])) {
    $gioHang = $_SESSION['cart'];
} elseif (!empty($_SESSION['cart_temp'])) {
    $gioHang = $_SESSION['cart_temp'];
}

if (empty($gioHang)) {
    echo "<script>alert('Giỏ hàng trống hoặc phiên giao dịch đã hết hạn!'); window.location.href='../../index.php';</script>";
    exit;
}

$_SESSION['cart_temp'] = $gioHang;
$tongTien = 0;
foreach ($gioHang as $sanPham) {
    if (isset($sanPham['soLuong'], $sanPham['giaBan'])) {
        $tongTien += $sanPham['giaBan'] * $sanPham['soLuong'];
    }
}

// ── Thông tin khách hàng (nếu đã đăng nhập) ─────────────────────────────────
$thongTinKH      = null;
$danhSachDiaChi  = [];
$diaChiMacDinh   = '';

if ($isLoggedIn) {
    $maND = (int)$_SESSION['nguoi_dung_id'];

    $stmtKH = $pdo->prepare("SELECT tenND, sdt, email FROM NguoiDung WHERE maND = ? LIMIT 1");
    $stmtKH->execute([$maND]);
    $thongTinKH = $stmtKH->fetch();

    $stmtDC = $pdo->prepare("SELECT maDC, diaChiChiTiet, laMacDinh FROM DiaChiGiaoHang WHERE maND = ? ORDER BY laMacDinh DESC, maDC ASC");
    $stmtDC->execute([$maND]);
    $danhSachDiaChi = $stmtDC->fetchAll();

    // Địa chỉ mặc định (để pre-fill vào ô nhập mới nếu chưa có)
    foreach ($danhSachDiaChi as $dc) {
        if ($dc['laMacDinh']) {
            $diaChiMacDinh = $dc['diaChiChiTiet'];
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiến Hành Thanh Toán - Book Sales</title>
    <link rel="stylesheet" href="../../../GiaoDien/style.css">
    <link rel="stylesheet" href="../../GiaoDien/style.css">
    <link rel="stylesheet" href="thanhToan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* ── Chọn loại địa chỉ ── */
        .chon-loai-dc { display: flex; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
        .chon-loai-dc label {
            display: flex; align-items: center; gap: 8px;
            padding: 9px 16px; border-radius: 8px; cursor: pointer;
            border: 1.5px solid #e5e7eb; font-size: .875rem; font-weight: 500;
            transition: border-color .2s, background .2s;
        }
        .chon-loai-dc input[type=radio] { accent-color: #2563eb; }
        .chon-loai-dc label:has(input:checked) { border-color: #2563eb; background: #eff6ff; color: #1e40af; }

        /* ── Dropdown địa chỉ đã lưu ── */
        .khung-dc-da-luu { margin-bottom: 10px; }
        .khung-dc-da-luu select {
            width: 100%; padding: 10px 14px;
            border: 1.5px solid #e5e7eb; border-radius: 8px;
            font-size: .9rem; font-family: inherit; outline: none;
            transition: border-color .2s; background: #fff;
        }
        .khung-dc-da-luu select:focus { border-color: #2563eb; }

        /* ── Link thêm địa chỉ ── */
        .link-them-dc { font-size: .8rem; color: #2563eb; text-decoration: underline; }

        /* ── Ẩn/hiện khung nhập địa chỉ mới ── */
        .khung-dc-moi { display: none; }
        .khung-dc-moi.hien { display: block; }
    </style>
</head>
<body class="bg-gray-50 checkout-body">

<div class="checkout-container">
    <header class="checkout-header">
        <a href="../../../index.php" class="back-link"><i class="fas fa-arrow-left"></i> Quay lại cửa hàng</a>
        <h2>Bảo Mật Thanh Toán Cửa Hàng Sách</h2>
    </header>

    <div class="checkout-content">
        <!-- Cột Form Nhập Liệu -->
        <div class="checkout-info">
            <form action="xuLyThanhToan.php" method="POST" id="form-thanh-toan">

                <!-- Thông tin khách hàng -->
                <div class="section-box">
                    <h3><i class="fas fa-map-marker-alt"></i> Thông tin nhận hàng</h3>

                    <?php if ($isLoggedIn && $thongTinKH): ?>
                    <!-- Khách đã đăng nhập: tự điền thông tin -->
                    <div class="form-group">
                        <label>Họ và tên</label>
                        <input type="text" name="hoten" required
                               value="<?= htmlspecialchars($thongTinKH['tenND'] ?? '') ?>"
                               placeholder="Nhập đầy đủ họ tên...">
                    </div>
                    <div class="form-group-row">
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="tel" name="sdt" pattern="[0-9]{10,11}" required
                                   value="<?= htmlspecialchars($thongTinKH['sdt'] ?? '') ?>"
                                   placeholder="Số điện thoại...">
                        </div>
                        <div class="form-group">
                            <label>Email (Để nhận hóa đơn)</label>
                            <input type="email" name="email" required
                                   value="<?= htmlspecialchars($thongTinKH['email'] ?? '') ?>"
                                   placeholder="Email liên lạc...">
                        </div>
                    </div>

                    <!-- Chọn địa chỉ -->
                    <div class="form-group">
                        <label>Địa chỉ giao hàng</label>

                        <?php if (!empty($danhSachDiaChi)): ?>
                        <!-- Chọn: địa chỉ đã lưu vs nhập mới -->
                        <div class="chon-loai-dc">
                            <label>
                                <input type="radio" name="loai_dia_chi" value="da_luu"
                                       id="radio-da-luu" checked
                                       onchange="choiLoaiDiaChi('da_luu')">
                                <i class="fas fa-bookmark"></i> Địa chỉ đã lưu
                            </label>
                            <label>
                                <input type="radio" name="loai_dia_chi" value="moi"
                                       id="radio-moi"
                                       onchange="choiLoaiDiaChi('moi')">
                                <i class="fas fa-plus"></i> Nhập địa chỉ mới
                            </label>
                        </div>

                        <!-- Dropdown địa chỉ đã lưu -->
                        <div class="khung-dc-da-luu" id="khung-da-luu">
                            <select name="ma_dia_chi" id="select-dia-chi">
                            <?php foreach ($danhSachDiaChi as $dc): ?>
                                <option value="<?= (int)$dc['maDC'] ?>"
                                        <?= $dc['laMacDinh'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dc['diaChiChiTiet']) ?>
                                    <?= $dc['laMacDinh'] ? ' ★ Mặc định' : '' ?>
                                </option>
                            <?php endforeach; ?>
                            </select>
                            <a href="../taiKhoan/capNhat.php" class="link-them-dc" style="margin-top:6px;display:inline-block;">
                                <i class="fas fa-plus-circle"></i> Thêm địa chỉ mới trong hồ sơ
                            </a>
                        </div>

                        <!-- Ô nhập địa chỉ mới (ẩn mặc định) -->
                        <div class="khung-dc-moi" id="khung-dc-moi">
                            <input type="text" name="dia_chi_moi"
                                   id="nhap-dia-chi-moi"
                                   placeholder="Số nhà, Tên đường, Phường/Xã, Quận/Huyện, Tỉnh/TP">
                        </div>

                        <?php else: ?>
                        <!-- Chưa có địa chỉ nào → chỉ hiện ô nhập -->
                        <input type="hidden" name="loai_dia_chi" value="moi">
                        <input type="text" name="dia_chi_moi"
                               value="<?= htmlspecialchars($diaChiMacDinh) ?>"
                               required
                               placeholder="Số nhà, Tên đường, Phường/Xã, Quận/Huyện, Tỉnh/TP">
                        <a href="../taiKhoan/capNhat.php" class="link-them-dc" style="margin-top:6px;display:inline-block;">
                            <i class="fas fa-save"></i> Lưu địa chỉ vào hồ sơ để dùng lần sau
                        </a>
                        <?php endif; ?>
                    </div>

                    <?php else: ?>
                    <!-- Khách vãng lai: tất cả để trống -->
                    <div class="form-group">
                        <label>Họ và tên</label>
                        <input type="text" name="hoten" required placeholder="Nhập đầy đủ họ tên...">
                    </div>
                    <div class="form-group-row">
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="tel" name="sdt" pattern="[0-9]{10,11}" required placeholder="Số điện thoại...">
                        </div>
                        <div class="form-group">
                            <label>Email (Để nhận hóa đơn)</label>
                            <input type="email" name="email" required placeholder="Email liên lạc...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Địa chỉ giao hàng</label>
                        <input type="hidden" name="loai_dia_chi" value="moi">
                        <input type="text" name="dia_chi_moi" required
                               placeholder="Số nhà, Tên đường, Phường/Xã, Quận/Huyện, Tỉnh/TP">
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Phương thức thanh toán -->
                <div class="section-box">
                    <h3><i class="fas fa-wallet"></i> Phương thức thanh toán</h3>

                    <label class="payment-option">
                        <input type="radio" name="phuong_thuc" value="1" checked>
                        <div class="option-content">
                            <div class="icon text-green"><i class="fas fa-money-bill-wave"></i></div>
                            <div class="details">
                                <strong>Thanh toán tiền mặt khi nhận hàng (COD)</strong>
                                <p>Trả tiền mặt trực tiếp cho người giao hàng.</p>
                            </div>
                        </div>
                    </label>

                    <label class="payment-option">
                        <input type="radio" name="phuong_thuc" value="2">
                        <div class="option-content">
                            <div class="icon text-qr"><i class="fas fa-qrcode"></i></div>
                            <div class="details">
                                <strong>Chuyển khoản qua mã VietQR (Tự Động)</strong>
                                <p>TPBANK - LÊ MINH ĐỨC (Giữ kho 7 phút).</p>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Nút xác nhận -->
                <div class="action-footer">
                    <p class="terms">Bằng việc đặt hàng, bạn đồng ý với Điều khoản Sử dụng và Chính sách của chúng tôi.</p>
                    <button type="submit" class="btn-submit-order">
                        Hoàn Tất Đặt Mua <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Cột Tổng Kết Giỏ Hàng -->
        <div class="checkout-summary">
            <div class="section-box summary-box">
                <div class="summary-header">
                    <h3>Đơn đặt hàng (<?= count($gioHang) ?>)</h3>
                    <a href="../../../index.php">Sửa giỏ hàng</a>
                </div>

                <div class="cart-scroll-list">
                    <?php foreach ($gioHang as $sp): ?>
                    <div class="summary-item">
                        <div class="s-img-wrapper">
                            <img src="<?= htmlspecialchars($sp['hinhAnh']) ?>" alt="Cover">
                            <span class="s-qty-badge"><?= $sp['soLuong'] ?></span>
                        </div>
                        <div class="s-info">
                            <h4 title="<?= htmlspecialchars($sp['tenSach']) ?>"><?= htmlspecialchars($sp['tenSach']) ?></h4>
                        </div>
                        <div class="s-price"><?= number_format($sp['giaBan'] * $sp['soLuong'], 0, ',', '.') ?>đ</div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="summary-calc">
                    <div class="summary-row">
                        <span>Tạm tính</span>
                        <span><?= number_format($tongTien, 0, ',', '.') ?>đ</span>
                    </div>
                    <div class="summary-row">
                        <span>Phí vận chuyển</span>
                        <span class="text-green font-medium">Miễn phí</span>
                    </div>
                </div>

                <div class="summary-total">
                    <div class="total-label">
                        <span>Tổng cộng</span>
                        <small>Đã bao gồm VAT</small>
                    </div>
                    <span class="total-amount"><?= number_format($tongTien, 0, ',', '.') ?> đ</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
/* Chuyển đổi hiển thị giữa địa chỉ đã lưu và nhập mới */
function choiLoaiDiaChi(loai) {
    var khungDaLuu = document.getElementById('khung-da-luu');
    var khungMoi   = document.getElementById('khung-dc-moi');
    var selectDC   = document.getElementById('select-dia-chi');
    var nhapMoi    = document.getElementById('nhap-dia-chi-moi');

    if (loai === 'da_luu') {
        if (khungDaLuu) khungDaLuu.style.display = '';
        if (khungMoi)   khungMoi.classList.remove('hien');
        if (selectDC)   selectDC.required = true;
        if (nhapMoi)    nhapMoi.required  = false;
    } else {
        if (khungDaLuu) khungDaLuu.style.display = 'none';
        if (khungMoi)   khungMoi.classList.add('hien');
        if (selectDC)   selectDC.required = false;
        if (nhapMoi)    nhapMoi.required  = true;
    }
}
</script>
</body>
</html>
