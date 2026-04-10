<?php
/**
 * taiKhoan/capNhat.php — Cập nhật thông tin tài khoản
 * Thuần PHP form POST — không AJAX.
 */
session_start();
require_once '../../../KetNoi/config/db.php';

$isLoggedIn = isset($_SESSION['nguoi_dung_id']);
if (!$isLoggedIn) {
    header('Location: ../../../index.php');
    exit;
}

$maND    = (int)$_SESSION['nguoi_dung_id'];
$thongBao = '';
$loaiTB   = ''; // success | error

// ── Xử lý POST ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hoTen  = trim($_POST['hoten']  ?? '');
    $sdt    = trim($_POST['sdt']    ?? '');
    $email  = trim($_POST['email']  ?? '');
    $mkCu   = trim($_POST['mk_cu']  ?? '');
    $mkMoi  = trim($_POST['mk_moi'] ?? '');
    $mkXN   = trim($_POST['mk_xn']  ?? '');

    $errors = [];
    if (empty($hoTen)) $errors[] = 'Họ tên không được để trống.';

    if (empty($errors)) {
        // Cập nhật thông tin cơ bản
        $stmtU = $pdo->prepare("UPDATE NguoiDung SET tenND = ?, sdt = ?, email = ? WHERE maND = ?");
        $stmtU->execute([$hoTen, $sdt ?: null, $email ?: null, $maND]);
        $_SESSION['ten_nguoi_dung'] = $hoTen;

        // Đổi mật khẩu nếu có nhập
        if (!empty($mkCu)) {
            $stmtTK = $pdo->prepare("SELECT matKhau FROM TaiKhoan WHERE maND = ? LIMIT 1");
            $stmtTK->execute([$maND]);
            $tk = $stmtTK->fetch();

            if (!$tk || $tk['matKhau'] !== $mkCu) {
                $errors[] = 'Mật khẩu cũ không đúng!';
            } elseif ($mkMoi !== $mkXN) {
                $errors[] = 'Mật khẩu mới và xác nhận không khớp!';
            } elseif (strlen($mkMoi) < 1) {
                $errors[] = 'Mật khẩu mới không được để trống!';
            } else {
                $stmtPW = $pdo->prepare("UPDATE TaiKhoan SET matKhau = ? WHERE maND = ?");
                $stmtPW->execute([$mkMoi, $maND]);
            }
        }

        if (empty($errors)) {
            $thongBao = 'Cập nhật thông tin thành công!';
            $loaiTB   = 'success';
        } else {
            $thongBao = implode(' ', $errors);
            $loaiTB   = 'error';
        }
    } else {
        $thongBao = implode(' ', $errors);
        $loaiTB   = 'error';
    }
}

// ── Lấy thông tin hiện tại ──────────────────────────────────────────────────
$stmtND = $pdo->prepare("SELECT tenND, sdt, email FROM NguoiDung WHERE maND = ? LIMIT 1");
$stmtND->execute([$maND]);
$nd = $stmtND->fetch();

$stmtTK2 = $pdo->prepare("SELECT tenDN FROM TaiKhoan WHERE maND = ? LIMIT 1");
$stmtTK2->execute([$maND]);
$tk2 = $stmtTK2->fetch();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập Nhật Thông Tin - Book Sales</title>
    <link rel="stylesheet" href="../../../GiaoDien/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <script>const dangDangNhap = true;</script>
    <script>var cartServerData = <?= json_encode($_SESSION['cart'] ?? [], JSON_UNESCAPED_UNICODE) ?>;</script>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f5f5f5; }
        .cn-page { max-width: 600px; margin: 40px auto; padding: 0 16px 60px; }
        .cn-back { display: inline-flex; align-items: center; gap: 6px; color: #6b7280; font-size: .875rem; text-decoration: none; margin-bottom: 20px; }
        .cn-back:hover { color: #ee4d2d; }
        .cn-card { background: #fff; border-radius: 14px; padding: 32px; box-shadow: 0 1px 8px rgba(0,0,0,.08); }
        .cn-card h1 { font-size: 1.3rem; font-weight: 700; margin: 0 0 6px; color: #111; }
        .cn-card .sub { color: #6b7280; font-size: .875rem; margin-bottom: 28px; }
        .cn-avatar { width: 70px; height: 70px; border-radius: 50%; background: linear-gradient(135deg,#6366f1,#8b5cf6); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.8rem; font-weight: 700; margin: 0 auto 20px; }
        .cn-sep { border: none; border-top: 1px solid #f3f4f6; margin: 24px 0; }
        .cn-group { margin-bottom: 18px; }
        .cn-group label { display: block; font-size: .85rem; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .cn-group input {
            width: 100%; padding: 10px 14px; border: 1.5px solid #e5e7eb; border-radius: 8px;
            font-size: .9rem; font-family: inherit; outline: none; transition: border-color .2s;
        }
        .cn-group input:focus { border-color: #6366f1; }
        .cn-hint { font-size: .78rem; color: #9ca3af; margin-top: 4px; }
        .cn-submit {
            width: 100%; padding: 12px; background: #ee4d2d; color: #fff; border: none;
            border-radius: 8px; font-size: 1rem; font-weight: 700; cursor: pointer;
            font-family: inherit; transition: background .2s; margin-top: 8px;
        }
        .cn-submit:hover { background: #c73e20; }
        .cn-tb { padding: 12px 16px; border-radius: 8px; font-size: .9rem; margin-bottom: 20px; font-weight: 500; }
        .cn-tb.success { background: #dcfce7; color: #15803d; }
        .cn-tb.error   { background: #fee2e2; color: #dc2626; }
        .cn-sec-title { font-size: .95rem; font-weight: 700; color: #374151; margin: 0 0 16px; }
    </style>
</head>
<body>
<?php include_once '../../../CuaHang/TrangBanHang/GiaoDien/header.php'; ?>

<div class="cn-page">
    <a href="../../../index.php" class="cn-back"><i class="fas fa-arrow-left"></i> Quay lại cửa hàng</a>

    <div class="cn-card">
        <div class="cn-avatar"><?= mb_strtoupper(mb_substr($nd['tenND'] ?? 'U', 0, 1, 'UTF-8'), 'UTF-8') ?></div>
        <h1 style="text-align:center">Cập Nhật Thông Tin</h1>
        <p class="sub" style="text-align:center">Tài khoản: <strong><?= htmlspecialchars($tk2['tenDN'] ?? '') ?></strong></p>

        <?php if ($thongBao): ?>
        <div class="cn-tb <?= $loaiTB ?>">
            <i class="fas fa-<?= $loaiTB === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
            <?= htmlspecialchars($thongBao) ?>
        </div>
        <?php endif; ?>

        <form action="" method="POST">
            <p class="cn-sec-title"><i class="fas fa-user"></i> Thông tin cá nhân</p>

            <div class="cn-group">
                <label for="hoten">Họ và tên</label>
                <input type="text" id="hoten" name="hoten" value="<?= htmlspecialchars($nd['tenND'] ?? '') ?>" required>
            </div>
            <div class="cn-group">
                <label for="sdt">Số điện thoại</label>
                <input type="tel" id="sdt" name="sdt" value="<?= htmlspecialchars($nd['sdt'] ?? '') ?>" placeholder="0912345678">
            </div>
            <div class="cn-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($nd['email'] ?? '') ?>" placeholder="example@email.com">
            </div>

            <hr class="cn-sep">
            <p class="cn-sec-title"><i class="fas fa-lock"></i> Đổi mật khẩu <small style="font-weight:400;color:#9ca3af">(để trống nếu không đổi)</small></p>

            <div class="cn-group">
                <label for="mk_cu">Mật khẩu cũ</label>
                <input type="password" id="mk_cu" name="mk_cu" placeholder="Nhập mật khẩu hiện tại">
            </div>
            <div class="cn-group">
                <label for="mk_moi">Mật khẩu mới</label>
                <input type="password" id="mk_moi" name="mk_moi" placeholder="Nhập mật khẩu mới">
            </div>
            <div class="cn-group">
                <label for="mk_xn">Xác nhận mật khẩu mới</label>
                <input type="password" id="mk_xn" name="mk_xn" placeholder="Nhập lại mật khẩu mới">
            </div>

            <button type="submit" class="cn-submit"><i class="fas fa-save"></i> Lưu thông tin</button>
        </form>
    </div>
</div>

<?php include_once '../../../CuaHang/TrangBanHang/GioHang/formGioHang.php'; ?>
<script src="../../../PhuongThuc/cart.js"></script>
<script src="../../../PhuongThuc/components/xacNhanDangXuat.js"></script>
<script src="../../../PhuongThuc/components/xacThuc.js"></script>
<script>
function toggleUserMenu(e) {
    e.stopPropagation();
    var menu = document.getElementById('userDropdown');
    if (menu) menu.classList.toggle('open');
}
document.addEventListener('click', function() {
    var menu = document.getElementById('userDropdown');
    if (menu) menu.classList.remove('open');
});
function moCapNhatThongTin(e) {
    if (e) e.preventDefault();
    window.location.href = 'CuaHang/TrangBanHang/taiKhoan/capNhat.php';
}
</script>
</body>
</html>
