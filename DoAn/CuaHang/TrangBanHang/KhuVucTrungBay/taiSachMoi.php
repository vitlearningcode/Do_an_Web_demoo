<?php
/**
 * taiSachMoi.php
 * Tải danh sách Top 8 sách mới phát hành gần đây nhất.
 *
 * Yêu cầu: $pdo đã được khởi tạo từ file gọi (index.php).
 * Kết quả:
 *   $ds_sachmoi — Top 8 sách mới nhất (sắp xếp theo namSX DESC, maSach DESC)
 */

// ================================================================
// THUẬT TOÁN SÁCH MỚI
// ORDER BY namSX DESC, maSach DESC
// ================================================================
$ds_sachmoi = $pdo->query("
    SELECT
        s.maSach, s.tenSach, s.giaBan, s.namSX, s.moTa, s.loaiBia AS hinhThucBia, s.soLuongTon,
        (SELECT tenNXB FROM NhaXuatBan nxb WHERE nxb.maNXB = s.maNXB) AS nhaXuatBan,
        (SELECT urlAnh FROM HinhAnhSach WHERE maSach = s.maSach LIMIT 1) AS hinhAnh,
        (SELECT GROUP_CONCAT(tg.tenTG SEPARATOR ', ')
         FROM Sach_TacGia stg JOIN TacGia tg ON stg.maTG = tg.maTG
         WHERE stg.maSach = s.maSach) AS tacGia,
        (SELECT tenTL FROM TheLoai tl
         JOIN Sach_TheLoai stl ON stl.maTL = tl.maTL
         WHERE stl.maSach = s.maSach LIMIT 1) AS theLoai,
        (SELECT ROUND(AVG(diemDG), 1) FROM DanhGiaSach WHERE maSach = s.maSach) AS diemTB,
        (SELECT COUNT(*) FROM DanhGiaSach WHERE maSach = s.maSach) AS soReview
    FROM Sach s
    WHERE s.trangThai = 'DangKD'
    ORDER BY s.namSX DESC, s.maSach DESC
    LIMIT 8
")->fetchAll(PDO::FETCH_ASSOC);
