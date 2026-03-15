1.create table NguoiDung
- maNd
- tenNd
- diachi
- sdt
- email


2.create table TaiKhoan
- tenDN
- matKhau

3.create table vaiTro
- mavt
- tenvt

4.create table Sach
- maSach
- theloai
- giaNhap
- soLuongNhap
- soLuongTon
- giaBan
- khuyenMai
- trangThai - NgungKD - DangKD


5.create table chitietthongtinsach
- tenSach
- namSX
- ngonNgu
- moTaSach
- tenNXB
- tenTG
- loaiBiaSach (bia mem - bia cung)


6.create table ncc
- mancc
- tenNCC
- diachi
- sdt
- email
- mức Chiếc khấu 

7.create table PhieuNhap
- maPn
- tenNCC 
- tongLuongNhap
- ngaylap
- sotienDaThanhToan
- tongtien
- trangThai - ENG 

8.create table chiTietPN
- maPn
- mancc
- masach
- soLuongNhap
- giaNhap
- mức Chiếc khấu 
- thanhTien

9.create table PhieuXuat
- maPX
- ngaylap
- tongLuongXuat
- tongtien


10.create table chiTietPX
- maPX
- maNd
- masach
- soLuong
- giaBan
- tiengiam float
- thanhTien

11.create table DoanhThu 
- maDT
- thoigian
- lợi nhuận 
- DoanhThu
- soDon

12.create table congno
- maCn
- tieno
- thoihan

13.create table CTCN
- maCn
- maPn
- 
- sotienchuathanhtoan
- thoihanno

14.create table DanhGiaSach
- mucDanhGia
- nhanXet
- soLuongDG

