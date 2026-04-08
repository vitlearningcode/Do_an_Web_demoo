// ==================== THÀNH PHẦN XỬ LÝ SỰ KIỆN THẺ SÁCH ====================

class XuLyTheSach {
  constructor(tuyChon = {}) {
    // Các hàm callback có thể truyền từ file app.js
    this.khiThemVaoGio = tuyChon.onAddToCart || (() => {});
    this.khiXemNhanh = tuyChon.onQuickView || (() => {});
    this.khiYeuThich = tuyChon.onWishlist || (() => {});

    // Quản lý danh sách ID sách yêu thích (Lưu tạm trên trình duyệt)
    this.danhSachYeuThich = new Set(tuyChon.wishlists || []);

    // Tự động tìm và gắn sự kiện ngay khi trang load xong
    document.addEventListener("DOMContentLoaded", () => {
      this.ganSuKien();
    });
  }

  // Hàm này đi tìm các nút bấm (do PHP in ra) để gài sự kiện Click
  ganSuKien() {
    // 1. Xử lý nút Yêu thích (Trái tim)
    document.querySelectorAll(".book-quick-btn.wishlist").forEach((nutBam) => {
      nutBam.addEventListener("click", (suKien) => {
        suKien.stopPropagation(); // Ngăn click lan ra thẻ cha
        suKien.preventDefault();

        const maSach = nutBam.getAttribute("data-id");
        this.xuLyYeuThich(maSach, nutBam);
      });
    });

    // 2. Xử lý nút Xem Nhanh (Con mắt)
    document
      .querySelectorAll('.book-quick-btn[title="Xem nhanh"]')
      .forEach((nutBam) => {
        nutBam.addEventListener("click", (suKien) => {
          suKien.stopPropagation();
          suKien.preventDefault();

          // Tìm nút Bấm này và truyền thẳng nó vào hàm mo() của Popup
          if (typeof hopThoaiChiTietSach !== "undefined") {
            hopThoaiChiTietSach.mo(nutBam);
          }
        });
      });

    // 3. Click vào toàn bộ thẻ Sách cũng chuyển sang trang chi tiết
    document.querySelectorAll(".book-card").forEach((theSach) => {
      theSach.addEventListener("click", () => {
        const maSach = theSach.getAttribute("data-id");
        if (maSach) {
          window.location.href = `ChucNang/CuaHang/ChiTietSach/index.php?id=${maSach}`;
        }
      });
    });
  }

  // Logic đổi màu trái tim khi nhấn Yêu thích
  xuLyYeuThich(maSach, nutBam) {
    if (!maSach) return;

    if (this.danhSachYeuThich.has(maSach)) {
      // Đang thích -> Bỏ thích
      this.danhSachYeuThich.delete(maSach);
      nutBam.classList.remove("active");
      nutBam.innerHTML = '<i class="far fa-heart"></i>'; // Tim rỗng

      if (typeof hienThiThongBao !== "undefined") {
        hienThiThongBao("Đã xóa khỏi danh sách yêu thích");
      }
    } else {
      // Chưa thích -> Thêm vào thích
      this.danhSachYeuThich.add(maSach);
      nutBam.classList.add("active");
      nutBam.innerHTML = '<i class="fas fa-heart"></i>'; // Tim đặc

      if (typeof hienThiThongBao !== "undefined") {
        hienThiThongBao("Đã thêm vào danh sách yêu thích");
      }
    }

    this.khiYeuThich(Array.from(this.danhSachYeuThich));
  }
}

// Khởi tạo đối tượng dùng chung
const theSach = new XuLyTheSach();

// Hỗ trợ xuất module nếu cần
if (typeof module !== "undefined" && module.exports) {
  module.exports = { XuLyTheSach, theSach };
}
