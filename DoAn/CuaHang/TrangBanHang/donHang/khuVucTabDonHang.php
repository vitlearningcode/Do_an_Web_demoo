<?php
/**
 * khuVucTabDonHang.php — HTML danh sách tab (Tất cả, Chờ Duyệt, Đang Giao...)
 * Yêu cầu: $tabHienTai (string)
 */
$danhSachTab = [
    'tat-ca'    => 'Tất Cả',
    'cho-duyet' => 'Chờ Duyệt',
    'dang-giao' => 'Đang Giao',
    'da-giao'   => 'Đã Giao',
    'da-huy'    => 'Đã Hủy',
    'danh-gia'  => 'Đánh Giá',
];
?>
<div class="dh-tabs" role="tablist">
    <?php foreach ($danhSachTab as $slug => $nhanTab): ?>
    <a href="?tab=<?= $slug ?>"
       class="dh-tab <?= $tabHienTai === $slug ? 'active' : '' ?>"
       role="tab"
       aria-selected="<?= $tabHienTai === $slug ? 'true' : 'false' ?>">
        <?= $nhanTab ?>
    </a>
    <?php endforeach; ?>
</div>
