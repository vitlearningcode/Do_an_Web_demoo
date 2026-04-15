<?php
/**
 * ============================================================
 * LUỒNG VÀO: Được require_once từ TẤT CẢ các file PHP cần DB
 *   index.php → require db.php
 *   xuly_dangnhap.php → require db.php
 *   luuGioHang.php → require db.php  ... v.v.
 *
 * OUTPUT: Biến $pdo (PDO instance) sẵn sàng dùng ở file gọi.
 *
 * MÔI TRƯỜNG:
 *   - XAMPP cục bộ: đọc thông tin từ env_local.php (không commit lên git)
 *   - Production/CI: đọc từ environment variables (GitHub Secrets / server env)
 *
 * BẢO MẬT:
 *   - Kết nối qua SSL (MYSQL_ATTR_SSL_CA) tới Aiven Cloud DB
 *   - Dùng PDO Prepared Statements → chống SQL Injection
 *   - ERRMODE_EXCEPTION: mọi lỗi DB đều throw Exception (bắt được bằng try/catch)
 * ============================================================
 */

// -------------------------------------------------------------
// BƯỚC 1: Nạp biến môi trường cục bộ (chỉ tồn tại trên XAMPP)
// env_local.php chứa: putenv('AIVEN_DB_HOST=...') v.v.
// File này bị .gitignore — không bao giờ lên GitHub
// -------------------------------------------------------------
$env_file = __DIR__ . '/../env_local.php';
if (file_exists($env_file)) {
    require_once $env_file;
}

// -------------------------------------------------------------
// BƯỚC 2: Đọc thông tin kết nối từ environment
// Trên production: được set bởi hosting/CI (GitHub Actions Secrets)
// Trên XAMPP: được set bởi env_local.php ở bước 1
// -------------------------------------------------------------
$host  = getenv('AIVEN_DB_HOST');      // Hostname Aiven Cloud MySQL
$port  = getenv('AIVEN_DB_PORT');      // Thường là 14699
$db    = getenv('AIVEN_DB_DATABASE');  // Tên database
$user  = getenv('AIVEN_DB_USER');      // Username
$pass  = getenv('AIVEN_DB_PASSWORD');  // Password
$sslCA = getenv('AIVEN_SSL_CA');       // Đường dẫn tới file CA certificate

// -------------------------------------------------------------
// BƯỚC 3: Xây dựng DSN và thiết lập options kết nối
// charset=utf8mb4 đảm bảo lưu được tiếng Việt + emoji
// -------------------------------------------------------------
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw PDOException khi lỗi
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Kết quả trả về là mảng kết hợp
    PDO::MYSQL_ATTR_SSL_CA       => $sslCA,                 // SSL: dùng CA certificate
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,        // Tắt verify hostname (Aiven yêu cầu)
];

// -------------------------------------------------------------
// BƯỚC 4: Tạo kết nối PDO → biến $pdo
// $pdo là biến TOÀN CỤC — file nào require db.php đều có $pdo
// Nếu kết nối thất bại → die() ngay, không chạy tiếp
// -------------------------------------------------------------
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // Đặt timezone server về +07:00 (GMT+7 Việt Nam)
    // Quan trọng cho các so sánh thời gian flash sale NOW() BETWEEN ngayBD AND ngayKT
    $pdo->exec("SET time_zone = '+07:00';");
} catch (PDOException $e) {
    die("Kết nối DB thất bại: " . $e->getMessage());
}

// -------------------------------------------------------------
// BƯỚC 5: Nạp helper ảnh toàn cục
// Hàm anhSach() và anhBanner() sẽ có sẵn ở mọi trang require db.php
// Tránh phải require riêng ở từng file
// -------------------------------------------------------------
require_once __DIR__ . '/../../PhuongThuc/layDuongDanAnh.php';
?>