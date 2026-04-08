// ==================== CÁC HÀM TIỆN ÍCH DÙNG CHUNG ====================

// Hàm định dạng tiền tệ sang chuẩn Việt Nam Đồng (VNĐ)
// Dùng khi cần tính toán tổng tiền trong giỏ hàng bằng JS
function dinhDangTien(giaTien) {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  }).format(giaTien);
}

// Hàm hiển thị popup thông báo góc màn hình (Toast)
function hienThiThongBao(noiDung) {
  const theThongBao = document.getElementById("toast");
  const theNoiDungThongBao = document.getElementById("toast-message");

  if (theThongBao && theNoiDungThongBao) {
    theNoiDungThongBao.textContent = noiDung;
    theThongBao.classList.add("show");

    // Tự động ẩn thông báo sau 3 giây (3000ms)
    setTimeout(() => {
      theThongBao.classList.remove("show");
    }, 3000);
  }
}

// LƯU Ý: Đã xóa hoàn toàn hàm createBookCard() vì HTML bây giờ do PHP in ra trực tiếp!

// Hỗ trợ xuất module (Nếu dự án có dùng bộ đóng gói, nếu không có thể bỏ qua)
if (typeof module !== "undefined" && module.exports) {
  module.exports = { dinhDangTien, hienThiThongBao };
}
