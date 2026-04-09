<?php
/**
 * formXemNhanhSach.php — v2
 * HTML skeleton Modal "Xem Nhanh Sách".
 *
 * ✅ Toàn bộ HTML render sẵn từ PHP — JS KHÔNG tạo HTML mới.
 * ✅ Mọi icon đều là inline SVG (Lucide) trong PHP — không phụ thuộc Font Awesome cho icon chức năng.
 * ✅ JS đọc data-* từ .book-card → điền vào các ID có sẵn bên dưới.
 * ✅ Không AJAX, không JSON.
 *
 * CSS : GiaoDien/xemNhanhSach.css
 * JS  : PhuongThuc/xemNhanhSach.js
 */
?>
<div id="modal-xem-nhanh"
     class="mn-overlay"
     role="dialog"
     aria-modal="true"
     aria-labelledby="mn-ten-sach">

  <div class="mn-khung">

    <!-- Nút đóng (×) — Lucide X icon -->
    <button id="mn-dong" class="mn-nut-dong" type="button" title="Đóng" aria-label="Đóng modal">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
           stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
           aria-hidden="true">
        <path d="M18 6 6 18"/>
        <path d="m6 6 12 12"/>
      </svg>
    </button>

    <!-- Nội dung cuộn được (flex-col → flex-row trên desktop) -->
    <div class="mn-inner">

      <!-- ════ CỘT TRÁI — 40% — ảnh bìa ════ -->
      <div class="mn-trai">

        <!-- Ảnh bìa + nhãn -->
        <div class="mn-anh-wrapper">
          <img id="mn-anh"
               src="https://placehold.co/300x400/eff6ff/2563eb?text=📚"
               alt="Ảnh bìa sách"
               class="mn-anh"
               loading="eager">
          <div class="mn-nhan-list">
            <span id="mn-nhan-loai" class="mn-nhan mn-nhan-loai" style="display:none"></span>
            <span id="mn-nhan-giam" class="mn-nhan mn-nhan-giam"  style="display:none"></span>
          </div>
        </div>

        <!-- Yêu thích + Chia sẻ — Lucide Heart & Share2 -->
        <div class="mn-hanh-dong">
          <button id="mn-yeu-thich" class="mn-btn-hanh-dong yeu-thich" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 aria-hidden="true">
              <path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5"/>
            </svg>
            Yêu thích
          </button>
          <button id="mn-chia-se" class="mn-btn-hanh-dong chia-se" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 aria-hidden="true">
              <circle cx="18" cy="5" r="3"/>
              <circle cx="6" cy="12" r="3"/>
              <circle cx="18" cy="19" r="3"/>
              <line x1="8.59" x2="15.42" y1="13.51" y2="17.49"/>
              <line x1="15.41" x2="8.59" y1="6.51" y2="10.49"/>
            </svg>
            Chia sẻ
          </button>
        </div>

      </div><!-- /mn-trai -->

      <!-- ════ CỘT PHẢI — 60% — chi tiết sách ════ -->
      <div class="mn-phai">

        <!-- Thể loại — text-sm font-semibold text-blue-600 uppercase -->
        <span id="mn-the-loai" class="mn-the-loai"></span>

        <!-- Tên sách — text-3xl font-bold -->
        <h2 id="mn-ten-sach" class="mn-ten-sach">Đang tải...</h2>

        <!-- Tác giả — text-lg text-gray-600 -->
        <p class="mn-tac-gia">Tác giả: <span id="mn-tac-gia-ten" class="mn-tac-gia-ten"></span></p>

        <!-- Hàng đánh giá: sao · điểm · số đánh giá · đã bán -->
        <div class="mn-hang-danh-gia">
          <div class="mn-sao-nhom">
            <!-- 5 ngôi sao — JS thay className, KHÔNG tạo mới -->
            <div id="mn-sao" class="mn-sao" aria-label="Điểm đánh giá">
              <i class="far fa-star" data-vi-tri="1"></i>
              <i class="far fa-star" data-vi-tri="2"></i>
              <i class="far fa-star" data-vi-tri="3"></i>
              <i class="far fa-star" data-vi-tri="4"></i>
              <i class="far fa-star" data-vi-tri="5"></i>
            </div>
            <span id="mn-diem-tb" class="mn-diem-tb"></span>
          </div>
          <!-- Các dấu · (chấm tách) — ẩn/hiện tuỳ theo data -->
          <span id="mn-dg-sep-1" class="mn-dg-dot" style="display:none"></span>
          <span id="mn-so-review"  class="mn-so-review"></span>
          <span id="mn-dg-sep-2" class="mn-dg-dot"   style="display:none"></span>
          <span id="mn-tong-ban"   class="mn-tong-ban"></span>
        </div>

        <!-- Khu vực giá — p-6 bg-gray-50 rounded-2xl -->
        <div class="mn-gia-khu-vuc">
          <div class="mn-gia-hang">
            <span id="mn-gia-hien-tai" class="mn-gia-chinh"></span>
            <span id="mn-gia-goc"      class="mn-gia-cu"    style="display:none"></span>
            <span id="mn-badge-giam"   class="mn-badge-giam" style="display:none"></span>
          </div>
        </div>

        <!-- Lưới thông tin — grid-cols-2, mỗi hàng flex justify-between border-b -->
        <div class="mn-thong-tin">
          <div class="mn-thong-tin-hang">
            <span class="mn-nhan-thong-tin">Nhà xuất bản</span>
            <span id="mn-nxb"           class="mn-gia-tri-thong-tin">—</span>
          </div>
          <div class="mn-thong-tin-hang">
            <span class="mn-nhan-thong-tin">Năm sản xuất</span>
            <span id="mn-nam-sx"        class="mn-gia-tri-thong-tin">—</span>
          </div>
          <div class="mn-thong-tin-hang">
            <span class="mn-nhan-thong-tin">Hình thức bìa</span>
            <span id="mn-hinh-thuc-bia" class="mn-gia-tri-thong-tin">—</span>
          </div>
          <div class="mn-thong-tin-hang">
            <span class="mn-nhan-thong-tin">Tồn kho</span>
            <span id="mn-ton-kho"       class="mn-gia-tri-thong-tin">—</span>
          </div>
        </div>

        <!-- Mô tả sách — line-clamp-4 + Xem thêm -->
        <div class="mn-mo-ta-khu-vuc">
          <h3 class="mn-mo-ta-tieu-de">Mô tả sách</h3>
          <p  id="mn-mo-ta" class="mn-mo-ta">Đang tải mô tả...</p>
          <button id="mn-xem-them" class="mn-nut-xem-them" type="button" style="display:none">
            Xem thêm
          </button>
        </div>

        <!-- Khu vực mua hàng — mt-auto flex-col gap-4 -->
        <div class="mn-mua-hang-khu-vuc">

          <!-- Số lượng + Nút thêm giỏ -->
          <div class="mn-mua-hang">
            <div class="mn-so-luong-box">
              <button id="mn-giam-sl" class="mn-btn-sl" type="button" aria-label="Giảm số lượng">−</button>
              <input  id="mn-so-luong" class="mn-input-sl" type="number" value="1" min="1" readonly aria-label="Số lượng">
              <button id="mn-tang-sl" class="mn-btn-sl" type="button" aria-label="Tăng số lượng">+</button>
            </div>
            <!-- Nút Thêm vào giỏ — Lucide ShoppingCart -->
            <button id="mn-them-vao-gio" class="mn-btn-them-gio" type="button">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                   aria-hidden="true">
                <circle cx="8" cy="21" r="1"/>
                <circle cx="19" cy="21" r="1"/>
                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
              </svg>
              Thêm vào giỏ hàng
            </button>
          </div>

          <!-- Bảo đảm — flex justify-between border-t — Lucide ShieldCheck & Truck -->
          <div class="mn-bao-dam">
            <div class="mn-bao-dam-muc chinh-hang">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                   aria-hidden="true">
                <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/>
                <path d="m9 12 2 2 4-4"/>
              </svg>
              <span>100% Sách chính hãng</span>
            </div>
            <div class="mn-bao-dam-muc giao-hang">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                   aria-hidden="true">
                <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/>
                <path d="M15 18H9"/>
                <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/>
                <circle cx="17" cy="18" r="2"/>
                <circle cx="7" cy="18" r="2"/>
              </svg>
              <span>Giao hàng toàn quốc</span>
            </div>
          </div>

        </div><!-- /mn-mua-hang-khu-vuc -->

      </div><!-- /mn-phai -->

    </div><!-- /mn-inner -->

  </div><!-- /mn-khung -->

</div><!-- /#modal-xem-nhanh -->
