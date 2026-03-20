<?php
require_once '../config/db.php'; 

class Sach_DAO 
{
    private PDO $conn;

    public function __construct() 
    {
        global $pdo; 
        if (isset($pdo)) {
            $this->conn = $pdo;
        } else {
            die("Lỗi kết nối CSDL trong Sach_DAO.");
        }
    }

    public function getSachTheoBoLoc($mangTheLoai = [], $khoangGia = '',$sort = 'newest') 
    {
        $sql = "SELECT s.maSach, s.tenSach, s.giaBan, 
                       (SELECT urlAnh FROM HinhAnhSach WHERE maSach = s.maSach LIMIT 1) as urlAnh 
                FROM Sach s ";
        
        $conditions = [];
        $params = [];
        $havingClause = ""; // Thêm biến chứa điều kiện HAVING

        // 1. Lọc theo Thể loại (LOGIC: AND - Sách phải có TẤT CẢ các thể loại được chọn)
        if (!empty($mangTheLoai)) {
            $sql .= " JOIN Sach_TheLoai st ON s.maSach = st.maSach ";
            
            // Vẫn dùng IN để lấy ra các dòng có chứa các thể loại được chọn
            $placeholders = implode(',', array_fill(0, count($mangTheLoai), '?'));
            $conditions[] = "st.maTL IN ($placeholders)";
            
            foreach ($mangTheLoai as $maTL) {
                $params[] = $maTL;
            }

            // ĐIỂM MẤU CHỐT LÀ ĐÂY: 
            // Đếm số lượng mã thể loại khác nhau của cuốn sách đó.
            // Nếu khách chọn 2 thể loại, sách đó phải có Count = 2 thì mới lấy.
            $soLuongTheLoaiYeuCau = count($mangTheLoai);
            $havingClause = " HAVING COUNT(DISTINCT st.maTL) = $soLuongTheLoaiYeuCau ";
        }

        // 2. Lọc theo Giá (Giữ nguyên)
        if (!empty($khoangGia)) {
            switch ($khoangGia) {
                case 'duoi_100': $conditions[] = "s.giaBan < 100000"; break;
                case '100_500': $conditions[] = "s.giaBan >= 100000 AND s.giaBan <= 500000"; break;
                case '500_1000': $conditions[] = "s.giaBan > 500000 AND s.giaBan <= 1000000"; break;
                case 'tren_1000': $conditions[] = "s.giaBan > 1000000"; break;
            }
        }

        $conditions[] = "s.trangThai = 'DangKD'"; // Chỉ lấy sách đang bán

        // Gắn WHERE vào SQL
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        // Bắt buộc phải có GROUP BY
        $sql .= " GROUP BY s.maSach";

        // Gắn HAVING vào sau GROUP BY
        if (!empty($havingClause)) {
            $sql .= $havingClause;
        }
        if (!empty($havingClause)) {
        $sql .= $havingClause;
    }

    // ====== THÊM LOGIC SẮP XẾP VÀO ĐÂY ======
    switch ($sort) {
        case 'price-asc':
            $sql .= " ORDER BY s.giaBan ASC"; // Giá thấp đến cao
            break;
        case 'price-desc':
            $sql .= " ORDER BY s.giaBan DESC"; // Giá cao đến thấp
            break;
        case 'newest':
        default:
            $sql .= " ORDER BY s.namSX DESC, s.maSach DESC"; // Mới nhất (dựa vào năm sản xuất)
            break;
    }

    // Thực thi
    $stmt = $this->conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>