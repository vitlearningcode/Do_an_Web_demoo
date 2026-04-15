/**
 * ============================================================
 * LUỒNG: TOAST THÔNG BÁO NỔI (thongBao.js)
 *
 * GỌI BỞI: Nhiều file JS trong hệ thống:
 *   - hienThiThongBao("...") — hàm tắt toàn cục (cách cũ)
 *   - toast.success/error/warning/info("...") — API object
 *   - thongBao.thanhCong("...") — gọi trực tiếp
 *
 * YÊU CẦU HTML (PHP render sẵn trong footer.php):
 *   <div id="toast" class="toast-notification">
 *     <i class="fas"></i>
 *     <span id="toast-message"></span>
 *   </div>
 *   CSS: .toast-notification { display:none } → .show { display:flex }
 *
 * PHÂN BIỆT VỚI cart-toast (trong cart.js):
 *   toast này: thông báo hệ thống (login, wishlist, lỗi...)
 *   cart-toast: dành riêng cho giỏ hàng (id="cart-toast")
 *   → Hai toast hoàn toàn độc lập, không xung đột
 *
 * ALIAS PATTERN (tránh breaking change):
 *   thongBao.thanhCong() ← cách gọi mới (class method)
 *   toast.success()      ← alias tương thích code cũ
 *   hienThiThongBao()    ← alias hàm toàn cục (cách cũ nhất)
 * ============================================================
 */

class PopupThongBao {
  constructor() {
    this.theThongBao   = null;  // <div id="toast">
    this.theTinNhan    = null;  // <span id="toast-message">
    this.theBieuTuong  = null;  // <i> icon bên trong toast
    this.boDemThoiGian = null;  // setTimeout ID — reset khi gọi liên tục

    // Đợi DOM parse xong mới tìm phần tử
    // (script có thể load ở <head> trước khi <body> render xong)
    document.addEventListener("DOMContentLoaded", () => this.khoiTao());
  }

  khoiTao() {
    // CHỈ TÌM phần tử do PHP render — KHÔNG tự tạo HTML
    // Lý do: giữ HTML trong PHP để dễ bảo trì, không vi phạm nguyên tắc no-innerHTML
    this.theThongBao = document.getElementById("toast");
    this.theTinNhan  = document.getElementById("toast-message");

    if (this.theThongBao) {
      // Tìm thẻ <i> bên trong toast (PHP đã render sẵn)
      this.theBieuTuong = this.theThongBao.querySelector("i");
    } else {
      // Cảnh báo lập trình viên — không hiện cho user
      console.warn(
        "Lỗi: Chưa tìm thấy khung HTML của Popup Thông báo trên trang!",
      );
    }
  }

  /**
   * Hiển thị toast với nội dung và kiểu tuỳ chỉnh
   * @param {string} tinNhan  - Nội dung thông báo
   * @param {string} kieu     - 'thanh_cong' | 'loi' | 'canh_bao' | 'thong_tin'
   * @param {number} thoiGian - Thời gian hiển thị (ms), mặc định 3000
   */
  hienThi(tinNhan, kieu = "thanh_cong", thoiGian = 3000) {
    // Fallback: nếu DOM chưa sẵn sàng (hiếm gặp) → alert() thay thế
    if (!this.theThongBao) {
      alert(tinNhan);
      return;
    }

    this.theTinNhan.textContent = tinNhan;

    // Reset class icon về base (.fas) trước khi thêm icon mới
    // Tránh icon cũ còn sót lại từ lần hiển thị trước
    this.theBieuTuong.className = "fas";

    // Đổi icon và màu theo loại thông báo (Font Awesome icons)
    switch (kieu) {
      case "thanh_cong":
        this.theBieuTuong.classList.add("fa-check-circle");
        this.theBieuTuong.style.color = "#10b981"; // Xanh lá — thành công
        break;
      case "loi":
        this.theBieuTuong.classList.add("fa-exclamation-circle");
        this.theBieuTuong.style.color = "#ef4444"; // Đỏ — lỗi
        break;
      case "canh_bao":
        this.theBieuTuong.classList.add("fa-exclamation-triangle");
        this.theBieuTuong.style.color = "#f59e0b"; // Vàng — cảnh báo
        break;
      case "thong_tin":
        this.theBieuTuong.classList.add("fa-info-circle");
        this.theBieuTuong.style.color = "#3b82f6"; // Xanh dương — thông tin
        break;
    }

    // Hiển thị toast (CSS transition: opacity/transform sẽ chạy)
    this.theThongBao.classList.add("show");

    // Reset timer nếu user gọi toast liên tục (tránh toast biến mất sớm)
    if (this.boDemThoiGian) {
      clearTimeout(this.boDemThoiGian);
    }

    // Tự ẩn sau thoiGian ms
    this.boDemThoiGian = setTimeout(() => {
      this.an();
    }, thoiGian);
  }

  /**
   * Ẩn toast (xóa class 'show')
   */
  an() {
    if (this.theThongBao) {
      this.theThongBao.classList.remove("show"); // CSS: fade out / slide down
    }
  }

  // --- Các hàm viết tắt (Sugar methods) ---
  thanhCong(tinNhan, thoiGian = 3000) { this.hienThi(tinNhan, "thanh_cong", thoiGian); }
  loi(tinNhan, thoiGian = 3000)       { this.hienThi(tinNhan, "loi", thoiGian); }
  canhBao(tinNhan, thoiGian = 3000)   { this.hienThi(tinNhan, "canh_bao", thoiGian); }
  thongTin(tinNhan, thoiGian = 3000)  { this.hienThi(tinNhan, "thong_tin", thoiGian); }
}

// Singleton toàn cục — các file JS khác dùng thẳng biến này
const thongBao = new PopupThongBao();

// ============================================================
// ALIAS PATTERN — Backward compatibility
// Code cũ dùng toast.success() / hienThiThongBao()
// Thay vì sửa tất cả nơi gọi → tạo alias trỏ về thongBao
// ============================================================

// API kiểu object: toast.success("...") → thongBao.thanhCong("...")
window.toast = {
  success: (msg) => thongBao.thanhCong(msg),
  error:   (msg) => thongBao.loi(msg),
  warning: (msg) => thongBao.canhBao(msg),
  info:    (msg) => thongBao.thongTin(msg),
};

// Hàm toàn cục cũ (gọi từ PHP inline onclick hoặc file JS thời đầu)
window.hienThiThongBao = function (noiDung) {
  thongBao.thanhCong(noiDung);
};
