<?php
/**
 * taiKhoan/capNhat.php — Cập nhật thông tin tài khoản + Quản lý địa chỉ giao hàng
 * Thuần PHP form POST — không AJAX.
 */
session_start();
require_once '../../../KetNoi/config/db.php';

$isLoggedIn = isset($_SESSION['nguoi_dung_id']);
if (!$isLoggedIn) {
    header('Location: ../../../index.php');
    exit;
}

$maND     = (int)$_SESSION['nguoi_dung_id'];
$thongBao = '';
$loaiTB   = ''; // success | error

// ── Xử lý POST thông tin cá nhân ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hanh_dong_thong_tin'])) {
    $hoTen = trim($_POST['hoten']  ?? '');
    $sdt   = trim($_POST['sdt']    ?? '');
    $email = trim($_POST['email']  ?? '');
    $mkCu  = trim($_POST['mk_cu']  ?? '');
    $mkMoi = trim($_POST['mk_moi'] ?? '');
    $mkXN  = trim($_POST['mk_xn']  ?? '');

    $danhSachLoi = [];
    if (empty($hoTen)) $danhSachLoi[] = 'Họ tên không được để trống.';

    if (empty($danhSachLoi)) {
        $stmtCapNhat = $pdo->prepare("UPDATE NguoiDung SET tenND = ?, sdt = ?, email = ? WHERE maND = ?");
        $stmtCapNhat->execute([$hoTen, $sdt ?: null, $email ?: null, $maND]);
        $_SESSION['ten_nguoi_dung'] = $hoTen;

        if (!empty($mkCu)) {
            $stmtTK = $pdo->prepare("SELECT matKhau FROM TaiKhoan WHERE maND = ? LIMIT 1");
            $stmtTK->execute([$maND]);
            $taiKhoan = $stmtTK->fetch();

            if (!$taiKhoan || $taiKhoan['matKhau'] !== $mkCu) {
                $danhSachLoi[] = 'Mật khẩu cũ không đúng!';
            } elseif ($mkMoi !== $mkXN) {
                $danhSachLoi[] = 'Mật khẩu mới và xác nhận không khớp!';
            } elseif (strlen($mkMoi) < 1) {
                $danhSachLoi[] = 'Mật khẩu mới không được để trống!';
            } else {
                $stmtDoiMK = $pdo->prepare("UPDATE TaiKhoan SET matKhau = ? WHERE maND = ?");
                $stmtDoiMK->execute([$mkMoi, $maND]);
            }
        }
    }

    if (empty($danhSachLoi)) {
        $thongBao = 'Cập nhật thông tin thành công!';
        $loaiTB   = 'success';
    } else {
        $thongBao = implode(' ', $danhSachLoi);
        $loaiTB   = 'error';
    }
}

// ── Xử lý POST địa chỉ giao hàng ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hanh_dong_dia_chi'])) {
    $hanhDong = $_POST['hanh_dong_dia_chi'];

    if ($hanhDong === 'them_moi') {
        $diaChiMoi = trim($_POST['dia_chi_moi'] ?? '');
        $laMacDinh = isset($_POST['la_mac_dinh']) ? 1 : 0;

        if (!empty($diaChiMoi)) {
            if ($laMacDinh) {
                $pdo->prepare("UPDATE DiaChiGiaoHang SET laMacDinh = 0 WHERE maND = ?")->execute([$maND]);
            }
            $stmtThem = $pdo->prepare("INSERT INTO DiaChiGiaoHang (maND, diaChiChiTiet, laMacDinh) VALUES (?, ?, ?)");
            $stmtThem->execute([$maND, $diaChiMoi, $laMacDinh]);
            $thongBao = 'Đã thêm địa chỉ mới thành công!';
            $loaiTB   = 'success';
        } else {
            $thongBao = 'Địa chỉ không được để trống.';
            $loaiTB   = 'error';
        }

    } elseif ($hanhDong === 'dat_mac_dinh') {
        $maDC = (int)($_POST['ma_dc'] ?? 0);
        $stmtKT = $pdo->prepare("SELECT maDC FROM DiaChiGiaoHang WHERE maDC = ? AND maND = ?");
        $stmtKT->execute([$maDC, $maND]);
        if ($stmtKT->fetch()) {
            $pdo->prepare("UPDATE DiaChiGiaoHang SET laMacDinh = 0 WHERE maND = ?")->execute([$maND]);
            $pdo->prepare("UPDATE DiaChiGiaoHang SET laMacDinh = 1 WHERE maDC = ? AND maND = ?")->execute([$maDC, $maND]);
            $thongBao = 'Đã đặt làm địa chỉ mặc định.';
            $loaiTB   = 'success';
        }

    } elseif ($hanhDong === 'xoa_dia_chi') {
        $maDC = (int)($_POST['ma_dc'] ?? 0);
        $pdo->prepare("DELETE FROM DiaChiGiaoHang WHERE maDC = ? AND maND = ?")->execute([$maDC, $maND]);
        $thongBao = 'Đã xóa địa chỉ.';
        $loaiTB   = 'success';
    }
}

// ── Lấy thông tin hiện tại ──────────────────────────────────────────────────
$stmtND  = $pdo->prepare("SELECT tenND, sdt, email FROM NguoiDung WHERE maND = ? LIMIT 1");
$stmtND->execute([$maND]);
$nguoiDung = $stmtND->fetch();

$stmtTK = $pdo->prepare("SELECT tenDN FROM TaiKhoan WHERE maND = ? LIMIT 1");
$stmtTK->execute([$maND]);
$taiKhoan = $stmtTK->fetch();

$stmtDC = $pdo->prepare("SELECT maDC, diaChiChiTiet, laMacDinh FROM DiaChiGiaoHang WHERE maND = ? ORDER BY laMacDinh DESC, maDC ASC");
$stmtDC->execute([$maND]);
$danhSachDiaChi = $stmtDC->fetchAll();
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
        .cn-trang { max-width: 640px; margin: 40px auto; padding: 0 16px 60px; }
        .cn-quay-lai { display: inline-flex; align-items: center; gap: 6px; color: #6b7280; font-size: .875rem; text-decoration: none; margin-bottom: 20px; }
        .cn-quay-lai:hover { color: #ee4d2d; }
        .cn-the { background: #fff; border-radius: 14px; padding: 32px; box-shadow: 0 1px 8px rgba(0,0,0,.08); margin-bottom: 24px; }
        .cn-avatar { width: 70px; height: 70px; border-radius: 50%; background: linear-gradient(135deg,#6366f1,#8b5cf6); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.8rem; font-weight: 700; margin: 0 auto 20px; }
        .cn-cap { color: #6b7280; font-size: .875rem; text-align: center; margin-bottom: 24px; }
        .cn-sep { border: none; border-top: 1px solid #f3f4f6; margin: 20px 0; }
        .cn-nhom { margin-bottom: 16px; }
        .cn-nhom label { display: block; font-size: .85rem; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .cn-nhom input { width: 100%; padding: 10px 14px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: .9rem; font-family: inherit; outline: none; transition: border-color .2s; }
        .cn-nhom input:focus { border-color: #6366f1; }
        .cn-tieu-muc { font-size: 1rem; font-weight: 700; color: #374151; margin: 0 0 18px; display: flex; align-items: center; gap: 8px; }
        .cn-tieu-muc i { color: #2563eb; }
        .cn-nut-luu { width: 100%; padding: 12px; background: #ee4d2d; color: #fff; border: none; border-radius: 8px; font-size: 1rem; font-weight: 700; cursor: pointer; font-family: inherit; transition: background .2s; margin-top: 8px; }
        .cn-nut-luu:hover { background: #c73e20; }
        .cn-thong-bao { padding: 12px 16px; border-radius: 8px; font-size: .9rem; margin-bottom: 20px; font-weight: 500; display: flex; align-items: center; gap: 8px; }
        .cn-thong-bao.success { background: #dcfce7; color: #15803d; }
        .cn-thong-bao.error   { background: #fee2e2; color: #dc2626; }

        /* ── Địa chỉ ── */
        .dc-danh-sach { display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px; }
        .dc-dong { display: flex; align-items: flex-start; gap: 12px; padding: 14px 16px; border: 1.5px solid #e5e7eb; border-radius: 10px; background: #fafafa; }
        .dc-dong.mac-dinh { border-color: #22c55e; background: #f0fdf4; }
        .dc-noi-dung { flex: 1; }
        .dc-van-ban { font-size: .875rem; color: #1f2937; font-weight: 500; line-height: 1.5; }
        .dc-mac-dinh-badge { display: inline-block; margin-top: 4px; background: #22c55e; color: #fff; font-size: .72rem; font-weight: 700; padding: 2px 8px; border-radius: 10px; }
        .dc-hanh-dong { display: flex; flex-direction: column; gap: 6px; align-items: flex-end; }
        .dc-nut { font-size: .75rem; padding: 5px 10px; border-radius: 6px; border: 1px solid; cursor: pointer; font-family: inherit; font-weight: 600; background: transparent; transition: background .15s; }
        .dc-nut.mac-dinh { border-color: #2563eb; color: #2563eb; }
        .dc-nut.mac-dinh:hover { background: #eff6ff; }
        .dc-nut.xoa { border-color: #ef4444; color: #ef4444; }
        .dc-nut.xoa:hover { background: #fee2e2; }
        .dc-them-khung { border: 1.5px dashed #d1d5db; border-radius: 10px; padding: 18px; }
        .dc-them-khung label { font-size: .85rem; font-weight: 600; color: #374151; display: block; margin-bottom: 6px; }
        .dc-them-khung input[type=text] { width: 100%; padding: 10px 14px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: .9rem; font-family: inherit; outline: none; transition: border-color .2s; margin-bottom: 10px; }
        .dc-them-khung input[type=text]:focus { border-color: #6366f1; }
        .dc-tuy-chon-mac-dinh { display: flex; align-items: center; gap: 8px; margin-bottom: 14px; font-size: .875rem; color: #374151; cursor: pointer; }
        .dc-nut-them { padding: 10px 18px; background: #2563eb; color: #fff; border: none; border-radius: 8px; font-size: .875rem; font-weight: 700; cursor: pointer; font-family: inherit; transition: background .2s; display: inline-flex; align-items: center; gap: 6px; }
        .dc-nut-them:hover { background: #1d4ed8; }
        .dc-trong { text-align: center; padding: 20px; color: #9ca3af; font-size: .875rem; }
    </style>
</head>
<body>
<?php include_once '../../../CuaHang/TrangBanHang/GiaoDien/header.php'; ?>

<div class="cn-trang">
    <a href="../../../index.php" class="cn-quay-lai"><i class="fas fa-arrow-left"></i> Quay lại cửa hàng</a>

    <?php if ($thongBao): ?>
    <div class="cn-thong-bao <?= $loaiTB ?>">
        <i class="fas fa-<?= $loaiTB === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
        <?= htmlspecialchars($thongBao) ?>
    </div>
    <?php endif; ?>

    <!-- ── THÔNG TIN CÁ NHÂN ── -->
    <div class="cn-the">
        <div class="cn-avatar"><?= mb_strtoupper(mb_substr($nguoiDung['tenND'] ?? 'U', 0, 1, 'UTF-8'), 'UTF-8') ?></div>
        <p class="cn-cap">Tài khoản: <strong><?= htmlspecialchars($taiKhoan['tenDN'] ?? '') ?></strong></p>

        <form action="" method="POST">
            <input type="hidden" name="hanh_dong_thong_tin" value="1">
            <p class="cn-tieu-muc"><i class="fas fa-user"></i> Thông Tin Cá Nhân</p>
            <div class="cn-nhom">
                <label for="hoten">Họ và tên</label>
                <input type="text" id="hoten" name="hoten" value="<?= htmlspecialchars($nguoiDung['tenND'] ?? '') ?>" required>
            </div>
            <div class="cn-nhom">
                <label for="sdt">Số điện thoại</label>
                <input type="tel" id="sdt" name="sdt" value="<?= htmlspecialchars($nguoiDung['sdt'] ?? '') ?>" placeholder="0912345678">
            </div>
            <div class="cn-nhom">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($nguoiDung['email'] ?? '') ?>" placeholder="example@email.com">
            </div>

            <hr class="cn-sep">
            <p class="cn-tieu-muc"><i class="fas fa-lock"></i> Đổi Mật Khẩu <small style="font-weight:400;color:#9ca3af;font-size:.82rem;">(để trống nếu không đổi)</small></p>
            <div class="cn-nhom">
                <label for="mk_cu">Mật khẩu cũ</label>
                <input type="password" id="mk_cu" name="mk_cu" placeholder="Nhập mật khẩu hiện tại">
            </div>
            <div class="cn-nhom">
                <label for="mk_moi">Mật khẩu mới</label>
                <input type="password" id="mk_moi" name="mk_moi" placeholder="Nhập mật khẩu mới">
            </div>
            <div class="cn-nhom">
                <label for="mk_xn">Xác nhận mật khẩu mới</label>
                <input type="password" id="mk_xn" name="mk_xn" placeholder="Nhập lại mật khẩu mới">
            </div>
            <button type="submit" class="cn-nut-luu"><i class="fas fa-save"></i> Lưu thông tin</button>
        </form>
    </div>

    <!-- ── ĐỊA CHỈ GIAO HÀNG ── -->
    <div class="cn-the">
        <p class="cn-tieu-muc"><i class="fas fa-map-marker-alt"></i> Địa Chỉ Giao Hàng</p>
        <p style="font-size:.85rem;color:#6b7280;margin:-10px 0 18px;">Địa chỉ mặc định sẽ được điền sẵn khi thanh toán.</p>

        <?php if (empty($danhSachDiaChi)): ?>
        <div class="dc-trong">
            <i class="fas fa-map-marker-alt" style="font-size:2rem;color:#d1d5db;display:block;margin-bottom:8px;"></i>
            Bạn chưa lưu địa chỉ nào. Thêm ngay bên dưới!
        </div>
        <?php else: ?>
        <div class="dc-danh-sach">
        <?php foreach ($danhSachDiaChi as $dc): ?>
            <div class="dc-dong <?= $dc['laMacDinh'] ? 'mac-dinh' : '' ?>">
                <div class="dc-noi-dung">
                    <div class="dc-van-ban"><?= htmlspecialchars($dc['diaChiChiTiet']) ?></div>
                    <?php if ($dc['laMacDinh']): ?>
                    <span class="dc-mac-dinh-badge"><i class="fas fa-check"></i> Mặc định</span>
                    <?php endif; ?>
                </div>
                <div class="dc-hanh-dong">
                    <?php if (!$dc['laMacDinh']): ?>
                    <form method="POST" action="" style="margin:0;">
                        <input type="hidden" name="hanh_dong_dia_chi" value="dat_mac_dinh">
                        <input type="hidden" name="ma_dc" value="<?= (int)$dc['maDC'] ?>">
                        <button type="submit" class="dc-nut mac-dinh">Đặt mặc định</button>
                    </form>
                    <?php endif; ?>
                    <form method="POST" action="" style="margin:0;" onsubmit="return confirm('Xóa địa chỉ này?')">
                        <input type="hidden" name="hanh_dong_dia_chi" value="xoa_dia_chi">
                        <input type="hidden" name="ma_dc" value="<?= (int)$dc['maDC'] ?>">
                        <button type="submit" class="dc-nut xoa"><i class="fas fa-trash-alt"></i> Xóa</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Form thêm địa chỉ mới -->
        <div class="dc-them-khung">
            <form method="POST" action="">
                <input type="hidden" name="hanh_dong_dia_chi" value="them_moi">
                <label for="dia-chi-moi">Thêm địa chỉ mới</label>
                <input type="text" id="dia-chi-moi" name="dia_chi_moi"
                       placeholder="Số nhà, Tên đường, Phường/Xã, Quận/Huyện, Tỉnh/TP" required>
                <label class="dc-tuy-chon-mac-dinh">
                    <input type="checkbox" name="la_mac_dinh" value="1"
                           <?= empty($danhSachDiaChi) ? 'checked' : '' ?>>
                    Đặt làm địa chỉ mặc định
                </label>
                <button type="submit" class="dc-nut-them">
                    <i class="fas fa-plus"></i> Thêm địa chỉ
                </button>
            </form>
        </div>
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
