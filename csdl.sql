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


-- BookSM.KhuyenMai definition

CREATE TABLE "KhuyenMai" (
  "maKM" varchar(20) NOT NULL,
  "tenKM" varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  "ngayBatDau" datetime NOT NULL,
  "ngayKetThuc" datetime NOT NULL,
  PRIMARY KEY ("maKM")
);


-- BookSM.NguoiDung definition

CREATE TABLE "NguoiDung" (
  "maND" int NOT NULL AUTO_INCREMENT,
  "tenND" varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  "sdt" int DEFAULT NULL,
  "email" varchar(100) DEFAULT NULL,
  "ngayTao" datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY ("maND"),
  UNIQUE KEY "sdt" ("sdt"),
  UNIQUE KEY "email" ("email")
);


-- BookSM.NhaCungCap definition

CREATE TABLE "NhaCungCap" (
  "maNCC" int NOT NULL AUTO_INCREMENT,
  "tenNCC" varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  "sdt" varchar(15) DEFAULT NULL,
  "email" varchar(100) DEFAULT NULL,
  "chietKhauMacDinh" float DEFAULT '0',
  PRIMARY KEY ("maNCC")
);


-- BookSM.NhaXuatBan definition

CREATE TABLE "NhaXuatBan" (
  "maNXB" int NOT NULL AUTO_INCREMENT,
  "tenNXB" varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  "diaChi" varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  PRIMARY KEY ("maNXB")
);


-- BookSM.PhuongThucThanhToan definition

CREATE TABLE "PhuongThucThanhToan" (
  "maPT" int NOT NULL AUTO_INCREMENT,
  "tenPT" varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  PRIMARY KEY ("maPT")
);


-- BookSM.QuangCao definition

CREATE TABLE "QuangCao" (
  "maQC" int NOT NULL AUTO_INCREMENT,
  "hinhAnh" varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  "nhan" varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  "tieuDe" varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  "moTa" text COLLATE utf8mb4_unicode_ci,
  "chuNut" varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  "mauNen" varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'blue',
  "trangThai" int DEFAULT '1',
  PRIMARY KEY ("maQC")
);


-- BookSM.TacGia definition

CREATE TABLE "TacGia" (
  "maTG" int NOT NULL AUTO_INCREMENT,
  "tenTG" varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  "tieuSu" text,
  PRIMARY KEY ("maTG")
);


-- BookSM.TheLoai definition

CREATE TABLE "TheLoai" (
  "maTL" int NOT NULL AUTO_INCREMENT,
  "tenTL" varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  PRIMARY KEY ("maTL")
);


-- BookSM.VaiTro definition

CREATE TABLE "VaiTro" (
  "maVT" int NOT NULL AUTO_INCREMENT,
  "tenVT" varchar(50) NOT NULL,
  PRIMARY KEY ("maVT")
);


-- BookSM.CongNo definition

CREATE TABLE "CongNo" (
  "maCN" int NOT NULL AUTO_INCREMENT,
  "maNCC" int NOT NULL,
  "tongNo" decimal(15,2) NOT NULL DEFAULT '0.00',
  "capNhatCuoi" datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY ("maCN"),
  KEY "maNCC" ("maNCC"),
  CONSTRAINT "CongNo_ibfk_1" FOREIGN KEY ("maNCC") REFERENCES "NhaCungCap" ("maNCC")
);


-- BookSM.DiaChiGiaoHang definition

CREATE TABLE "DiaChiGiaoHang" (
  "maDC" int NOT NULL AUTO_INCREMENT,
  "maND" int NOT NULL,
  "diaChiChiTiet" varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  "laMacDinh" tinyint(1) DEFAULT '0',
  PRIMARY KEY ("maDC"),
  KEY "maND" ("maND"),
  CONSTRAINT "DiaChiGiaoHang_ibfk_1" FOREIGN KEY ("maND") REFERENCES "NguoiDung" ("maND") ON DELETE CASCADE
);


-- BookSM.DonHang definition

CREATE TABLE "DonHang" (
  "maDH" varchar(20) NOT NULL,
  "maND" int NOT NULL,
  "maDC" int NOT NULL,
  "maPT" int NOT NULL,
  "ngayDat" datetime DEFAULT CURRENT_TIMESTAMP,
  "tongTien" decimal(15,2) NOT NULL DEFAULT '0.00',
  "trangThai" enum('ChoDuyet','DangGiao','HoanThanh','DaHuy') DEFAULT 'ChoDuyet',
  PRIMARY KEY ("maDH"),
  KEY "maND" ("maND"),
  KEY "maDC" ("maDC"),
  KEY "maPT" ("maPT"),
  CONSTRAINT "DonHang_ibfk_1" FOREIGN KEY ("maND") REFERENCES "NguoiDung" ("maND"),
  CONSTRAINT "DonHang_ibfk_2" FOREIGN KEY ("maDC") REFERENCES "DiaChiGiaoHang" ("maDC"),
  CONSTRAINT "DonHang_ibfk_3" FOREIGN KEY ("maPT") REFERENCES "PhuongThucThanhToan" ("maPT")
);


-- BookSM.PhieuNhap definition

CREATE TABLE "PhieuNhap" (
  "maPN" varchar(20) NOT NULL,
  "tongLuongNhap" int DEFAULT '0',
  "ngayLap" datetime DEFAULT CURRENT_TIMESTAMP,
  "soTienDaThanhToan" decimal(15,2) DEFAULT '0.00',
  "tongTien" decimal(15,2) DEFAULT '0.00',
  "trangThai" enum('Waiting','Completed','Returned') DEFAULT 'Waiting',
  "maNCC" int NOT NULL,
  PRIMARY KEY ("maPN"),
  KEY "maNCC" ("maNCC"),
  CONSTRAINT "PhieuNhap_ibfk_1" FOREIGN KEY ("maNCC") REFERENCES "NhaCungCap" ("maNCC")
);


-- BookSM.Sach definition

CREATE TABLE "Sach" (
  "maSach" varchar(20) NOT NULL,
  "tenSach" varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  "maNXB" int NOT NULL,
  "namSX" int DEFAULT NULL,
  "loaiBia" enum('Bìa Mềm','Bìa Cứng') DEFAULT 'Bìa Mềm',
  "giaBan" decimal(12,2) NOT NULL,
  "soLuongTon" int DEFAULT '0',
  "moTa" text,
  "trangThai" enum('DangKD','NgungKD') DEFAULT 'DangKD',
  PRIMARY KEY ("maSach"),
  KEY "maNXB" ("maNXB"),
  CONSTRAINT "Sach_ibfk_1" FOREIGN KEY ("maNXB") REFERENCES "NhaXuatBan" ("maNXB")
);


-- BookSM.Sach_TacGia definition

CREATE TABLE "Sach_TacGia" (
  "maSach" varchar(20) NOT NULL,
  "maTG" int NOT NULL,
  PRIMARY KEY ("maSach","maTG"),
  KEY "maTG" ("maTG"),
  CONSTRAINT "Sach_TacGia_ibfk_1" FOREIGN KEY ("maSach") REFERENCES "Sach" ("maSach") ON DELETE CASCADE,
  CONSTRAINT "Sach_TacGia_ibfk_2" FOREIGN KEY ("maTG") REFERENCES "TacGia" ("maTG") ON DELETE CASCADE
);


-- BookSM.Sach_TheLoai definition

CREATE TABLE "Sach_TheLoai" (
  "maSach" varchar(20) NOT NULL,
  "maTL" int NOT NULL,
  PRIMARY KEY ("maSach","maTL"),
  KEY "maTL" ("maTL"),
  CONSTRAINT "Sach_TheLoai_ibfk_1" FOREIGN KEY ("maSach") REFERENCES "Sach" ("maSach") ON DELETE CASCADE,
  CONSTRAINT "Sach_TheLoai_ibfk_2" FOREIGN KEY ("maTL") REFERENCES "TheLoai" ("maTL") ON DELETE CASCADE
);


-- BookSM.TaiKhoan definition

CREATE TABLE "TaiKhoan" (
  "tenDN" varchar(50) NOT NULL,
  "matKhau" varchar(255) NOT NULL,
  "maND" int NOT NULL,
  "maVT" int NOT NULL,
  "trangThai" enum('on','off') DEFAULT 'on',
  PRIMARY KEY ("tenDN"),
  KEY "maND" ("maND"),
  KEY "maVT" ("maVT"),
  CONSTRAINT "TaiKhoan_ibfk_1" FOREIGN KEY ("maND") REFERENCES "NguoiDung" ("maND") ON DELETE CASCADE,
  CONSTRAINT "TaiKhoan_ibfk_2" FOREIGN KEY ("maVT") REFERENCES "VaiTro" ("maVT")
);


-- BookSM.ChiTietDH definition

CREATE TABLE "ChiTietDH" (
  "maDH" varchar(20) NOT NULL,
  "maSach" varchar(20) NOT NULL,
  "soLuong" int NOT NULL,
  "giaBan" decimal(12,2) NOT NULL,
  "maKM" varchar(20) DEFAULT NULL,
  "thanhTien" decimal(15,2) NOT NULL,
  PRIMARY KEY ("maDH","maSach"),
  KEY "maSach" ("maSach"),
  KEY "maKM" ("maKM"),
  CONSTRAINT "ChiTietDH_ibfk_1" FOREIGN KEY ("maDH") REFERENCES "DonHang" ("maDH") ON DELETE CASCADE,
  CONSTRAINT "ChiTietDH_ibfk_2" FOREIGN KEY ("maSach") REFERENCES "Sach" ("maSach"),
  CONSTRAINT "ChiTietDH_ibfk_3" FOREIGN KEY ("maKM") REFERENCES "KhuyenMai" ("maKM"),
  CONSTRAINT "ChiTietDH_chk_1" CHECK ((`soLuong` > 0))
);


-- BookSM.ChiTietKhuyenMai definition

CREATE TABLE "ChiTietKhuyenMai" (
  "maKM" varchar(20) NOT NULL,
  "maSach" varchar(20) NOT NULL,
  "phanTramGiam" int NOT NULL,
  "soLuongKhuyenMai" int DEFAULT '0',
  PRIMARY KEY ("maKM","maSach"),
  KEY "maSach" ("maSach"),
  CONSTRAINT "ChiTietKhuyenMai_ibfk_1" FOREIGN KEY ("maKM") REFERENCES "KhuyenMai" ("maKM") ON DELETE CASCADE,
  CONSTRAINT "ChiTietKhuyenMai_ibfk_2" FOREIGN KEY ("maSach") REFERENCES "Sach" ("maSach") ON DELETE CASCADE,
  CONSTRAINT "ChiTietKhuyenMai_chk_1" CHECK ((`phanTramGiam` in (10,22,33)))
);


-- BookSM.ChiTietPN definition

CREATE TABLE "ChiTietPN" (
  "maPN" varchar(20) NOT NULL,
  "maSach" varchar(20) NOT NULL,
  "soLuongNhap" int NOT NULL,
  "giaNhap" decimal(12,2) NOT NULL,
  "chietKhau" float DEFAULT '0',
  "thanhTien" decimal(15,2) NOT NULL,
  PRIMARY KEY ("maPN","maSach"),
  KEY "maSach" ("maSach"),
  CONSTRAINT "ChiTietPN_ibfk_1" FOREIGN KEY ("maPN") REFERENCES "PhieuNhap" ("maPN") ON DELETE CASCADE,
  CONSTRAINT "ChiTietPN_ibfk_2" FOREIGN KEY ("maSach") REFERENCES "Sach" ("maSach"),
  CONSTRAINT "ChiTietPN_chk_1" CHECK ((`soLuongNhap` > 0))
);


-- BookSM.DanhGiaSach definition

CREATE TABLE "DanhGiaSach" (
  "maDG" int NOT NULL AUTO_INCREMENT,
  "maSach" varchar(20) NOT NULL,
  "maND" int NOT NULL,
  "diemDG" int DEFAULT NULL,
  "nhanXet" text,
  "ngayDG" datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY ("maDG"),
  KEY "maSach" ("maSach"),
  KEY "maND" ("maND"),
  CONSTRAINT "DanhGiaSach_ibfk_1" FOREIGN KEY ("maSach") REFERENCES "Sach" ("maSach") ON DELETE CASCADE,
  CONSTRAINT "DanhGiaSach_ibfk_2" FOREIGN KEY ("maND") REFERENCES "NguoiDung" ("maND"),
  CONSTRAINT "DanhGiaSach_chk_1" CHECK (((`diemDG` >= 1) and (`diemDG` <= 5)))
);


-- BookSM.HinhAnhSach definition

CREATE TABLE "HinhAnhSach" (
  "maHA" int NOT NULL AUTO_INCREMENT,
  "maSach" varchar(20) NOT NULL,
  "urlAnh" varchar(255) NOT NULL,
  PRIMARY KEY ("maHA"),
  KEY "maSach" ("maSach"),
  CONSTRAINT "HinhAnhSach_ibfk_1" FOREIGN KEY ("maSach") REFERENCES "Sach" ("maSach") ON DELETE CASCADE
);


-- BookSM.LichSuThanhToanPN definition

CREATE TABLE "LichSuThanhToanPN" (
  "maLSTT" int NOT NULL AUTO_INCREMENT,
  "maPN" varchar(20) NOT NULL,
  "ngayThanhToan" datetime DEFAULT CURRENT_TIMESTAMP,
  "soTienTra" decimal(15,2) NOT NULL,
  "hinhThucTra" varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  "ghiChu" varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  PRIMARY KEY ("maLSTT"),
  KEY "maPN" ("maPN"),
  CONSTRAINT "LichSuThanhToanPN_ibfk_1" FOREIGN KEY ("maPN") REFERENCES "PhieuNhap" ("maPN") ON DELETE CASCADE
);



-- CHỌN DATABASE
USE BookSM;

-- ========================================================
-- 1. BỔ SUNG NHÀ XUẤT BẢN (Dùng INSERT IGNORE để không lỗi nếu đã có)
-- Sách cũ của ông đang dùng maNXB 1, 2, 3 nên ta phải đảm bảo chúng tồn tại
-- ========================================================
INSERT IGNORE INTO NhaXuatBan (maNXB, tenNXB, diaChi) VALUES
(1, 'NXB Trẻ', '161B Lý Chính Thắng, Quận 3, TP.HCM'),
(2, 'NXB Kim Đồng', '55 Quang Trung, Hai Bà Trưng, Hà Nội'),
(3, 'NXB Tổng Hợp TP.HCM', '62 Nguyễn Thị Minh Khai, Q1, TP.HCM'),
(4, 'NXB Nhã Nam', '59 Đỗ Quang, Cầu Giấy, Hà Nội');

-- ========================================================
-- 2. BỔ SUNG TÁC GIẢ MỚI (Từ ID 4 trở đi, 1-3 của ông đã có)
-- ========================================================
INSERT IGNORE INTO TacGia (maTG, tenTG, tieuSu) VALUES
(4, 'Nguyễn Nhật Ánh', 'Nhà văn chuyên viết truyện tuổi thơ và tuổi mới lớn.'),
(5, 'Fujiko F. Fujio', 'Tác giả bộ truyện tranh Doraemon huyền thoại.'),
(6, 'Mario Puzo', 'Nhà văn người Mỹ, nổi tiếng với tiểu thuyết Bố Già.'),
(7, 'Nam Cao', 'Nhà văn hiện thực xuất sắc của Việt Nam.'),
(8, 'Stephen Hawking', 'Nhà vật lý lý thuyết, vũ trụ học nổi tiếng.'),
(9, 'Daniel Kahneman', 'Nhà tâm lý học, đoạt giải Nobel Kinh tế.'),
(10, 'Tô Hoài', 'Nhà văn lớn của Việt Nam, nổi tiếng với Dế Mèn Phiêu Lưu Ký.'),
(11, 'Gosho Aoyama', 'Tác giả bộ truyện Thám tử lừng danh Conan.');

-- ========================================================
-- 3. BỔ SUNG THỂ LOẠI (Từ ID 6 trở đi, 1-5 của ông đã có)
-- ========================================================
INSERT IGNORE INTO TheLoai (maTL, tenTL) VALUES
(6, 'Thiếu nhi'), 
(7, 'Truyện tranh');

-- ========================================================
-- 4. THÊM 20 CUỐN SÁCH MỚI (Từ S004 -> S023)
-- ========================================================
INSERT INTO Sach (maSach, tenSach, maNXB, namSX, loaiBia, giaBan, soLuongTon, moTa, trangThai) VALUES
('S004', 'Mắt Biếc', 1, 2019, 'Bìa Cứng', 110000.00, 120, 'Câu chuyện tình yêu buồn nhưng đẹp của Ngạn và Hà Lan.', 'DangKD'),
('S005', 'Doraemon Tập 1', 2, 2022, 'Bìa Mềm', 20000.00, 500, 'Tập đầu tiên của chú mèo máy đến từ tương lai.', 'DangKD'),
('S006', 'Bố Già', 3, 2018, 'Bìa Cứng', 150000.00, 80, 'Tiểu thuyết kinh điển về thế giới Mafia ở Mỹ.', 'DangKD'),
('S007', 'Chí Phèo', 1, 2015, 'Bìa Mềm', 45000.00, 300, 'Tác phẩm văn học hiện thực xuất sắc của Nam Cao.', 'DangKD'),
('S008', 'Lược Sử Thời Gian', 3, 2021, 'Bìa Cứng', 180000.00, 60, 'Cuốn sách khoa học phổ thông về vũ trụ.', 'DangKD'),
('S009', 'Tư Duy Nhanh Và Chậm', 4, 2019, 'Bìa Mềm', 210000.00, 90, 'Cuốn sách tâm lý học kinh tế học hành vi.', 'DangKD'),
('S010', 'Tôi Thấy Hoa Vàng Trên Cỏ Xanh', 1, 2020, 'Bìa Mềm', 95000.00, 130, 'Truyện dài về tuổi thơ ở một làng quê nghèo.', 'DangKD'),
('S011', 'Dế Mèn Phiêu Lưu Ký', 2, 2023, 'Bìa Cứng', 120000.00, 180, 'Tác phẩm văn học thiếu nhi nổi tiếng của Tô Hoài.', 'DangKD'),
('S012', 'Quẳng Gánh Lo Đi Và Vui Sống', 1, 2021, 'Bìa Mềm', 75000.00, 110, 'Sách kỹ năng giúp vượt qua sự lo âu trong cuộc sống.', 'DangKD'),
('S013', 'Cho Tôi Xin Một Vé Đi Tuổi Thơ', 1, 2018, 'Bìa Mềm', 88000.00, 140, 'Một chuyến tàu đưa độc giả về với tuổi thơ tinh nghịch.', 'DangKD'),
('S014', 'Chiến Tranh Tiền Tệ', 4, 2020, 'Bìa Cứng', 165000.00, 70, 'Bức tranh toàn cảnh về những cuộc chiến tài chính khốc liệt.', 'DangKD'),
('S015', 'Vũ Trụ Trong Vỏ Hạt Dẻ', 3, 2022, 'Bìa Cứng', 195000.00, 45, 'Tiếp nối của Lược Sử Thời Gian, khám phá sâu hơn về vũ trụ.', 'DangKD'),
('S016', 'Shin Cậu Bé Bút Chì Tập 1', 2, 2021, 'Bìa Mềm', 22000.00, 400, 'Câu chuyện hài hước về cậu nhóc Shinnosuke.', 'DangKD'),
('S017', 'Tâm Lý Học Đám Đông', 3, 2019, 'Bìa Mềm', 115000.00, 85, 'Nghiên cứu về tâm lý và hành vi của con người khi ở trong đám đông.', 'DangKD'),
('S018', 'Conan Tập 100', 2, 2023, 'Bìa Mềm', 25000.00, 600, 'Tập thứ 100 của bộ truyện Thám tử lừng danh Conan.', 'DangKD'),
('S019', 'Nhà Đầu Tư Thông Minh', 4, 2021, 'Bìa Cứng', 250000.00, 50, 'Cuốn sách nền tảng về đầu tư giá trị.', 'DangKD'),
('S020', 'Hai Số Phận', 1, 2017, 'Bìa Cứng', 135000.00, 95, 'Tiểu thuyết về cuộc đời của hai con người có xuất thân trái ngược.', 'DangKD'),
('S021', 'Đảo Mộng Mơ', 1, 2020, 'Bìa Mềm', 82000.00, 160, 'Câu chuyện về trí tưởng tượng phong phú của tuổi thơ.', 'DangKD'),
('S022', 'Tuổi Trẻ Đáng Giá Bao Nhiêu', 4, 2018, 'Bìa Mềm', 80000.00, 200, 'Những chia sẻ chân thành về việc tận dụng tuổi thanh xuân.', 'DangKD'),
('S023', 'Khởi Nghiệp Tinh Gọn', 3, 2021, 'Bìa Mềm', 145000.00, 80, 'Cách tiếp cận mới để đổi mới liên tục trong kinh doanh.', 'DangKD');

-- ========================================================
-- 5. NỐI SÁCH VỚI TÁC GIẢ (Bảng Sach_TacGia)
-- ========================================================
INSERT INTO Sach_TacGia (maSach, maTG) VALUES
('S004', 4), ('S010', 4), ('S013', 4), ('S021', 4), -- Nguyễn Nhật Ánh
('S005', 5), -- Fujiko
('S006', 6), -- Mario Puzo
('S007', 7), -- Nam Cao
('S008', 8), ('S015', 8), -- Stephen Hawking
('S009', 9), -- Daniel Kahneman
('S011', 10), -- Tô Hoài
('S012', 1), -- Dale Carnegie (ID = 1 từ dữ liệu cũ của ông)
('S016', 5), -- Shin (Mượn tạm tác giả Fujiko cho đủ data test)
('S018', 11); -- Gosho Aoyama

-- ========================================================
-- 6. NỐI SÁCH VỚI THỂ LOẠI (Bảng Sach_TheLoai)
-- (Sử dụng lại ID 1-5 của ông và 6-7 mới thêm)
-- ========================================================
INSERT INTO Sach_TheLoai (maSach, maTL) VALUES
('S004', 2), ('S006', 2), ('S007', 2), ('S010', 2), ('S013', 2), ('S020', 2), ('S021', 2), -- Văn học (2)
('S005', 7), ('S016', 7), ('S018', 7), -- Truyện tranh (7)
('S008', 4), ('S015', 4), -- Khoa học (4)
('S009', 5), ('S017', 5), -- Tâm lý học (5)
('S011', 6), -- Thiếu nhi (6)
('S012', 1), ('S022', 1), -- Kỹ năng sống (1)
('S014', 3), ('S019', 3), ('S023', 3); -- Kinh tế (3)

-- ========================================================
-- 7. THÊM HÌNH ẢNH SÁCH
-- ========================================================
INSERT INTO HinhAnhSach (maSach, urlAnh) VALUES
('S004', 'https://picsum.photos/seed/s004/300/400'),
('S005', 'https://picsum.photos/seed/s005/300/400'),
('S006', 'https://picsum.photos/seed/s006/300/400'),
('S007', 'https://picsum.photos/seed/s007/300/400'),
('S008', 'https://picsum.photos/seed/s008/300/400'),
('S009', 'https://picsum.photos/seed/s009/300/400'),
('S010', 'https://picsum.photos/seed/s010/300/400'),
('S011', 'https://picsum.photos/seed/s011/300/400'),
('S012', 'https://picsum.photos/seed/s012/300/400'),
('S013', 'https://picsum.photos/seed/s013/300/400'),
('S014', 'https://picsum.photos/seed/s014/300/400'),
('S015', 'https://picsum.photos/seed/s015/300/400'),
('S016', 'https://picsum.photos/seed/s016/300/400'),
('S017', 'https://picsum.photos/seed/s017/300/400'),
('S018', 'https://picsum.photos/seed/s018/300/400'),
('S019', 'https://picsum.photos/seed/s019/300/400'),
('S020', 'https://picsum.photos/seed/s020/300/400'),
('S021', 'https://picsum.photos/seed/s021/300/400'),
('S022', 'https://picsum.photos/seed/s022/300/400'),
('S023', 'https://picsum.photos/seed/s023/300/400');