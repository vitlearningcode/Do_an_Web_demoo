# 📚 BookSM - Hệ Thống Quản Lý Cửa Hàng Sách

## 📖 Giới thiệu dự án
BookSM là một hệ thống quản lý cửa hàng sách toàn diện, được thiết kế và tối ưu hóa từ khâu nhập kho, quản lý công nợ nhà cung cấp cho đến quy trình mua hàng và chạy chiến dịch Marketing (Flash Sale). Dự án được phát triển nhằm đáp ứng các yêu cầu nghiệp vụ thực tế khắt khe nhất của quy trình thương mại điện tử.

## 🚀 Công nghệ sử dụng
* **Frontend:** HTML, CSS, JavaScript
* **Backend:** PHP
* **Cơ sở dữ liệu:** MySQL (Cloud Hosting: Aiven)
* **Môi trường Web Server:** XAMPP (Apache)

---

## ⚙️ Logic Nghiệp Vụ Cốt Lõi (Business Logic)

### 1. Quản lý Nhập Hàng & Công Nợ (Kho & Kế Toán)
* **Khởi tạo Phiếu Nhập:** Tự động lấy chiết khấu mặc định từ Nhà Cung Cấp áp dụng cho từng đầu sách. Hệ thống tự tính tổng số lượng và tổng tiền, khởi tạo ở trạng thái `Waiting`.
* **Thanh toán & Cập nhật:** Quản lý lịch sử chi tiền cho từng phiếu nhập. Khi số tiền thanh toán đạt đủ tổng giá trị phiếu, hệ thống tự động chuyển trạng thái thành `Completed` và cộng dồn số lượng sách vào Tồn Kho.
* **Xử lý hoàn trả:** Hỗ trợ hủy phiếu nhập (`Returned`) và tự động đối soát lại số lượng tồn kho.

### 2. Thuật toán Flash Sale (Marketing)
* **Lọc hàng tồn:** Tự động quét và trích xuất ngẫu nhiên các tựa sách có thời gian lưu kho vượt ngưỡng (ví dụ: > 6 tháng).
* **Phân bổ giá ưu đãi:** Áp dụng ngẫu nhiên các mức giảm (10%, 22%, 33%) cho danh sách sách đã lọc theo từng khung giờ thiết lập trước.
* **Hiển thị Real-time:** Giá Flash Sale tự động gạch ngang giá gốc trên giao diện người dùng và tự động khôi phục khi hết hạn khung giờ.

### 3. Quy trình Mua Hàng & Xử Lý Đơn (Bán Hàng)
* **Sổ địa chỉ linh hoạt:** Khách hàng có thể lưu trữ và quản lý nhiều địa chỉ giao hàng, thiết lập địa chỉ mặc định.
* **Chốt đơn & Tạm giữ kho:** Đơn hàng khởi tạo ở trạng thái `ChoDuyet`. Khi Admin xác nhận chuyển sang `DangGiao`, hệ thống sẽ tạm trừ số lượng tồn kho để giữ hàng.
* **Hoàn tất & Đánh giá:** Khi đơn hàng đạt trạng thái `HoanThanh`, khách hàng được cấp quyền đánh giá và chấm điểm (1-5 sao) cho từng tựa sách đã mua. Nếu đơn bị hủy, hệ thống tự động hoàn trả số lượng vào kho.

### 4. Quản trị Danh mục Sản Phẩm Tối Ưu
* **Đa Thể Loại & Đa Tác Giả:** Một tựa sách được phép gắn với nhiều tác giả và nhiều thể loại khác nhau, tránh lặp lại dữ liệu.
* **Quản lý Media:** Hỗ trợ upload nhiều hình ảnh cho một tựa sách (bìa trước, mục lục, bìa sau) phục vụ cho tính năng Slider Gallery.

---

## 🗄️ Cấu Trúc Cơ Sở Dữ Liệu (Database Schema)
Hệ thống được thiết kế chuẩn hóa với **22 bảng**, đảm bảo tính toàn vẹn dữ liệu và tối ưu hiệu suất truy vấn.

**1. Khối Người Dùng & Phân Quyền**
* `NguoiDung`: Thông tin cá nhân khách hàng/admin.
* `TaiKhoan`: Thông tin đăng nhập và trạng thái bảo mật.
* `VaiTro`: Quản lý phân quyền hệ thống.
* `DiaChiGiaoHang`: Sổ địa chỉ của khách hàng.

**2. Khối Sản Phẩm & Danh Mục**
* `Sach`, `TheLoai`, `NhaXuatBan`, `TacGia`
* Bảng trung gian giải quyết quan hệ n-n: `Sach_TheLoai`, `Sach_TacGia`
* `HinhAnhSach`: Quản lý bộ sưu tập ảnh của từng sản phẩm.

**3. Khối Bán Hàng & Marketing**
* `DonHang`, `ChiTietDH`, `PhuongThucThanhToan`
* `KhuyenMai`, `ChiTietKhuyenMai`: Lõi xử lý logic Flash Sale.
* `DanhGiaSach`: Quản lý Review & Rating.

**4. Khối Nhập Kho & Công Nợ**
* `NhaCungCap`, `PhieuNhap`, `ChiTietPN`
* `CongNo`: Quản lý tổng nợ theo nhà cung cấp.
* `LichSuThanhToanPN`: Dòng tiền chi ra chi tiết cho từng phiếu nhập.





























##📦 1. Logic Nghiệp vụ Nhập Hàng & Thanh toán Công nợ (Khối Admin)
Đây là "trái tim" của hệ thống quản lý kho, được thiết kế theo đúng logic xịn sò của bro:

Tạo Phiếu Nhập: Admin chọn Nhà Cung Cấp -> Chọn danh sách sách cần nhập. Hệ thống tự động kéo chietKhauMacDinh từ bảng NhaCungCap điền vào từng cuốn sách, tính toán ra tongLuongNhap và tongTien. Lưu trạng thái ban đầu là Waiting.

Thanh toán Phiếu Nhập: Admin vào xem phiếu Waiting, nhập số tiền xuất quỹ trả cho NCC.

Hệ thống lưu lịch sử chi tiền vào LichSuThanhToanPN.

Cộng dồn số tiền đó vào soTienDaThanhToan của Phiếu Nhập.

Kiểm tra: Nếu soTienDaThanhToan >= tongTien -> Tự động chuyển trạng thái phiếu thành Completed.

Cập nhật Tồn Kho: Ngay khi phiếu chuyển sang Completed, hệ thống lập tức chạy vòng lặp, lấy số lượng sách mới nhập cộng dồn vào cột soLuongTon trong bảng Sach.

Xử lý Hủy (Returned): Nếu phiếu nhập bị lỗi, Admin nhấn Hủy. Hệ thống hoàn lại tiền, cập nhật trạng thái Returned và trừ ngược lại số lượng tồn kho (nếu trước đó đã lỡ cộng).

⚡ 2. Logic Thuật toán Flash Sale (Khối Marketing)
Thiết lập Khung Giờ: Admin tạo một chiến dịch Khuyến Mãi mới (có ngày/giờ bắt đầu và kết thúc).

Thuật toán Lọc Sản Phẩm: Quét trong kho những cuốn sách có thời gian lưu kho quá hạn mức (ví dụ > 6 tháng). Hệ thống ngẫu nhiên bốc ra 10-20 cuốn đưa vào hàng đợi.

Áp mức giảm giá: Phân bổ ngẫu nhiên mức giảm 10%, 22%, hoặc 33% cho các cuốn sách này và lưu thẳng vào bảng ChiTietKhuyenMai.

Hiển thị Real-time: Khi khách hàng truy cập web vào đúng khung giờ đó, giao diện tự động lấy giaBan trừ đi phanTramGiam, hiển thị giá gạch ngang và label Flash Sale. Qua giờ, tự động trở về giá gốc.

🛒 3. Logic Mua Hàng & Xử lý Đơn Hàng (Khối Khách hàng & Admin)
Quản lý Sổ Địa Chỉ: Trước khi mua, khách hàng có thể thêm nhiều địa chỉ giao hàng (Nhà riêng, Công ty) và set 1 địa chỉ làm laMacDinh.

Xử lý Giỏ hàng & Chốt đơn: Khách chọn sách, chọn địa chỉ, chọn phương thức thanh toán. Hệ thống chốt giá (bao gồm cả giá Flash Sale nếu đang trong khung giờ), lưu vào DonHang với trạng thái ChoDuyet.

Quy trình Duyệt & Giao Hàng:

Bước 1: Admin xác nhận -> Chuyển sang DangGiao. Hệ thống tạm thời trừ số lượng tồn kho trong bảng Sach để giữ hàng.

Bước 2: Khách nhận hàng -> Chuyển sang HoanThanh.

Trường hợp Hủy: Nếu khách/Admin hủy đơn (DaHuy), hệ thống tự động cộng trả lại số lượng tồn kho như cũ.

Đánh Giá (Review): Khách hàng chỉ được phép chấm điểm (1-5 sao) và bình luận cho những cuốn sách nằm trong đơn hàng đã HoanThanh.

📚 4. Logic Quản lý Danh mục Tối ưu
Xử lý đa Tác Giả & Thể Loại: Khi thêm 1 tựa sách mới, Admin có thể tick chọn nhiều Thể Loại (Hành động, Tâm lý...) và nhiều Tác Giả cùng lúc. Dữ liệu sẽ đổ trơn tru vào 2 bảng trung gian Sach_TheLoai và Sach_TacGia.

Quản lý Hình Ảnh: Cho phép upload tối đa 4-5 tấm ảnh cho 1 cuốn sách (Bìa trước, Mục lục, Bìa sau) lưu vào HinhAnhSach để hiển thị Slider động trên trang chi tiết sản phẩm
