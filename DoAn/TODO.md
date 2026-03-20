## chức năng lọc sách theo nhiều thể loại:
   <?php
// ... Code kết nối PDO với database Aiven của bạn nằm ở đây ...
// $pdo = new PDO(...);

// 1. Nhận mảng ID thể loại từ Frontend gửi lên (Giả sử qua $_POST)
// Ví dụ mảng nhận được: [1, 2, 5] (Người dùng chọn 3 thể loại: Shounen, Phiêu lưu, Hành động)
$the_loai_duoc_chon = isset($_POST['genres']) ? $_POST['genres'] : [];

// Kiểm tra xem người dùng có chọn thể loại nào không
if (!empty($the_loai_duoc_chon)) {
    
    // 2. Đếm số lượng thể loại người dùng đã chọn
    $so_luong = count($the_loai_duoc_chon); // Kết quả: 3

    // 3. Tạo ra chuỗi các dấu chấm hỏi (?, ?, ?) tùy theo số lượng
    // array_fill sẽ tạo mảng chứa các dấu '?', sau đó implode sẽ nối chúng lại bằng dấu phẩy
    $placeholders = implode(',', array_fill(0, $so_luong, '?')); 

    // 4. Viết câu SQL động
    $sql = "
        SELECT s.* FROM sach s
        JOIN sach_the_loai stl ON s.id = stl.sach_id
        WHERE stl.the_loai_id IN ($placeholders)
        GROUP BY s.id
        HAVING COUNT(DISTINCT stl.the_loai_id) = ?
    ";

    // 5. Chuẩn bị mảng giá trị để truyền vào (bind) cho các dấu ?
    $tham_so = $the_loai_duoc_chon; // Đưa mảng [1, 2, 5] vào trước để lấp vào IN (?, ?, ?)
    $tham_so[] = $so_luong;         // Thêm con số đếm (3) vào cuối cùng để lấp vào HAVING COUNT = ?

    try {
        // Thực thi câu lệnh SQL an toàn
        $stmt = $pdo->prepare($sql);
        $stmt->execute($tham_so);
        
        $sach_phu_hop = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Trả kết quả về cho Frontend bằng JSON
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'data' => $sach_phu_hop
        ]);

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi truy vấn: ' . $e->getMessage()]);
    }

} else {
    // Nếu không chọn thể loại nào, bạn có thể viết câu SQL SELECT * FROM sach bình thường
    echo json_encode(['success' => false, 'message' => 'Chưa chọn thể loại nào']);
}
?>