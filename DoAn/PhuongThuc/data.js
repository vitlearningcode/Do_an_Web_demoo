// ==========================================
// DỮ LIỆU TĨNH CỦA GIAO DIỆN (UI DATA)
// ==========================================

// LƯU Ý: Đã xóa toàn bộ mảng dữ liệu sách ảo.
// Sách bây giờ được lấy THẬT từ Database thông qua PHP!

const danhSachBanner = [
  {
    id: 1,
    hinhAnh: "https://picsum.photos/seed/banner1/1200/400",
    nhan: "Khuyến mãi tháng 10",
    tieuDe: "Hội Sách Mùa Thu\nGiảm Giá Lên Đến 50%",
    moTa: "Khám phá hàng ngàn tựa sách hấp dẫn với mức giá ưu đãi nhất trong năm. Miễn phí giao hàng toàn quốc.",
    chuNutBam: "Mua Ngay",
    mauNen: "blue", // Giữ nguyên tên màu tiếng Anh nếu CSS đang dùng class này (.blue, .emerald)
  },
  {
    id: 2,
    hinhAnh: "https://picsum.photos/seed/banner2/1200/400",
    nhan: "Sách Mới",
    tieuDe: "Tuần Lễ Sách Mới\nTặng Kèm Bookmark",
    moTa: "Cập nhật những tựa sách mới nhất từ các nhà xuất bản hàng đầu. Quà tặng độc quyền cho 100 đơn hàng đầu tiên.",
    chuNutBam: "Khám Phá",
    mauNen: "emerald",
  },
  {
    id: 3,
    hinhAnh: "https://picsum.photos/seed/banner3/1200/400",
    nhan: "Độc Quyền",
    tieuDe: "Bộ Sách Kỹ Năng\nDành Cho Sinh Viên",
    moTa: "Trang bị hành trang vững chắc cho tương lai với bộ sách kỹ năng thiết yếu. Giảm thêm 10% cho học sinh, sinh viên.",
    chuNutBam: "Xem Chi Tiết",
    mauNen: "purple",
  },
];

const danhSachDanhMuc = [
  { ten: "Văn học", bieuTuong: "📚", mauSac: "purple" },
  { ten: "Kinh tế", bieuTuong: "📈", mauSac: "blue" },
  { ten: "Tâm lý", bieuTuong: "🧠", mauSac: "green" },
  { ten: "Thiếu nhi", bieuTuong: "🧸", mauSac: "yellow" },
  { ten: "Khoa học", bieuTuong: "🔬", mauSac: "cyan" },
  { ten: "Ngoại ngữ", bieuTuong: "🌍", mauSac: "red" },
];

// Nếu file JS xử lý banner (heroCarousel.js) cần dùng biến này, ta gắn nó vào window
window.danhSachBanner = danhSachBanner;
window.danhSachDanhMuc = danhSachDanhMuc;
