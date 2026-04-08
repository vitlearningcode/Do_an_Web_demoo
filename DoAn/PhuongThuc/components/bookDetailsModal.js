// ==================== KHUNG POPUP CHI TIẾT SÁCH (KHÔNG DÙNG API) ====================

class HopThoaiChiTietSach {
  constructor() {
    this.dangMo = false;
    this.sachHienTai = null; // Lưu thông tin cuốn sách đang xem
    this.soLuong = 1;

    this.thanhPhan = {
      khungChinh: null,
      anhBia: null,
      tenSach: null,
      tacGia: null,
      giaBan: null,
      giaGoc: null,
      moTa: null,
      oNhapSoLuong: null,
    };

    // Tự động khởi tạo khi trang load xong
    document.addEventListener("DOMContentLoaded", () => this.khoiTao());
  }

  khoiTao() {
    // 1. Tìm các thẻ HTML của cái khung Popup (đã được PHP in ẩn sẵn ở cuối trang)
    this.thanhPhan.khungChinh = document.getElementById("book-modal");
    this.thanhPhan.anhBia = document.getElementById("book-detail-image");
    this.thanhPhan.tenSach = document.getElementById("book-detail-name");
    this.thanhPhan.tacGia = document.getElementById("book-detail-author");
    this.thanhPhan.giaBan = document.getElementById("book-detail-price");
    this.thanhPhan.giaGoc = document.getElementById("book-detail-original");
    this.thanhPhan.moTa = document.getElementById("book-detail-description");
    this.thanhPhan.oNhapSoLuong = document.getElementById("qty-input");

    this.ganSuKien();
  }

  ganSuKien() {
    // Nút tắt Popup (dấu X)
    const nutDong = document.getElementById("book-close");
    if (nutDong) {
      nutDong.addEventListener("click", () => this.dong());
    }

    // Bấm ra ngoài vùng đen cũng tắt Popup
    if (this.thanhPhan.khungChinh) {
      this.thanhPhan.khungChinh.addEventListener("click", (suKien) => {
        if (suKien.target === this.thanhPhan.khungChinh) this.dong();
      });
    }

    // Xử lý nút Cộng / Trừ số lượng
    const nutTru = document.getElementById("qty-minus");
    const nutCong = document.getElementById("qty-plus");

    if (nutTru) nutTru.addEventListener("click", () => this.capNhatSoLuong(-1));
    if (nutCong)
      nutCong.addEventListener("click", () => this.capNhatSoLuong(1));

    // Xử lý nút "Thêm vào giỏ" ở trong Popup
    const nutThemVaoGio = document.getElementById("btn-add-to-cart");
    if (nutThemVaoGio) {
      nutThemVaoGio.addEventListener("click", () => this.themVaoGioHang());
    }

    // Bấm nút Esc để đóng
    document.addEventListener("keydown", (suKien) => {
      if (suKien.key === "Escape" && this.dangMo) this.dong();
    });
  }

  // HÀM NÀY QUAN TRỌNG: Gọi khi bấm nút "Xem nhanh"
  mo(nutBam) {
    // 1. Móc toàn bộ dữ liệu đang giấu trong cái Nút Bấm ra
    this.sachHienTai = {
      id: nutBam.getAttribute("data-id"),
      ten: nutBam.getAttribute("data-tensach"),
      gia: nutBam.getAttribute("data-giaban"),
      giaGoc: nutBam.getAttribute("data-giagoc") || "",
      anh: nutBam.getAttribute("data-hinhanh"),
      tacGia: nutBam.getAttribute("data-tacgia") || "Đang cập nhật",
      moTa:
        nutBam.getAttribute("data-mota") || "Chưa có mô tả cho cuốn sách này.",
    };

    this.soLuong = 1;

    // 2. Bơm dữ liệu vào khung Popup
    this.capNhatGiaoDien();

    // 3. Hiển thị Popup lên
    this.dangMo = true;
    this.thanhPhan.khungChinh?.classList.add("active");
    document.body.style.overflow = "hidden"; // Chống cuộn trang
  }

  dong() {
    this.dangMo = false;
    this.thanhPhan.khungChinh?.classList.remove("active");
    document.body.style.overflow = "";
    this.sachHienTai = null;
  }

  capNhatGiaoDien() {
    if (!this.sachHienTai) return;

    // Thay thế hình ảnh, chữ nghĩa trong Popup bằng dữ liệu vừa móc được
    if (this.thanhPhan.anhBia) {
      this.thanhPhan.anhBia.src = this.sachHienTai.anh;
      this.thanhPhan.anhBia.alt = this.sachHienTai.ten;
    }
    if (this.thanhPhan.tenSach)
      this.thanhPhan.tenSach.textContent = this.sachHienTai.ten;
    if (this.thanhPhan.tacGia)
      this.thanhPhan.tacGia.textContent = `Tác giả: ${this.sachHienTai.tacGia}`;
    if (this.thanhPhan.moTa)
      this.thanhPhan.moTa.textContent = this.sachHienTai.moTa;

    // Dùng hàm định dạng tiền từ utils.js
    if (this.thanhPhan.giaBan && typeof dinhDangTien !== "undefined") {
      this.thanhPhan.giaBan.textContent = dinhDangTien(this.sachHienTai.gia);
    }

    if (this.thanhPhan.giaGoc) {
      if (this.sachHienTai.giaGoc) {
        this.thanhPhan.giaGoc.textContent =
          typeof dinhDangTien !== "undefined"
            ? dinhDangTien(this.sachHienTai.giaGoc)
            : this.sachHienTai.giaGoc;
        this.thanhPhan.giaGoc.style.display = "inline";
      } else {
        this.thanhPhan.giaGoc.style.display = "none";
      }
    }

    if (this.thanhPhan.oNhapSoLuong)
      this.thanhPhan.oNhapSoLuong.value = this.soLuong;
  }

  capNhatSoLuong(thayDoi) {
    this.soLuong += thayDoi;
    if (this.soLuong < 1) this.soLuong = 1;
    if (this.soLuong > 99) this.soLuong = 99;
    if (this.thanhPhan.oNhapSoLuong)
      this.thanhPhan.oNhapSoLuong.value = this.soLuong;
  }

  themVaoGioHang() {
    if (!this.sachHienTai) return;

    // Định dạng lại tên biến để khớp với cái Giỏ hàng
    const thongTinSachChuan = {
      id: this.sachHienTai.id,
      name: this.sachHienTai.ten,
      price: parseFloat(this.sachHienTai.gia),
      image: this.sachHienTai.anh,
    };

    // Truyền qua hàm Giỏ Hàng
    if (typeof cartDrawer !== "undefined") {
      cartDrawer.addItem(thongTinSachChuan, this.soLuong);
      if (typeof hienThiThongBao !== "undefined") {
        hienThiThongBao(
          `Đã thêm ${this.soLuong} cuốn "${this.sachHienTai.ten}" vào giỏ`,
        );
      } else {
        alert(`Đã thêm ${this.soLuong} cuốn "${this.sachHienTai.ten}" vào giỏ`);
      }
    }
    this.dong();
  }
}

// Khởi tạo đối tượng toàn cục
const hopThoaiChiTietSach = new HopThoaiChiTietSach();
