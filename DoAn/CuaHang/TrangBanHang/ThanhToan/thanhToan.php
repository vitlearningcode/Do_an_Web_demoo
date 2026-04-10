<?php
session_start();
// Khởi tạo biến để tránh lỗi include nếu có
$isLoggedIn = isset($_SESSION['nguoi_dung_id']);

$cart = [];
// Đọc giỏ hàng từ Session (đã được luuGioHang.php đồng bộ — không dùng json_decode từ POST)
if (!empty($_SESSION['cart'])) {
    $cart = $_SESSION['cart'];
} elseif (!empty($_SESSION['cart_temp'])) {
    // Fallback: tránh mất giỏ hàng khi F5
    $cart = $_SESSION['cart_temp'];
}

if (empty($cart)) {
    echo "<script>alert('Giỏ hàng trống hoặc phiên giao dịch đã hết hạn!'); window.location.href='../../index.php';</script>";
    exit;
}

$_SESSION['cart_temp'] = $cart;
$tongTien = 0;
foreach ($cart as $item) {
    if (isset($item['soLuong']) && isset($item['giaBan'])) {
        $tongTien += ($item['giaBan'] * $item['soLuong']);
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiến Hành Thanh Toán - Book Sales</title>
    <!-- Trỏ CSS về thư mục gốc và CSS riêng cho checkout -->
    <link rel="stylesheet" href="../../../GiaoDien/style.css">
    <link rel="stylesheet" href="../../GiaoDien/style.css">
    <link rel="stylesheet" href="thanhToan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
                    <div class="form-group">
                        <label>Họ và tên</label>
                        <input type="text" name="hoten" required placeholder="Nhập đầy đủ họ tên...">
                    </div>
                    <div class="form-group-row">
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <!-- Pattern đơn giản cho SĐT VN -->
                            <input type="tel" name="sdt" pattern="[0-9]{10,11}" required placeholder="Số điện thoại...">
                        </div>
                        <div class="form-group">
                            <label>Email (Để nhận hóa đơn)</label>
                            <input type="email" name="email" required placeholder="Email liên lạc...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Địa chỉ giao hàng</label>
                        <input type="text" name="diachi" required placeholder="Số nhà, Tên đường, Phường/Xã, Quận/Huyện, Tỉnh/TP">
                    </div>
                </div>

                <!-- Lựa chọn tài chính -->
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

                <!-- Action Button -->
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
                    <h3>Đơn đặt hàng (<?= count($cart) ?>)</h3>
                    <a href="../../../index.php">Sửa giỏ hàng</a>
                </div>
                
                <div class="cart-scroll-list">
                    <?php foreach ($cart as $s) { ?>
                    <div class="summary-item">
                        <div class="s-img-wrapper">
                            <img src="<?= htmlspecialchars($s['hinhAnh']) ?>" alt="Cover">
                            <span class="s-qty-badge"><?= $s['soLuong'] ?></span>
                        </div>
                        <div class="s-info">
                            <h4 title="<?= htmlspecialchars($s['tenSach']) ?>"><?= htmlspecialchars($s['tenSach']) ?></h4>
                        </div>
                        <div class="s-price"><?= number_format($s['giaBan'] * $s['soLuong'], 0, ',', '.') ?>đ</div>
                    </div>
                    <?php } ?>
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
    // Phục vụ xóa localstorage cart nếu nộp form (vì ta sẽ dùng PHP xử lý kho)
    // Tốt hơn là xóa Session hoặc LocalStorage sau khi tạo Đơn thành công ở trang xử lý, vì lỡ họ nộp form lỗi (sai định dạng email) thì PHP back lại vẫn còn giỏ.
</script>
</body>
</html>
