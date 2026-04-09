# Kế hoạch Thiết kế Giỏ Hàng & Thanh Toán

Dự án yêu cầu xây dựng giỏ hàng và luồng thanh toán hoàn chỉnh bằng PHP thuần (không sử dụng JSON/AJAX qua API), tắt tạm điều kiện đăng nhập, và thiết kế toàn bộ luồng quy trình thanh toán + trừ/hoàn kho.

## 1. Thiết kế Giỏ hàng Modal (Drawer)

*   **Tạo file giao diện:** `DoAn/CuaHang/TrangBanHang/ChiTietSach/formGioHang.php` chứa HTML tĩnh đã được cung cấp (Overlay làm mờ nền, panel bên phải).
*   **CSS:** Tạo `DoAn/GiaoDien/gioHang.css` để chứa các quy tắc hiển thị Drawer Giỏ hàng (dựa trên class cung cấp).
*   **JS Quản lý State:** Thay content cho file `DoAn/PhuongThuc/cart.js` định nghĩa object `cartDrawer`.
*   **Giao tiếp PHP:** JS sẽ lưu các item bằng LocalStorage (phần Client). Khi click thanh toán, tạo một POST form đẩy array qua cho `thanhToan.php`.
*   **Tắt yêu cầu đăng nhập:** Tạm thời vô hiệu hóa khối script chặn trong `btnThemGioHang.js` và `xemNhanhSach.js` khi bấm "Thêm vào giỏ".

## 2. Luồng Thanh Toán (Checkout Flow)

Để đáp ứng yêu cầu luồng "Trừ kho tạm thời - Thanh toán - Hoàn kho nếu hủy", đây là cách hệ thống PHP thuần sẽ vận hành:

1.  **Gửi Giỏ Hàng:** Khi khách bấm "Tiến hành thanh toán" ở Giỏ hàng (Drawer), JS sẽ chuyển thông tin giỏ hàng vào một `input type="hidden"` và `submit()` form qua trang `thanhToan.php`. 
2.  **Khởi tạo đơn tạm & Trừ kho (Hold Stock):**
    *   Trang `thanhToan.php` nhận dữ liệu giỏ hàng, xác minh lại tồn kho (`soLuongTon`).
    *   Tạo ngay 1 Record trong `DonHang` với trạng thái là **`DangThanhToan`**.
    *   **Trừ số lượng tồn** trong bảng `Sach` tương ứng.
3.  **Thu thập thông tin & PPTT:**
    *   Nếu khách hàng đang Login, lấy địa chỉ. Nếu khách lạ trống rỗng.
    *   Khách Hàng chọn phương thức: Tiền mặt hoặc Chuyển khoản QR.
4.  **Xử lý Giao Dịch:**
    *   ***Tiền Mặt:*** Khách xác nhận -> Update trạng thái Đơn hàng thành `ChoDuyet`. Gửi email. Chuyển sang "Màn hình cảm ơn".
    *   ***Chuyển khoản QR:*** Chuyển sang trang mã QR tĩnh (VietQR API). Thêm nút "Tôi đã hoàn tất chuyển khoản" -> Đổi `ChoDuyet` -> Gửi Email. (Giả lập Confirm).
5.  **Hủy giao dịch & Trả kho:**
    *   Nếu khách bấm nút "Hủy thanh toán / Quay về trang chủ", PHP chạy logic lấy những món ở đơn hàng `DangThanhToan` cộng lại vào `Sach` (`soLuongTon = soLuongTon + sl`), sau đó đổi Đơn thành `DaHuy`. Hoặc đơn giản là xóa dòng `Đơn Hang` nháp đó.

## User Review Required

> [!IMPORTANT]
> - **Gửi Email PHP:** Môi trường Localhost (XAMPP) yêu cầu PHPMailer + Mật khẩu ứng dụng Gmail để gửi thành công. Tôi sẽ viết sẵn Code bằng PHPMailer, bạn hãy cho biết là dự án đã `composer require phpmailer/phpmailer` chưa nhé? Hay tôi sẽ viết hàm gửi thư để sẵn và in ra màn hình Alert?
> - **QR Code Chuyển Khoản:** Bạn cần cung cấp sẵn Cú pháp số TK để tôi nhúng VietQR: Ngân hàng gì, Số Tài Khoản, Tên Tài Khoản.
> - **Lỗi Ngưng Phiên bản (Drop-out):** Thông thường khóa Kho như bạn mô tả có rủi ro nếu người dùng **tắt hẳn tab Web** mà không nhấn Hủy, hàng trong kho sẽ bị kẹt vĩnh viễn ở trạng thái `DangThanhToan`. Giải pháp của tôi: Thêm 1 script ở `trangchu` kiểm tra. Nếu User ấy vào lại, lấy đơn kẹt đó hủy và trả kho. Bạn đồng ý không?

## Proposed Changes

### Thay đổi Cửa Hàng

#### [NEW] `c:\xampp\htdocs\DoAn-Web\DoAn\CuaHang\TrangBanHang\ChiTietSach\formGioHang.php`
Chứa mã HTML Drawer bạn vừa gửi.
#### [NEW] `c:\xampp\htdocs\DoAn-Web\DoAn\PhuongThuc\cart.js`
Xử lý LocalStorage, DOM event.
#### [NEW] `c:\xampp\htdocs\DoAn-Web\DoAn\GiaoDien\gioHang.css`
Chức CSS hiển thị cho Giỏ Hàng.
#### [MODIFY] `c:\xampp\htdocs\DoAn-Web\DoAn\index.php`
Tích hợp CSS/JS Giỏ Hàng vào thẻ Head. Load `formGioHang.php` ở Footer.
#### [MODIFY] `c:\xampp\htdocs\DoAn-Web\DoAn\PhuongThuc\btnThemGioHang.js`
Tạm tắt bắt login, gọi trực tiếp `cartDrawer.addItem()`.
#### [MODIFY] `c:\xampp\htdocs\DoAn-Web\DoAn\PhuongThuc\xemNhanhSach.js`
Tạm tắt bắt login Modal Xem nhanh.
