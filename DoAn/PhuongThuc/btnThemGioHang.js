// ==========================================
// XỬ LÝ THÊM VÀO GIỎ HÀNG (BẮT BUỘC ĐĂNG NHẬP)
// ==========================================

window.themVaoGioHang = function (suKien, nutBam) {
  // Ngăn click lan truyền lên thẻ <a> cha (tránh mở trang chi tiết sách)
  if (suKien) {
    suKien.stopPropagation();
    suKien.preventDefault();
  }

  // 1. KIỂM TRA ĐĂNG NHẬP
  if (typeof dangDangNhap === "undefined" || !dangDangNhap) {
    alert("Bạn cần đăng nhập để thêm sách vào giỏ hàng!");
    if (typeof openLogin === "function") openLogin();
    return;
  }

  // 2. TÌM KHUNG CHỨA SÁCH (.book-card)
  var theSach = nutBam.closest(".book-card");
  if (!theSach) {
    console.error("Lỗi: Không tìm thấy .book-card!");
    return;
  }

  // 3. KIỂM TRA HẾT HÀNG — đọc data-ton-kho do PHP render
  var tonKhoKT = parseInt(theSach.dataset.tonKho, 10);
  if (!isNaN(tonKhoKT) && tonKhoKT <= 0) {
    alert("Sản phẩm này hiện đã hết hàng!");
    return; // Dừng hoàn toàn, không thêm vào giỏ
  }

  // 4. MÓC DỮ LIỆU từ data-* (Giá từ __giaSach — PHP inject từ DB, chống F12)
  var maSach = theSach.dataset.id || '';
  var thongTinSach = {
    maSach  : maSach,
    tenSach : theSach.dataset.name,
    giaBan  : (window.__giaSach && window.__giaSach[maSach]) ? window.__giaSach[maSach] : 0,
    hinhAnh : theSach.dataset.image,
    tacGia  : theSach.dataset.tacGia || "Đang cập nhật",
  };

  // 5. ĐƯA VÀO GIỎ HÀNG — cartDrawer sẽ show toast "Đã thêm..."
  if (typeof cartDrawer !== "undefined") {
    cartDrawer.addItem(thongTinSach, 1);
  } else {
    console.error("Lỗi: Không tìm thấy cartDrawer.");
  }
};