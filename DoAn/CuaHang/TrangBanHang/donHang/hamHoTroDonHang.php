<?php
/**
 * hamHoTroDonHang.php — Các hàm helper dùng trong trang đơn hàng
 * Hàm: thongTinBadge($trangThai) → trả về [class, nhãn] cho badge trạng thái
 */

/**
 * Trả về thông tin badge (class CSS + nhãn hiển thị) theo trạng thái đơn hàng
 *
 * @param string $trangThai Trạng thái từ DB: ChoDuyet | DangGiao | HoanThanh | DaHuy
 * @return array [classBadge, nhanBadge]
 */
function thongTinBadge(string $trangThai): array {
    return match($trangThai) {
        'ChoDuyet'   => ['cho-duyet',  'Chờ Duyệt'],
        'DangGiao'   => ['dang-giao',  'Đang Giao'],
        'HoanThanh'  => ['hoan-thanh', 'Đã Giao'],
        'DaHuy'      => ['da-huy',     'Đã Hủy'],
        default      => ['cho-duyet',  $trangThai],
    };
}
