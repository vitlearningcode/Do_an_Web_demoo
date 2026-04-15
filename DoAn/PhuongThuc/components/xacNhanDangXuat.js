/**
 * ============================================================
 * LUỒNG: MODAL XÁC NHẬN ĐĂNG XUẤT (xacNhanDangXuat.js)
 *
 * GỌI BỞI: header.php (hoặc layout.php) sau khi render modal HTML
 *   <script src="...xacNhanDangXuat.js"></script>
 *
 * HTML YÊU CẦU (PHP render sẵn trong modalDangXuat.php hoặc header.php):
 *   <div id="logout-modal" class="modal-overlay">
 *     <div class="modal-box">
 *       <button id="logout-confirm">Đăng xuất</button>
 *       <button id="logout-cancel">Hủy</button>
 *     </div>
 *   </div>
 *
 * LUỒNG HOẠT ĐỘNG:
 *   User nhấn "Đăng xuất" (trong header)
 *     → xacNhanDangXuat.mo() / logoutModal.mo()  ← Gọi từ onclick HTML
 *     → Modal hiện lên (class 'active')
 *     → User nhấn "Xác nhận" → xuLyXacNhan()
 *       → window.location.href = xuly_dangxuat.php
 *       → PHP: session_destroy() + redirect về trang chủ
 *     → User nhấn "Hủy" / ESC / click ngoài → dong() → modal đóng
 *
 * BIẾN TOÀN CỤC:
 *   xacNhanDangXuat — instance chính
 *   window.logoutModal — alias tương thích code cũ (onclick="logoutModal.mo()")
 *
 * PHỤ THUỘC:
 *   DUONG_DAN_GOC_JS — biến PHP inject vào <head>:
 *     <script>var DUONG_DAN_GOC_JS = "<?= $duongDanGoc ?>";</script>
 * ============================================================
 */

class XacNhanDangXuat {
  /**
   * @param {object} tuyChon
   *   onConfirm: function() — gọi khi user xác nhận (mặc định: rỗng)
   *   onCancel:  function() — gọi khi user hủy   (mặc định: rỗng)
   */
  constructor(tuyChon = {}) {
    this.dangMo   = false;                           // Trạng thái modal (mở/đóng)
    this.khiXacNhan = tuyChon.onConfirm || (() => {}); // Callback tùy chọn
    this.khiHuy     = tuyChon.onCancel  || (() => {});

    // Tham chiếu các phần tử DOM (tìm sau DOMContentLoaded)
    this.thanhPhan = {
      khungChinh: null, // <div id="logout-modal"> — overlay toàn màn hình
      nutXacNhan: null, // <button id="logout-confirm">
      nutHuy:     null, // <button id="logout-cancel">
    };

    // Đợi DOM sẵn sàng (script có thể load trước <body>)
    document.addEventListener("DOMContentLoaded", () => this.khoiTao());
  }

  khoiTao() {
    // TÌM phần tử do PHP render — không tạo DOM mới
    this.thanhPhan.khungChinh = document.getElementById("logout-modal");
    this.thanhPhan.nutXacNhan = document.getElementById("logout-confirm");
    this.thanhPhan.nutHuy     = document.getElementById("logout-cancel");

    // Chỉ gắn sự kiện nếu modal tồn tại trong trang
    // (Một số trang không có modal đăng xuất — khách vãng lai)
    if (this.thanhPhan.khungChinh) {
      this.ganSuKien();
    }
  }

  ganSuKien() {
    // Nút xác nhận: bắt đầu luồng đăng xuất thật
    if (this.thanhPhan.nutXacNhan) {
      this.thanhPhan.nutXacNhan.addEventListener("click", () =>
        this.xuLyXacNhan(),
      );
    }

    // Nút hủy: đóng modal, không làm gì thêm
    if (this.thanhPhan.nutHuy) {
      this.thanhPhan.nutHuy.addEventListener("click", () => this.dong());
    }

    // Click vào vùng overlay đen bên ngoài modal → đóng
    // suKien.target === khungChinh: phân biệt click vào overlay vs click vào nội dung modal
    if (this.thanhPhan.khungChinh) {
      this.thanhPhan.khungChinh.addEventListener("click", (suKien) => {
        if (suKien.target === this.thanhPhan.khungChinh) this.dong();
      });
    }

    // Phím ESC → đóng modal (UX: người dùng quen dùng ESC để thoát)
    document.addEventListener("keydown", (suKien) => {
      if (suKien.key === "Escape" && this.dangMo) {
        this.dong();
      }
    });
  }

  /**
   * Mở modal xác nhận đăng xuất
   * Gọi từ: onclick="logoutModal.mo()" trong header.php
   */
  mo() {
    this.dangMo = true;
    this.thanhPhan.khungChinh?.classList.add("active");  // CSS: display:flex / opacity:1
    document.body.style.overflow = "hidden"; // Khóa scroll trang nền
  }

  /**
   * Đóng modal (không đăng xuất)
   */
  dong() {
    this.dangMo = false;
    this.thanhPhan.khungChinh?.classList.remove("active");
    document.body.style.overflow = ""; // Mở lại scroll
    this.khiHuy(); // Gọi callback (nếu có)
  }

  /**
   * Xử lý khi user bấm "Xác nhận đăng xuất"
   * Luồng: đóng modal → gọi callback → redirect sang xuly_dangxuat.php
   */
  xuLyXacNhan() {
    this.dong(); // Đóng modal trước (tránh modal còn mở khi redirect)

    // Gọi callback tùy chọn (ví dụ: xóa localStorage, log analytics...)
    this.khiXacNhan();

    // Chuyển hướng sang file PHP xử lý đăng xuất:
    //   xuly_dangxuat.php → session_unset() + session_destroy() + redirect('/')
    // DUONG_DAN_GOC_JS: được PHP inject vào <head>, ví dụ: "/DoAn-Web/DoAn/"
    window.location.href = DUONG_DAN_GOC_JS + "CuaHang/PhienDangNhap/xuly_dangxuat.php";
  }
}

// Singleton toàn cục
const xacNhanDangXuat = new XacNhanDangXuat();

// Alias backward compatibility — code PHP cũ dùng: onclick="logoutModal.mo()"
window.logoutModal = xacNhanDangXuat;
