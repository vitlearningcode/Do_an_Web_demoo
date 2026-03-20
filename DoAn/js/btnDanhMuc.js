// ==========================================
// XỬ LÝ LOGIC TRANG DANH MỤC & BỘ LỌC SÁCH
// ==========================================

document.addEventListener("DOMContentLoaded", () => {
  // ---------------------------------------------------------
  // 1. LOGIC CHUYỂN ĐỔI GIAO DIỆN (Trang chủ <-> Danh mục)
  // ---------------------------------------------------------
  const btnDanhMuc = document.querySelector(".category-btn");
  const logoTrangChu = document.querySelector(".logo");
  const homeContent = document.getElementById("home-content");
  const categoryPage = document.getElementById("category-page");

  if (btnDanhMuc) {
    btnDanhMuc.addEventListener("click", (e) => {
      e.preventDefault();
      homeContent.style.display = "none";
      categoryPage.style.display = "block";

      // Lần đầu bấm vào thì gọi API load thể loại và load tất cả sách
      if (
        document
          .getElementById("theloai-filter-list")
          .innerHTML.includes("Đang tải")
      ) {
        loadTheLoaiFilter();
      }
      filterBooks();
    });
  }

  if (logoTrangChu) {
    logoTrangChu.addEventListener("click", () => {
      homeContent.style.display = "block";
      categoryPage.style.display = "none";
    });
  }

  // ---------------------------------------------------------
  // 2. GỌI API LẤY DANH SÁCH THỂ LOẠI (Checkbox)
  // ---------------------------------------------------------
  async function loadTheLoaiFilter() {
    try {
      const response = await fetch("./api/get_theloai.php");
      const res = await response.json();

      if (res.status === 200) {
        const container = document.getElementById("theloai-filter-list");
        if (!container) return;

        let html = "";
        res.data.forEach((tl) => {
          html += `
                        <label class="filter-pill-label">
                            <input type="checkbox" name="theloai" value="${tl.maTL}">
                            <span>${tl.tenTL}</span>
                        </label>
                    `;
        });
        container.innerHTML = html;
      }
    } catch (error) {
      console.error("Lỗi khi load thể loại:", error);
    }
  }

  // ---------------------------------------------------------
  // 3. GỌI API LỌC SÁCH ĐỘNG (Theo Thể loại, Giá & Sắp xếp)
  // ---------------------------------------------------------
  async function filterBooks() {
    // 1. Lấy mảng thể loại đang tick
    const theLoaiCheckboxes = document.querySelectorAll(
      'input[name="theloai"]:checked',
    );
    let mangTheLoai = Array.from(theLoaiCheckboxes).map((cb) => cb.value);

    // 2. Lấy giá trị Radio (Giá) đang chọn
    const giaRadio = document.querySelector('input[name="giatien"]:checked');
    let khoangGia = giaRadio ? giaRadio.value : "";

    // 3. Lấy giá trị Sắp xếp đang được chọn
    const sortSelect = document.getElementById("sort-select");
    let sortValue = sortSelect ? sortSelect.value : "newest";

    // 4. Xây dựng đường dẫn URL gọi API (Gộp tất cả vào đây)
    let url = `./api/get_sach_filter.php?`;
    if (mangTheLoai.length > 0) url += `theloai=${mangTheLoai.join(",")}&`;
    if (khoangGia) url += `gia=${khoangGia}&`;
    url += `sort=${sortValue}`; // Gắn tham số sort vào cuối cùng

    // 5. Gọi API
    try {
            const response = await fetch(url);
            const res = await response.json();
            const container = document.getElementById('book-list-container');
            
            if (res.status === 200) {
                // DÒNG NÀY RẤT QUAN TRỌNG: Lưu dữ liệu để file btnThemGiohang.js có thể lấy xài
                window.currentFilteredBooks = res.data; 

                let html = '';
        res.data.forEach((sach) => {
          let giaFormat = new Intl.NumberFormat("vi-VN", {
            style: "currency",
            currency: "VND",
          }).format(sach.giaBan);

          // 1. Thêm data-* vào thẻ div ngoài cùng để cất giấu dữ liệu
          html += `
                      <div class="book-card" 
                           data-id="${sach.maSach}" 
                           data-name="${sach.tenSach}" 
                           data-price="${sach.giaBan}" 
                           data-image="${sach.urlAnh || "https://picsum.photos/200/300"}">
                           
                        <div class="book-image">
                          <img src="${sach.urlAnh || "https://picsum.photos/200/300"}" alt="${sach.tenSach}" referrerPolicy="no-referrer">
                          
                          <div class="book-quick-actions">
                            <button class="book-quick-btn wishlist" title="Yêu thích">
                              <i class="far fa-heart"></i>
                            </button>
                            <button class="book-quick-btn" title="Xem nhanh">
                              <i class="fas fa-eye"></i>
                            </button>
                          </div>

                          <div class="book-quick-add">
                            <button onclick="event.stopPropagation(); addToCart(this)">
                              <i class="fas fa-shopping-cart"></i> Thêm Nhanh
                            </button>
                          </div>
                        </div>
                        
                        <div class="book-info">
                          <span class="book-category">Thể loại</span>
                          <h3 class="book-name">${sach.tenSach}</h3>
                          <p class="book-author">Đang cập nhật</p>
                          <div class="book-rating">
                            <i class="fas fa-star"></i>
                            <span>5.0</span>
                            <span class="reviews-count">(0)</span>
                          </div>
                          
                          <div class="book-card-bottom">
                            <div class="book-price">
                              <span class="current-price">${giaFormat}</span>
                            </div>
                            <button class="add-cart-btn" onclick="event.stopPropagation(); addToCart(this)">
                              <i class="fas fa-shopping-cart"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                    `;
        });
        container.innerHTML = html;
      } else {
        container.innerHTML = `<p style="text-align:center; width:100%; color:#888;">${res.message}</p>`;
      }
    } catch (error) {
      console.error("Lỗi khi lọc sách:", error);
    }
  }

  // ---------------------------------------------------------
  // 4. LẮNG NGHE SỰ KIỆN LỌC & NÚT RESET
  // ---------------------------------------------------------

  // Lắng nghe sự kiện khi người dùng đổi kiểu sắp xếp
  const sortSelectBox = document.getElementById("sort-select");
  if (sortSelectBox) {
    sortSelectBox.addEventListener("change", () => {
      filterBooks(); // Gọi lại hàm lọc sách
    });
  }

  // Lắng nghe sự kiện tick Thể loại / Giá tiền
  const sidebar = document.getElementById("filter-sidebar");
  if (sidebar) {
    sidebar.addEventListener("change", (e) => {
      if (e.target.tagName === "INPUT") {
        filterBooks();
      }
    });
  }

  // Nút Bỏ chọn tất cả
  const btnReset = document.getElementById("btn-reset-filter");
  if (btnReset) {
    btnReset.addEventListener("click", () => {
      document
        .querySelectorAll('input[name="theloai"]')
        .forEach((cb) => (cb.checked = false));
      const allPriceRadio = document.querySelector(
        'input[name="giatien"][value=""]',
      );
      if (allPriceRadio) allPriceRadio.checked = true;

      // Đặt sắp xếp về Mặc định
      if (sortSelectBox) sortSelectBox.value = "newest";

      filterBooks();
    });
  }

  window.filterBooks = filterBooks;
});
