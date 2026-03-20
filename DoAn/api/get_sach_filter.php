<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../BUS/Sach_BUS.php';

try {
    // Lấy tham số từ URL
    // Ví dụ URL: api/get_sach_filter.php?theloai=1,3&gia=100_500
    $theLoaiParam = isset($_GET['theloai']) ? $_GET['theloai'] : '';
    $khoangGia = isset($_GET['gia']) ? $_GET['gia'] : '';
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

    // Biến chuỗi "1,3" thành mảng [1, 3]
    $mangTheLoai = [];
    if (!empty($theLoaiParam)) {
        $mangTheLoai = explode(',', $theLoaiParam);
    }

    $sach_BUS = new Sach_BUS();
    $danhSach = $sach_BUS->getDanhSachSach($mangTheLoai, $khoangGia, $sort);

    if (count($danhSach) > 0) {
        echo json_encode([
            "status" => 200,
            "message" => "Thành công",
            "data" => $danhSach
        ]);
    } else {
        echo json_encode([
            "status" => 404,
            "message" => "Không tìm thấy sách phù hợp",
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



























