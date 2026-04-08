// ==================== THÀNH PHẦN BANNER TRƯỢT (CAROUSEL) ====================

class TrinhChieuBanner {
  constructor(idVungChua, tuyChon = {}) {
    this.vungChua = document.getElementById(idVungChua);
    this.danhSachBanner = [];
    this.viTriHienTai = 0;
    this.tuDongChay = tuyChon.autoPlay !== false;
    this.thoiGianChuyen = tuyChon.autoPlayInterval || 5000;
    this.boDemGio = null;

    // Nếu tìm thấy vùng chứa trên trang, thì khởi tạo sự kiện
    if (this.vungChua) {
      this.khoiTao();
    }
  }

  khoiTao() {
    // 1. Tìm tất cả các thẻ Banner con mà PHP ĐÃ IN SẴN
    this.danhSachBanner = Array.from(
      this.vungChua.querySelectorAll(".hero-slide"),
    );

    if (this.danhSachBanner.length === 0) return;

    // Hiển thị tấm Banner đầu tiên
    this.chuyenDenBanner(0);

    // Tạo các chấm tròn (Indicators) bên dưới
    this.taoCacChamTron();

    // Gắn sự kiện click, hover
    this.ganSuKien();

    if (this.tuDongChay) {
      this.batDauTuDongChay();
    }
  }

  taoCacChamTron() {
    const vungChuaCham = document.getElementById("hero-indicators");
    if (!vungChuaCham) return;

    vungChuaCham.innerHTML = ""; // Làm sạch vùng chứa chấm tròn

    this.danhSachBanner.forEach((_, viTri) => {
      const theCham = document.createElement("div");
      theCham.className = `hero-indicator ${viTri === 0 ? "active" : ""}`;

      // Bấm vào chấm tròn nào thì chuyển tới banner đó
      theCham.addEventListener("click", () => this.chuyenDenBanner(viTri));
      vungChuaCham.appendChild(theCham);
    });
  }

  ganSuKien() {
    // Nút Tới / Lui (Nếu PHP in ra)
    const nutLui = document.getElementById("hero-prev");
    const nutToi = document.getElementById("hero-next");

    if (nutLui) {
      nutLui.addEventListener("click", () => this.bannerTruoc());
    }

    if (nutToi) {
      nutToi.addEventListener("click", () => this.bannerTiepTheo());
    }

    // Dừng tự động chạy khi rê chuột vào Banner
    if (this.vungChua) {
      this.vungChua.addEventListener("mouseenter", () => this.dungTuDongChay());
      this.vungChua.addEventListener("mouseleave", () =>
        this.batDauTuDongChay(),
      );
    }
  }

  chuyenDenBanner(viTri) {
    // 1. Cập nhật class 'active' cho tấm Banner
    this.danhSachBanner.forEach((theBanner, i) => {
      theBanner.classList.toggle("active", i === viTri);
    });

    // 2. Cập nhật class 'active' cho các chấm tròn
    const danhSachCham = document.querySelectorAll(".hero-indicator");
    danhSachCham.forEach((theCham, i) => {
      theCham.classList.toggle("active", i === viTri);
    });

    this.viTriHienTai = viTri;
  }

  bannerTiepTheo() {
    const viTriTiep = (this.viTriHienTai + 1) % this.danhSachBanner.length;
    this.chuyenDenBanner(viTriTiep);
  }

  bannerTruoc() {
    const viTriTruoc =
      (this.viTriHienTai - 1 + this.danhSachBanner.length) %
      this.danhSachBanner.length;
    this.chuyenDenBanner(viTriTruoc);
  }

  batDauTuDongChay() {
    this.dungTuDongChay();
    this.boDemGio = setInterval(
      () => this.bannerTiepTheo(),
      this.thoiGianChuyen,
    );
  }

  dungTuDongChay() {
    if (this.boDemGio) {
      clearInterval(this.boDemGio);
      this.boDemGio = null;
    }
  }
}

// Hàm khởi tạo toàn cục để app.js có thể gọi
function khoiTaoTrinhChieuBanner() {
  return new TrinhChieuBanner("hero-slider");
}

// Đẩy ra window (giữ nguyên hàm tiếng Anh cũ để không lỗi file app.js chưa kịp sửa)
window.initHeroCarousel = khoiTaoTrinhChieuBanner;

// Hỗ trợ xuất module
if (typeof module !== "undefined" && module.exports) {
  module.exports = { TrinhChieuBanner, khoiTaoTrinhChieuBanner };
}
