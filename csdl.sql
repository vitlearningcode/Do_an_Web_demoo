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
