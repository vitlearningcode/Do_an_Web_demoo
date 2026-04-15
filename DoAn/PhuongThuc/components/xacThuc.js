/**
 * ============================================================
 * LUỒNG: MODAL ĐĂNG NHẬP / ĐĂNG KÝ (xacThuc.js)
 *
 * GỌI BỞI: header.php (render HTML modal) + cart.js / btnThemGioHang.js
 *   Khi user chưa đăng nhập và thực hiện thao tác cần auth:
 *     authModal.mo('dang_nhap') — mở modal đăng nhập
 *     authModal.mo('dang_ky')  — mở modal đăng ký
 *
 * HTML YÊU CẦU (PHP render sẵn trong modalDangNhap.php):
 *   <div id="auth-modal" class="modal-overlay">
 *     <h2>...</h2>
 *     <form id="auth-form" method="POST" action=".../xuly_dangnhap.php">
 *       ...
 *     </form>
 *     <form id="auth-form-dk" method="POST" action=".../xuly_dangky.php">
 *       ...
 *     </form>
 *     <div id="footer-text-dn">...</div>   ← "Chưa có tài khoản? Đăng ký"
 *     <div id="footer-text-dk">...</div>   ← "Đã có tài khoản? Đăng nhập"
 *     <button id="auth-close">✕</button>
 *   </div>
 *
 * LUỒNG HOẠT ĐỘNG:
 *   A. ĐĂNG NHẬP:
 *     User click nút "Đăng nhập" → authModal.mo('dang_nhap')
 *     → capNhatGiaoDien(): hiện form-dn, ẩn form-dk
 *     → User nhập liệu → form.submit() (thuần PHP, không fetch/AJAX)
 *     → PHP: xuly_dangnhap.php xác thực → set SESSION → redirect
 *
 *   B. ĐĂNG KÝ:
 *     User click "Đăng ký" hoặc chuyển chế độ → cheDo = 'dang_ky'
 *     → capNhatGiaoDien(): ẩn form-dn, hiện form-dk
 *     → User nhập → form.submit() → xuly_dangky.php
 *
 *   C. CHUYỂN CHẾ ĐỘ (Toggle):
 *     Link "Chưa có tài khoản?" → chuyenCheDo() → swap form hiển thị
 *
 * NGUYÊN TẮC THIẾT KẾ:
 *   - KHÔNG tạo HTML bằng innerHTML (no-innerHTML rule)
 *   - PHP render sẵn cả 2 form, JS chỉ toggle display
 *   - Form submit THẬT (không preventDefault) → PHP xử lý
 *
 * BIẾN TOÀN CỤC:
 *   authModal — instance chính
 *   authModal.open — alias cho authModal.mo (backward compatible)
 * ============================================================
 */

class HopThoaiXacThuc {
  /**
   * @param {object} tuyChon
   *   cheDo: 'dang_nhap' | 'dang_ky' — chế độ mặc định khi mở
   */
  constructor(tuyChon = {}) {
    this.dangMo = false;                              // Trạng thái modal
    this.cheDo  = tuyChon.cheDo || "dang_nhap";      // Chế độ hiện tại

    // Tham chiếu DOM — tìm trong khoiTao()
    this.thanhPhan = {
      khungChinh:    null, // <div id="auth-modal"> — overlay
      bieuMau:       null, // <form id="auth-form"> — form đăng nhập
      tieuDe:        null, // <h2> bên trong modal
      chuChuyenDoi:  null, // .modal-footer-text — link chuyển chế độ
      nutXacNhan:    null, // button[type="submit"] trong form
    };

    // Khởi tạo ngay (không đợi DOMContentLoaded vì script thường ở cuối body)
    this.khoiTao();
  }

  khoiTao() {
    // TÌM phần tử do PHP render sẵn — KHÔNG tạo DOM mới
    this.thanhPhan.khungChinh   = document.getElementById("auth-modal");
    this.thanhPhan.bieuMau      = document.getElementById("auth-form");
    this.thanhPhan.tieuDe       = this.thanhPhan.khungChinh?.querySelector("h2");
    this.thanhPhan.chuChuyenDoi = document.querySelector(".modal-footer-text");
    // Tìm nút submit trong form đăng nhập
    this.thanhPhan.nutXacNhan   = this.thanhPhan.bieuMau?.querySelector(
      'button[type="submit"]',
    );

    this.ganSuKien();
  }

  ganSuKien() {
    // Nút đóng ✕ (id="auth-close")
    const nutDong = document.getElementById("auth-close");
    if (nutDong) {
      nutDong.addEventListener("click", () => this.dong());
    }

    // Click vào overlay ngoài modal → đóng
    // suKien.target === khungChinh: đảm bảo click vào nền, không phải nội dung
    if (this.thanhPhan.khungChinh) {
      this.thanhPhan.khungChinh.addEventListener("click", (suKien) => {
        if (suKien.target === this.thanhPhan.khungChinh) this.dong();
      });
    }

    // Form submit handler
    // QUAN TRỌNG: KHÔNG gọi suKien.preventDefault() ở đây
    // → Để form submit THẬT sang PHP (xuly_dangnhap.php / xuly_dangky.php)
    if (this.thanhPhan.bieuMau) {
      this.thanhPhan.bieuMau.addEventListener("submit", (suKien) =>
        this.xuLyGuiForm(suKien),
      );
    }

    // ESC đóng modal (UX standard)
    document.addEventListener("keydown", (suKien) => {
      if (suKien.key === "Escape" && this.dangMo) {
        this.dong();
      }
    });
  }

  /**
   * Mở modal với chế độ chỉ định
   * @param {string} cheDoMoi - 'login' | 'dang_nhap' | 'dang_ky'
   *   'login' được chấp nhận để tương thích code cũ
   *
   * Gọi từ:
   *   onclick="authModal.mo('dang_nhap')" — từ nút trong header.php
   *   authModal.mo('dang_ky') — từ cart.js khi user chưa đăng nhập
   */
  mo(cheDoMoi = "login") {
    // Chuẩn hóa: 'login' (tiếng Anh) → 'dang_nhap' (nhất quán nội bộ)
    this.cheDo = cheDoMoi === "login" ? "dang_nhap" : cheDoMoi;

    this.capNhatGiaoDien();               // Hiện form phù hợp với cheDo
    this.dangMo = true;
    this.thanhPhan.khungChinh?.classList.add("active");  // CSS: hiện overlay
    document.body.style.overflow = "hidden";             // Khóa scroll trang nền

    // Auto-focus ô nhập liệu đầu tiên sau khi modal hiện (100ms: chờ CSS transition)
    setTimeout(() => {
      const oNhapLieuDauTien = this.thanhPhan.bieuMau?.querySelector("input");
      oNhapLieuDauTien?.focus(); // Optional chaining: không lỗi nếu không có input
    }, 100);
  }

  /**
   * Đóng modal + reset form
   */
  dong() {
    this.dangMo = false;
    this.thanhPhan.khungChinh?.classList.remove("active");
    document.body.style.overflow = "";
    this.thanhPhan.bieuMau?.reset(); // Xóa hết dữ liệu đã nhập trong form
  }

  /**
   * Toggle giữa đăng nhập và đăng ký
   * Gọi từ: link "Chưa có tài khoản? Đăng ký" / "Đã có tài khoản? Đăng nhập"
   */
  chuyenCheDo() {
    this.cheDo = this.cheDo === "dang_nhap" ? "dang_ky" : "dang_nhap";
    this.capNhatGiaoDien(); // Cập nhật UI theo chế độ mới
  }

  /**
   * Cập nhật giao diện modal theo chế độ hiện tại (dang_nhap / dang_ky)
   * PHP đã render sẵn CẢ 2 form — JS chỉ toggle display, KHÔNG dùng innerHTML
   */
  capNhatGiaoDien() {
    const formDN = document.getElementById("auth-form");    // Form đăng nhập
    const formDK = document.getElementById("auth-form-dk"); // Form đăng ký

    // Đổi tiêu đề modal (h2) theo chế độ
    if (this.thanhPhan.tieuDe) {
      this.thanhPhan.tieuDe.textContent =
        this.cheDo === "dang_nhap" ? "Đăng nhập" : "Đăng ký";
    }

    // Toggle form: chỉ hiện form phù hợp với cheDo
    // display="" (chuỗi rỗng): khôi phục display gốc từ CSS (thường là block/flex)
    if (formDN) formDN.style.display = this.cheDo === "dang_nhap" ? "" : "none";
    if (formDK) formDK.style.display = this.cheDo === "dang_ky"   ? "" : "none";

    // Toggle footer links (PHP render sẵn 2 dòng, JS ẩn/hiện theo chế độ)
    //   footer-text-dn: "Chưa có tài khoản? Đăng ký ngay"    → hiện khi đang ở đăng nhập
    //   footer-text-dk: "Đã có tài khoản? Đăng nhập ngay"    → hiện khi đang ở đăng ký
    const footerDN = document.getElementById("footer-text-dn");
    const footerDK = document.getElementById("footer-text-dk");
    if (footerDN) footerDN.style.display = this.cheDo === "dang_nhap" ? "" : "none";
    if (footerDK) footerDK.style.display = this.cheDo === "dang_ky"   ? "" : "none";
  }

  /**
   * Xử lý form submit
   * KHÔNG preventDefault — để form submit THẬT sang PHP
   * JS chỉ đóng modal trước khi trang redirect (tránh flash modal sau redirect)
   */
  xuLyGuiForm(suKien) {
    // Không chặn form submit — PHP sẽ xử lý và redirect
    // Chỉ đóng modal để UX mượt mà hơn khi trang chuyển hướng
    this.dong();
  }
}

// Singleton toàn cục
const authModal = new HopThoaiXacThuc();

// Alias: authModal.open() → authModal.mo() (backward compatible)
// Code PHP cũ có thể dùng: onclick="authModal.open('login')"
authModal.open = authModal.mo;

// Hỗ trợ CommonJS (nếu chạy trong môi trường Node.js / test)
if (typeof module !== "undefined" && module.exports) {
  module.exports = { HopThoaiXacThuc, authModal };
}
