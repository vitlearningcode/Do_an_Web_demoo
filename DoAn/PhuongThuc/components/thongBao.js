// ==================== THÀNH PHẦN THÔNG BÁO NỔI (TOAST) ====================

class PopupThongBao {
  constructor() {
    this.theThongBao = null;
    this.theTinNhan = null;
    this.theBieuTuong = null;
    this.boDemThoiGian = null;

    // Đợi HTML tải xong mới đi tìm thẻ
    document.addEventListener("DOMContentLoaded", () => this.khoiTao());
  }

  khoiTao() {
    // CHỈ ĐI TÌM THẺ DO PHP ĐÃ IN RA (Không tự tạo HTML nữa)
    this.theThongBao = document.getElementById("toast");
    this.theTinNhan = document.getElementById("toast-message");

    if (this.theThongBao) {
      this.theBieuTuong = this.theThongBao.querySelector("i");
    } else {
      console.warn(
        "Lỗi: Chưa tìm thấy khung HTML của Popup Thông báo trên trang!",
      );
    }
  }

  hienThi(tinNhan, kieu = "thanh_cong", thoiGian = 3000) {
    // Nếu chưa load xong HTML thì tạm thời dùng alert chữa cháy
    if (!this.theThongBao) {
      alert(tinNhan);
      return;
    }

    this.theTinNhan.textContent = tinNhan;

    // Reset lại class của icon
    this.theBieuTuong.className = "fas";

    // Đổi icon và màu sắc tùy theo kiểu thông báo
    switch (kieu) {
      case "thanh_cong":
        this.theBieuTuong.classList.add("fa-check-circle");
        this.theBieuTuong.style.color = "#10b981"; // Xanh lá
        break;
      case "loi":
        this.theBieuTuong.classList.add("fa-exclamation-circle");
        this.theBieuTuong.style.color = "#ef4444"; // Đỏ
        break;
      case "canh_bao":
        this.theBieuTuong.classList.add("fa-exclamation-triangle");
        this.theBieuTuong.style.color = "#f59e0b"; // Vàng
        break;
      case "thong_tin":
        this.theBieuTuong.classList.add("fa-info-circle");
        this.theBieuTuong.style.color = "#3b82f6"; // Xanh dương
        break;
    }

    this.theThongBao.classList.add("show");

    // Xóa bộ đếm cũ nếu người dùng bấm liên tục
    if (this.boDemThoiGian) {
      clearTimeout(this.boDemThoiGian);
    }

    // Tự động ẩn sau X giây
    this.boDemThoiGian = setTimeout(() => {
      this.an();
    }, thoiGian);
  }

  an() {
    if (this.theThongBao) {
      this.theThongBao.classList.remove("show");
    }
  }

  // Các hàm viết tắt cho dễ gọi
  thanhCong(tinNhan, thoiGian = 3000) {
    this.hienThi(tinNhan, "thanh_cong", thoiGian);
  }
  loi(tinNhan, thoiGian = 3000) {
    this.hienThi(tinNhan, "loi", thoiGian);
  }
  canhBao(tinNhan, thoiGian = 3000) {
    this.hienThi(tinNhan, "canh_bao", thoiGian);
  }
  thongTin(tinNhan, thoiGian = 3000) {
    this.hienThi(tinNhan, "thong_tin", thoiGian);
  }
}

// Khởi tạo đối tượng toàn cục
const thongBao = new PopupThongBao();

// ==================== BỘ ÁNH XẠ (ALIAS) CHỐNG SẬP WEB ====================
// Vì ở các file trước ta có dùng `toast.success` và `hienThiThongBao`,
// ta sẽ nối tất cả chúng về chung 1 hàm `thongBao.thanhCong` này để đồng bộ 100%.

window.toast = {
  success: (msg) => thongBao.thanhCong(msg),
  error: (msg) => thongBao.loi(msg),
  warning: (msg) => thongBao.canhBao(msg),
  info: (msg) => thongBao.thongTin(msg),
};

window.hienThiThongBao = function (noiDung) {
  thongBao.thanhCong(noiDung);
};
