<?php
// Lùi về 3 cấp thư mục (GiaoDien -> TrangBanHang -> CuaHang -> Gốc) để nạp kết nối PDO
require_once '../../../KetNoi/config/db.php'; 

// Kiểm tra xem có nhận được từ khóa hay không
if (isset($_POST['tu_khoa'])) {
    $tuKhoa = trim($_POST['tu_khoa']);
    
    // Nếu rỗng thì không làm gì cả
    if ($tuKhoa != '') {
        $tuKhoaTimKiem = "%" . $tuKhoa . "%";
        
        try {
            // NÂNG CẤP: Dùng Subquery để lấy Hình ảnh (từ bảng HinhAnhSach) 
            // và Tác giả (Gom nhóm từ bảng TacGia + Sach_TacGia)
            $cauTruyVan = "
                SELECT 
                    s.maSach, 
                    s.tenSach, 
                    s.giaBan, 
                    (SELECT ha.urlAnh FROM HinhAnhSach ha WHERE ha.maSach = s.maSach LIMIT 1) AS hinhAnh,
                    (SELECT GROUP_CONCAT(tg.tenTG SEPARATOR ', ') 
                     FROM TacGia tg 
                     JOIN Sach_TacGia st ON tg.maTG = st.maTG 
                     WHERE st.maSach = s.maSach) AS tacGia
                FROM Sach s 
                WHERE (
                    s.tenSach LIKE :tuKhoa 
                    OR s.maSach IN (
                        SELECT st2.maSach FROM Sach_TacGia st2
                        JOIN TacGia tg2 ON st2.maTG = tg2.maTG
                        WHERE tg2.tenTG LIKE :tuKhoa
                    )
                ) 
                AND s.trangThai = 'DangKD' 
                LIMIT 5
            ";
                           
            $lenhThucThi = $pdo->prepare($cauTruyVan);
            $lenhThucThi->execute(['tuKhoa' => $tuKhoaTimKiem]);
            $danhSachKetQua = $lenhThucThi->fetchAll();

            // Nếu có kết quả thì in ra HTML (Không dùng JSON)
            if (count($danhSachKetQua) > 0) {
                foreach ($danhSachKetQua as $sach) {
                    $anhHienThi = layDuongDanAnh($sach['hinhAnh'] ?? null, 'https://placehold.co/45x65/eff6ff/2563eb?text=Sách');
                    $tenSach = htmlspecialchars($sach['tenSach']);
                    $tacGia = htmlspecialchars($sach['tacGia'] ?: 'Đang cập nhật');
                    $giaBan = number_format($sach['giaBan'], 0, ',', '.') . ' ₫';
                    
                    // In ra thẻ HTML để hiển thị lên giao diện
                    echo '
                    <a href="#" class="muc-ket-qua">
                        <img src="' . $anhHienThi . '" alt="' . $tenSach . '">
                        <div class="thong-tin-sach-tim-kiem">
                            <h4>' . $tenSach . '</h4>
                            <p>Tác giả: ' . $tacGia . '</p>
                            <p class="gia-tien">' . $giaBan . '</p>
                        </div>
                    </a>';
                }
            } else {
                // Nếu không tìm thấy
                echo '<div style="padding: 15px; text-align: center; color: #6b7280; font-size: 14px;">Không tìm thấy sách phù hợp.</div>';
            }
        } catch (Exception $loi) {
            // Bắt lỗi nếu có trục trặc về CSDL
            echo '<div style="padding: 15px; text-align: center; color: red;">Lỗi truy vấn dữ liệu: ' . htmlspecialchars($loi->getMessage()) . '</div>';
        }
    }
}
?>