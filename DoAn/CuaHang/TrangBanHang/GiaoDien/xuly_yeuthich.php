<?php
session_start();
// Lùi về 3 cấp thư mục (GiaoDien -> TrangBanHang -> CuaHang -> Gốc) để lấy file db.php
require_once '../../../KetNoi/config/db.php'; 

// Kiểm tra xem khách hàng đã đăng nhập chưa
if (!isset($_SESSION['nguoi_dung_id'])) {
    echo "CHUA_DANG_NHAP";
    exit();
}

if (isset($_POST['ma_sach'])) {
    $ma_sach = $_POST['ma_sach'];
    $ma_nguoi_dung = $_SESSION['nguoi_dung_id'];

    try {
        // 1. Kiểm tra xem cuốn sách này đã nằm trong danh sách yêu thích chưa
        $cau_truy_van_kiem_tra = "SELECT * FROM SachYeuThich WHERE maND = :ma_nguoi_dung AND maSach = :ma_sach";
        $lenh_kiem_tra = $pdo->prepare($cau_truy_van_kiem_tra);
        $lenh_kiem_tra->execute([
            'ma_nguoi_dung' => $ma_nguoi_dung, 
            'ma_sach' => $ma_sach
        ]);
        
        if ($lenh_kiem_tra->rowCount() > 0) {
            // 2. Nếu đã có -> Khách hàng muốn bỏ thích -> Thực hiện XÓA
            $cau_truy_van_xoa = "DELETE FROM SachYeuThich WHERE maND = :ma_nguoi_dung AND maSach = :ma_sach";
            $lenh_xoa = $pdo->prepare($cau_truy_van_xoa);
            $lenh_xoa->execute([
                'ma_nguoi_dung' => $ma_nguoi_dung, 
                'ma_sach' => $ma_sach
            ]);
            echo "DA_XOA";
        } else {
            // 3. Nếu chưa có -> Khách hàng muốn thả tim -> Thực hiện THÊM
            $cau_truy_van_them = "INSERT INTO SachYeuThich (maND, maSach) VALUES (:ma_nguoi_dung, :ma_sach)";
            $lenh_them = $pdo->prepare($cau_truy_van_them);
            $lenh_them->execute([
                'ma_nguoi_dung' => $ma_nguoi_dung, 
                'ma_sach' => $ma_sach
            ]);
            echo "DA_THEM";
        }
    } catch (PDOException $loi_he_thong) {
        // Trả về thông báo lỗi nếu có trục trặc CSDL
        echo "LOI_HE_THONG";
    }
}
?>