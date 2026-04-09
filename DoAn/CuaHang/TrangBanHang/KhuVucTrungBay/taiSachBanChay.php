<?php
/**
 * taiSachBanChay.php
 * Tải danh sách Top 10 sách bán chạy nhất trong tháng hiện tại.
 *
 * Yêu cầu: $pdo đã được khởi tạo từ file gọi (index.php).
 * Kết quả:
 *   $ds_banchay — Top 10 sách bán nhiều nhất tháng (kèm Flash Sale real-time nếu có)
 */

// ================================================================
// THUẬT TOÁN SÁCH BÁN CHẠY
// Lọc theo tháng hiện tại: MONTH(ngayDat) = MONTH(NOW())
//   AND YEAR(ngayDat) = YEAR(NOW())
// SUM(soLuong) từ đơn HoanThanh trong tháng → sort DESC → top 10
// Kèm phanTramGiam Flash Sale real-time nếu cuốn sách đang giảm giá
// ================================================================
$ds_banchay = $pdo->query("
    SELECT
        s.maSach, s.tenSach, s.giaBan, s.moTa, s.namSX, s.loaiBia AS hinhThucBia, s.soLuongTon,
        (SELECT tenNXB FROM NhaXuatBan nxb WHERE nxb.maNXB = s.maNXB) AS nhaXuatBan,
        (SELECT urlAnh FROM HinhAnhSach WHERE maSach = s.maSach LIMIT 1) AS hinhAnh,
        (SELECT GROUP_CONCAT(tg.tenTG SEPARATOR ', ')
         FROM Sach_TacGia stg JOIN TacGia tg ON stg.maTG = tg.maTG
         WHERE stg.maSach = s.maSach) AS tacGia,
        (SELECT tenTL FROM TheLoai tl
         JOIN Sach_TheLoai stl ON stl.maTL = tl.maTL
         WHERE stl.maSach = s.maSach LIMIT 1) AS theLoai,
        (SELECT ROUND(AVG(diemDG), 1) FROM DanhGiaSach WHERE maSach = s.maSach) AS diemTB,
        (SELECT COUNT(*) FROM DanhGiaSach WHERE maSach = s.maSach) AS soReview,
        -- Số lượng bán trong tháng hiện tại
        IFNULL((
            SELECT SUM(ct.soLuong)
            FROM ChiTietDH ct
            JOIN DonHang dh ON dh.maDH = ct.maDH
            WHERE ct.maSach = s.maSach
              AND dh.trangThai = 'HoanThanh'
              AND MONTH(dh.ngayDat) = MONTH(NOW())
              AND YEAR(dh.ngayDat)  = YEAR(NOW())
        ), 0) AS tongBanThang,
        -- Giảm giá Flash Sale real-time (nếu đang trong khung giờ)
        (SELECT ckm.phanTramGiam
         FROM ChiTietKhuyenMai ckm
         JOIN KhuyenMai km ON km.maKM = ckm.maKM
         WHERE ckm.maSach = s.maSach
           AND NOW() BETWEEN km.ngayBatDau AND km.ngayKetThuc
         LIMIT 1) AS phanTramGiam
    FROM Sach s
    WHERE s.trangThai = 'DangKD'
    ORDER BY tongBanThang DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);
