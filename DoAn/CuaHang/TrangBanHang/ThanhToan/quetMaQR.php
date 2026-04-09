<?php
session_start();
$maDH = $_GET['maDH'] ?? 'DH_NULL';
$tien = $_GET['tien'] ?? 0;

$qrBank = 'TPBANK';
$qrSTK = '0814585526';
$qrName = 'LE MINH DUC';
// VietQR API Format
$qrUrl = "https://img.vietqr.io/image/{$qrBank}-{$qrSTK}-print.png?amount={$tien}&addInfo={$maDH}&accountName=" . urlencode($qrName);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh Toán QR Code</title>
    <style>
        body { background: #f3f4f6; font-family: sans-serif; display: flex; justify-content: center; padding: 2rem; }
        .qr-card { background: #fff; padding: 2.5rem; border-radius: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center; max-width: 450px; width: 100%; }
        .qr-img { border-radius: 0.5rem; margin: 1.5rem 0; width: 100%; max-width: 300px; height: auto; border: 4px solid #2563eb; padding: 0.5rem; }
        h2 { color: #1f2937; margin-top: 0; }
        .amount { color: #ea580c; font-size: 1.5rem; font-weight: bold; margin: 0.5rem 0; }
        .timer { font-size: 1.25rem; font-weight: bold; color: #ef4444; margin-bottom: 1.5rem; }
        .btn-confirm { background: #16a34a; color: #fff; border: none; width: 100%; padding: 1rem; border-radius: 0.5rem; font-size: 1.1rem; cursor: pointer; text-decoration: none; display: block; font-weight: bold; margin-bottom: 1rem; }
        .btn-cancel { background: #f3f4f6; color: #4b5563; border: none; width: 100%; padding: 1rem; border-radius: 0.5rem; font-size: 1.1rem; cursor: pointer; text-decoration: none; display: block; font-weight: bold; }
    </style>
</head>
<body>
    <div class="qr-card">
        <h2>Quét Mã Thanh Toán</h2>
        <p>Sử dụng App Ngân hàng của bạn để quét mã QR dưới đây.</p>
        
        <img src="<?= $qrUrl ?>" alt="QR Code" class="qr-img">
        
        <p>Số tiền cần chuyển:</p>
        <div class="amount"><?= number_format($tien, 0, ',', '.') ?> VNĐ</div>
        <p>Nội dung: <strong><?= htmlspecialchars($maDH) ?></strong></p>
        
        <div class="timer" id="countdown">07:00</div>
        
        <a href="thanhCong.php?maDH=<?= $maDH ?>" class="btn-confirm">Tôi đã chuyển khoản xong</a>
        <!-- Nút hủy về lý thuyết sẽ gọi DB trả kho, ta giả lập hủy -->
        <a href="huyThanhToan.php?maDH=<?= $maDH ?>" class="btn-cancel">Hủy thanh toán</a>
    </div>

    <!-- Script dọn dẹp LocalStorage -->
    <?php if (isset($_SESSION['xoa_cart_local'])): ?>
    <script>
        localStorage.removeItem('book_cart');
    </script>
    <?php unset($_SESSION['xoa_cart_local']); endif; ?>

    <script>
        let timeLeft = 420; // 7 minutes * 60s
        const timerEl = document.getElementById('countdown');
        
        setInterval(() => {
            if (timeLeft <= 0) {
                window.location.href = 'huyThanhToan.php?maDH=<?= $maDH ?>&timeout=1';
                return;
            }
            timeLeft--;
            const m = Math.floor(timeLeft / 60).toString().padStart(2, '0');
            const s = (timeLeft % 60).toString().padStart(2, '0');
            timerEl.textContent = `${m}:${s}`;
        }, 1000);
    </script>
</body>
</html>
