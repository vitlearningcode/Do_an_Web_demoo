<?php
session_start();
$maDH = $_GET['maDH'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt hàng thành công</title>
    <link rel="stylesheet" href="../../GiaoDien/style.css">
    <style>
        .success-box { max-width: 500px; margin: 100px auto; text-align: center; background: #fff; padding: 3rem; border-radius: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .success-box i { font-size: 5rem; color: #10b981; margin-bottom: 1rem; }
        .success-box h1 { color: #1f2937; margin-bottom: 0.5rem; }
        .success-box p { color: #6b7280; margin-bottom: 2rem; }
        .btn-home { display: inline-block; padding: 0.75rem 2rem; background: #2563eb; color: #fff; text-decoration: none; border-radius: 0.5rem; font-weight: bold; }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body style="background: #f3f4f6;">
    <div class="success-box">
        <i class="fas fa-check-circle"></i>
        <h1>Đặt hàng thành công!</h1>
        <p>Mã đơn hàng của bạn: <strong><?= htmlspecialchars($maDH) ?></strong></p>
        <p>Chúng tôi đã nhận được thông tin và sẽ gửi email xác nhận cho bạn trong giây lát.</p>
        <a href="../../../index.php" class="btn-home">Trở về cửa hàng</a>
    </div>
    
    <!-- Script dọn dẹp LocalStorage -->
    <?php if (isset($_SESSION['xoa_cart_local'])): ?>
    <script>
        localStorage.removeItem('book_cart');
    </script>
    <?php unset($_SESSION['xoa_cart_local']); endif; ?>
</body>
</html>
