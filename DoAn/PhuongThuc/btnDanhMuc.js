// ==========================================
// XỬ LÝ LOGIC TRANG DANH MỤC & BỘ LỌC SÁCH
// ==========================================

document.addEventListener("DOMContentLoaded", () => {
  // ---------------------------------------------------------
  // 1. LOGIC CHUYỂN ĐỔI GIAO DIỆN (Nút Danh Mục)
  // ---------------------------------------------------------
  const nutDanhMuc = document.querySelector(".category-btn");

  if (nutDanhMuc) {
    nutDanhMuc.addEventListener("click", (suKien) => {
      suKien.preventDefault();

      // THAY ĐỔI LỚN: Thay vì ẩn/hiện div bằng JS, ta chuyển hướng thẳng sang trang Lọc Sách của PHP
      // (Giả sử ông để trang lọc sách ở thư mục này)
      window.location.href = "ChucNang/CuaHang/LocSach/index.php";
    });
  }

  // ---------------------------------------------------------
  // 2. LẮNG NGHE SỰ KIỆN LỌC SÁCH (BÊN TRANG LỌC SÁCH)
  // ---------------------------------------------------------

  // Hàm thu thập thông số và chuyển hướng trang (Gửi Request cho PHP)
  function thucHienLocSach() {
    // 1. Lấy mảng thể loại đang tick
    const danhSachTheLoaiDaChon = document.querySelectorAll(
      'input[name="theloai"]:checked',
    );
    let mangTheLoai = Array.from(danhSachTheLoaiDaChon).map(
      (checkbox) => checkbox.value,
    );

    // 2. Lấy giá trị Radio (Khoảng giá) đang chọn
    const theGiaDaChon = document.querySelector(
      'input[name="giatien"]:checked',
    );
    let khoangGia = theGiaDaChon ? theGiaDaChon.value : "";

    // 3. Lấy giá trị Sắp xếp đang được chọn
    const hopSapXep = document.getElementById("sort-select");
    let kieuSapXep = hopSapXep ? hopSapXep.value : "newest";

    // 4. Xây dựng đường dẫn URL truyền tham số (GET Request)
    // File PHP hiện tại sẽ nhận các tham số này trên thanh địa chỉ
    let duongDan = window.location.pathname + "?";

    if (mangTheLoai.length > 0) {
      duongDan += `theloai=${mangTheLoai.join(",")}&`;
    }
    if (khoangGia) {
      duongDan += `gia=${khoangGia}&`;
    }
    duongDan += `sort=${kieuSapXep}`;

    // 5. Tải lại trang với đường dẫn mới để PHP làm việc
    window.location.href = duongDan;
  }

  // Lắng nghe thay đổi kiểu sắp xếp
  const hopSapXep = document.getElementById("sort-select");
  if (hopSapXep) {
    hopSapXep.addEventListener("change", () => {
      thucHienLocSach();
    });
  }

  // Lắng nghe thay đổi tick Thể loại / Giá tiền ở thanh Sidebar
  const thanhBoLoc = document.getElementById("filter-sidebar");
  if (thanhBoLoc) {
    thanhBoLoc.addEventListener("change", (suKien) => {
      // Nếu phần tử vừa thay đổi là input (checkbox/radio) thì gọi hàm lọc
      if (suKien.target.tagName === "INPUT") {
        thucHienLocSach();
      }
    });
  }

  // ---------------------------------------------------------
  // 3. NÚT BỎ CHỌN TẤT CẢ (RESET)
  // ---------------------------------------------------------
  const nutLamMoi = document.getElementById("btn-reset-filter");
  if (nutLamMoi) {
    nutLamMoi.addEventListener("click", () => {
      // Chỉ cần chuyển hướng về trang lọc sách gốc (xóa hết tham số trên URL)
      // PHP sẽ tự động load lại tất cả sách
      window.location.href = window.location.pathname;
    });
  }
});
