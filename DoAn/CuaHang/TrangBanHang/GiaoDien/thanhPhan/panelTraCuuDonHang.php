<?php
/**
 * panelTraCuuDonHang.php — Panel tra cứu đơn hàng (không cần đăng nhập)
 * Slide-in panel từ phải, nhập mã đơn hoặc số điện thoại
 * Phụ thuộc: scriptDauTrang.php (hàm moTraCuuDonHang/dongTraCuuDonHang)
 */
?>
<div id="overlay-tra-cuu" class="overlay-tra-cuu" onclick="dongTraCuuDonHang()"></div>
<div id="panel-tra-cuu-don-hang" class="panel-tra-cuu">
    <button class="panel-tra-cuu__dong" onclick="dongTraCuuDonHang()" aria-label="Đóng">&times;</button>
    <div class="panel-tra-cuu__dau">
        <i class="fas fa-box-open"></i>
        <h3>Theo Dõi Đơn Hàng</h3>
        <p>Nhập mã đơn hàng hoặc số điện thoại để tra cứu.</p>
    </div>
    <form class="panel-tra-cuu__form" action="<?= $duong_dan_goc ?>CuaHang/TrangBanHang/donHang/traDoc.php" method="POST">
        <div class="panel-tra-cuu__nhom">
            <label for="tra-cuu-ma-don">Mã đơn hàng</label>
            <input type="text" id="tra-cuu-ma-don" name="ma_don_hang" placeholder="VD: DH1745000012" autocomplete="off">
        </div>
        <div class="panel-tra-cuu__phan-cach">hoặc</div>
        <div class="panel-tra-cuu__nhom">
            <label for="tra-cuu-sdt">Số điện thoại</label>
            <input type="tel" id="tra-cuu-sdt" name="so_dien_thoai" placeholder="VD: 0901234567" autocomplete="off">
        </div>
        <button type="submit" class="panel-tra-cuu__nut-tim">
            <i class="fas fa-search"></i> Tra cứu ngay
        </button>
    </form>
</div>
