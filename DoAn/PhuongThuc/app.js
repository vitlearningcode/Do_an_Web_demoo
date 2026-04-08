// ==================== CẤU TRÚC CHÍNH CỦA ỨNG DỤNG ====================

// ==================== TRẠNG THÁI HỆ THỐNG (STATE) ====================
let vaiTroHienTai = "khach_hang";
let giaoDienAdminHienTai = "tong_quan";
let danhSachYeuThich = new Set();

// ==================== DANH SÁCH GIAO DIỆN QUẢN TRỊ ====================
const danhSachGiaoDienAdmin = {
  tong_quan: typeof overviewView !== "undefined" ? overviewView : null,
  nhap_sach:
    typeof importManagementView !== "undefined" ? importManagementView : null,
  thong_tin_sach:
    typeof bookInfoManagementView !== "undefined"
      ? bookInfoManagementView
      : null,
  doanh_thu:
    typeof revenueManagementView !== "undefined" ? revenueManagementView : null,
  ban_hang:
    typeof salesManagementView !== "undefined" ? salesManagementView : null,
  kho_hang:
    typeof inventoryManagementView !== "undefined"
      ? inventoryManagementView
      : null,
  bao_cao: typeof reportsView !== "undefined" ? reportsView : null,
  cai_dat: typeof settingsView !== "undefined" ? settingsView : null,
  lien_he: typeof contactView !== "undefined" ? contactView : null,
};

// Tiêu đề tương ứng cho từng giao diện
const tieuDeGiaoDien = {
  tong_quan: "Tổng quan",
  nhap_sach: "Quản lý nhập sách",
  thong_tin_sach: "Quản lý thông tin sách",
  doanh_thu: "Quản lý doanh thu",
  ban_hang: "Quản lý bán hàng",
  kho_hang: "Quản lý kho",
  bao_cao: "Báo cáo",
  cai_dat: "Cài đặt hệ thống",
  lien_he: "Hỗ trợ & Liên hệ",
};

// ==================== KHỞI TẠO ỨNG DỤNG ====================
document.addEventListener("DOMContentLoaded", () => {
  khoiTaoChuyenDoiVaiTro();
  khoiTaoDemNguocKhuyenMai();
  khoiTaoSuKienChoSach(); // Hàm mới thay thế cho renderBooks
  khoiTaoNutGioHang();
  khoiTaoNutDangNhap();
  khoiTaoMenuQuanTri();
  khoiTaoChatbot();

  console.log("✅ Hệ thống JavaScript đã khởi tạo thành công (Chế độ PHP SSR)");
});

// ==================== CHUYỂN ĐỔI VAI TRÒ (KHÁCH / ADMIN) ====================
function khoiTaoChuyenDoiVaiTro() {
  const nutKhachHang = document.getElementById("btn-customer");
  const nutAdmin = document.getElementById("btn-admin");
  const giaoDienCuaHang = document.getElementById("storefront-view");
  const giaoDienQuanTri = document.getElementById("admin-view");

  if (nutKhachHang && nutAdmin) {
    nutKhachHang.addEventListener("click", () => {
      vaiTroHienTai = "khach_hang";
      nutKhachHang.classList.add("active");
      nutAdmin.classList.remove("active");
      giaoDienCuaHang.style.display = "block";
      giaoDienQuanTri.style.display = "none";
    });

    nutAdmin.addEventListener("click", () => {
      vaiTroHienTai = "admin";
      nutAdmin.classList.add("active");
      nutKhachHang.classList.remove("active");
      giaoDienCuaHang.style.display = "none";
      giaoDienQuanTri.style.display = "flex";
      taiGiaoDienQuanTri(giaoDienAdminHienTai);
    });
  }
}

// ==================== ĐẾM NGƯỢC FLASH SALE ====================
function khoiTaoDemNguocKhuyenMai() {
  let gio = 12;
  let phut = 45;
  let giay = 30;

  setInterval(() => {
    giay--;
    if (giay < 0) {
      giay = 59;
      phut--;
    }
    if (phut < 0) {
      phut = 59;
      gio--;
    }
    if (gio < 0) {
      gio = 24;
    }

    const theGio = document.getElementById("hours");
    const thePhut = document.getElementById("minutes");
    const theGiay = document.getElementById("seconds");

    if (theGio) theGio.textContent = gio.toString().padStart(2, "0");
    if (thePhut) thePhut.textContent = phut.toString().padStart(2, "0");
    if (theGiay) theGiay.textContent = giay.toString().padStart(2, "0");
  }, 1000);
}

// ==================== XỬ LÝ SỰ KIỆN CHO SÁCH (TỪ PHP RENDER RA) ====================
function khoiTaoSuKienChoSach() {
  // 1. Xử lý nút "Thêm vào giỏ hàng"
  // (Giả định PHP in ra thẻ <button class="add-to-cart-btn" data-masach="S01">)
  const danhSachNutThemGio = document.querySelectorAll(".add-to-cart-btn");

  danhSachNutThemGio.forEach((nut) => {
    nut.addEventListener("click", function (suKien) {
      suKien.preventDefault(); // Ngăn trình duyệt load lại trang nếu nằm trong thẻ <a>

      const maSach = this.getAttribute("data-masach");
      const tenSach = this.getAttribute("data-tensach") || "Sản phẩm";

      console.log("🛒 Đang thêm sách mã:", maSach);

      // Chỗ này gọi hàm của Giỏ hàng (Sẽ Việt hóa file cartDrawer.js sau)
      if (typeof cartDrawer !== "undefined") {
        // cartDrawer.addItem(maSach, 1);
        // toast.success(`Đã thêm "${tenSach}" vào giỏ hàng`);
        alert(`Đã thêm "${tenSach}" vào giỏ!`); // Tạm thời dùng alert
      }
    });
  });

  // 2. Tương tự cho nút Yêu thích / Xem nhanh nếu có
  console.log(
    `✅ Đã gắn sự kiện click cho ${danhSachNutThemGio.length} nút thêm giỏ hàng.`,
  );
}

// ==================== NÚT GIỎ HÀNG (MENU TRÊN) ====================
function khoiTaoNutGioHang() {
  const nutGioHang = document.getElementById("btn-cart");
  if (nutGioHang) {
    nutGioHang.addEventListener("click", () => {
      // Gọi file xử lý giỏ hàng riêng
      if (typeof cartDrawer !== "undefined") {
        cartDrawer.toggle();
      }
    });
  }
}

// ==================== NÚT ĐĂNG NHẬP ====================
function khoiTaoNutDangNhap() {
  const nutDangNhap = document.getElementById("btn-login");
  if (nutDangNhap) {
    nutDangNhap.addEventListener("click", () => {
      if (typeof authModal !== "undefined") {
        authModal.open("login");
      }
    });
  }
}

// ==================== MENU SIDEBAR QUẢN TRỊ ====================
function khoiTaoMenuQuanTri() {
  const danhSachMenu = document.querySelectorAll(".nav-item[data-view]");
  danhSachMenu.forEach((menu) => {
    menu.addEventListener("click", (suKien) => {
      suKien.preventDefault();
      taiGiaoDienQuanTri(menu.dataset.view);
    });
  });

  const nutDangXuat = document.getElementById("btn-logout");
  if (nutDangXuat) {
    nutDangXuat.addEventListener("click", (suKien) => {
      suKien.preventDefault();
      if (typeof logoutModal !== "undefined") {
        logoutModal.open();
        logoutModal.onConfirm = () => {
          document.getElementById("btn-customer").click();
        };
      }
    });
  }
}

// ==================== TẢI GIAO DIỆN QUẢN TRỊ ====================
function taiGiaoDienQuanTri(tenGiaoDien) {
  giaoDienAdminHienTai = tenGiaoDien;

  // Cập nhật tiêu đề
  const theTieuDe = document.getElementById("admin-view-title");
  if (theTieuDe) {
    theTieuDe.textContent = tieuDeGiaoDien[tenGiaoDien] || "Tổng quan";
  }

  // Hiển thị nội dung
  const khuVucNoiDung = document.getElementById("admin-content");
  if (khuVucNoiDung && danhSachGiaoDienAdmin[tenGiaoDien]) {
    khuVucNoiDung.innerHTML = danhSachGiaoDienAdmin[tenGiaoDien].render();

    // Khởi tạo biểu đồ nếu có
    if (danhSachGiaoDienAdmin[tenGiaoDien].initChart) {
      danhSachGiaoDienAdmin[tenGiaoDien].initChart();
    }
  }

  // Đổi màu menu đang chọn (Active)
  document.querySelectorAll(".nav-item").forEach((menu) => {
    if (menu.dataset.view) {
      menu.classList.toggle("active", menu.dataset.view === tenGiaoDien);
    }
  });
}

// ==================== CHATBOT ====================
function khoiTaoChatbot() {
  // Logic chatbot nếu có
}
