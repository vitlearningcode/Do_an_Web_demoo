// ==========================================
// XỬ LÝ THÊM VÀO GIỎ HÀNG (CÓ KIỂM TRA ĐĂNG NHẬP)
// ==========================================

window.themVaoGioHang = function (event, nutBam) {
  if (event) {
    event.stopPropagation();
    event.preventDefault();
  }
  // RÀO CHẮN: Kiểm tra xem PHP có báo là đã đăng nhập chưa?
  /* --- TẠM TẮT BẮT LOGIN ---
  if (typeof dangDangNhap === "undefined" || !dangDangNhap) {
    if (typeof hienThiThongBao !== "undefined") {
      hienThiThongBao("Vui lòng đăng nhập để thêm sách vào giỏ!");
    } else {
      alert("Vui lòng đăng nhập để thêm sách vào giỏ!");
    }

    if (typeof authModal !== "undefined") {
      authModal.mo("dang_nhap");
    }
    return;
  }
  */

  // 1. Tìm ngược lên thẻ div bọc ngoài cùng của cuốn sách
  const theSach = nutBam.closest(".book-card");
  if (!theSach) {
    console.error("Lỗi: Không tìm thấy khung bọc thông tin sách!");
    return;
  }

  // 2. Móc dữ liệu trực tiếp từ các thuộc tính data-* mà file PHP đã in ra
  const thongTinSach = {
    maSach: theSach.dataset.id,
    tenSach: theSach.dataset.name,
    giaBan: parseFloat(theSach.dataset.price),
    hinhAnh: theSach.dataset.image,
    tacGia: "Đang cập nhật",
  };

  // 3. Đưa vào hệ thống xử lý giỏ hàng
  if (typeof cartDrawer !== "undefined") {
    cartDrawer.addItem(thongTinSach, 1);
  } else {
    console.error("Lỗi: Không tìm thấy hệ thống quản lý giỏ hàng.");
  }
};
