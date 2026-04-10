<?php
/**
 * ChiTietSach/layDanhGia.php — Render HTML danh sách đánh giá của 1 cuốn sách
 *
 * GET param: maSach=S001
 * PHP render hoàn toàn HTML — trả về fragment nhúng vào <iframe> trong modal.
 * Thuần PHP: không JSON, không AJAX.
 */
require_once '../../../KetNoi/config/db.php';

$maSach = trim($_GET['maSach'] ?? '');

$dsDanhGia  = [];
$tongDanhGia = 0;
$diemTB      = 0;
$tenSach     = '';

if ($maSach !== '') {
    // Lấy tên sách
    $stmtS = $pdo->prepare("SELECT tenSach FROM Sach WHERE maSach = ? LIMIT 1");
    $stmtS->execute([$maSach]);
    $sachRow = $stmtS->fetch();
    $tenSach = $sachRow['tenSach'] ?? '';

    // Lấy đánh giá kèm tên người dùng
    $stmtDG = $pdo->prepare("
        SELECT dg.diemDG, dg.nhanXet, dg.ngayDG, nd.tenND
        FROM DanhGiaSach dg
        JOIN NguoiDung nd ON dg.maND = nd.maND
        WHERE dg.maSach = ?
        ORDER BY dg.ngayDG DESC
        LIMIT 20
    ");
    $stmtDG->execute([$maSach]);
    $dsDanhGia   = $stmtDG->fetchAll();
    $tongDanhGia = count($dsDanhGia);

    if ($tongDanhGia > 0) {
        $tongDiem = array_sum(array_column($dsDanhGia, 'diemDG'));
        $diemTB   = round($tongDiem / $tongDanhGia, 1);
    }
}

// Helper render sao (chỉ dùng text + HTML elements, không JS)
function renderSao(int $diem, int $max = 5): string {
    $html = '';
    for ($i = 1; $i <= $max; $i++) {
        $class = $i <= $diem ? 'fas fa-star' : 'far fa-star';
        $html .= '<i class="' . $class . '" aria-hidden="true"></i>';
    }
    return $html;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đánh giá — <?= htmlspecialchars($tenSach) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', sans-serif;
            font-size: .9rem;
            color: #333;
            background: transparent;
            padding: 16px;
        }

        /* Tổng điểm */
        .dg-tong {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 16px;
            padding: 14px 16px;
            background: #fff8f0;
            border-radius: 10px;
            border: 1px solid #fed7aa;
        }
        .dg-diem-lon {
            font-size: 2.5rem;
            font-weight: 800;
            color: #ea580c;
            line-height: 1;
        }
        .dg-sao-tong { color: #f59e0b; font-size: 1.1rem; }
        .dg-tong-so  { font-size: .8rem; color: #6b7280; margin-top: 2px; }

        /* Mỗi đánh giá */
        .dg-item {
            display: flex;
            gap: 12px;
            padding: 14px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .dg-item:last-child { border-bottom: none; }

        .dg-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg,#6366f1,#8b5cf6);
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            font-size: .85rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .dg-body { flex: 1; min-width: 0; }
        .dg-ten  { font-weight: 600; font-size: .875rem; color: #111; margin-bottom: 4px; }
        .dg-sao  { color: #f59e0b; font-size: .8rem; margin-bottom: 6px; }
        .dg-nd   { font-size: .875rem; color: #374151; line-height: 1.5; }
        .dg-ngay { font-size: .75rem; color: #9ca3af; margin-top: 4px; }

        /* Rỗng */
        .dg-rong {
            text-align: center;
            padding: 30px;
            color: #9ca3af;
            font-size: .9rem;
        }
        .dg-rong i { font-size: 2.5rem; display: block; margin-bottom: 8px; opacity: .4; }
    </style>
</head>
<body>

<?php if ($tongDanhGia === 0): ?>
<div class="dg-rong">
    <i class="far fa-comment-alt"></i>
    Chưa có đánh giá nào cho cuốn sách này.
</div>
<?php else: ?>

<!-- Tổng điểm -->
<div class="dg-tong">
    <div class="dg-diem-lon"><?= $diemTB ?></div>
    <div>
        <div class="dg-sao-tong"><?= renderSao((int)round($diemTB)) ?></div>
        <div class="dg-tong-so"><?= $tongDanhGia ?> lượt đánh giá</div>
    </div>
</div>

<!-- Danh sách -->
<?php foreach ($dsDanhGia as $dg):
    $kyTuDau = mb_strtoupper(mb_substr($dg['tenND'], 0, 1, 'UTF-8'), 'UTF-8');
    $ngay    = date('d/m/Y', strtotime($dg['ngayDG']));
?>
<div class="dg-item">
    <div class="dg-avatar"><?= htmlspecialchars($kyTuDau) ?></div>
    <div class="dg-body">
        <div class="dg-ten"><?= htmlspecialchars($dg['tenND']) ?></div>
        <div class="dg-sao"><?= renderSao((int)$dg['diemDG']) ?></div>
        <?php if (!empty($dg['nhanXet'])): ?>
        <div class="dg-nd"><?= htmlspecialchars($dg['nhanXet']) ?></div>
        <?php endif; ?>
        <div class="dg-ngay"><?= $ngay ?></div>
    </div>
</div>
<?php endforeach; ?>

<?php endif; ?>

</body>
</html>
