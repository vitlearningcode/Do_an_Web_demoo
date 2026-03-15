1.create table NguoiDung
- maNd(khoa chinh)
- tenNd
- diachi
- sdt
- email


2.create table TaiKhoan
- tenDN(khoa chinh)
- matKhau
- maNd(khoa ngoai)
- mavt(khoa ngoai)

3.create table vaiTro
- mavt(khoa chinh)
- tenvt


4.create table Sach
- maSach(khoa chinh)
- tenSach
- AnhSach
- namSX
- ngonNgu
- moTaSach
- loaiBiaSach (bia mem - bia cung)
- soLuongTon
- giaBan
- khuyenMai
- trangThai (NgungKD - DangKD) 

5.create table Sach_TheLoai
- maSach(khoa chinh)
- maTheLoai(khoa chinh)

6.create table Sach_TacGia
- maSach(khoa chinh)
- maTG(khoa chinh)

7.create table Sach_NXB
- maSach(khoa chinh)
- maNXB(khoa chinh)

8.create table NXB
- maNXB(khoa chinh)
- tenNXB
- diachi

9.Create table TacGia
- maTG(khoa chinh)
- tenTG
- tieuSu

10.Create table TheLoai
- maTheLoai(khoa chinh)
- tenTheLoai

11.create table ncc
- mancc(khoa chinh)
- tenNCC
- diachi
- sdt
- email
- mức Chiếc khấu 

12.create table PhieuNhap
- maPn(khoa chinh)
- tongLuongNhap
- ngaylap
- sotienDaThanhToan
- tongtien
- trangThai (Completed/Waiting/returned) 
- mancc(khoa ngoai)

13.create table chiTietPN
- maPn(khoa chinh)
- masach(khoa chinh)
- soLuongNhap
- giaNhap
- mức Chiếc khấu 
- thanhTien

14.create table PhieuXuat
- maPX(khoa chinh)
- ngaylap
- tongLuongXuat
- tongtien


15.create table chiTietPX
- maPX(khoa chinh)
- maNd(khoa ngoai)
- masach(khoa ngoai)
- soLuong
- giaBan
- tiengiam float
- thanhTien

16.create table DoanhThu 
- maDT(khoa chinh)
- thoigian
- lợi nhuận 
- DoanhThu
- soDon

17.create table congno
- maCn(khoa chinh)
- tieno
- thoihan

18.create table CTCN
- maCn(khoa chinh)
- maPn(khoa chinh)
- mancc(khoa chinh)
- sotienchuathanhtoan
- thoihanno

19.create table DanhGiaSach
- maDGS(khoa chinh)
- diemDG(1*->5*)
- nhanXet
- maSach(khoa ngoai)
- maNd(khoa ngoai)

