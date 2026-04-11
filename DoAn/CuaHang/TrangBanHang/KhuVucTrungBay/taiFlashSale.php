<?php
/**
 * taiFlashSale.php
 * Tải danh sách sách Flash Sale và thời gian kết thúc chương trình.
 *
 * Yêu cầu: $pdo đã được khởi tạo từ file gọi (index.php).
 * Kết quả:
 *   $ds_flashsale         — Mảng sách đang Flash Sale (tối đa 5 cuốn)
 *   $flashSale_ThoiGianKT — Thời gian kết thúc Flash Sale gần nhất
 */

// ================================================================
// THUẬT TOÁN FLASH SALE
// Chỉ hiển thị khi NOW() nằm trong [ngayBatDau, ngayKetThuc]
// phanTramGiam thực từ ChiTietKhuyenMai (10% | 22% | 33%)
// ================================================================
$ds_flashsale = $pdo->query("
    SELECT
        s.maSach, s.tenSach, s.giaBan, s.moTa, s.namSX, s.loaiBia AS hinhThucBia, s.soLuongTon,
        ckm.phanTramGiam,
        ckm.soLuongKhuyenMai,
        ROUND(s.giaBan * (1 - ckm.phanTramGiam / 100)) AS giaSau,
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
        IFNULL((
            SELECT SUM(ct.soLuong)
            FROM ChiTietDH ct
            JOIN DonHang dh ON dh.maDH = ct.maDH
            WHERE ct.maSach = s.maSach AND dh.trangThai = 'HoanThanh'
        ), 0) AS tongBan
    FROM Sach s
    JOIN ChiTietKhuyenMai ckm ON ckm.maSach = s.maSach
    JOIN KhuyenMai km ON km.maKM = ckm.maKM
    WHERE s.trangThai = 'DangKD'
      AND NOW() BETWEEN km.ngayBatDau AND km.ngayKetThuc
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Thời gian kết thúc Flash Sale (cho đồng hồ đếm ngược)
$flashSale_ThoiGianKT = $pdo->query("
    SELECT ngayKetThuc FROM KhuyenMai
    WHERE NOW() BETWEEN ngayBatDau AND ngayKetThuc
    ORDER BY ngayKetThuc ASC LIMIT 1
")->fetchColumn();
