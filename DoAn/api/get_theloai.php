<?php
// Thêm 2 dòng này vào đầu file để ép PHP hiện lỗi
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../BUS/TheLoai_BUS.php';

try {
    $theLoai_BUS = new TheLoai_BUS();
    $danhSach = $theLoai_BUS->getDanhSachTheLoai();

    // Do DTO của bạn có các thuộc tính public, json_encode sẽ tự động parse object thành JSON hợp lệ
    if (count($danhSach) > 0) {
        echo json_encode([
            "status" => 200,
            "message" => "Thành công",
            "data" => $danhSach
        ]);
    } else {
        echo json_encode([
            "status" => 404,
            "message" => "Không có dữ liệu",
            "data" => []
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => 500,
        "message" => "Lỗi server: " . $e->getMessage()
    ]);
}
?>