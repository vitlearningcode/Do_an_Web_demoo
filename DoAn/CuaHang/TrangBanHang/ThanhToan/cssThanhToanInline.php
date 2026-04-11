<?php
/**
 * cssThanhToanInline.php — Inline CSS riêng cho trang thanh toán
 * Đặt bên trong <head> của thanhToan.php
 */
?>
<style>
    /* ── Chọn loại địa chỉ ── */
    .chon-loai-dc { display: flex; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
    .chon-loai-dc label {
        display: flex; align-items: center; gap: 8px;
        padding: 9px 16px; border-radius: 8px; cursor: pointer;
        border: 1.5px solid #e5e7eb; font-size: .875rem; font-weight: 500;
        transition: border-color .2s, background .2s;
    }
    .chon-loai-dc input[type=radio] { accent-color: #2563eb; }
    .chon-loai-dc label:has(input:checked) { border-color: #2563eb; background: #eff6ff; color: #1e40af; }

    /* ── Dropdown địa chỉ đã lưu ── */
    .khung-dc-da-luu { margin-bottom: 10px; }
    .khung-dc-da-luu select {
        width: 100%; padding: 10px 14px;
        border: 1.5px solid #e5e7eb; border-radius: 8px;
        font-size: .9rem; font-family: inherit; outline: none;
        transition: border-color .2s; background: #fff;
    }
    .khung-dc-da-luu select:focus { border-color: #2563eb; }

    /* ── Link thêm địa chỉ ── */
    .link-them-dc { font-size: .8rem; color: #2563eb; text-decoration: underline; }

    /* ── Ẩn/hiện khung nhập địa chỉ mới ── */
    .khung-dc-moi { display: none; }
    .khung-dc-moi.hien { display: block; }
</style>
