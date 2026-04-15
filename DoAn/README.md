# 📚 Book Sales Management — Tài liệu luồng hệ thống

> **Kiến trúc:** Thuần PHP
> **Bảo mật:** No-Trust Client — giá, tồn kho luôn lấy từ DB; client không được tin.  
> **Session:** `session_start()` gọi ở mọi entry point trước bất kỳ output nào.

---

## Mục lục

1. [Luồng chính: Trang chủ (index.php)](#1-luồng-chính-trang-chủ)
2. [Luồng xác thực: Đăng ký / Đăng nhập / Đăng xuất](#2-luồng-xác-thực)
3. [Luồng giỏ hàng (Khách / Đã đăng nhập)](#3-luồng-giỏ-hàng)
4. [Luồng thanh toán](#4-luồng-thanh-toán)
5. [Luồng chi tiết sách & đánh giá](#5-luồng-chi-tiết-sách--đánh-giá)
6. [Luồng trang thể loại](#6-luồng-trang-thể-loại)
7. [Luồng quản lý đơn hàng (Khách hàng)](#7-luồng-quản-lý-đơn-hàng)
8. [Luồng Admin (Chủ cửa hàng)](#8-luồng-admin)
9. [Kiến trúc bảo mật giá (No-Trust)](#9-kiến-trúc-bảo-mật-giá)
10. [Cấu trúc Session](#10-cấu-trúc-session)
11. [Cấu trúc thư mục](#11-cấu-trúc-thư-mục)

---

## 1. Luồng chính: Trang chủ

### 1a. Luồng CHÍNH — Người dùng đã đăng nhập

```
[Browser] GET /DoAn/index.php
    │
    ▼
[index.php]
    ├─ session_start()                          ← Bắt buộc đầu tiên
    ├─ require db.php                           ← Kết nối PDO → $pdo
    ├─ require taiFlashSale.php                 ← Query DB: flash sale đang chạy
    ├─ require taiSachBanChay.php               ← Query DB: top sách bán chạy
    ├─ require taiSachMoi.php                   ← Query DB: sách mới nhất
    ├─ require taiDanhSach_DanhMuc.php          ← Query DB: danh mục
    ├─ require taiQuangCao.php                  ← Query DB: banner quảng cáo
    ├─ require khoiDauTrangChu.php
    │       └─ $isLoggedIn = isset($_SESSION['nguoi_dung_id'])
    │       └─ $phai_xoa_cart = kiểm tra cookie/session 'xoa_cart_local'
    │
    ├─ [IF $isLoggedIn]
    │       └─ require layGioHangCoGia.php      ← Query DB: giỏ hàng + giá + tồn kho
    │               Output: $cartServerDataJson, $giaSachMapJson, $tonKhoMapJson
    │
    ├─ PHP render HTML (SSR):
    │       ├─ <script>var cartServerData = <?= $cartServerDataJson ?></script>
    │       ├─ <script>var __giaSach = <?= $giaSachMapJson ?></script>
    │       ├─ <script>var __tonKhoMap = <?= $tonKhoMapJson ?></script>
    │       ├─ khuVucHeroBanner.php             ← HTML banner slides từ DB
    │       ├─ khuVucFlashSale.php              ← HTML flash sale từ DB
    │       ├─ khuVucSachBanChay.php            ← HTML sách bán chạy
    │       └─ khuVucSachMoi.php                ← HTML sách mới
    │
    └─ JS load (theo thứ tự):
            ├─ thongBao.js       ← Khởi tạo toast notification
            ├─ trinhChieuBanner.js ← Tự động chạy banner slider
            ├─ bookCard.js       ← Gắn sự kiện quick view cho .book-card
            ├─ cart.js           ← init(): đọc cartServerData → render cart drawer
            ├─ xacThuc.js        ← Khởi tạo modal đăng nhập/đăng ký
            ├─ xacNhanDangXuat.js ← Khởi tạo modal đăng xuất
            ├─ chatbot.js        ← Khởi tạo chatbot
            ├─ btnDanhMuc.js     ← Xử lý nút lọc danh mục
            ├─ btnThemGioHang.js ← Đăng ký window.themVaoGioHang()
            ├─ app.js            ← Khởi tạo tìm kiếm, yêu thích, dropdown
            ├─ xemNhanhSach.js   ← Khởi tạo quick view modal
            └─ scriptTrangChu.php:
                    ├─ new TrinhChieuBanner('hero-slider')  ← Khởi động banner
                    └─ setInterval(demNguoc, 1000)           ← Đồng hồ flash sale
```

### 1b. Luồng PHỤ — Khách chưa đăng nhập

```
[Browser] GET /DoAn/index.php
    │
    ▼ (tương tự trên, bỏ require layGioHangCoGia.php)
    │
    ├─ <script>var cartServerData = null</script>   ← JS biết đây là khách
    └─ cart.js init():
            └─ cartServerData === null
              
```

---

## 2. Luồng xác thực

### 2a. Đăng nhập

```
[Browser] 
    │  User nhập form trong modal (xacThuc.js render HTML có sẵn từ modalDangNhap.php)
    │  Submit POST → method="POST" action="/PhienDangNhap/xuly_dangnhap.php"
    ▼
[xuly_dangnhap.php]
    ├─ session_start()
    ├─ require db.php
    ├─ Nhận: $_POST['tendangnhap'], $_POST['matkhau']
    ├─ Hash: $matkhau = md5($_POST['matkhau'])       ← Hash một chiều MD5
    ├─ Query: SELECT TaiKhoan JOIN NguoiDung JOIN VaiTro
    │         WHERE tenDN = ? AND matKhau = ? AND trangThai = 'on'
    │
    ├─ [Nếu tìm thấy user]
    │       ├─ $_SESSION['nguoi_dung_id']  = $user['maND']
    │       ├─ $_SESSION['tendangnhap']    = $user['tenDN']
    │       ├─ $_SESSION['ten_nguoi_dung'] = $user['tenND']
    │       ├─ $_SESSION['vaitro']         = $user['tenVT']
    │       │
    │       ├─ Query GioHang DB → Khôi phục giỏ hàng:
    │       │       $_SESSION['cart'] = [{maSach, soLuong}, ...]
    │       │       (KHÔNG lưu giaBan vào session — bảo mật giá)
    │       │
    │       └─ [IF vaiTro == 'admin']
    │               header → ChuCuaHang/index.php
    │           [ELSE]
    │               header → index.php
    │
    └─ [Nếu sai] echo <script>alert(...); history.back()</script>
```

### 2b. Đăng ký

```
[Browser] Submit POST → xuly_dangky.php
    ▼
[xuly_dangky.php]
    ├─ session_start() + require db.php
    ├─ Nhận: hoten, tendangnhap, email, sodienthoai, matkhau
    ├─ md5(matkhau)
    ├─ Kiểm tra trùng: TaiKhoan.tenDN, NguoiDung.email/sdt
    ├─ $pdo->beginTransaction()
    │       ├─ INSERT NguoiDung (tenND, sdt, email)
    │       ├─ $maND = lastInsertId()
    │       └─ INSERT TaiKhoan (tenDN, matKhau, maND, maVT='Khách hàng', trangThai='on')
    ├─ $pdo->commit()
    └─ [Lỗi] $pdo->rollBack()
```

### 2c. Đăng xuất

```
[Browser] Click "Đăng xuất" → xuly_dangxuat.php
    ▼
[xuly_dangxuat.php]
    ├─ session_start()
    ├─ setcookie('xoa_cart_local', '1', time()+60, '/')  ← Báo JS xóa localStorage
    ├─ session_unset() + session_destroy()               ← Xóa toàn bộ session
    └─ header → index.php
            └─ khoiDauTrangChu.php phát hiện cookie 'xoa_cart_local'
               → PHP render: <script>localStorage.removeItem('book_cart')</script>
               → Xóa cookie
```

---

## 3. Luồng giỏ hàng

### 3a. Thêm sản phẩm vào giỏ

```
[User click nút "Thêm giỏ hàng" trên .book-card]
    │  onclick="themVaoGioHang(event, this)"
    ▼
[btnThemGioHang.js → window.themVaoGioHang()]
    ├─ [Chưa đăng nhập] → alert + openLogin()  ← Dừng
    ├─ [data-ton-kho <= 0] → alert "Hết hàng"  ← Dừng
    ├─ Đọc data từ .book-card:
    │       maSach  = dataset.id
    │       tenSach = dataset.name
    │       giaBan  = window.__giaSach[maSach]  ← Giá từ DB (PHP inject), KHÔNG từ data-price
    │       hinhAnh = dataset.image
    │       tacGia  = dataset.tacGia
    └─ cartDrawer.addItem(thongTinSach, 1)
            ├─ Kiểm tra tồn kho từ window.__tonKhoMap
            ├─ Nếu có sẵn trong giỏ → tăng soLuong (giới hạn tonKho)
            ├─ Nếu mới → push vào cartArr
            ├─ showToast("Đã thêm...")
            └─ saveCart()
                    ├─ localStorage.setItem('book_cart', JSON)   ← Lưu local
                    └─ [Đã đăng nhập] syncForm.submit()          ← Đẩy lên server
                            └─ POST cart_json → luuGioHang.php   ← Đồng bộ DB
```

### 3b. Đồng bộ giỏ hàng lên server (hidden form + iframe)

```
[cart.js saveCart()]
    │  syncJson.value = JSON.stringify(cartArr)
    │  syncForm.submit()  ← Form target="_blank" hidden iframe
    ▼
[GioHang/luuGioHang.php]
    ├─ session_start()
    ├─ [Chưa đăng nhập] → 403 exit
    ├─ Nhận: $_POST['cart_json']
    ├─ json_decode → $cartArr
    ├─ $pdo->beginTransaction()
    │       ├─ DELETE FROM GioHang WHERE maND = ?       ← Xóa giỏ cũ
    │       └─ INSERT GioHang (maND, maSach, soLuong)   ← Chèn giỏ mới
    │          ON DUPLICATE KEY UPDATE soLuong
    │          (KHÔNG lưu giaBan — bảo mật giá)
    ├─ $pdo->commit()
    └─ $_SESSION['cart'] = [{maSach, soLuong}]  ← Cập nhật session (không có giá)
```

### 3c. Khôi phục giỏ hàng khi đăng nhập

```
[xuly_dangnhap.php] (sau khi xác thực thành công)
    │
    └─ Query: SELECT GioHang JOIN Sach WHERE maND = ?
       $_SESSION['cart'] = [{maSach, soLuong}]
       (Giá KHÔNG lưu vào session)
```

---

## 4. Luồng thanh toán

### 4a. Truy cập trang thanh toán

```
[User click "Thanh toán" trong cart drawer]
    │  href="ThanhToan/thanhToan.php"
    ▼
[thanhToan.php]
    ├─ session_start() + require db.php
    ├─ require kiemTraGioHang.php:
    │       ├─ Đọc $_SESSION['cart'] hoặc $_SESSION['cart_temp']
    │       ├─ [Giỏ rỗng] → alert + redirect index.php
    │       ├─ Query DB: lấy giá thật (có flash sale nếu có)
    │       │       giaBan = COALESCE(giaSau_flash_sale, giaBan_goc)
    │       ├─ $_SESSION['cart_temp'] = $gioHang  ← Lưu giỏ đã xác thực
    │       └─ $tongTien = sum(giaBan * soLuong)  ← Tính toán phía server
    │
    ├─ require layThongTinKhachHang.php  ← Nếu đăng nhập: query thông tin user
    ├─ require danhSachDiaChi.php        ← Danh sách địa chỉ đã lưu
    └─ Render HTML form thông tin giao hàng + tóm tắt giỏ (giá từ DB)
```

### 4b. Đặt hàng

```
[User submit form → POST xuLyThanhToan.php]
    ▼
[xuLyThanhToan.php]
    ├─ session_start() + require db.php
    ├─ [REQUEST_METHOD != POST hoặc cart_temp rỗng] → die()
    ├─ $gioHang = $_SESSION['cart_temp']            ← Lấy giỏ đã xác thực server
    ├─ Nhận: hoten, sdt, email, loai_dia_chi, phuong_thuc
    │
    ├─ XÁC ĐỊNH người dùng:
    │       [Đã đăng nhập] → $maND = $_SESSION['nguoi_dung_id']
    │       [Khách vãng lai] → die() -> return index
    │
    ├─ XÁC ĐỊNH địa chỉ:
    │       [loai='da_luu'] → verify maDC thuộc về user (chống IDOR)
    │       [loai='moi']    → INSERT DiaChiGiaoHang mới
    │
    ├─ $maDonHang = 'DH' . time() . rand(10,99)
    ├─ $pdo->beginTransaction()
    │       ├─ FOR EACH sản phẩm trong $gioHang:
    │       │       ├─ Query DB: soLuongTon, giaBan, giaSau  ← FOR UPDATE (lock row)
    │       │       ├─ [Không đủ kho] → throw Exception → rollBack()
    │       │       ├─ $giaChinh = giaSau ?? giaBan           ← Giá từ DB, KHÔNG từ session
    │       │       └─ UPDATE Sach SET soLuongTon -= soLuong  ← Trừ kho
    │       │
    │       ├─ INSERT DonHang (maDH, maND, maDC, maPT, tongTien, trangThai='ChoDuyet')
    │       └─ INSERT ChiTietDH (maDH, maSach, soLuong, giaBan←DB, thanhTien)
    │
    ├─ $pdo->commit()
    ├─ unset($_SESSION['cart_temp'], $_SESSION['cart'])
    ├─ DELETE FROM GioHang WHERE maND = ?           ← Xóa giỏ DB
    ├─ $_SESSION['xoa_cart_local'] = true           ← Báo JS xóa localStorage
    │
    └─ [phuong_thuc = 2 (QR)] → redirect quetMaQR.php
       [phuong_thuc = 1 (COD)] → redirect thanhCong.php
```

### 4c. Luồng thay thế — Thanh toán QR

```
[xuLyThanhToan.php] → header → quetMaQR.php?maDH=...&tien=...
    ▼
[quetMaQR.php]
    ├─ Hiển thị mã QR tĩnh (hình ảnh)
    └─ Hiển thị số tiền cần chuyển khoản
    (Không có auto-verify — admin xác nhận thủ công)
```

---

## 5. Luồng chi tiết sách & đánh giá

### 5a. Xem nhanh (Quick View)

```
[User click nút "Xem nhanh" trên .book-card]
    │  onclick trong bookCard.js → XuLyTheSach.moXemNhanh()
    ▼
[xemNhanhSach.js]
    ├─ Đọc data-* từ .book-card (maSach, tenSach, giaBan...)
    ├─ Gọi PHP qua iframe hidden:
    │       iframe.src = "ChiTietSach/layDanhGia.php?maSach=..."
    ├─ Cập nhật DOM modal (#quick-view-modal)
    └─ Modal.classList.add('active')
```

### 5b. Trang chi tiết sách

```
[User click vào tên/ảnh sách]
    │  href="ChiTietSach/layChiTietSach.php?maSach=..."
    ▼
[layChiTietSach.php]
    ├─ session_start() + require db.php
    ├─ $maSach = $_GET['maSach']
    ├─ Query: Sach JOIN HinhAnhSach JOIN TacGia JOIN DanhMuc
    ├─ Query: DanhGia (đánh giá của user)
    ├─ [Đã đăng nhập] require layGioHangCoGia.php  ← Giá + tồn kho
    └─ Render toàn bộ trang chi tiết (SSR)
```

### 5c. Gửi đánh giá

```
[User submit form đánh giá]
    │  POST → donHang/xuly_danhGia.php
    ▼
[xuly_danhGia.php]
    ├─ Bắt buộc đăng nhập (kiểm tra session)
    ├─ Kiểm tra user đã mua sách và đơn hàng đã COMPLETED
    ├─ INSERT/UPDATE DanhGia (maSach, maND, soSao, noiDung)
    └─ redirect về trang đơn hàng
```

---

## 6. Luồng trang thể loại

```
[User click danh mục]
    │  href="GiaoDien/trangTheLoai.php?danhMuc=..."
    ▼
[trangTheLoai.php]
    ├─ session_start() + require db.php
    ├─ $danhMuc = $_GET['danhMuc'] (lọc theo thể loại)
    ├─ $sapXep  = $_GET['sapXep']  (sắp xếp: giá, mới, bán chạy)
    ├─ Query: Sach WHERE danhMuc = ? ORDER BY ...
    ├─ [Đã đăng nhập] require layGioHangCoGia.php
    └─ Render danh sách sách dạng grid
```

---

## 7. Luồng quản lý đơn hàng

```
[User click "Đơn hàng của tôi"]
    ▼
[donHang/theoDoiDonHang.php]
    ├─ session_start() + require db.php
    ├─ [Chưa đăng nhập] → redirect index.php
    ├─ require hamHoTroDonHang.php    ← Hàm helper
    ├─ require layDuLieuDonHang.php   ← Query đơn hàng theo maND + filter tab
    ├─ require locTabDanhGia.php      ← Lọc đơn cần đánh giá
    └─ Render: khuVucTabDonHang + danhSachTheDonHang + modalDanhGiaSanPham

[User hủy đơn] → POST xuly_huyDon.php
    ├─ Kiểm tra đơn thuộc về user
    ├─ Kiểm tra trangThai = 'ChoDuyet' (chỉ cho hủy khi chờ duyệt)
    ├─ UPDATE DonHang SET trangThai = 'DaHuy'
    └─ UPDATE Sach SET soLuongTon += soLuong  ← Hoàn kho
```

---

## 8. Luồng Admin

### 8a. Xác thực Admin

```
[Admin vào ChuCuaHang/index.php]
    ▼
[ChuCuaHang/_kiemTraQuyen.php]   ← include ở đầu mọi trang admin
    ├─ session_start()
    ├─ [Chưa đăng nhập] → redirect index.php (trang chủ)
    └─ [vaiTro != 'admin'] → redirect index.php
```

### 8b. Quản lý sản phẩm

```
[Admin] → TrangQuanLy/sachVaTonKho.php
    ├─ Xem danh sách sách + tồn kho (Query JOIN nhiều bảng)
    ├─ Thêm/Sửa sách → POST xử lý trong cùng file
    └─ Upload ảnh → lưu đường dẫn vào HinhAnhSach
```

### 8c. Quản lý khuyến mãi & Banner

```
[Admin] → TrangQuanLy/khuyenMai.php
    ├─ Xem danh sách KhuyenMai + ChiTietKhuyenMai
    ├─ include _formQuangCao.php  ← Form dùng chung cho Thêm + Sửa banner
    └─ POST → INSERT/UPDATE KhuyenMai + ChiTietKhuyenMai
```

---

## 9. Kiến trúc bảo mật giá (No-Trust)

Đây là điểm quan trọng nhất của hệ thống:

```
❌ KHÔNG BAO GIỜ làm:
    - Lấy giá từ $_POST['giaBan'] (user có thể sửa form)
    - Lấy giá từ $_SESSION['cart'][i]['giaBan'] nếu session đó do client gửi lên
    - Tính tổng tiền từ data-price trong HTML (user có thể F12 sửa)

✅ LUÔN làm:
    - Giá lấy từ DB tại thời điểm xử lý (kiemTraGioHang, xuLyThanhToan)
    - Áp dụng flash sale từ DB (JOIN ChiTietKhuyenMai WHERE NOW() BETWEEN ngayBD AND ngayKT)
    - Sử dụng FOR UPDATE khi trừ kho (tránh race condition)
    - Verify địa chỉ thuộc về user trước khi dùng (chống IDOR)
```

**Chuỗi giá tin cậy:**

```
DB (Sach.giaBan)
    → layGioHangCoGia.php → PHP inject vào <script>var __giaSach = {...}</script>
    → btnThemGioHang.js đọc window.__giaSach[maSach]  (KHÔNG đọc data-price)
    → cart.js lưu vào localStorage/session
    → kiemTraGioHang.php XÁC THỰC LẠI từ DB (bỏ qua giá trong session)
    → xuLyThanhToan.php XÁC THỰC LẠI từ DB lần nữa (FOR UPDATE)
    → INSERT ChiTietDH.giaBan ← Giá đã xác thực 2 lần
```

---

## 10. Cấu trúc Session

| Key | Kiểu | Mô tả | Được set tại |
|-----|------|--------|--------------|
| `nguoi_dung_id` | int | Mã người dùng | xuly_dangnhap.php |
| `tendangnhap` | string | Tên đăng nhập | xuly_dangnhap.php |
| `ten_nguoi_dung` | string | Họ tên hiển thị | xuly_dangnhap.php |
| `vaitro` | string | 'admin' hoặc 'Khách hàng' | xuly_dangnhap.php |
| `cart` | array | `[{maSach, soLuong}]` — KHÔNG có giaBan | luuGioHang.php, xuly_dangnhap.php |
| `cart_temp` | array | `[{maSach,tenSach,giaBan←DB,soLuong,hinhAnh,tacGia}]` | kiemTraGioHang.php |
| `xoa_cart_local` | bool | Cờ báo JS xóa localStorage sau đăng xuất/thanh toán | xuly_dangxuat.php, xuLyThanhToan.php |

---

## 11. Cấu trúc thư mục

```
DoAn/
├── index.php                          ← Entry point trang chủ
├── KetNoi/
│   ├── config/db.php                  ← Kết nối PDO + load layDuongDanAnh.php
│   └── env_local.php                  ← Biến môi trường XAMPP cục bộ
│
├── CuaHang/
│   ├── PhienDangNhap/
│   │   ├── xuly_dangnhap.php          ← [POST] Đăng nhập → set session
│   │   ├── xuly_dangky.php            ← [POST] Đăng ký → INSERT DB (transaction)
│   │   └── xuly_dangxuat.php          ← Hủy session + set cookie xóa cart
│   │
│   ├── TrangBanHang/
│   │   ├── GioHang/
│   │   │   ├── formGioHang.php        ← HTML cart drawer + sync form/iframe
│   │   │   └── luuGioHang.php         ← [POST] Đồng bộ cart → GioHang DB
│   │   │
│   │   ├── ThanhToan/
│   │   │   ├── thanhToan.php          ← Trang thanh toán (require kiemTraGioHang)
│   │   │   ├── kiemTraGioHang.php     ← [include] Xác thực giỏ + lấy giá DB
│   │   │   ├── xuLyThanhToan.php      ← [POST] Đặt hàng + trừ kho (transaction)
│   │   │   ├── quetMaQR.php           ← Hiển thị QR thanh toán
│   │   │   └── thanhCong.php          ← Màn hình đặt hàng thành công
│   │   │
│   │   ├── ChiTietSach/
│   │   │   ├── layChiTietSach.php     ← Trang chi tiết sách (SSR)
│   │   │   ├── layDanhGia.php         ← [iframe] HTML đánh giá của 1 sách
│   │   │   └── formXemNhanhSach.php   ← HTML quick view modal
│   │   │
│   │   ├── GiaoDien/
│   │   │   ├── header.php             ← Header chung (include dauTrang + panels)
│   │   │   ├── footer.php             ← Footer chung
│   │   │   ├── trangTheLoai.php       ← Trang thể loại sách
│   │   │   └── thanhPhan/             ← Các thành phần con của header/footer
│   │   │
│   │   ├── donHang/
│   │   │   ├── theoDoiDonHang.php     ← Trang quản lý đơn hàng (user)
│   │   │   ├── traDoc.php             ← Tra cứu đơn không cần đăng nhập
│   │   │   ├── xuly_huyDon.php        ← Hủy đơn + hoàn kho
│   │   │   └── xuly_danhGia.php       ← Gửi đánh giá sản phẩm
│   │   │
│   │   ├── taiKhoan/
│   │   │   ├── capNhat.php            ← Trang cập nhật thông tin cá nhân
│   │   │   ├── sachYeuThich.php       ← Danh sách sách yêu thích
│   │   │   └── xuLyCapNhatThongTin.php ← [POST] Cập nhật DB
│   │   │
│   │   └── KhuVucTrungBay/            ← Các "widget" trang chủ
│   │       ├── khuVucHeroBanner.php   ← HTML banner slides
│   │       ├── khuVucFlashSale.php    ← HTML flash sale
│   │       ├── khuVucSachBanChay.php  ← HTML sách bán chạy
│   │       ├── khuVucSachMoi.php      ← HTML sách mới
│   │       ├── khoiDauTrangChu.php    ← Init: $isLoggedIn, $phai_xoa_cart
│   │       ├── taiFlashSale.php       ← Query DB flash sale
│   │       ├── taiSachBanChay.php     ← Query DB sách bán chạy
│   │       ├── taiSachMoi.php         ← Query DB sách mới
│   │       └── scriptTrangChu.php     ← JS: khởi banner + đồng hồ flash sale
│   │
│   └── ChuCuaHang/                    ← Khu vực Admin
│       ├── _kiemTraQuyen.php          ← Middleware kiểm tra quyền admin
│       ├── index.php                  ← Dashboard admin
│       └── TrangQuanLy/               ← Các trang quản lý
│
├── PhuongThuc/                        ← Thư viện PHP + JS
│   ├── layDuongDanAnh.php             ← Helper: tạo đường dẫn ảnh an toàn
│   ├── layGioHangCoGia.php            ← Helper: cartServerData + giaSachMap + tonKhoMap
│   ├── trinhChieuBanner.js            ← Component: banner slider
│   ├── cart.js                        ← Component: giỏ hàng (localStorage + sync)
│   ├── app.js                         ← App-level: tìm kiếm, yêu thích, dropdown
│   ├── btnThemGioHang.js              ← Handler: thêm vào giỏ
│   ├── btnDanhMuc.js                  ← Handler: lọc danh mục
│   ├── xemNhanhSach.js                ← Quick view modal
│   └── components/
│       ├── thongBao.js                ← Toast notification (PopupThongBao)
│       ├── bookCard.js                ← Book card interactions (XuLyTheSach)
│       ├── xacThuc.js                 ← Modal đăng nhập/đăng ký (HopThoaiXacThuc)
│       ├── xacNhanDangXuat.js         ← Modal xác nhận đăng xuất
│       └── chatbot.js                 ← Chatbot AI (TroLyAo)
│
└── GiaoDien/                          ← CSS
    ├── style.css                      ← Entry point: @import tất cả CSS partials
    ├── storefront.css                 ← Layout trang chủ
    ├── books.css                      ← Card sách, grid
    ├── cart.css                       ← Cart drawer
    ├── components.css                 ← Modal, shared components
    └── ... (17 partials khác)
```

---

*Tài liệu được tạo: 2026-04-15 | Phiên bản: 1.0*
