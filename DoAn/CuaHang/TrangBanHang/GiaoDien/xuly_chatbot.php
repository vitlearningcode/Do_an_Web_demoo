<?php
// 1. Tắt hiển thị lỗi mặc định của PHP để không hỏng JSON
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');

// 2. Nhúng file kết nối
// Nhúng cấu hình môi trường bảo mật
include_once "../../../KetNoi/env_local.php";
include_once "../../../KetNoi/config/db.php";

if (!isset($pdo)) {
    echo json_encode(["error" => "Không tìm thấy kết nối PDO. Kiểm tra lại db.php"]);
    exit;
}

try {
   // =========================================================================
    // 3. LẤY DỮ LIỆU SÁCH (Giữ nguyên như cũ)
    // =========================================================================
    $sql = "SELECT maSach, tenSach, moTa, giaBan FROM Sach WHERE trangThai = 'DangKD' LIMIT 15";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $danhSachSach = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $context_sach = "Dưới đây là danh sách sách hiện có tại cửa hàng:\n";
    foreach ($danhSachSach as $sach) {
        $tenSach = htmlspecialchars($sach['tenSach'], ENT_QUOTES, 'UTF-8');
        $moTa = $sach['moTa'] ? htmlspecialchars($sach['moTa'], ENT_QUOTES, 'UTF-8') : 'Đang cập nhật';
        $gia = number_format((float)$sach['giaBan']);
        $context_sach .= "- Tên sách: {$tenSach} | Giá gốc: {$gia}đ | Mô tả: {$moTa}\n";
    }

    // =========================================================================
    // 3.5 LẤY DỮ LIỆU KHUYẾN MÃI (ĐÃ KHỚP TÊN CỘT TRONG CSDL)
    // Lấy các chương trình đang diễn ra (ngayBatDau <= hiện tại <= ngayKetThuc)
    // =========================================================================
    $sqlKM = "
        SELECT km.tenKM, s.tenSach, ct.phanTramGiam, km.ngayKetThuc
        FROM KhuyenMai km
        JOIN ChiTietKhuyenMai ct ON km.maKM = ct.maKM
        JOIN Sach s ON ct.maSach = s.maSach
        WHERE km.ngayBatDau <= NOW() AND km.ngayKetThuc >= NOW()
    ";
    $stmtKM = $pdo->prepare($sqlKM);
    $stmtKM->execute();
    $danhSachKM = $stmtKM->fetchAll(PDO::FETCH_ASSOC);

    $context_khuyen_mai = "";
    if (count($danhSachKM) > 0) {
        $context_khuyen_mai = "Danh sách các chương trình khuyến mãi ĐANG DIỄN RA:\n";
        foreach ($danhSachKM as $km) {
            $tenKM = htmlspecialchars($km['tenKM'], ENT_QUOTES, 'UTF-8');
            $tenSachKM = htmlspecialchars($km['tenSach'], ENT_QUOTES, 'UTF-8');
            $giam = $km['phanTramGiam'];
            
            // Đã đổi $km['ngayKT'] thành $km['ngayKetThuc']
            $ngayKetThuc = date('d/m/Y H:i', strtotime($km['ngayKetThuc'])); 
            
            $context_khuyen_mai .= "- Chương trình '{$tenKM}': Sách '{$tenSachKM}' đang được GIẢM {$giam}% (Hạn chót: {$ngayKetThuc})\n";
        }
    } else {
        $context_khuyen_mai = "Hiện tại cửa hàng không có chương trình khuyến mãi nào.\n";
    }
    // =========================================================================
    // 4. NHẬN DỮ LIỆU TỪ JAVASCRIPT
    // =========================================================================
    $inputData = json_decode(file_get_contents('php://input'), true);
    $userMsg = $inputData['message'] ?? '';

    if (empty($userMsg)) {
        echo json_encode(["error" => "Chưa có nội dung tin nhắn"]);
        exit;
    }

    // =========================================================================
    // 5. CẤU HÌNH GEMINI API & TẠO PROMPT THÔNG MINH
    // =========================================================================
    $apiKey = GEMINI_API_KEY; // Đã giấu API Key an toàn trong env_local.php
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

    // Gộp cả sách và khuyến mãi vào Prompt
    $prompt = "Bạn là nhân viên tư vấn nhiệt tình của tiệm sách BookSM. \n\n" . 
              "📚 DỮ LIỆU SÁCH: \n" . $context_sach . "\n\n" .
              "🎁 THÔNG TIN KHUYẾN MÃI: \n" . $context_khuyen_mai . "\n\n" .
              "Yêu cầu của khách: " . $userMsg . "\n\n" .
              "LƯU Ý QUAN TRỌNG CHO BẠN:\n" .
              "1. Nếu khách hỏi về sách đang có khuyến mãi, HÃY TỰ ĐỘNG TÍNH TOÁN và báo giá cuối cùng (giá sau khi trừ % giảm giá) cho khách.\n" .
              "2. Hãy nhắc khéo khách về thời hạn kết thúc khuyến mãi để chốt sale.\n" .
              "3. Trả lời thân thiện, ngắn gọn, có sử dụng emoji phù hợp.";

    $payload = [
        "contents" => [
            ["parts" => [["text" => $prompt]]]
        ]
    ];

    // KIỂM TRA LỖI ÉP KIỂU JSON (Rất quan trọng để tránh lỗi 400)
    $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE);
    if ($jsonPayload === false) {
        echo json_encode(["error" => "Lỗi gói dữ liệu: " . json_last_error_msg()]);
        exit;
    }

    // 6. Gọi API Gemini
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload); // Dùng chuỗi JSON đã kiểm tra
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 7. Xử lý kết quả trả về
    if ($httpCode !== 200) {
        // Lấy chính xác câu báo lỗi của Google để in ra màn hình
        $googleError = json_decode($response, true);
        $errorMsg = $googleError['error']['message'] ?? "Lỗi không xác định từ Google";
        echo json_encode(["error" => "Google báo lỗi ($httpCode): " . $errorMsg]);
    } else {
        echo $response;
    }

} catch (Exception $e) {
    echo json_encode(["error" => "Lỗi hệ thống: " . $e->getMessage()]);
}
?>