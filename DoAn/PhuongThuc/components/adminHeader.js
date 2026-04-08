// ==================== THÀNH PHẦN THANH TIÊU ĐỀ QUẢN TRỊ ====================

class ThanhTieuDeQuanTri {
  constructor(tuyChon = {}) {
    this.tieuDe = tuyChon.title || "Tổng quan";
    this.nguoiDung = tuyChon.user || {
      name: "Admin User",
      role: "Quản lý cửa hàng",
    };
    this.khiTimKiem = tuyChon.onSearch || (() => {});
    this.khiDangXuat = tuyChon.onLogout || (() => {});

    this.thanhPhan = {
      tieuDe: null,
      oTimKiem: null,
      tenNguoiDung: null,
      vaiTroNguoiDung: null,
    };

    this.khoiTao();
  }

  khoiTao() {
    // Tìm các thẻ HTML do PHP in ra
    this.thanhPhan.tieuDe = document.getElementById("admin-view-title");
    this.thanhPhan.oTimKiem = document.querySelector(".admin-search input");
    this.thanhPhan.tenNguoiDung = document.querySelector(".admin-user-name");
    this.thanhPhan.vaiTroNguoiDung = document.querySelector(".admin-user-role");

    this.ganSuKien();
    this.capNhatThongTinNguoiDung();
  }

  ganSuKien() {
    // 1. Chức năng tìm kiếm (Sử dụng hàm trì hoãn để không gọi liên tục khi gõ)
    if (this.thanhPhan.oTimKiem) {
      this.thanhPhan.oTimKiem.addEventListener(
        "input",
        this.triHoan((suKien) => {
          this.khiTimKiem(suKien.target.value);
        }, 300),
      );

      // Phím tắt Ctrl+K hoặc Cmd+K để tự động focus vào ô tìm kiếm
      document.addEventListener("keydown", (suKien) => {
        if ((suKien.ctrlKey || suKien.metaKey) && suKien.key === "k") {
          suKien.preventDefault();
          this.thanhPhan.oTimKiem?.focus();
        }
      });
    }

    // 2. Nút chuông thông báo
    const nutThongBao = document.querySelector(".admin-notification-btn");
    if (nutThongBao) {
      nutThongBao.addEventListener("click", () => {
        // Dùng hàm từ file utils.js đã Việt hóa
        if (typeof hienThiThongBao !== "undefined") {
          hienThiThongBao("Bạn có 3 thông báo mới");
        } else {
          alert("Bạn có 3 thông báo mới");
        }
      });
    }
  }

  // Hàm tiện ích chống gọi liên tục khi gõ phím
  triHoan(hamThucThi, thoiGianCho) {
    let boDemGIO;
    return function (...thamSo) {
      clearTimeout(boDemGIO);
      boDemGIO = setTimeout(() => hamThucThi.apply(this, thamSo), thoiGianCho);
    };
  }

  thietLapTieuDe(tieuDeMoi) {
    this.tieuDe = tieuDeMoi;
    if (this.thanhPhan.tieuDe) {
      this.thanhPhan.tieuDe.textContent = tieuDeMoi;
    }
  }

  capNhatThongTinNguoiDung(nguoiDungMoi) {
    if (nguoiDungMoi) {
      this.nguoiDung = nguoiDungMoi;
    }

    if (this.thanhPhan.tenNguoiDung) {
      this.thanhPhan.tenNguoiDung.textContent = this.nguoiDung.name;
    }
    if (this.thanhPhan.vaiTroNguoiDung) {
      this.thanhPhan.vaiTroNguoiDung.textContent = this.nguoiDung.role;
    }
  }
}

// Hỗ trợ xuất module (Nếu có dùng)
if (typeof module !== "undefined" && module.exports) {
  module.exports = { ThanhTieuDeQuanTri };
}
