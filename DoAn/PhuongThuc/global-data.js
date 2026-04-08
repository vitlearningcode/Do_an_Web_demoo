// ==========================================
// CẦU NỐI DỮ LIỆU TOÀN CỤC (GLOBAL DATA BRIDGE)
// ==========================================

// Gắn dữ liệu tĩnh (Banner, Danh mục) vào window để các file JS khác có thể đọc được
if (typeof danhSachBanner !== "undefined") {
  window.danhSachBanner = danhSachBanner;
}

if (typeof danhSachDanhMuc !== "undefined") {
  window.danhSachDanhMuc = danhSachDanhMuc;
}

// Xóa console.log báo cáo số lượng sách ảo cũ, thay bằng thông báo tiếng Việt
console.log("✅ Đã tải xong dữ liệu tĩnh (Banner & Danh mục)");
