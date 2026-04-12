<?php
// config/db.php

// 1. Nếu đang chạy trên XAMPP (có file env_local.php), thì nạp thông tin vào
$env_file = __DIR__ . '/../env_local.php'; // Đảm bảo đường dẫn này trỏ đúng ra ngoài thư mục gốc
if (file_exists($env_file)) {
    require_once $env_file;
}

// 2. Lấy thông tin (Từ XAMPP hoặc từ GitHub Secrets)
$host = getenv('AIVEN_DB_HOST');
$port = getenv('AIVEN_DB_PORT');
$db   = getenv('AIVEN_DB_DATABASE');
$user = getenv('AIVEN_DB_USER');
$pass = getenv('AIVEN_DB_PASSWORD');
$sslCA = getenv('AIVEN_SSL_CA');

// 3. Tiến hành kết nối
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_SSL_CA       => $sslCA,
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false 
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Kết nối DB thất bại: " . $e->getMessage());
}

// 4. [Global Helper] Nạp sẵn hàm xử lý ảnh để các trang có thể dùng (anhSach, anhBanner)
require_once __DIR__ . '/../../PhuongThuc/layDuongDanAnh.php';
?>