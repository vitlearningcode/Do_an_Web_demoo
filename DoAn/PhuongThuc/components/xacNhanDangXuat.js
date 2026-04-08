// ==================== KHUNG XÁC NHẬN ĐĂNG XUẤT ====================

class XacNhanDangXuat {
  constructor(tuyChon = {}) {
    this.dangMo = false;
    this.khiXacNhan = tuyChon.onConfirm || (() => {});
    this.khiHuy = tuyChon.onCancel || (() => {});

    this.thanhPhan = {
      khungChinh: null,
      nutXacNhan: null,
      nutHuy: null,
    };

    document.addEventListener("DOMContentLoaded", () => this.khoiTao());
  }

  khoiTao() {
    // CHỈ ĐI TÌM HTML DO PHP IN RA
    this.thanhPhan.khungChinh = document.getElementById("logout-modal");
    this.thanhPhan.nutXacNhan = document.getElementById("logout-confirm");
    this.thanhPhan.nutHuy = document.getElementById("logout-cancel");

    if (this.thanhPhan.khungChinh) {
      this.ganSuKien();
    }
  }

  ganSuKien() {
    // Nút xác nhận đăng xuất
    if (this.thanhPhan.nutXacNhan) {
      this.thanhPhan.nutXacNhan.addEventListener("click", () =>
        this.xuLyXacNhan(),
      );
    }

    // Nút hủy
    if (this.thanhPhan.nutHuy) {
      this.thanhPhan.nutHuy.addEventListener("click", () => this.dong());
    }

    // Bấm ra ngoài vùng đen để đóng
    if (this.thanhPhan.khungChinh) {
      this.thanhPhan.khungChinh.addEventListener("click", (suKien) => {
        if (suKien.target === this.thanhPhan.khungChinh) this.dong();
      });
    }

    // Phím ESC để đóng
    document.addEventListener("keydown", (suKien) => {
      if (suKien.key === "Escape" && this.dangMo) {
        this.dong();
      }
    });
  }

  mo() {
    this.dangMo = true;
    this.thanhPhan.khungChinh?.classList.add("active");
    document.body.style.overflow = "hidden";
  }

  dong() {
    this.dangMo = false;
    this.thanhPhan.khungChinh?.classList.remove("active");
    document.body.style.overflow = "";
    this.khiHuy();
  }

  xuLyXacNhan() {
    this.dong();

    // 1. Gọi hàm callback nếu có
    this.khiXacNhan();

    // 2. CHUẨN PHP: Chuyển hướng tới file xử lý đăng xuất của PHP để xóa Session
    // Ông tạo file này để chạy session_destroy() nhé
    window.location.href = "ChucNang/CuaHang/XacThuc/dang_xuat.php";
  }
}

// Khởi tạo đối tượng toàn cục
const xacNhanDangXuat = new XacNhanDangXuat();

// Giữ lại tên cũ để các file khác gọi không bị lỗi
window.logoutModal = xacNhanDangXuat;
