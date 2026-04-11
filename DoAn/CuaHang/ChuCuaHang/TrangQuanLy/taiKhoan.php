<?php
// ══════════════════════════════════════════════════════
//  taiKhoan.php — Quản lý tài khoản người dùng
// ══════════════════════════════════════════════════════

// [BẢO MẬT] Kiểm tra quyền Admin — chặn truy cập trực tiếp
require_once __DIR__ . '/../_kiemTraQuyen.php';
$timKiem = trim($_GET['tim'] ?? '');
$locVT   = $_GET['loc_vt'] ?? '';

$dieuKien = "WHERE 1=1";
$params   = [];
if ($timKiem !== '') {
    $dieuKien .= " AND (tk.tenDN LIKE ? OR nd.tenND LIKE ? OR nd.email LIKE ?)";
    $params[] = "%$timKiem%"; $params[] = "%$timKiem%"; $params[] = "%$timKiem%";
}
if ($locVT !== '') {
    $dieuKien .= " AND tk.maVT = ?";
    $params[] = $locVT;
}

try {
    $dsTK = $pdo->prepare("
        SELECT tk.tenDN, tk.trangThai, tk.maVT,
               nd.maND, nd.tenND, nd.email, nd.sdt, nd.ngayTao,
               vt.tenVT
        FROM TaiKhoan tk
        JOIN NguoiDung nd ON tk.maND = nd.maND
        JOIN VaiTro vt ON tk.maVT = vt.maVT
        $dieuKien
        ORDER BY nd.ngayTao DESC
    ");
    $dsTK->execute($params);
    $dsTaiKhoan = $dsTK->fetchAll(PDO::FETCH_ASSOC);

    $dsVaiTro = $pdo->query("SELECT maVT, tenVT FROM VaiTro")->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $dsTaiKhoan = $dsVaiTro = [];
}

$baseUrl = 'index.php?trang=taiKhoan';
?>

<div class="adm-section-header">
    <div>
        <div class="adm-section-title">Quản lý tài khoản</div>
        <div class="adm-section-subtitle">Tổng cộng <?= count($dsTaiKhoan) ?> tài khoản</div>
    </div>
</div>

<!-- FILTER -->
<form method="GET" action="index.php">
<input type="hidden" name="trang" value="taiKhoan">
<div class="adm-card" style="margin-bottom:16px">
    <div class="adm-filter-bar">
        <div class="adm-search-input">
            <i class="fas fa-search"></i>
            <input type="text" name="tim" placeholder="Tên đăng nhập, họ tên, email..." value="<?= htmlspecialchars($timKiem) ?>">
        </div>
        <select class="adm-select" name="loc_vt">
            <option value="">Tất cả vai trò</option>
            <?php foreach ($dsVaiTro as $vt): ?>
                <option value="<?= $vt['maVT'] ?>" <?= $locVT == $vt['maVT'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($vt['tenVT']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="adm-btn adm-btn-primary"><i class="fas fa-filter"></i> Lọc</button>
        <a href="<?= $baseUrl ?>" class="adm-btn adm-btn-outline"><i class="fas fa-times"></i> Xóa lọc</a>
    </div>
</div>
</form>

<!-- TABLE -->
<div class="adm-card">
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Tên đăng nhập</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>SĐT</th>
                    <th>Vai trò</th>
                    <th>Ngày tạo</th>
                    <th>Trạng thái</th>
                    <th style="text-align:center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($dsTaiKhoan)): ?>
                <tr><td colspan="8"><div class="adm-empty"><i class="fas fa-users"></i><p>Không tìm thấy tài khoản nào.</p></div></td></tr>
            <?php else: ?>
                <?php foreach ($dsTaiKhoan as $tk): ?>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px">
                            <div style="width:32px;height:32px;border-radius:50%;background:<?= $tk['maVT']==1?'#dbeafe':'#f1f5f9' ?>;display:flex;align-items:center;justify-content:center;font-size:13px;color:<?= $tk['maVT']==1?'#2563eb':'#64748b' ?>">
                                <i class="fas <?= $tk['maVT']==1?'fa-user-shield':'fa-user' ?>"></i>
                            </div>
                            <strong><?= htmlspecialchars($tk['tenDN']) ?></strong>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($tk['tenND']) ?></td>
                    <td style="font-size:13px"><?= htmlspecialchars($tk['email'] ?? '—') ?></td>
                    <td style="font-size:13px"><?= htmlspecialchars($tk['sdt'] ?? '—') ?></td>
                    <td>
                        <?php if ($tk['maVT'] == 1): ?>
                            <span class="adm-badge adm-badge-purple"><i class="fas fa-crown"></i> <?= htmlspecialchars($tk['tenVT']) ?></span>
                        <?php else: ?>
                            <span class="adm-badge adm-badge-info"><?= htmlspecialchars($tk['tenVT']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:13px;color:#94a3b8">
                        <?= $tk['ngayTao'] ? date('d/m/Y', strtotime($tk['ngayTao'])) : '—' ?>
                    </td>
                    <td>
                        <?php if ($tk['trangThai'] === 'on'): ?>
                            <span class="adm-badge adm-badge-success"><i class="fas fa-circle" style="font-size:8px"></i> Hoạt động</span>
                        <?php else: ?>
                            <span class="adm-badge adm-badge-danger"><i class="fas fa-circle" style="font-size:8px"></i> Bị khóa</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center">
                        <!-- Không cho tắt tài khoản Admin -->
                        <?php if ($tk['maVT'] != 1): ?>
                        <form method="POST" action="XuLy/capNhatTaiKhoan.php" style="display:inline">
                            <input type="hidden" name="tenDN" value="<?= htmlspecialchars($tk['tenDN']) ?>">
                            <button type="submit"
                                class="adm-btn adm-btn-sm <?= $tk['trangThai']==='on' ? 'adm-btn-danger' : 'adm-btn-success' ?>"
                                onclick="return confirm('<?= $tk['trangThai']==='on' ? 'Khóa' : 'Mở khóa' ?> tài khoản <?= htmlspecialchars($tk['tenDN']) ?>?')">
                                <i class="fas <?= $tk['trangThai']==='on' ? 'fa-lock' : 'fa-lock-open' ?>"></i>
                                <?= $tk['trangThai']==='on' ? 'Khóa' : 'Mở khóa' ?>
                            </button>
                        </form>
                        <?php else: ?>
                            <span style="color:#94a3b8;font-size:13px">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
