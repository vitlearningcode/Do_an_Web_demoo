<?php
/**
 * modalDanhGiaSanPham.php — Modal đánh giá sản phẩm
 * PHP render sẵn HTML (form + sao), JS chỉ toggle class và điền value
 * Form POST → xuly_danhGia.php
 */
?>
<div id="review-overlay" class="dh-review-overlay" role="dialog" aria-modal="true" aria-labelledby="review-tieu-de">
    <div class="dh-review-modal">
        <button class="dh-modal-close" type="button" onclick="dongModalDanhGia()" aria-label="Đóng">&times;</button>
        <h3 id="review-tieu-de">Đánh giá sản phẩm</h3>
        <p class="dh-book-name" id="review-ten-sach"></p>

        <!-- Form POST thuần PHP → xuly_danhGia.php -->
        <form action="xuly_danhGia.php" method="POST" id="form-danh-gia">
            <input type="hidden" name="maDH"   id="review-maDH"   value="">
            <input type="hidden" name="maSach" id="review-maSach" value="">
            <input type="hidden" name="diem"   id="review-diem"   value="0">

            <!-- 5 sao — JS chỉ thêm/xóa class 'selected', không tạo element mới -->
            <div class="dh-star-row" id="review-stars" role="group" aria-label="Chọn số sao">
                <span class="dh-star" data-vi-tri="1" onclick="chonSao(1)" title="1 sao">&#9733;</span>
                <span class="dh-star" data-vi-tri="2" onclick="chonSao(2)" title="2 sao">&#9733;</span>
                <span class="dh-star" data-vi-tri="3" onclick="chonSao(3)" title="3 sao">&#9733;</span>
                <span class="dh-star" data-vi-tri="4" onclick="chonSao(4)" title="4 sao">&#9733;</span>
                <span class="dh-star" data-vi-tri="5" onclick="chonSao(5)" title="5 sao">&#9733;</span>
            </div>

            <textarea class="dh-review-textarea"
                      name="nhanXet"
                      id="review-nhanXet"
                      placeholder="Chia sẻ cảm nhận của bạn về cuốn sách này..."
                      rows="4"></textarea>

            <div class="dh-review-actions">
                <button type="button" class="dh-btn-cancel-review" onclick="dongModalDanhGia()">Hủy</button>
                <button type="submit" class="dh-btn-submit-review">
                    <i class="fas fa-paper-plane"></i> Gửi đánh giá
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Toast thông báo trang đơn hàng -->
<div id="dh-toast" class="dh-toast"></div>
