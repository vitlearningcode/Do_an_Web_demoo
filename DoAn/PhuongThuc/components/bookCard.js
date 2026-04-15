/**
 * ============================================================
 * LUỒNG: XỬ LÝ SỰ KIỆN THẺ SÁCH (bookCard.js)
 *
 * GỌI BỞI: index.php / trangTheLoai.php sau khi render danh sách sách
 *   <script src="...bookCard.js"></script>
 *
 * HTML YÊU CẦU (PHP render sẵn qua bookCard.php cho từng sách):
 *   <div class="book-card" data-id="S001">
 *     <button class="btn-wishlist" data-id="S001"><i class="far fa-heart"></i></button>
 *     <button class="btn-quickview" data-id="S001" data-ten="..." ...></button>
 *   </div>
 *
 * LUỒNG WISHLIST (Yêu thích):
 *   User click ❤ → xuLyYeuThich(maSach, nutBam)
 *     → Nếu CHƯA thích: thêm vào Set, đổi icon far→fas (tim rỗng → tim đặc)
 *     → Nếu ĐÃ thích:   xóa khỏi Set, đổi icon fas→far (tim đặc → tim rỗng)
 *     → Hiện toast thông báo (hienThiThongBao)
 *     → Gọi callback khiYeuThich (nếu truyền từ ngoài)
 *   GHI CHÚ: Wishlist chỉ lưu ở phía client (JS Set)
 *            Chưa có API lưu DB — tính năng UI demo
 *
 * LUỒNG XEM NHANH (Quick View):
 *   User click 👁 → hopThoaiChiTietSach.mo(nutBam)
 *     → nutBam chứa data-id, data-ten, data-gia... (PHP đã nhúng)
 *     → Modal chi tiết sách mở ra với dữ liệu từ data attributes
 *     → (xem thêm: xemNhanhSach.js)
 *
 * NGUYÊN TẮC:
 *   - JS KHÔNG tạo HTML — PHP render tất cả thẻ sách
 *   - JS chỉ gắn event listener lên phần tử PHP đã render
 *   - stopPropagation(): tránh event lan lên thẻ .book-card cha
 *   - Dùng className toggle (không innerHTML) để đổi icon tim
 *
 * BIẾN TOÀN CỤC:
 *   theSach — instance chính (các file khác gọi: theSach.ganSuKien())
 * ============================================================
 */

class XuLyTheSach {
  /**
   * @param {object} tuyChon
   *   onAddToCart: function(maSach) — callback khi thêm giỏ (hiện chưa dùng)
   *   onQuickView: function(maSach) — callback khi xem nhanh (hiện chưa dùng)
   *   onWishlist:  function(danhSach) — callback khi toggle yêu thích
   *   wishlists:   string[] — danh sách maSach đã yêu thích ban đầu
   */
  constructor(tuyChon = {}) {
    // Callbacks: có thể truyền từ file app.js hoặc trang cụ thể
    this.khiThemVaoGio = tuyChon.onAddToCart || (() => {});
    this.khiXemNhanh   = tuyChon.onQuickView  || (() => {});
    this.khiYeuThich   = tuyChon.onWishlist   || (() => {});

    // Set lưu maSach đang được yêu thích (chỉ tồn tại trong phiên trình duyệt)
    // Set: tra cứu O(1), tránh trùng lặp — phù hợp hơn Array cho trường hợp này
    this.danhSachYeuThich = new Set(tuyChon.wishlists || []);

    // Gắn sự kiện khi DOM sẵn sàng
    // (script thường load ở cuối body, nhưng vẫn dùng DOMContentLoaded cho an toàn)
    document.addEventListener("DOMContentLoaded", () => {
      this.ganSuKien();
    });
  }

  /**
   * Tìm tất cả nút trên trang và gắn event listener
   * Gọi lại hàm này nếu có thêm thẻ sách sau khi trang load
   * (ví dụ: load thêm sách bằng pagination)
   */
  ganSuKien() {
    // --- 1. NÚT YÊU THÍCH (trái tim) ---
    // PHP render mỗi .btn-wishlist với data-id="S001"
    document.querySelectorAll(".btn-wishlist").forEach((nutBam) => {
      nutBam.addEventListener("click", (suKien) => {
        suKien.stopPropagation(); // Ngăn click lan lên .book-card (tránh navigate trang)
        suKien.preventDefault();  // Ngăn hành vi mặc định (nếu là <a>)

        const maSach = nutBam.getAttribute("data-id");
        this.xuLyYeuThich(maSach, nutBam);
      });
    });

    // --- 2. NÚT XEM NHANH (con mắt) ---
    // PHP render mỗi .btn-quickview với data-id, data-ten, data-gia, data-hinh...
    document
      .querySelectorAll('.btn-quickview')
      .forEach((nutBam) => {
        nutBam.addEventListener("click", (suKien) => {
          suKien.stopPropagation();
          suKien.preventDefault();

          // Truyền nutBam (chứa data-*) vào modal — modal sẽ đọc dữ liệu từ đó
          // hopThoaiChiTietSach: global instance từ xemNhanhSach.js
          if (typeof hopThoaiChiTietSach !== "undefined") {
            hopThoaiChiTietSach.mo(nutBam);
          }
        });
      });

    // --- 3. CLICK VÀO THẺ SÁCH (đã gỡ bỏ) ---
    // Trước đây: click thẻ → chuyển trang chi tiết
    // Hiện tại: click thẻ → mở Quick View modal (được xử lý trong xemNhanhSach.js)
    // Giữ comment này để giải thích tại sao không có handler cho .book-card
  }

  /**
   * Xử lý toggle yêu thích (tim đặc ↔ tim rỗng)
   * Thay đổi class trên thẻ <i> (Font Awesome: far=rỗng, fas=đặc)
   *
   * @param {string} maSach - Mã sách lấy từ data-id
   * @param {Element} nutBam - Phần tử button .btn-wishlist đã được click
   */
  xuLyYeuThich(maSach, nutBam) {
    if (!maSach) return; // Guard: không xử lý nếu không có maSach

    // Tìm thẻ <i> bên trong nút (PHP render: <i class="far fa-heart">)
    // Dùng querySelector thay vì innerHTML để chỉ đổi class, không tạo lại element
    const iconTim = nutBam.querySelector('i');

    if (this.danhSachYeuThich.has(maSach)) {
      // ĐÃ thích → Bỏ thích
      this.danhSachYeuThich.delete(maSach);
      nutBam.classList.remove("active");

      if (iconTim) {
        iconTim.classList.remove("fas"); // Bỏ tim đặc
        iconTim.classList.add("far");    // Thêm tim rỗng
      }

      // Thông báo cho user (hienThiThongBao từ thongBao.js)
      if (typeof hienThiThongBao !== "undefined") {
        hienThiThongBao("Đã xóa khỏi danh sách yêu thích");
      }
    } else {
      // CHƯA thích → Thêm vào thích
      this.danhSachYeuThich.add(maSach);
      nutBam.classList.add("active");

      if (iconTim) {
        iconTim.classList.remove("far"); // Bỏ tim rỗng
        iconTim.classList.add("fas");    // Thêm tim đặc
      }

      if (typeof hienThiThongBao !== "undefined") {
        hienThiThongBao("Đã thêm vào danh sách yêu thích");
      }
    }

    // Gọi callback với danh sách hiện tại (Array.from: Set → Array để serialize dễ hơn)
    this.khiYeuThich(Array.from(this.danhSachYeuThich));
  }
}

// Singleton toàn cục
const theSach = new XuLyTheSach();

// Hỗ trợ CommonJS (test môi trường Node.js)
if (typeof module !== "undefined" && module.exports) {
  module.exports = { XuLyTheSach, theSach };
}
