// // ==========================================
// // XỬ LÝ THÊM VÀO GIỎ HÀNG (CÓ KIỂM TRA ĐĂNG NHẬP)
// // ==========================================

// window.themVaoGioHang = function (event, nutBam) {
//   if (event) {
//     event.stopPropagation();
//     event.preventDefault();
//   }
//   // RÀO CHẮN: Kiểm tra xem PHP có báo là đã đăng nhập chưa?
//   /* --- TẠM TẮT BẮT LOGIN ---
//   if (typeof dangDangNhap === "undefined" || !dangDangNhap) {
//     if (typeof hienThiThongBao !== "undefined") {
//       hienThiThongBao("Vui lòng đăng nhập để thêm sách vào giỏ!");
//     } else {
//       alert("Vui lòng đăng nhập để thêm sách vào giỏ!");
//     }

//     if (typeof authModal !== "undefined") {
//       authModal.mo("dang_nhap");
//     }
//     return;
//   }
//   */

//   // 1. Tìm ngược lên thẻ div bọc ngoài cùng của cuốn sách
//   const theSach = nutBam.closest(".book-card");
//   if (!theSach) {
//     console.error("Lỗi: Không tìm thấy khung bọc thông tin sách!");
//     return;
//   }

//   // 2. Móc dữ liệu trực tiếp từ các thuộc tính data-* mà file PHP đã in ra
//   const thongTinSach = {
//     maSach: theSach.dataset.id,
//     tenSach: theSach.dataset.name,
//     giaBan: parseFloat(theSach.dataset.price),
//     hinhAnh: theSach.dataset.image,
//     tacGia: "Đang cập nhật",
//   };

//   // 3. Đưa vào hệ thống xử lý giỏ hàng
//   if (typeof cartDrawer !== "undefined") {
//     cartDrawer.addItem(thongTinSach, 1);
//   } else {
//     console.error("Lỗi: Không tìm thấy hệ thống quản lý giỏ hàng.");
//   }
// };
// =====================================================================================================





// ==========================================
// XỬ LÝ THÊM VÀO GIỎ HÀNG (BẮT BUỘC ĐĂNG NHẬP)
// ==========================================

// ==========================================
// XỬ LÝ THÊM VÀO GIỎ HÀNG (BẮT BUỘC ĐĂNG NHẬP)
// ==========================================

window.themVaoGioHang = function (suKien, nutBam) {
  // Ngăn chặn sự kiện click lan truyền (ví dụ: tránh mở trang chi tiết sách khi bấm thêm giỏ hàng)
  if (suKien) {
    suKien.stopPropagation();
    suKien.preventDefault();
  }

  // 1. KIỂM TRA ĐĂNG NHẬP
  // Biến dangDangNhap được lấy từ file index.php (<script>const dangDangNhap = true/false;</script>)
  if (typeof dangDangNhap === "undefined" || !dangDangNhap) {
    
    // Hiển thị thông báo (Dùng hàm thông báo nếu có, không thì dùng mặc định của trình duyệt)
    alert("Bạn cần đăng nhập để thêm sách vào giỏ hàng!");

    // Tự động mở form Đăng nhập (gọi hàm openLogin từ file app.js)
    if (typeof openLogin === "function") {
      openLogin();
    } else {
      console.error("Lỗi: Không tìm thấy hàm openLogin() để hiển thị form.");
    }
    
    // Kết thúc hàm sớm, không cho chạy tiếp xuống phần thêm giỏ hàng
    return; 
  }

  // 2. NẾU ĐÃ ĐĂNG NHẬP -> TÌM LẤY KHUNG CHỨA SÁCH
  const theSach = nutBam.closest(".book-card");
  if (!theSach) {
    console.error("Lỗi: Không tìm thấy khung bọc thông tin sách (.book-card)!");
    return;
  }

  // 3. MÓC DỮ LIỆU TỪ CÁC THUỘC TÍNH DATA-* TRONG HTML (Được tạo ra bởi bookCard.php)
  const thongTinSach = {
    maSach: theSach.dataset.id,
    tenSach: theSach.dataset.name,
    giaBan: parseFloat(theSach.dataset.price),
    hinhAnh: theSach.dataset.image,
    tacGia: theSach.dataset.tacGia || "Đang cập nhật",
  };

  // 4. ĐƯA DỮ LIỆU VÀO HỆ THỐNG QUẢN LÝ GIỎ HÀNG
  if (typeof cartDrawer !== "undefined") {
    // Gọi hàm addItem từ file cart.js
    cartDrawer.addItem(thongTinSach, 1);
  } else {
    console.error("Lỗi: Không tìm thấy hệ thống quản lý giỏ hàng (cartDrawer).");
  }
};