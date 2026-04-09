Dạ đây là toàn bộ mã nguồn sử dụng **PDO** cho 2 file xử lý của bạn. Bạn chỉ cần copy và dán đè vào file tương ứng nhé:

### 1. File `xuly_dangnhap.php`
```php
<?php
session_start();
require_once 'KetNoi/config/db.php'; // File này cung cấp biến $pdo

if (isset($_POST['btn_dangnhap'])) {
    $tendangnhap = $_POST['tendangnhap'];
    $matkhau = $_POST['matkhau']; 

    // Truy vấn kết hợp bảng TaiKhoan, NguoiDung và VaiTro bằng PDO
    $sql = "SELECT tk.tenDN, tk.maND, tk.maVT, vt.tenVT, nd.tenND 
            FROM TaiKhoan tk
            JOIN NguoiDung nd ON tk.maND = nd.maND
            JOIN VaiTro vt ON tk.maVT = vt.maVT
            WHERE tk.tenDN = :tendangnhap 
            AND tk.matKhau = :matkhau 
            AND tk.trangThai = 'on' LIMIT 1";
            
    // Sử dụng Prepare Statement để bảo mật
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'tendangnhap' => $tendangnhap,
        'matkhau' => $matkhau
    ]);
    
    // fetch() sẽ lấy ra 1 dòng dữ liệu dưới dạng mảng
    $user = $stmt->fetch();

    if ($user) {
        // Lưu thông tin vào session
        $_SESSION['nguoi_dung_id'] = $user['maND']; 
        $_SESSION['tendangnhap'] = $user['tenDN'];
        $_SESSION['ten_nguoi_dung'] = $user['tenND'];
        $_SESSION['vaitro'] = $user['tenVT'];

        // Kiểm tra phân quyền dựa theo tên vai trò
        if (strtolower($user['tenVT']) == 'admin') {
            header("Location: CuaHang/ChuCuaHang/index.php");
        } else {
            // Khách hàng -> Về trang chủ
            header("Location: index.php"); 
        }
        exit();
    } else {
        echo "<script>alert('Tên đăng nhập / mật khẩu không đúng, hoặc tài khoản đang bị khóa!'); window.history.back();</script>";
    }
}
?>
```

### 2. File `xuly_dangky.php`
```php
<?php
session_start();
require_once 'KetNoi/config/db.php'; // Cung cấp biến $pdo

if (isset($_POST['btn_dangky'])) {
    $hoten = $_POST['hoten'];
    $tendangnhap = $_POST['tendangnhap'];
    $email = $_POST['email'];
    $sodienthoai = $_POST['sodienthoai'];
    $matkhau = $_POST['matkhau'];

    try {
        // 1. Kiểm tra trùng lặp Tên đăng nhập
        $stmt_tk = $pdo->prepare("SELECT tenDN FROM TaiKhoan WHERE tenDN = :tendangnhap");
        $stmt_tk->execute(['tendangnhap' => $tendangnhap]);
        if ($stmt_tk->fetch()) {
            echo "<script>alert('Tên đăng nhập đã tồn tại!'); window.history.back();</script>";
            exit();
        }

        // 2. Kiểm tra trùng lặp Email hoặc SĐT (Bảng NguoiDung)
        $stmt_nd = $pdo->prepare("SELECT maND FROM NguoiDung WHERE email = :email OR sdt = :sodienthoai");
        $stmt_nd->execute(['email' => $email, 'sodienthoai' => $sodienthoai]);
        if ($stmt_nd->fetch()) {
            echo "<script>alert('Email hoặc Số điện thoại đã được sử dụng!'); window.history.back();</script>";
            exit();
        }

        // 3. Lấy mã Vai Trò của "Khách hàng" để gán mặc định
        $stmt_vt = $pdo->query("SELECT maVT FROM VaiTro WHERE tenVT LIKE '%Khách hàng%' OR tenVT LIKE '%Khach hang%' LIMIT 1");
        $row_vt = $stmt_vt->fetch();
        $maVT = $row_vt ? $row_vt['maVT'] : 2; // Dự phòng ID 2

        // 4. Bắt đầu lưu vào CSDL (Dùng Transaction trong PDO)
        $pdo->beginTransaction();
        
        // Bước A: Thêm vào bảng NguoiDung trước
        $sql_nd = "INSERT INTO NguoiDung (tenND, sdt, email) VALUES (:hoten, :sodienthoai, :email)";
        $stmt_insert_nd = $pdo->prepare($sql_nd);
        $stmt_insert_nd->execute([
            'hoten' => $hoten,
            'sodienthoai' => $sodienthoai,
            'email' => $email
        ]);
        
        // Lấy ID (maND) vừa tự động tăng sinh ra
        $maND = $pdo->lastInsertId();

        // Bước B: Dùng mã maND đó thêm vào bảng TaiKhoan
        $sql_tk = "INSERT INTO TaiKhoan (tenDN, matKhau, maND, maVT, trangThai) 
                   VALUES (:tendangnhap, :matkhau, :maND, :maVT, 'on')";
        $stmt_insert_tk = $pdo->prepare($sql_tk);
        $stmt_insert_tk->execute([
            'tendangnhap' => $tendangnhap,
            'matkhau' => $matkhau,
            'maND' => $maND,
            'maVT' => $maVT
        ]);

        // Xác nhận lưu dữ liệu (Commit)
        $pdo->commit();
        echo "<script>alert('Đăng ký thành công! Vui lòng đăng nhập.'); window.location.href='index.php';</script>";

    } catch (Exception $e) {
        // Nếu có lỗi, hoàn tác toàn bộ (Rollback)
        $pdo->rollBack();
        echo "<script>alert('Lỗi hệ thống: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}
?>
```

Chào bạn, tiếp tục hoàn thiện hệ thống nhé! 

Việc tạo một form xác nhận đăng xuất dạng Modal (popup làm mờ nền) thay vì click vào là đăng xuất ngay lập tức sẽ mang lại trải nghiệm người dùng (UX) rất tốt, tránh trường hợp khách hàng vô tình click nhầm.

Dựa trên cấu trúc sẵn có ở `header.php`, tôi sẽ hướng dẫn bạn thiết kế form này sao cho đồng bộ về kích thước (w=403px) và phong cách với form Đăng nhập / Đăng ký.

### Bước 1: Thêm mã HTML cho Form Đăng xuất
Bạn hãy dán đoạn mã này vào file `header.php` (hoặc nơi bạn đang để mã HTML của form đăng nhập, đăng ký):

```html
<div id="logout-modal" class="auth-modal logout-size">
    <div class="modal-header">
        <h2>Xác nhận đăng xuất</h2>
        <span class="close-btn" onclick="closeModal()">&times;</span>
    </div>
    <div class="modal-body" style="text-align: center; margin-top: 15px;">
        <p style="font-size: 15px; color: #333; margin-bottom: 30px;">Bạn có chắc chắn muốn đăng xuất khỏi hệ thống không?</p>
        
        <div class="input-group" style="display: flex; justify-content: space-between; width: 343px; margin: 0 auto;">
            <button type="button" class="btn-cancel" onclick="closeModal()">Hủy bỏ</button>
            <a href="xuly_dangxuat.php" class="btn-confirm">Đăng xuất</a>
        </div>
    </div>
</div>
```

### Bước 2: Cập nhật CSS
Trong file CSS (ví dụ `style.css`), hãy bổ sung thêm các class này. Chúng ta sẽ chia đôi chiều rộng `343px` ra làm 2 nút (mỗi nút khoảng `164px` và khoảng cách giữa chúng là `15px`).

```css
/* Kích thước riêng cho form Đăng xuất (ngắn hơn các form khác) */
.logout-size {
    width: 403px;
    height: 230px; 
}

/* Nút Hủy bỏ (Màu xám) */
.btn-cancel {
    width: 164px;
    height: 44px;
    background-color: #f1f1f1;
    color: #333;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}

/* Nút Đăng xuất (Màu đỏ cảnh báo) */
.btn-confirm {
    width: 164px;
    height: 44px;
    background-color: #ef4444; 
    color: #ffffff;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    text-align: center;
    line-height: 44px; /* Căn giữa chữ theo chiều dọc */
}

.btn-confirm:hover {
    background-color: #dc2626; /* Màu đỏ đậm hơn khi trỏ chuột vào */
}
```

### Bước 3: Cập nhật JavaScript
Trong đoạn mã `app.js` (hoặc thẻ `<script>` ở cuối trang), bạn cần viết thêm hàm `openLogout()` và cập nhật lại hàm `closeModal()` để nó biết cách đóng cả form đăng xuất.

```javascript
// Hàm mở form đăng xuất
function openLogout() {
    let overlay = document.getElementById('modal-overlay');
    let logoutModal = document.getElementById('logout-modal');

    // Ẩn các modal khác nếu đang mở
    let loginModal = document.getElementById('login-modal');
    let registerModal = document.getElementById('register-modal');
    if (loginModal) loginModal.style.display = 'none';
    if (registerModal) registerModal.style.display = 'none';

    // Hiện modal đăng xuất
    if (overlay && logoutModal) {
        overlay.style.display = 'block';
        logoutModal.style.display = 'block';
    }
}

// Cập nhật lại hàm closeModal (Thêm dòng tắt logout-modal)
function closeModal() {
    let overlay = document.getElementById('modal-overlay');
    let loginModal = document.getElementById('login-modal');
    let registerModal = document.getElementById('register-modal');
    let logoutModal = document.getElementById('logout-modal'); // Thêm dòng này

    if (overlay) overlay.style.display = 'none';
    if (loginModal) loginModal.style.display = 'none';
    if (registerModal) registerModal.style.display = 'none';
    if (logoutModal) logoutModal.style.display = 'none'; // Thêm dòng này
}
```

### Bước 4: Tạo file `xuly_dangxuat.php`
Tạo một file mới cùng cấp với `index.php` và `xuly_dangnhap.php` có tên là `xuly_dangxuat.php`. Nội dung cực kỳ ngắn gọn vì chỉ dùng để xóa Session bằng PHP thuần:

```php
<?php
session_start(); // Bắt buộc phải có để nhận diện session hiện tại

// Xóa tất cả các biến session
session_unset();

// Hủy hoàn toàn session
session_destroy();

// Điều hướng quay trở lại trang chủ
header("Location: index.php");
exit();
?>
```

### Bước 5 (Quan trọng): Chỉnh sửa nút nhấn trong `header.php`
Trong file `header.php` của bạn, nút đăng xuất (khi người dùng đã đăng nhập) đang gọi một hàm JavaScript cũ. Bạn cần sửa lại thuộc tính `onclick`:

**Mã cũ của bạn:**
```html
<button class="action-btn" onclick="xacNhanDangXuat.mo()" style="color: #ef4444;">
    <i class="fas fa-sign-out-alt"></i>
</button>
```

**Mã mới (Thay `xacNhanDangXuat.mo()` thành `openLogout()`):**
```html
<button class="action-btn" onclick="openLogout()" style="color: #ef4444;">
    <i class="fas fa-sign-out-alt"></i>
</button>
```

Vậy là hoàn tất! Bây giờ khi bạn click vào nút Đăng xuất trên thanh điều hướng, một cửa sổ xác nhận sẽ bật lên. Nếu bạn nhấn "Đăng xuất", hệ thống sẽ xóa toàn bộ trạng thái tài khoản và đẩy bạn về trang `index.php`. Bạn thử lưu lại và test xem nhé!