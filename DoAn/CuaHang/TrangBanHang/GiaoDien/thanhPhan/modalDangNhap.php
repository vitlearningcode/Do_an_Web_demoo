<?php
/**
 * modalDangNhap.php — Các modal xác thực: Đăng nhập + Đăng ký + Đăng xuất
 * PHP render sẵn HTML, JS chỉ toggle display/class — không innerHTML
 * Yêu cầu: $duong_dan_goc (string) đã được khai báo
 */
?>
<div id="modal-overlay" class="modal-overlay" onclick="closeModal()"></div>

<!-- Modal đăng nhập -->
<div id="login-modal" class="auth-modal login-size">
    <div class="modal-header">
        <h2>Chào mừng trở lại</h2>
        <p>Đăng nhập để tiếp tục mua sắm và theo dõi đơn hàng</p>
        <span class="close-btn" onclick="closeModal()">&times;</span>
    </div>
    <div class="modal-body">
        <form action="<?= $duong_dan_goc ?>xuly_dangnhap.php" method="POST">
            <div class="input-group">
                <label>Tên đăng nhập</label>
                <input type="text" name="tendangnhap" placeholder="Nhập tên đăng nhập" required>
            </div>
            <div class="input-group">
                <label>Mật khẩu</label>
                <input type="password" name="matkhau" placeholder="Nhập mật khẩu" required>
            </div>
            <button type="submit" name="btn_dangnhap" class="btn-primary">Đăng nhập &rarr;</button>
        </form>
    </div>
    <div class="modal-footer">
        <p>Chưa có tài khoản? <a href="javascript:void(0)" onclick="openRegister()">Đăng ký ngay</a></p>
    </div>
</div>

<!-- Modal đăng ký -->
<div id="register-modal" class="auth-modal register-size">
    <div class="modal-header">
        <h2>Tạo tài khoản mới</h2>
        <p>Đăng ký để nhận nhiều ưu đãi hấp dẫn từ Book Sales</p>
        <span class="close-btn" onclick="closeModal()">&times;</span>
    </div>
    <div class="modal-body">
        <form action="<?= $duong_dan_goc ?>xuly_dangky.php" method="POST">
            <div class="input-group">
                <label>Họ và tên</label>
                <input type="text" name="hoten" placeholder="Nhập họ và tên" required>
            </div>
            <div class="input-group">
                <label>Tên đăng nhập</label>
                <input type="text" name="tendangnhap" placeholder="Nhập tên đăng nhập" required>
            </div>
            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Nhập địa chỉ email" required>
            </div>
            <div class="input-group">
                <label>Số điện thoại</label>
                <input type="text" name="sodienthoai" placeholder="Nhập số điện thoại" required>
            </div>
            <div class="input-group">
                <label>Mật khẩu</label>
                <input type="password" name="matkhau" placeholder="Nhập mật khẩu" required>
            </div>
            <button type="submit" name="btn_dangky" class="btn-primary">Đăng ký &rarr;</button>
        </form>
    </div>
    <div class="modal-footer">
        <p>Đã có tài khoản? <a href="javascript:void(0)" onclick="openLogin()">Đăng nhập</a></p>
    </div>
</div>

<!-- Modal đăng xuất -->
<div id="logout-modal" class="auth-modal logout-size">
    <div class="modal-header">
        <h2>Xác nhận đăng xuất</h2>
        <span class="close-btn" onclick="closeModal()" id="logout-modal-close">&times;</span>
    </div>
    <div class="modal-body" style="text-align: center; margin-top: 15px;">
        <p style="font-size: 15px; color: #333; margin-bottom: 30px;">Bạn có chắc chắn muốn đăng xuất khỏi hệ thống không?</p>
        <div class="input-group" style="display: flex; justify-content: space-between; width: 343px; margin: 0 auto;">
            <button type="button" class="btn-cancel" id="logout-cancel" onclick="closeModal()">Hủy bỏ</button>
            <!-- Dùng href tuyệt đối → đúng từ mọi trang. JS xacNhanDangXuat.js cũng gắn listener vào đây -->
            <a href="<?= $duong_dan_goc ?>xuly_dangxuat.php" class="btn-confirm" id="logout-confirm">Đăng xuất</a>
        </div>
    </div>
</div>

