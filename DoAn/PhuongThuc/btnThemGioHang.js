/**
 * ============================================================
 * LUỒNG: THÊMm VÀO GIỏ HÀNG
 *
 * GọI BỚI: Nút "Thêm giỏ hàng" trên mỗi .book-card
 *   onclick="themVaoGioHang(event, this)"
 *   bookCard.js và xemNhanhSach.js đều gọi hàm này
 *
 * LÀM VIỆC VỚI:
 *   - window.__giaSach   (PHP inject từ DB qua layGioHangCoGia.php)
 *   - window.__tonKhoMap (PHP inject từ DB qua layGioHangCoGia.php)
 *   - cartDrawer         (từ cart.js — dùng API addItem)
 *
 * BẢO MẬT GIÁ (No-Trust Client):
 *   GIÁ ĐƯỢC LẤY TỪ:   window.__giaSach[maSach]
 *   GIÁ KHÔNG LẤY TỪ: data-price HTML (user có thể F12 sửa)
 *   => Ngay cả nếu user sửa data-price, __giaSach không có ảnh hưởng
 *   => Server (kiemTraGioHang.php, xuLyThanhToan.php) vẫn query DB lại
 * ============================================================
 */

// Đăng ký hàm toàn cục — các file PHP gọi bố sương qua onclick=... inline
window.themVaoGioHang = function (suKien, nutBam) {
  // Ngăn click lan truyền lên thẻ <a> cha (tránh mở trang chi tiết sách)
  if (suKien) {
    suKien.stopPropagation();
    suKien.preventDefault();
  }

  // -----------------------------------------------------------
  // BƯỚC 1: KIỂM TRA ĐĂNG NHẬP
  // dangDangNhap được PHP inject inline trong <head>:
  //   <script>const dangDangNhap = <?= $isLoggedIn ? 'true' : 'false' ?></script>
  // -----------------------------------------------------------
  if (typeof dangDangNhap === "undefined" || !dangDangNhap) {
    alert("Bạn cần đăng nhập để thêm sách vào giỏ hàng!");
    if (typeof openLogin === "function") openLogin(); // Mở modal đăng nhập (app.js)
    return; // Dừng — không thêm
  }

  // -----------------------------------------------------------
  // BƯỚC 2: TÌM KHUNG CHỨA SÁCH (.book-card)
  // nutBam là nút bấm (this) — closest() tìm phần tử cha gần nhất có class .book-card
  // -----------------------------------------------------------
  var theSach = nutBam.closest(".book-card");
  if (!theSach) {
    console.error("Lỗi: Không tìm thấy .book-card!");
    return;
  }

  // -----------------------------------------------------------
  // BƯỚC 3: KIỂM TRA HẾT HÀNG
  // data-ton-kho được PHP render từ DB trong bookCard.php:
  //   <div class="book-card" data-ton-kho="<?= $sach['soLuongTon'] ?>">
  // -----------------------------------------------------------
  var tonKhoKT = parseInt(theSach.dataset.tonKho, 10);
  if (!isNaN(tonKhoKT) && tonKhoKT <= 0) {
    alert("Sản phẩm này hiện đã hết hàng!");
    return; // Dừng hoàn toàn, không thêm vào giỏ
  }

  // -----------------------------------------------------------
  // BƯỚC 4: LẤY DỮ LIỆU SÁCH
  // GIÁ LẤY TỪ: window.__giaSach[maSach] (PHP inject từ DB)
  // GIÁ KHÔNG LẤY: data-price (dễ bị F12 sửa)
  // window.__giaSach được tạo bởi:
  //   layGioHangCoGia.php → $giaSachMapJson →
  //   index.php: <script>var __giaSach = <?= $giaSachMapJson ?></script>
  // -----------------------------------------------------------
  var maSach = theSach.dataset.id || '';
  var thongTinSach = {
    maSach  : maSach,
    tenSach : theSach.dataset.name,
    // BẢO MẬT: Lấy giá từ map PHP inject (từ DB), không từ data-price
    giaBan  : (window.__giaSach && window.__giaSach[maSach]) ? window.__giaSach[maSach] : 0,
    hinhAnh : theSach.dataset.image,
    tacGia  : theSach.dataset.tacGia || "Đang cập nhật",
  };

  // -----------------------------------------------------------
  // BƯỚC 5: ĐƯĂ VÀO GIỏ HÀNG
  // cartDrawer.addItem() (từ cart.js):
  //   - Kiểm tra tồn kho từ window.__tonKhoMap
  //   - Nếu đã có → tăng soLuong (giới hạn tonKho)
  //   - Nếu mới → push vào cartArr
  //   - showToast("Dã thêm...")
  //   - saveCart() → localStorage + iframe form sync lên luuGioHang.php
  // -----------------------------------------------------------
  if (typeof cartDrawer !== "undefined") {
    cartDrawer.addItem(thongTinSach, 1);
  } else {
    console.error("Lỗi: Không tìm thấy cartDrawer.");
  }
};