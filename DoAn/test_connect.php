<?php
require_once __DIR__ . '/config/db.php';

try {
    $stmt = $pdo->query("SELECT 'Kết nối thành công tới Aiven MySQL!' AS msg");
    $row = $stmt->fetch();
    echo "<h1>" . $row['msg'] . "</h1>";
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
    exit(1); // Trả về lỗi để GitHub Actions biết là test thất bại
}
?>