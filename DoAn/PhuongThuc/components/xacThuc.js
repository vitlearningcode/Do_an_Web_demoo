// ==================== THÀNH PHẦN KHUNG ĐĂNG NHẬP / ĐĂNG KÝ ====================

class HopThoaiXacThuc {
  constructor(tuyChon = {}) {
    this.dangMo = false;
    this.cheDo = tuyChon.cheDo || "dang_nhap"; // 'dang_nhap' hoặc 'dang_ky'

    this.thanhPhan = {
      khungChinh: null,
      bieuMau: null,
      tieuDe: null,
      chuChuyenDoi: null,
      nutXacNhan: null,
    };

    this.khoiTao();
  }

  khoiTao() {
    this.thanhPhan.khungChinh = document.getElementById("auth-modal");
    this.thanhPhan.bieuMau = document.getElementById("auth-form");
    this.thanhPhan.tieuDe = this.thanhPhan.khungChinh?.querySelector("h2");
    this.thanhPhan.chuChuyenDoi = document.querySelector(".modal-footer-text");
    this.thanhPhan.nutXacNhan = this.thanhPhan.bieuMau?.querySelector(
      'button[type="submit"]',
    );

    this.ganSuKien();
  }

  ganSuKien() {
    const nutDong = document.getElementById("auth-close");
    if (nutDong) {
      nutDong.addEventListener("click", () => this.dong());
    }

    // Bấm ra ngoài khung thì đóng
    if (this.thanhPhan.khungChinh) {
      this.thanhPhan.khungChinh.addEventListener("click", (suKien) => {
        if (suKien.target === this.thanhPhan.khungChinh) this.dong();
      });
    }

    // XỬ LÝ FORM SUBMIT
    if (this.thanhPhan.bieuMau) {
      this.thanhPhan.bieuMau.addEventListener("submit", (suKien) =>
        this.xuLyGuiForm(suKien),
      );
    }

    // Bấm nút Esc trên bàn phím để đóng
    document.addEventListener("keydown", (suKien) => {
      if (suKien.key === "Escape" && this.dangMo) {
        this.dong();
      }
    });
  }

  mo(cheDoMoi = "login") {
    // Đổi 'login' thành tiếng việt để đồng bộ
    this.cheDo = cheDoMoi === "login" ? "dang_nhap" : cheDoMoi;

    this.capNhatGiaoDien();
    this.dangMo = true;
    this.thanhPhan.khungChinh?.classList.add("active");
    document.body.style.overflow = "hidden"; // Chống cuộn trang

    // Tự động focus vào ô nhập liệu đầu tiên
    setTimeout(() => {
      const oNhapLieuDauTien = this.thanhPhan.bieuMau?.querySelector("input");
      oNhapLieuDauTien?.focus();
    }, 100);
  }

  dong() {
    this.dangMo = false;
    this.thanhPhan.khungChinh?.classList.remove("active");
    document.body.style.overflow = "";
    this.thanhPhan.bieuMau?.reset();
  }

  chuyenCheDo() {
    this.cheDo = this.cheDo === "dang_nhap" ? "dang_ky" : "dang_nhap";
    this.capNhatGiaoDien();
  }

  capNhatGiaoDien() {
    // Đổi chữ Tiêu đề
    if (this.thanhPhan.tieuDe) {
      this.thanhPhan.tieuDe.textContent =
        this.cheDo === "dang_nhap" ? "Đăng nhập" : "Đăng ký";
    }

    // Đổi chữ Nút bấm
    if (this.thanhPhan.nutXacNhan) {
      this.thanhPhan.nutXacNhan.textContent =
        this.cheDo === "dang_nhap" ? "Đăng nhập" : "Đăng ký";
    }

    // Đổi dòng chữ "Chưa có tài khoản..." ở dưới
    if (this.thanhPhan.chuChuyenDoi) {
      if (this.cheDo === "dang_nhap") {
        this.thanhPhan.chuChuyenDoi.innerHTML =
          'Chưa có tài khoản? <a href="#" onclick="authModal.chuyenCheDo(); return false;">Đăng ký ngay</a>';
      } else {
        this.thanhPhan.chuChuyenDoi.innerHTML =
          'Đã có tài khoản? <a href="#" onclick="authModal.chuyenCheDo(); return false;">Đăng nhập ngay</a>';
      }
    }
  }

  xuLyGuiForm(suKien) {
    // LƯU Ý QUAN TRỌNG VỚI PHP:
    // Nếu ông đã gắn thẻ <form action="file_php_cua_ong.php" method="POST">
    // thì HÃY XÓA DÒNG `suKien.preventDefault();` dưới đây đi để PHP tự chuyển trang.

    // Tạm thời giữ lại để test giao diện không bị load lại trang:
    suKien.preventDefault();

    // Nếu gọi thông báo từ utils.js
    if (typeof hienThiThongBao !== "undefined") {
      hienThiThongBao(
        this.cheDo === "dang_nhap"
          ? "Đang gửi yêu cầu đăng nhập..."
          : "Đang gửi yêu cầu đăng ký...",
      );
    }

    // Sau khi nộp form thì đóng bảng
    this.dong();
  }
}

// Khởi tạo đối tượng toàn cục để app.js có thể gọi
const authModal = new HopThoaiXacThuc();

// Alias (ánh xạ) lại hàm open thành mo để file app.js cũ không bị lỗi
authModal.open = authModal.mo;

// Hỗ trợ xuất module (nếu dùng)
if (typeof module !== "undefined" && module.exports) {
  module.exports = { HopThoaiXacThuc, authModal };
}
