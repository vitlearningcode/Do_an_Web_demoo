<?php
require_once '../DTO/TheLoai_DTO.php';
// Nhúng file db.php của bạn vào (kiểm tra lại đường dẫn ../config/db.php cho chuẩn với thư mục của bạn)
require_once '../config/db.php'; 

class TheLoai_DAO 
{
    private PDO $conn;

    public function __construct() 
    {
        // Gọi biến $pdo mang tính toàn cục (global) đã được tạo sẵn từ file db.php
        global $pdo; 
        
        if (isset($pdo)) {
            $this->conn = $pdo;
        } else {
            die("Lỗi: Không tìm thấy kết nối cơ sở dữ liệu.");
        }
    }

    /**
     * Lấy tất cả thể loại
     * @return TheLoai_DTO[]
     */
    public function getAll(): array 
    {
        $dsTheLoai = [];
        $sql = "SELECT maTL, tenTL FROM TheLoai";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        // fetchAll đã được set mặc định là FETCH_ASSOC trong db.php nên ở đây có hay không truyền tham số đều được
        $result = $stmt->fetchAll(); 
        
        // Tận dụng hàm fromArray cực xịn của DTO
        foreach ($result as $row) {
            $dsTheLoai[] = TheLoai_DTO::fromArray($row);
        }
        
        return $dsTheLoai;
    }
}
?>