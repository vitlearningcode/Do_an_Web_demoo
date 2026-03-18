1. create table NguoiDung
- maND (khoa chinh)
- tenND
- sdt
- email
- ngayTao

2. create table TaiKhoan
- tenDN (khoa chinh)
- matKhau
- trangThai
- maND (khoa ngoai)
- maVT (khoa ngoai)

3. create table VaiTro
- maVT (khoa chinh)
- tenVT

4. create table DiaChiGiaoHang
- maDC (khoa chinh)
- diaChiChiTiet
- laMacDinh
- maND (khoa ngoai)

5. create table TheLoai
- maTL (khoa chinh)
- tenTL

6. create table NhaXuatBan
- maNXB (khoa chinh)
- tenNXB
- diaChi

7. create table TacGia
- maTG (khoa chinh)
- tenTG
- tieuSu

8. create table Sach
- maSach (khoa chinh)
- tenSach
- namSX
- loaiBia
- giaBan
- soLuongTon
- moTa
- trangThai
- maNXB (khoa ngoai)

9. create table Sach_TheLoai
- maSach (khoa chinh, khoa ngoai)
- maTL (khoa chinh, khoa ngoai)

10. create table Sach_TacGia
- maSach (khoa chinh, khoa ngoai)
- maTG (khoa chinh, khoa ngoai)

11. create table HinhAnhSach
- maHA (khoa chinh)
- urlAnh
- maSach (khoa ngoai)

12. create table PhuongThucThanhToan
- maPT (khoa chinh)
- tenPT

13. create table KhuyenMai
- maKM (khoa chinh)
- tenKM
- ngayBatDau
- ngayKetThuc

14. create table ChiTietKhuyenMai
- maKM (khoa chinh, khoa ngoai)
- maSach (khoa chinh, khoa ngoai)
- phanTramGiam
- soLuongKhuyenMai

15. create table DonHang
- maDH (khoa chinh)
- ngayDat
- tongTien
- trangThai
- maND (khoa ngoai)
- maDC (khoa ngoai)
- maPT (khoa ngoai)

16. create table ChiTietDH
- maDH (khoa chinh, khoa ngoai)
- maSach (khoa chinh, khoa ngoai)
- soLuong
- giaBan
- thanhTien
- maKM (khoa ngoai)

17. create table DanhGiaSach
- maDG (khoa chinh)
- diemDG
- nhanXet
- ngayDG
- maSach (khoa ngoai)
- maND (khoa ngoai)

18. create table NhaCungCap
- maNCC (khoa chinh)
- tenNCC
- sdt
- email
- chietKhauMacDinh

19. create table PhieuNhap
- maPN (khoa chinh)
- tongLuongNhap
- ngayLap
- soTienDaThanhToan
- tongTien
- trangThai
- maNCC (khoa ngoai)

20. create table ChiTietPN
- maPN (khoa chinh, khoa ngoai)
- maSach (khoa chinh, khoa ngoai)
- soLuongNhap
- giaNhap
- chietKhau
- thanhTien

21. create table CongNo
- maCN (khoa chinh)
- tongNo
- capNhatCuoi
- maNCC (khoa ngoai)

22. create table LichSuThanhToanPN
- maLSTT (khoa chinh)
- ngayThanhToan
- soTienTra
- hinhThucTra
- ghiChu
- maPN (khoa ngoai)


USE BookSM;

-- Xóa dữ liệu cũ (nếu có) để test lại từ đầu không bị trùng lặp
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE ChiTietDH; TRUNCATE TABLE DonHang; TRUNCATE TABLE ChiTietKhuyenMai; TRUNCATE TABLE KhuyenMai;
TRUNCATE TABLE ChiTietPN; TRUNCATE TABLE LichSuThanhToanPN; TRUNCATE TABLE PhieuNhap; TRUNCATE TABLE CongNo; TRUNCATE TABLE NhaCungCap;
TRUNCATE TABLE DanhGiaSach; TRUNCATE TABLE HinhAnhSach; TRUNCATE TABLE Sach_TacGia; TRUNCATE TABLE Sach_TheLoai; TRUNCATE TABLE Sach;
TRUNCATE TABLE TacGia; TRUNCATE TABLE NhaXuatBan; TRUNCATE TABLE TheLoai; TRUNCATE TABLE PhuongThucThanhToan;
TRUNCATE TABLE DiaChiGiaoHang; TRUNCATE TABLE TaiKhoan; TRUNCATE TABLE VaiTro; TRUNCATE TABLE NguoiDung;
SET FOREIGN_KEY_CHECKS = 1;

-- ========================================================
-- 1. KHỐI NGƯỜI DÙNG & TÀI KHOẢN
-- ========================================================
INSERT INTO VaiTro (maVT, tenVT) VALUES (1, 'Admin'), (2, 'Khách hàng');

INSERT INTO NguoiDung (maND, tenND, sdt, email) VALUES 
(1, 'Chủ Cửa Hàng (Admin)', '0999999999', 'admin@booksm.com'),
(2, 'Lê Thanh Hòa', '0901234567', 'hoa@gmail.com'),
(3, 'Lê Minh Đức', '0912345678', 'duc@gmail.com'),
(4, 'demo_TK_dabo', '0912345678', 'demo@gmail.com');

INSERT INTO TaiKhoan (tenDN, matKhau, maND, maVT, trangThai) VALUES 
('admin', '1', 1, 1, 'on'),
('duc', '1', 2, 2, 'on'),
('hoa', '1', 3, 2, 'on'),
('demo', '1', 4, 2, 'off');;

INSERT INTO DiaChiGiaoHang (maDC, maND, diaChiChiTiet, laMacDinh) VALUES 
(1, 2, '123 Lê Lợi, Quận 1, TP.HCM', 1),
(2, 2, 'long xiêng (Giờ hành chính)', 0),
(3, 3, '456 Trần Phú, Ba Đình, Hà Nội', 1);

-- ========================================================
-- 2. KHỐI THÔNG TIN SÁCH 
-- ========================================================
INSERT INTO TheLoai (maTL, tenTL) VALUES 
(1, 'Kỹ năng sống'), (2, 'Văn học'), (3, 'Kinh tế'), (4, 'Khoa học'), (5, 'Tâm lý học');

INSERT INTO NhaXuatBan (maNXB, tenNXB, diaChi) VALUES 
(1, 'NXB Trẻ', '161 Lý Chính Thắng, TP.HCM'),
(2, 'NXB Tổng Hợp', '62 Nguyễn Thị Minh Khai, TP.HCM'),
(3, 'NXB Thế Giới', '46 Trần Hưng Đạo, Hà Nội');

INSERT INTO TacGia (maTG, tenTG, tieuSu) VALUES 
(1, 'Dale Carnegie', 'Tác giả người Mỹ, chuyên gia phát triển bản thân'),
(2, 'Paulo Coelho', 'Tiểu thuyết gia người Brazil'),
(3, 'Yuval Noah Harari', 'Nhà sử học, triết gia người Israel');

-- Nhập 3 cuốn sách làm mồi (Có cuốn bán chạy, có cuốn tồn kho lâu để test Flash Sale)
INSERT INTO Sach (maSach, tenSach, maNXB, namSX, loaiBia, giaBan, soLuongTon, moTa, trangThai) VALUES 
('S001', 'Đắc Nhân Tâm', 1, 2023, 'Bìa Mềm', 86000, 150, 'Sách hay về giao tiếp', 'DangKD'),
('S002', 'Nhà Giả Kim', 2, 2022, 'Bìa Mềm', 79000, 45, 'Tiểu thuyết bán chạy nhất mọi thời đại', 'NgungKD'),
('S003', 'Sapiens: Lược Sử Loài Người', 3, 2021, 'Bìa Cứng', 250000, 120, 'Sách khoa học lịch sử', 'DangKD');

INSERT INTO Sach_TheLoai (maSach, maTL) VALUES ('S001', 1), ('S002', 2), ('S003', 4);
INSERT INTO Sach_TacGia (maSach, maTG) VALUES ('S001', 1), ('S002', 2), ('S003', 3);
INSERT INTO HinhAnhSach (maSach, urlAnh) VALUES 
('S001', 'https://picsum.photos/seed/book1/300/400'),
('S002', 'https://picsum.photos/seed/book2/300/400'),
('S003', 'https://picsum.photos/seed/book5/300/400');

-- ========================================================
-- 3. KHỐI NHẬP HÀNG & CÔNG NỢ (Test thuật toán Kho)
-- ========================================================
INSERT INTO NhaCungCap (maNCC, tenNCC, sdt, email, chietKhauMacDinh) VALUES 
(1, 'Công ty Phát Hành Sách FAHASA', '1900636467', 'info@fahasa.com', 10),
(2, 'Nhà sách Phương Nam', '1900123456', 'contact@phuongnam.com', 15);

-- Phiếu nhập 1: Đã thanh toán đủ (Completed)
INSERT INTO PhieuNhap (maPN, tongLuongNhap, ngayLap, soTienDaThanhToan, tongTien, trangThai, maNCC) VALUES 
('PN001', 150, '2026-02-01 08:00:00', 11610000, 11610000, 'Completed', 1);

INSERT INTO ChiTietPN (maPN, maSach, soLuongNhap, giaNhap, chietKhau, thanhTien) VALUES 
('PN001', 'S001', 150, 86000, 10, 11610000); -- Giảm 10% Fahasa

INSERT INTO LichSuThanhToanPN (maPN, ngayThanhToan, soTienTra, hinhThucTra, ghiChu) VALUES 
('PN001', '2026-02-01 09:00:00', 11610000, 'Chuyển khoản VCB', 'Thanh toán đứt phiếu PN001');

-- Phiếu nhập 2: Đang nợ, chưa trả đồng nào (Waiting) - Nhập Sapiens
INSERT INTO PhieuNhap (maPN, tongLuongNhap, ngayLap, soTienDaThanhToan, tongTien, trangThai, maNCC) VALUES 
('PN002', 120, '2026-03-10 10:00:00', 0, 25500000, 'Waiting', 2);

INSERT INTO ChiTietPN (maPN, maSach, soLuongNhap, giaNhap, chietKhau, thanhTien) VALUES 
('PN002', 'S003', 120, 250000, 15, 25500000); -- Giảm 15% Phương Nam

-- Cập nhật Công nợ tổng (Đang nợ Phương Nam 25.5 củ)
INSERT INTO CongNo (maNCC, tongNo) VALUES (1, 0), (2, 25500000);

-- ========================================================
-- 4. KHỐI MARKETING (Test thuật toán Flash Sale)
-- ========================================================
-- Tạo chiến dịch Flash Sale đang diễn ra
INSERT INTO KhuyenMai (maKM, tenKM, ngayBatDau, ngayKetThuc) VALUES 
('FS_TODAY', 'Flash Sale Giá Sốc - Giờ Vàng', '2026-03-17 00:00:00', '2026-03-18 23:59:59');

-- Giả lập thuật toán đã bốc 2 cuốn sách cũ đưa vào Flash Sale
INSERT INTO ChiTietKhuyenMai (maKM, maSach, phanTramGiam, soLuongKhuyenMai) VALUES 
('FS_TODAY', 'S003', 22, 50),  -- Sapiens giảm 22% (Giống file data.js)
('FS_TODAY', 'S001', 10, 20);  -- Đắc Nhân Tâm giảm 10%

-- ========================================================
-- 5. KHỐI ĐƠN HÀNG 
-- ========================================================
INSERT INTO PhuongThucThanhToan (maPT, tenPT) VALUES (1, 'Tiền mặt'), (2, 'Chuyển khoản');

-- Đơn hàng 1: Đã hoàn thành (Giao thành công cuốn Nhà Giả Kim)
INSERT INTO DonHang (maDH, maND, maDC, maPT, ngayDat, tongTien, trangThai) VALUES 
('DH_0001', 2, 1, 1, '2026-03-15 14:30:00', 79000, 'HoanThanh');

INSERT INTO ChiTietDH (maDH, maSach, soLuong, giaBan, maKM, thanhTien) VALUES 
('DH_0001', 'S002', 1, 79000, NULL, 79000);

INSERT INTO DanhGiaSach (maSach, maND, diemDG, nhanXet, ngayDG) VALUES 
('S002', 2, 5, 'Sách bọc màng co cẩn thận, nội dung rất hay!', '2026-03-17 08:00:00');

-- Đơn hàng 2: Đang chờ duyệt (Mua Đắc Nhân Tâm trong giờ Flash Sale)
INSERT INTO DonHang (maDH, maND, maDC, maPT, ngayDat, tongTien, trangThai) VALUES 
('DH_0002', 3, 3, 2, '2026-03-17 10:15:00', 77400, 'ChoDuyet');

-- Giá gốc 86k, mua lúc có mã FS_TODAY giảm 10% nên giá bán ghi nhận là 77.400đ
INSERT INTO ChiTietDH (maDH, maSach, soLuong, giaBan, maKM, thanhTien) VALUES 
('DH_0002', 'S001', 1, 77400, 'FS_TODAY', 77400);
