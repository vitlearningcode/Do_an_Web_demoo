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


-- BookSM.SachYeuThich definition

CREATE TABLE "SachYeuThich" (
  "maND" int NOT NULL,
  "maSach" varchar(20) NOT NULL,
  "ngayThem" datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY ("maND","maSach"),
  KEY "fk_syt_sach" ("maSach"),
  CONSTRAINT "fk_syt_nd" FOREIGN KEY ("maND") REFERENCES "NguoiDung" ("maND") ON DELETE CASCADE,
  CONSTRAINT "fk_syt_sach" FOREIGN KEY ("maSach") REFERENCES "Sach" ("maSach") ON DELETE CASCADE
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


-- BookSM.GioHang definition

CREATE TABLE "GioHang" (
  "maND" int NOT NULL,
  "maSach" varchar(20) NOT NULL,
  "soLuong" int NOT NULL DEFAULT '1',
  PRIMARY KEY ("maND","maSach"),
  KEY "fk_gh_sach" ("maSach"),
  CONSTRAINT "fk_gh_nd" FOREIGN KEY ("maND") REFERENCES "NguoiDung" ("maND") ON DELETE CASCADE,
  CONSTRAINT "fk_gh_sach" FOREIGN KEY ("maSach") REFERENCES "Sach" ("maSach") ON DELETE CASCADE,
  CONSTRAINT "chk_gh_sl" CHECK ((`soLuong` > 0))
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