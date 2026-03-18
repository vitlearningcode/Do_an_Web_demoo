
<?php
// env_local.php
// File này CHỈ dùng trên máy XAMPP của bạn và KHÔNG BAO GIỜ push lên GitHub

putenv("AIVEN_DB_HOST=booksm-2026-leeduck0712.f.aivencloud.com");
putenv("AIVEN_DB_PORT=22280");
putenv("AIVEN_DB_DATABASE=defaultdb");
putenv("AIVEN_DB_USER=avnadmin");
putenv("AIVEN_DB_PASSWORD=mật-khẩu-thật-của-bạn");

// Đường dẫn tuyệt đối tới file ca.pem trong máy bạn
putenv("AIVEN_SSL_CA=" . __DIR__ . "/certs/ca.pem");