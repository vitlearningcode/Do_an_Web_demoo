<?php
/**
 * locTabDanhGia.php — Lọc danh sách đơn hàng cho tab "Đánh Giá"
 * Chỉ giữ lại đơn HoanThanh có ít nhất 1 sản phẩm chưa được đánh giá.
 *
 * Input:  $tabHienTai, $dsDonHang, $chiTietDH, $daDanhGia
 * Output: $dsDonHang (array đã lọc)
 */

if ($tabHienTai === 'danh-gia') {
    $dsDonHang = array_filter($dsDonHang, function ($donHang) use ($chiTietDH, $daDanhGia) {
        if ($donHang['trangThai'] !== 'HoanThanh') return false;
        $danhSachSanPham = $chiTietDH[$donHang['maDH']] ?? [];
        foreach ($danhSachSanPham as $sanPham) {
            if (empty($daDanhGia[$sanPham['maSach']])) return true;
        }
        return false;
    });
}
