// ==================== KHUNG TẠO ẢNH BÌA SÁCH BẰNG AI ====================

class HopThoaiTaoAnhAI {
  constructor(tuyChon = {}) {
    this.dangMo = false;
    this.tenSach = "";
    this.khiChonAnh = tuyChon.onImageSelect || (() => {});
    this.danhSachAnhDaTao = [];
    this.dangXuLy = false;

    this.thanhPhan = {
      khungChinh: null,
      tieuDeSach: null,
      oNhapMoTa: null,
      nutTaoAnh: null,
      khuVucChuaAnh: null,
      hieuUngTai: null,
    };

    // Mảng ảnh giả lập (Thực tế sẽ gọi API từ AI để lấy ảnh)
    this.anhGiaLap = [
      "https://picsum.photos/seed/ai1/300/400",
      "https://picsum.photos/seed/ai2/300/400",
      "https://picsum.photos/seed/ai3/300/400",
      "https://picsum.photos/seed/ai4/300/400",
      "https://picsum.photos/seed/ai5/300/400",
      "https://picsum.photos/seed/ai6/300/400",
    ];

    document.addEventListener("DOMContentLoaded", () => this.khoiTao());
  }

  khoiTao() {
    // TÌM CÁC THẺ DO PHP ĐÃ IN RA SẴN
    this.thanhPhan.khungChinh = document.getElementById(
      "image-generator-modal",
    );
    this.thanhPhan.tieuDeSach = document.getElementById("ai-book-title");
    this.thanhPhan.oNhapMoTa = document.getElementById("ai-prompt");
    this.thanhPhan.nutTaoAnh = document.getElementById("generate-btn");
    this.thanhPhan.khuVucChuaAnh = document.getElementById("generated-images");
    this.thanhPhan.hieuUngTai = document.getElementById("ai-loading");

    if (this.thanhPhan.khungChinh) {
      this.ganSuKien();
    } else {
      console.warn("Chưa tìm thấy HTML của khung tạo ảnh AI trên trang này.");
    }
  }

  ganSuKien() {
    // Nút đóng khung (Dấu X)
    const nutDong = document.getElementById("ai-modal-close");
    if (nutDong) {
      nutDong.addEventListener("click", () => this.dong());
    }

    // Bấm ra ngoài vùng đen thì đóng khung
    if (this.thanhPhan.khungChinh) {
      this.thanhPhan.khungChinh.addEventListener("click", (suKien) => {
        if (suKien.target === this.thanhPhan.khungChinh) this.dong();
      });
    }

    // Bấm nút tạo ảnh
    if (this.thanhPhan.nutTaoAnh) {
      this.thanhPhan.nutTaoAnh.addEventListener("click", () =>
        this.tienHanhTaoAnh(),
      );
    }
  }

  mo(tenSachDeTao) {
    this.tenSach = tenSachDeTao;
    this.danhSachAnhDaTao = [];

    if (this.thanhPhan.tieuDeSach) {
      this.thanhPhan.tieuDeSach.textContent = `Tạo ảnh bìa cho: ${tenSachDeTao}`;
    }

    if (this.thanhPhan.oNhapMoTa) {
      this.thanhPhan.oNhapMoTa.value = "";
    }

    if (this.thanhPhan.khuVucChuaAnh) {
      this.thanhPhan.khuVucChuaAnh.innerHTML = "";
    }

    this.dangMo = true;
    this.thanhPhan.khungChinh?.classList.add("active");
    document.body.style.overflow = "hidden";
  }

  dong() {
    this.dangMo = false;
    this.thanhPhan.khungChinh?.classList.remove("active");
    document.body.style.overflow = "";
  }

  async tienHanhTaoAnh() {
    if (this.dangXuLy) return;

    this.dangXuLy = true;
    this.thanhPhan.nutTaoAnh.disabled = true;
    this.thanhPhan.nutTaoAnh.innerHTML =
      '<i class="fas fa-spinner fa-spin"></i> Đang tạo...';
    this.thanhPhan.hieuUngTai.style.display = "block";
    this.thanhPhan.khuVucChuaAnh.innerHTML = "";

    // Giả lập thời gian chờ AI xử lý (2 giây)
    await new Promise((hoanThanh) => setTimeout(hoanThanh, 2000));

    // Lấy 6 ảnh ngẫu nhiên
    const mangXaoTron = [...this.anhGiaLap].sort(() => Math.random() - 0.5);
    this.danhSachAnhDaTao = mangXaoTron.slice(0, 6);

    this.hienThiAnhDaTao();

    this.dangXuLy = false;
    this.thanhPhan.nutTaoAnh.disabled = false;
    this.thanhPhan.nutTaoAnh.innerHTML = '<i class="fas fa-magic"></i> Tạo ảnh';
    this.thanhPhan.hieuUngTai.style.display = "none";
  }

  // Chỗ này bắt buộc dùng HTML động vì dữ liệu API AI trả về
  hienThiAnhDaTao() {
    if (!this.thanhPhan.khuVucChuaAnh) return;

    this.thanhPhan.khuVucChuaAnh.innerHTML = this.danhSachAnhDaTao
      .map(
        (anh, viTri) => `
      <div class="ai-image" data-index="${viTri}" onclick="hopThoaiTaoAnhAI.chonAnh(${viTri})">
        <img src="${anh}" alt="Ảnh AI ${viTri + 1}" referrerPolicy="no-referrer">
        <div class="select-icon"><i class="fas fa-check"></i></div>
      </div>
    `,
      )
      .join("");
  }

  chonAnh(viTri) {
    document
      .querySelectorAll(".generated-images .ai-image")
      .forEach((theAnh) => {
        theAnh.classList.remove("selected");
      });

    const anhDuocChon = document.querySelector(
      `.generated-images .ai-image[data-index="${viTri}"]`,
    );
    if (anhDuocChon) {
      anhDuocChon.classList.add("selected");
    }

    const duongDanAnh = this.danhSachAnhDaTao[viTri];
    this.khiChonAnh(duongDanAnh);

    if (typeof hienThiThongBao !== "undefined") {
      hienThiThongBao("Đã chọn ảnh bìa thành công!");
    } else {
      alert("Đã chọn ảnh bìa thành công!");
    }

    setTimeout(() => this.dong(), 500);
  }
}

const hopThoaiTaoAnhAI = new HopThoaiTaoAnhAI({
  onImageSelect: (duongDanAnh) => {
    console.log("Đã chọn ảnh:", duongDanAnh);
    window.selectedBookImage = duongDanAnh;
  },
});

// Giữ lại tên cũ để các file Admin không bị lỗi
window.imageGeneratorModal = hopThoaiTaoAnhAI;
