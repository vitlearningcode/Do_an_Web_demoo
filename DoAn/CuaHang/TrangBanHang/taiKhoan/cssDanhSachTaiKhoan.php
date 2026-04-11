<?php
/**
 * cssDanhSachTaiKhoan.php — Inline CSS riêng cho trang cập nhật tài khoản
 * Đặt trong <head> của capNhat.php
 */
?>
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
