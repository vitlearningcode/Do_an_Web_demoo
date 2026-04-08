// ==================== HỆ THỐNG GIỎ HÀNG (LƯU TRỮ CỤC BỘ) ====================

// Khởi tạo mảng giỏ hàng từ LocalStorage (Để F5 không bị mất)
let danhSachGioHang = JSON.parse(localStorage.getItem("gioHang_BookSM")) || [];

// 1. Hàm khởi tạo các sự kiện đóng/mở ngăn kéo giỏ hàng
function khoiTaoGioHang() {
  const nutMoGio = document.getElementById("btn-cart");
  const nganKeoGio = document.getElementById("cart-drawer");
  const lopPhuDen = document.getElementById("cart-overlay");
  const nutDongGio = document.getElementById("cart-close");

  if (nutMoGio && nganKeoGio && lopPhuDen) {
    nutMoGio.addEventListener("click", () => {
      // RÀO CHẮN: Phải đăng nhập mới cho mở xem giỏ hàng
      if (typeof dangDangNhap === "undefined" || !dangDangNhap) {
        if (typeof hienThiThongBao !== "undefined") {
          hienThiThongBao("Vui lòng đăng nhập để xem giỏ hàng!");
        } else {
          alert("Vui lòng đăng nhập để xem giỏ hàng!");
        }

        if (typeof authModal !== "undefined") authModal.mo("dang_nhap");
        return;
      }

      nganKeoGio.classList.add("active");
      lopPhuDen.classList.add("active");
    });

    const dongGioHang = () => {
      nganKeoGio.classList.remove("active");
      lopPhuDen.classList.remove("active");
    };

    if (nutDongGio) nutDongGio.addEventListener("click", dongGioHang);
    lopPhuDen.addEventListener("click", dongGioHang);
  }

  // Tự động cập nhật giao diện ngay khi load trang
  capNhatGiaoDienGioHang();
}

// 2. Hàm thêm sách vào giỏ
function themSachVaoGio(sach, soLuong = 1) {
  // Rào chắn bảo mật lớp 2 phòng hờ gọi từ chỗ khác
  if (typeof dangDangNhap === "undefined" || !dangDangNhap) {
    if (typeof authModal !== "undefined") authModal.mo("dang_nhap");
    return;
  }

  // Tìm xem sách này đã có trong giỏ chưa
  const sachDaCo = danhSachGioHang.find((monHang) => monHang.id === sach.id);

  if (sachDaCo) {
    sachDaCo.quantity += soLuong;
  } else {
    // Đẩy dữ liệu sách mới vào mảng
    danhSachGioHang.push({
      id: sach.id,
      name: sach.name,
      price: sach.price,
      image: sach.image,
      quantity: soLuong,
    });
  }

  LuuVaoBoNhoGhiNho();
  capNhatGiaoDienGioHang();
}

// 3. Hàm xóa 1 cuốn sách khỏi giỏ
function xoaKhoiGio(maSach) {
  danhSachGioHang = danhSachGioHang.filter((monHang) => monHang.id !== maSach);
  LuuVaoBoNhoGhiNho();
  capNhatGiaoDienGioHang();
}

// 4. Hàm tăng/giảm số lượng
function thayDoiSoLuongTrongGio(maSach, thayDoi) {
  const sachCuaToi = danhSachGioHang.find((monHang) => monHang.id === maSach);

  if (sachCuaToi) {
    sachCuaToi.quantity += thayDoi;

    // Nếu giảm về 0 thì xóa luôn
    if (sachCuaToi.quantity < 1) {
      xoaKhoiGio(maSach);
    } else {
      LuuVaoBoNhoGhiNho();
      capNhatGiaoDienGioHang();
    }
  }
}

// 5. Hàm lưu tạm vào trình duyệt
function LuuVaoBoNhoGhiNho() {
  localStorage.setItem("gioHang_BookSM", JSON.stringify(danhSachGioHang));
}

// 6. Hàm cập nhật lại cục HTML của Giỏ hàng
function capNhatGiaoDienGioHang() {
  const theDemSoLuong = document.getElementById("cart-count");
  const khuVucChuaHang = document.getElementById("cart-items");
  const theTongTien = document.getElementById("cart-total");

  if (!khuVucChuaHang) return;

  // Nếu chưa đăng nhập, clear hiển thị và thoát luôn
  if (typeof dangDangNhap === "undefined" || !dangDangNhap) {
    if (theDemSoLuong) {
      theDemSoLuong.textContent = 0;
      theDemSoLuong.classList.add("hidden");
    }
    khuVucChuaHang.innerHTML =
      '<p class="cart-empty">Vui lòng đăng nhập để sử dụng giỏ hàng</p>';
    if (theTongTien) theTongTien.textContent = "0đ";
    return;
  }

  // Tính tổng
  const tongSoCuon = danhSachGioHang.reduce(
    (tong, monHang) => tong + monHang.quantity,
    0,
  );
  const tongTien = danhSachGioHang.reduce(
    (tong, monHang) => tong + monHang.price * monHang.quantity,
    0,
  );

  // Cập nhật số nhỏ góc trên
  if (theDemSoLuong) {
    theDemSoLuong.textContent = tongSoCuon;
    theDemSoLuong.classList.toggle("hidden", tongSoCuon === 0);
  }

  // Cập nhật tổng tiền (Dùng hàm dinhDangTien từ utils.js)
  if (theTongTien) {
    theTongTien.textContent =
      typeof dinhDangTien !== "undefined"
        ? dinhDangTien(tongTien)
        : tongTien + "đ";
  }

  // Vẽ danh sách sách trong ngăn kéo
  if (danhSachGioHang.length === 0) {
    khuVucChuaHang.innerHTML = '<p class="cart-empty">Giỏ hàng trống</p>';
  } else {
    let html = "";
    danhSachGioHang.forEach((monHang) => {
      let giaFormat =
        typeof dinhDangTien !== "undefined"
          ? dinhDangTien(monHang.price)
          : monHang.price;

      html += `
          <div class="cart-item">
            <img src="${monHang.image}" alt="${monHang.name}">
            <div class="cart-item-info">
              <h4 class="cart-item-name">${monHang.name}</h4>
              <p class="cart-item-price">${giaFormat}</p>
              <div class="cart-item-qty">
                <button onclick="thayDoiSoLuongTrongGio('${monHang.id}', -1)">-</button>
                <span>${monHang.quantity}</span>
                <button onclick="thayDoiSoLuongTrongGio('${monHang.id}', 1)">+</button>
              </div>
            </div>
            <button class="cart-item-remove" onclick="xoaKhoiGio('${monHang.id}')">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        `;
    });
    khuVucChuaHang.innerHTML = html;
  }
}

// ==================== KẾT NỐI VỚI NÚT THANH TOÁN ====================
document.addEventListener("DOMContentLoaded", () => {
  khoiTaoGioHang();

  // Xử lý khi nhấn nút Thanh Toán
  const nutThanhToan = document.querySelector(".checkout-btn");
  if (nutThanhToan) {
    nutThanhToan.addEventListener("click", () => {
      if (danhSachGioHang.length === 0) {
        alert("Giỏ hàng của bạn đang trống!");
        return;
      }

      // Chuyển hướng sang trang Thanh Toán của PHP
      window.location.href = "ChucNang/CuaHang/ThanhToan/index.php";
    });
  }
});

// Gắn hàm vào window để các nút gọi được (từ các thuộc tính onclick)
window.thayDoiSoLuongTrongGio = thayDoiSoLuongTrongGio;
window.xoaKhoiGio = xoaKhoiGio;
window.themSachVaoGio = themSachVaoGio;

// Alias (ánh xạ) lại tên đối tượng cũ để app.js không bị sập
const cartDrawer = {
  addItem: themSachVaoGio,
  toggle: () => document.getElementById("btn-cart")?.click(),
};
window.cartDrawer = cartDrawer;
