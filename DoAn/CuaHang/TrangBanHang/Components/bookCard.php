<?php
/**
 * bookCard.php
 * Chứa hàm hienThiTheSach() — render card hiển thị thông tin một cuốn sách.
 * Được include từ index.php; dùng chung scope $pdo, không require DB ở đây.
 *
 * v2: Bổ sung data-* attributes cho modal Xem Nhanh.
 *     JS đọc trực tiếp từ card — không AJAX, không JSON.
 */

/**
 * hienThiTheSach()
 *
 * @param array $sach      Dữ liệu một cuốn sách từ DB
 * @param array $nhanHieu  Danh sách nhãn [['class'=>'...','label'=>'...']]
 * @return string          HTML card sách
 */
function hienThiTheSach(array $sach, array $nhanHieu = [], string $customHtmlBottom = ''): string
{
    // ── Dữ liệu cơ bản ──────────────────────────────────────────────────
    $anh        = !empty($sach['hinhAnh'])  ? htmlspecialchars($sach['hinhAnh'], ENT_QUOTES)
                                            : 'https://placehold.co/300x400/eff6ff/2563eb?text=📚';
    $ten        = htmlspecialchars($sach['tenSach'] ?? '', ENT_QUOTES);
    $tacGia     = htmlspecialchars(!empty($sach['tacGia'])  ? $sach['tacGia']  : 'Đang cập nhật', ENT_QUOTES);
    $theLoai    = htmlspecialchars(!empty($sach['theLoai']) ? $sach['theLoai'] : '', ENT_QUOTES);
    $giaBan     = (float)($sach['giaBan']  ?? 0);
    $giaSau     = isset($sach['giaSau'])    ? (float)$sach['giaSau'] : null;
    $diem       = (float)($sach['diemTB']  ?? 0);
    $soLuotDG   = (int)($sach['soReview']  ?? 0);
    $maSach     = htmlspecialchars($sach['maSach'] ?? '', ENT_QUOTES);
    $phanTramGiam = (int)($sach['phanTramGiam'] ?? 0);
    $giaHienTai = ($giaSau !== null) ? $giaSau : $giaBan;

    // ── Data-* cho Modal Xem Nhanh (dùng ENT_QUOTES để an toàn trong thuộc tính) ───
    $dataTacGia  = htmlspecialchars($sach['tacGia']       ?? '', ENT_QUOTES);
    $dataTheLoai = htmlspecialchars($sach['theLoai']      ?? '', ENT_QUOTES);
    $dataDiem    = htmlspecialchars((string)($sach['diemTB']  ?? ''), ENT_QUOTES);
    $dataReviews = htmlspecialchars((string)($sach['soReview'] ?? ''), ENT_QUOTES);
    $dataGiam    = htmlspecialchars((string)$phanTramGiam,  ENT_QUOTES);
    // tongBanThang (bán chạy) hoặc tongBan (tổng), ưu tiên tongBanThang
    $dataDaBan   = htmlspecialchars((string)($sach['tongBanThang'] ?? ($sach['tongBan'] ?? '')), ENT_QUOTES);
    // Mô tả: rút ngắn + loại bỏ xuống dòng để an toàn trong data-attribute
    $moTaTam     = str_replace(["\r\n", "\n", "\r"], ' ', $sach['moTa'] ?? '');
    $dataMoTa    = htmlspecialchars(mb_substr($moTaTam, 0, 600), ENT_QUOTES);
    $dataNXB     = htmlspecialchars($sach['nhaXuatBan']  ?? 'Đang cập nhật', ENT_QUOTES);
    $dataNamSX   = htmlspecialchars((string)($sach['namSX'] ?? 'Đang cập nhật'), ENT_QUOTES);
    $dataBia     = htmlspecialchars($sach['hinhThucBia'] ?? 'Đang cập nhật', ENT_QUOTES);
    $dataTonKho  = htmlspecialchars((string)($sach['soLuongTon']   ?? '0'), ENT_QUOTES);

    // ── Nhãn góc trái dọc ────────────────────────────────────────────────
    $nhanHtml = '';
    foreach ($nhanHieu as $nhan) {
        $nhanHtml .= "<span class=\"book-badge {$nhan['class']}\">{$nhan['label']}</span>\n";
    }

    // ── Khối đánh giá ────────────────────────────────────────────────────
    if ($diem > 0) {
        $danhGiaHtml = "
            <div class=\"book-rating\">
                <i class=\"fas fa-star star-icon\"></i>
                <span class=\"rating-score\">{$diem}</span>
                <span class=\"rating-dot\"></span>
                <span class=\"rating-count\">({$soLuotDG})</span>
            </div>";
    } else {
        $danhGiaHtml = "
            <div class=\"book-rating\">
                <i class=\"far fa-star star-icon\"></i>
                <span class=\"rating-count\">Chưa có đánh giá</span>
            </div>";
    }

    // ── Giá hiển thị ─────────────────────────────────────────────────────
    $giaHienThi = number_format($giaHienTai, 0, ',', '.');
    $giaGocHtml = ($giaSau !== null)
        ? '<span class="original-price">' . number_format($giaBan, 0, ',', '.') . ' ₫</span>'
        : '';

    $danhMucHtml = $theLoai ? "<span class=\"book-category\">{$theLoai}</span>" : '';

    // URL trang chi tiết sách (điều hướng server-side, không dùng JS)
    $urlChiTiet = 'CuaHang/TrangBanHang/ChiTietSach/layChiTietSach.php?maSach=' . urlencode($sach['maSach'] ?? '');

    return "
    <a class=\"book-card\"
       href=\"{$urlChiTiet}\"
       data-id=\"{$maSach}\"
       data-name=\"{$ten}\"
       data-price=\"{$giaHienTai}\"
       data-gia-ban=\"{$giaBan}\"
       data-image=\"{$anh}\"
       data-giam=\"{$dataGiam}\"
       data-tac-gia=\"{$dataTacGia}\"
       data-the-loai=\"{$dataTheLoai}\"
       data-diem=\"{$dataDiem}\"
       data-reviews=\"{$dataReviews}\"
       data-da-ban=\"{$dataDaBan}\"
       data-mo-ta=\"{$dataMoTa}\"
       data-nxb=\"{$dataNXB}\"
       data-nam-sx=\"{$dataNamSX}\"
       data-bia=\"{$dataBia}\"
       data-ton-kho=\"{$dataTonKho}\">

    <div class=\"book-image\">
        " . ($nhanHtml ? "<div class=\"book-badges\">{$nhanHtml}</div>" : '') . "
        <img src=\"{$anh}\" alt=\"{$ten}\" loading=\"lazy\">

        <!-- Nút phải: tim + mắt — ẩn, slide-in khi hover -->
        <div class=\"book-actions-right\">
            <button class=\"btn-action-icon btn-wishlist\" title=\"Yêu thích\" onclick=\"thayDoiYeuThich(event, this)\">
                <i class=\"far fa-heart\"></i>
            </button>
            <button class=\"btn-action-icon btn-quickview\" title=\"Xem nhanh\">
                <i class=\"fas fa-eye\"></i>
            </button>
        </div>

        <!-- Nút dưới: Thêm Nhanh — ẩn, slide-up khi hover -->
        <div class=\"book-add-quick\">
            <button class=\"btn-add-quick\" onclick=\"themVaoGioHang(event, this)\">
                <i class=\"fas fa-shopping-cart\"></i> Thêm Nhanh
            </button>
        </div>
    </div>

    <div class=\"book-info\">
        {$danhMucHtml}
        <h4 class=\"book-title\">{$ten}</h4>
        <p class=\"book-author\">{$tacGia}</p>
        {$danhGiaHtml}
        <div class=\"book-footer\">
            <div class=\"book-price-block\">
                <span class=\"current-price\">{$giaHienThi} ₫</span>
                {$giaGocHtml}
            </div>
            <button class=\"btn-add-to-cart\" onclick=\"themVaoGioHang(event, this)\" title=\"Thêm vào giỏ\">
                <i class=\"fas fa-shopping-cart\"></i>
            </button>
        </div>
        {$customHtmlBottom}
    </div>
</a>";
}
