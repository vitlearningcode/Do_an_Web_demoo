# Kế Hoạch Tái Cấu Trúc: Tách File PHP Thành Từng Chức Năng Đơn Lẻ

## Mô Tả

Dự án hiện có nhiều file PHP "đa năng" — mỗi file đảm nhận nhiều chức năng (truy vấn DB, xử lý POST, render HTML, logic JS inline). Yêu cầu là tách tất cả thành **mỗi file chỉ làm đúng 1 việc**, đặt tên file/biến/hàm tiếng Việt.

---

## Điều Cần Lưu Ý

> [!IMPORTANT]
> - **Không dùng AJAX/JSON** — tất cả giao tiếp qua form POST hoặc GET thuần PHP
> - **Không chèn HTML từ JS** — JS chỉ toggle class, điền textContent/value
> - Tất cả tên file, tên hàm PHP, tên biến PHP phải **tiếng Việt** (đã Việt hóa)
> - Sau khi tách → các file gốc chỉ còn `require_once` gọi về các file con

> [!WARNING]
> Các file JS (`.js`) **giữ nguyên** — yêu cầu chỉ áp dụng cho PHP. JS đã thuần tiếng Việt ở phần lớn file.

---

## Phạm Vi Tái Cấu Trúc

### Kiểm Tra Hiện Trạng

| File | Kích Thước | Vấn Đề |
|---|---|---|
| `donHang/index.php` | 386 dòng | Truy vấn DB + xử lý tab + render HTML + inline JS |
| `taiKhoan/capNhat.php` | 305 dòng | Xử lý POST thông tin + POST địa chỉ + lấy dữ liệu + render HTML + inline `<style>` |
| `ThanhToan/thanhToan.php` | 327 dòng | Kiểm tra giỏ hàng + lấy KH + render form + inline `<style>` + inline JS |
| `GiaoDien/header.php` (TrangBanHang) | 311 dòng | Topbar + SearchBox + nút đăng nhập + Modal tra cứu đơn + Modal hỗ trợ + Modal đăng nhập + Modal đăng ký + Modal đăng xuất + Chatbot |
| `GiaoDien/footer.php` (TrangBanHang) | 147 dòng | Footer HTML + Modal auth + Chatbot + Modal đăng xuất + Toast |
| `GiaoDien/header.php` (ChuCuaHang) | 166 dòng | Query DB badge + render `<!DOCTYPE html>` + Sidebar + Topbar |
| `GiaoDien/footer.php` (ChuCuaHang) | 59 dòng | Đóng main + Toast + Overlay + Script hamburger |
| `index.php` (gốc) | 229 dòng | Load dữ liệu + Hero banner + Flash Sale + Categories + BanChay + SachMoi + inline JS |

---

## Kế Hoạch Tách File Chi Tiết

---

### 1. `GiaoDien/header.php` (TrangBanHang) → Tách thành:

#### [MODIFY] [header.php](file:///c:/xampp/htdocs/DoAn-Web/DoAn/CuaHang/TrangBanHang/GiaoDien/header.php)
Chỉ còn `require_once` gọi các file con.

#### [NEW] `CuaHang/TrangBanHang/GiaoDien/thanhPhan/dauTrang.php`
Phần `<div id="dau-trang-co-dinh">` — TopBar + Header + Nav (logo, search, nút giỏ hàng, nút đăng nhập/user dropdown)

#### [NEW] `CuaHang/TrangBanHang/GiaoDien/thanhPhan/panelTraCuuDonHang.php`
Modal/Panel tra cứu đơn hàng (slide-in panel)

#### [NEW] `CuaHang/TrangBanHang/GiaoDien/thanhPhan/panelHoTroKhachHang.php`
Panel hỗ trợ khách hàng (FAQ, hotline)

#### [NEW] `CuaHang/TrangBanHang/GiaoDien/thanhPhan/modalDangNhap.php`
Form đăng nhập (cả login-modal + register-modal + logout-modal theo format hiện tại của header)

#### [NEW] `CuaHang/TrangBanHang/GiaoDien/thanhPhan/scriptDauTrang.php`
JS: `moTraCuuDonHang()`, `dongTraCuuDonHang()`, `moHoTro()`, `dongHoTro()`

---

### 2. `GiaoDien/footer.php` (TrangBanHang) → Tách thành:

#### [MODIFY] [footer.php](file:///c:/xampp/htdocs/DoAn-Web/DoAn/CuaHang/TrangBanHang/GiaoDien/footer.php)
Chỉ còn `require_once` gọi các file con.

#### [NEW] `CuaHang/TrangBanHang/GiaoDien/thanhPhan/khuVucCuoiTrang.php`
HTML footer (logo, links, newsletter)

#### [NEW] `CuaHang/TrangBanHang/GiaoDien/thanhPhan/modalXacNhanDangXuat.php`
Modal xác nhận đăng xuất (đang ở footer)

#### [NEW] `CuaHang/TrangBanHang/GiaoDien/thanhPhan/khungChatbot.php`
HTML khung chatbot (chatbot-toggle + chatbot panel)

#### [NEW] `CuaHang/TrangBanHang/GiaoDien/thanhPhan/thanhPhanToast.php`
`<div id="toast">` thông báo nổi

#### [NEW] `CuaHang/TrangBanHang/GiaoDien/thanhPhan/modalAuthCu.php`
Modal auth dạng cũ (auth-modal) hiện đang bị trùng với header mới

---

### 3. `GiaoDien/header.php` (ChuCuaHang) → Tách thành:

#### [MODIFY] [header.php](file:///c:/xampp/htdocs/DoAn-Web/DoAn/CuaHang/ChuCuaHang/GiaoDien/header.php)
Chỉ còn phần PHP logic + `require_once` gọi các file con.

#### [NEW] `CuaHang/ChuCuaHang/GiaoDien/thanhPhan/demDonChoDuyet.php`
Logic PHP: query DB đếm đơn chờ duyệt → trả `$soDonChoDuyet`

#### [NEW] `CuaHang/ChuCuaHang/GiaoDien/thanhPhan/khuVucSidebar.php`
HTML Sidebar (logo + nav items + footer link)

#### [NEW] `CuaHang/ChuCuaHang/GiaoDien/thanhPhan/khuVucTopbar.php`
HTML Topbar (hamburger + tiêu đề + nút xem trang + user chip)

---

### 4. `GiaoDien/footer.php` (ChuCuaHang) → Tách thành:

#### [MODIFY] [footer.php](file:///c:/xampp/htdocs/DoAn-Web/DoAn/CuaHang/ChuCuaHang/GiaoDien/footer.php)
Chỉ còn `require_once` gọi các file con.

#### [NEW] `CuaHang/ChuCuaHang/GiaoDien/thanhPhan/toastAdminThongBao.php`
HTML toast admin + overlay sidebar

#### [NEW] `CuaHang/ChuCuaHang/GiaoDien/thanhPhan/scriptAdminLayout.php`
JS: hamburger toggle + auto-hide URL flash message + đóng HTML (`</body></html>`)

---

### 5. `donHang/index.php` → Tách thành:

#### [MODIFY] [index.php](file:///c:/xampp/htdocs/DoAn-Web/DoAn/CuaHang/TrangBanHang/donHang/index.php)
Chỉ còn `require_once` các file con + `require_once header/footer`.

#### [NEW] `CuaHang/TrangBanHang/donHang/layDuLieuDonHang.php`
PHP: kiểm tra đăng nhập + lấy `$dsDonHang` + `$chiTietDH` + `$daDanhGia` từ DB

#### [NEW] `CuaHang/TrangBanHang/donHang/locTabDanhGia.php`
PHP: lọc `$dsDonHang` cho tab "Đánh giá"

#### [NEW] `CuaHang/TrangBanHang/donHang/hamHoTroDonHang.php`
PHP function: `thongTinBadge(string $trangThai): array` (đổi tên từ `badgeInfo`)

#### [NEW] `CuaHang/TrangBanHang/donHang/khuVucTabDonHang.php`
HTML: render danh sách tab (tat-ca, cho-duyet, dang-giao...)

#### [NEW] `CuaHang/TrangBanHang/donHang/danhSachTheDonHang.php`
HTML: foreach render từng card đơn hàng + danhsách sản phẩm + nút đánh giá

#### [NEW] `CuaHang/TrangBanHang/donHang/modalDanhGiaSanPham.php`
HTML: modal đánh giá (form POST + star rating)

#### [NEW] `CuaHang/TrangBanHang/donHang/scriptDonHang.php`
JS inline: `moModalDanhGia`, `dongModalDanhGia`, `chonSao`, validate form, toggleUserMenu

---

### 6. `taiKhoan/capNhat.php` → Tách thành:

#### [MODIFY] [capNhat.php](file:///c:/xampp/htdocs/DoAn-Web/DoAn/CuaHang/TrangBanHang/taiKhoan/capNhat.php)
Chỉ còn `require_once` + render khung chính.

#### [NEW] `CuaHang/TrangBanHang/taiKhoan/xuLyCapNhatThongTin.php`
PHP: xử lý POST `hanh_dong_thong_tin` (cập nhật tenND, sdt, email, đổi mật khẩu)

#### [NEW] `CuaHang/TrangBanHang/taiKhoan/xuLyDiaChi.php`
PHP: xử lý POST `hanh_dong_dia_chi` (thêm mới, đặt mặc định, xóa)

#### [NEW] `CuaHang/TrangBanHang/taiKhoan/layThongTinTaiKhoan.php`
PHP: query DB lấy `$nguoiDung`, `$taiKhoan`, `$danhSachDiaChi`

#### [NEW] `CuaHang/TrangBanHang/taiKhoan/formThongTinCaNhan.php`
HTML: form cập nhật họ tên, sdt, email, đổi mật khẩu

#### [NEW] `CuaHang/TrangBanHang/taiKhoan/danhSachDiaChi.php`
HTML: danh sách địa chỉ đã lưu + form đặt mặc định + form xóa

#### [NEW] `CuaHang/TrangBanHang/taiKhoan/formThemDiaChi.php`
HTML: form thêm địa chỉ mới

#### [NEW] `CuaHang/TrangBanHang/taiKhoan/cssDanhSachTaiKhoan.php`
Inline `<style>` block cho trang cập nhật tài khoản (tách từ `<head>`)

---

### 7. `ThanhToan/thanhToan.php` → Tách thành:

#### [MODIFY] [thanhToan.php](file:///c:/xampp/htdocs/DoAn-Web/DoAn/CuaHang/TrangBanHang/ThanhToan/thanhToan.php)
Chỉ còn `require_once` + render khung chính.

#### [NEW] `CuaHang/TrangBanHang/ThanhToan/kiemTraGioHang.php`
PHP: lấy `$gioHang` từ session, kiểm tra trống, tính `$tongTien`

#### [NEW] `CuaHang/TrangBanHang/ThanhToan/layThongTinKhachHang.php`
PHP: lấy thông tin KH + danh sách địa chỉ nếu đã đăng nhập

#### [NEW] `CuaHang/TrangBanHang/ThanhToan/formThongTinNhanHang.php`
HTML: phần form thông tin giao hàng (họ tên, sdt, email, địa chỉ)

#### [NEW] `CuaHang/TrangBanHang/ThanhToan/formPhuongThucThanhToan.php`
HTML: phần chọn phương thức thanh toán (COD, QR)

#### [NEW] `CuaHang/TrangBanHang/ThanhToan/tomTatGioHang.php`
HTML: cột tổng kết đơn hàng (danh sách sản phẩm + tổng tiền)

#### [NEW] `CuaHang/TrangBanHang/ThanhToan/cssThanhToanInline.php`
Inline `<style>` cho trang thanh toán (chọn loại địa chỉ, dropdown)

#### [NEW] `CuaHang/TrangBanHang/ThanhToan/scriptThanhToan.php`
JS: `choiLoaiDiaChi(loai)` — toggle hiển thị địa chỉ

---

### 8. `index.php` (gốc) → Tách thành:

#### [MODIFY] [index.php](file:///c:/xampp/htdocs/DoAn-Web/DoAn/index.php)
Chỉ còn `require_once` các file con + render layout.

#### [NEW] `CuaHang/TrangBanHang/KhuVucTrungBay/khuVucHeroBanner.php`
HTML: section hero banner (slider + nút prev/next + indicators)

#### [NEW] `CuaHang/TrangBanHang/KhuVucTrungBay/khuVucFlashSale.php`
HTML: section Flash Sale (header + đồng hồ + grid sách)

#### [NEW] `CuaHang/TrangBanHang/KhuVucTrungBay/khuVucDanhMuc.php`
HTML: section Danh mục (categories-grid)

#### [NEW] `CuaHang/TrangBanHang/KhuVucTrungBay/khuVucSachBanChay.php`
HTML: section Sách bán chạy nhất

#### [NEW] `CuaHang/TrangBanHang/KhuVucTrungBay/khuVucSachMoi.php`
HTML: section Sách mới phát hành

#### [NEW] `CuaHang/TrangBanHang/KhuVucTrungBay/scriptTrangChu.php`
JS: khởi tạo Banner Slider + đồng hồ đếm ngược Flash Sale

#### [NEW] `CuaHang/TrangBanHang/KhuVucTrungBay/khoiDauTrangChu.php`
PHP: session_start, require db, kiểm tra login, load các require_once data, xử lý xóa cart cookie

---

## Cấu Trúc Thư Mục Sau Khi Tách

```
DoAn/
├── index.php                     ← chỉ còn gọi require_once
│
└── CuaHang/
    ├── ChuCuaHang/
    │   ├── index.php             ← không đổi (router)
    │   └── GiaoDien/
    │       ├── header.php        ← gọi require_once
    │       ├── footer.php        ← gọi require_once
    │       └── thanhPhan/
    │           ├── demDonChoDuyet.php
    │           ├── khuVucSidebar.php
    │           ├── khuVucTopbar.php
    │           ├── toastAdminThongBao.php
    │           └── scriptAdminLayout.php
    │
    └── TrangBanHang/
        ├── GiaoDien/
        │   ├── header.php        ← gọi require_once
        │   ├── footer.php        ← gọi require_once
        │   └── thanhPhan/
        │       ├── dauTrang.php
        │       ├── panelTraCuuDonHang.php
        │       ├── panelHoTroKhachHang.php
        │       ├── modalDangNhap.php
        │       ├── scriptDauTrang.php
        │       ├── khuVucCuoiTrang.php
        │       ├── modalXacNhanDangXuat.php
        │       ├── khungChatbot.php
        │       └── thanhPhanToast.php
        │
        ├── KhuVucTrungBay/
        │   ├── khoiDauTrangChu.php  (NEW)
        │   ├── khuVucHeroBanner.php (NEW)
        │   ├── khuVucFlashSale.php  (NEW)
        │   ├── khuVucDanhMuc.php    (NEW)
        │   ├── khuVucSachBanChay.php (NEW)
        │   ├── khuVucSachMoi.php    (NEW)
        │   ├── scriptTrangChu.php   (NEW)
        │   ├── taiFlashSale.php
        │   ├── taiSachBanChay.php
        │   └── taiSachMoi.php
        │
        ├── donHang/
        │   ├── index.php            ← gọi require_once
        │   ├── layDuLieuDonHang.php (NEW)
        │   ├── locTabDanhGia.php    (NEW)
        │   ├── hamHoTroDonHang.php  (NEW)
        │   ├── khuVucTabDonHang.php (NEW)
        │   ├── danhSachTheDonHang.php (NEW)
        │   ├── modalDanhGiaSanPham.php (NEW)
        │   └── scriptDonHang.php    (NEW)
        │
        ├── taiKhoan/
        │   ├── capNhat.php          ← gọi require_once
        │   ├── xuLyCapNhatThongTin.php (NEW)
        │   ├── xuLyDiaChi.php       (NEW)
        │   ├── layThongTinTaiKhoan.php (NEW)
        │   ├── formThongTinCaNhan.php (NEW)
        │   ├── danhSachDiaChi.php   (NEW)
        │   ├── formThemDiaChi.php   (NEW)
        │   └── cssDanhSachTaiKhoan.php (NEW)
        │
        └── ThanhToan/
            ├── thanhToan.php        ← gọi require_once
            ├── kiemTraGioHang.php   (NEW)
            ├── layThongTinKhachHang.php (NEW)
            ├── formThongTinNhanHang.php (NEW)
            ├── formPhuongThucThanhToan.php (NEW)
            ├── tomTatGioHang.php    (NEW)
            ├── cssThanhToanInline.php (NEW)
            └── scriptThanhToan.php  (NEW)
```

---

## Kế Hoạch Xác Minh

### Kiểm Tra Thủ Công
1. Truy cập `http://localhost/DoAn-Web/DoAn/index.php` — trang chủ hiển thị đúng, slider chạy, flash sale đếm ngược
2. Đăng nhập → kiểm tra giỏ hàng, header user dropdown
3. Vào `/donHang/index.php` → kiểm tra tab, đánh giá sản phẩm
4. Vào `/taiKhoan/capNhat.php` → thêm/xóa địa chỉ, đổi thông tin
5. Vào `/ThanhToan/thanhToan.php` → chọn địa chỉ đã lưu vs mới
6. Truy cập admin `/CuaHang/ChuCuaHang/index.php` → sidebar, topbar

### Kiểm Tra Cú Pháp
```powershell
php -l DoAn/index.php
php -l DoAn/CuaHang/TrangBanHang/donHang/index.php
php -l DoAn/CuaHang/TrangBanHang/taiKhoan/capNhat.php
# ... tất cả file mới
```
