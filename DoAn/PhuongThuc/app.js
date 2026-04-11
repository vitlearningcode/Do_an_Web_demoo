// ==================== CẤU TRÚC CHÍNH CỦA TRANG CỬA HÀNG ====================
// Ghi chú: Trang Admin đã có trang riêng (CuaHang/ChuCuaHang/).
//          File này CHỈ phục vụ trang cửa hàng (storefront) — index.php.
// ==================== TRẠNG THÁI HỆ THỐNG (STATE) ====================
let danhSachYeuThich = new Set();

// ==================== KHỞI TẠO ỨNG DỤNG ====================
document.addEventListener("DOMContentLoaded", () => {
  khoiTaoDemNguocKhuyenMai();
  khoiTaoNutGioHang();
  khoiTaoNutDangNhap();

  console.log("✅ Hệ thống JavaScript trang cửa hàng đã khởi tạo (Chế độ PHP SSR)");
});

// ==================== ĐẾM NGƯỢC FLASH SALE ====================
// Ghi chú: Đồng hồ đếm ngược thực từ thời gian KhuyenMai trong DB được
//          PHP gán vào biến ketThucFlashSale rồi JS đếm ngược phía client.
function khoiTaoDemNguocKhuyenMai() {
  // Chỉ chạy nếu có biến thời gian kết thúc từ PHP (xem index.php)
  // Flash sale timer đã được xử lý inline trong index.php
}

// ==================== NÚT GIỎ HÀNG (MENU TRÊN) ====================
function khoiTaoNutGioHang() {
  // cart.js xử lý giỏ hàng (gắn sự kiện cho #btn-cart)
  // Hàm này dự phòng nếu cần logic bổ sung
}

// ==================== NÚT ĐĂNG NHẬP ====================
function khoiTaoNutDangNhap() {
  const nutDangNhap = document.getElementById("btn-login");
  if (nutDangNhap) {
    nutDangNhap.addEventListener("click", () => {
      if (typeof authModal !== "undefined") {
        authModal.mo("dang_nhap");
      }
    });
  }
}

// ============================================
// Đăng nhập / Đăng ký / Đăng xuất — Modal
// ============================================

// Hàm mở form Đăng nhập
function openLogin() {
    let overlay       = document.getElementById('modal-overlay');
    let loginModal    = document.getElementById('login-modal');
    let registerModal = document.getElementById('register-modal');
    let logoutModal   = document.getElementById('logout-modal');

    if (overlay)       overlay.style.display       = 'block';
    if (loginModal)    loginModal.style.display    = 'block';
    if (registerModal) registerModal.style.display = 'none';
    if (logoutModal)   logoutModal.style.display   = 'none';
}

// Hàm mở form Đăng ký
function openRegister() {
    let overlay       = document.getElementById('modal-overlay');
    let loginModal    = document.getElementById('login-modal');
    let registerModal = document.getElementById('register-modal');
    let logoutModal   = document.getElementById('logout-modal');

    if (overlay)       overlay.style.display       = 'block';
    if (loginModal)    loginModal.style.display    = 'none';
    if (registerModal) registerModal.style.display = 'block';
    if (logoutModal)   logoutModal.style.display   = 'none';
}

// Hàm mở form Đăng xuất
function openLogout() {
    let overlay       = document.getElementById('modal-overlay');
    let loginModal    = document.getElementById('login-modal');
    let registerModal = document.getElementById('register-modal');
    let logoutModal   = document.getElementById('logout-modal');

    if (overlay)       overlay.style.display       = 'block';
    if (loginModal)    loginModal.style.display    = 'none';
    if (registerModal) registerModal.style.display = 'none';
    if (logoutModal)   logoutModal.style.display   = 'block';
}

// Hàm đóng tất cả các Modal
function closeModal() {
    let overlay       = document.getElementById('modal-overlay');
    let loginModal    = document.getElementById('login-modal');
    let registerModal = document.getElementById('register-modal');
    let logoutModal   = document.getElementById('logout-modal');

    if (overlay)       overlay.style.display       = 'none';
    if (loginModal)    loginModal.style.display    = 'none';
    if (registerModal) registerModal.style.display = 'none';
    if (logoutModal)   logoutModal.style.display   = 'none';
}

// ===================================================
// Menu dropdown nút đăng nhập
// ===================================================

// Bật/tắt menu dropdown người dùng
function toggleUserMenu(event) {
    event.stopPropagation();
    let menu = document.getElementById('userDropdown');
    if (menu) {
        menu.classList.toggle('show');
    }
}

// Đóng menu khi click ra ngoài
document.addEventListener('click', function(event) {
    let menu = document.getElementById('userDropdown');
    let btn  = document.getElementById('btn-user-profile');

    if (menu && menu.classList.contains('show')) {
        if (btn && !btn.contains(event.target) && !menu.contains(event.target)) {
            menu.classList.remove('show');
        }
    }
});

// ==================== CẬP NHẬT THÔNG TIN ====================
function moCapNhatThongTin(event) {
    if (event) event.preventDefault();
    window.location.href = 'CuaHang/TrangBanHang/taiKhoan/capNhat.php';
}
//==========================================================gợi ý tìm kiếm==========================================================
// ========================================================
// CHỨC NĂNG: THANH TÌM KIẾM THÔNG MINH (Không dùng JSON)
// ========================================================

function timKiemNhanh(tuKhoa) {
    let khungKetQua = document.getElementById('danh-sach-ket-qua');
    
    // Nếu xóa hết chữ thì ẩn khung đi
    if (tuKhoa.trim() === '') {
        khungKetQua.style.display = 'none';
        khungKetQua.innerHTML = '';
        return;
    }
    
    // Gói dữ liệu để gửi đi
    let duLieuGuiDi = new FormData();
    duLieuGuiDi.append('tu_khoa', tuKhoa);

    // CẬP NHẬT ĐƯỜNG DẪN MỚI TRỎ VÀO THƯ MỤC GIAO DIỆN
    fetch(DUONG_DAN_GOC_JS + 'CuaHang/TrangBanHang/GiaoDien/xuly_timkiem_nhanh.php', {
        method: 'POST',
        body: duLieuGuiDi
    })
    .then(phanHoi => phanHoi.text()) // Lấy dữ liệu dưới dạng text thuần (HTML)
    .then(htmlMoi => {
        if (htmlMoi.trim() !== '') {
            // Thay thế nội dung HTML cũ bằng HTML mới và hiện khung lên
            khungKetQua.innerHTML = htmlMoi;
            khungKetQua.style.display = 'block';
        }
    })
    .catch(loi => {
        console.log('Đã xảy ra lỗi khi tìm kiếm:', loi);
    });
}

// Chức năng phụ: Click ra ngoài vùng tìm kiếm thì tự động ẩn danh sách
document.addEventListener('click', function(suKien) {
    let khungTimKiem = document.getElementById('khung-tim-kiem');
    let khungKetQua = document.getElementById('danh-sach-ket-qua');
    
    // Nếu click không nằm trong khung tìm kiếm
    if (khungTimKiem && !khungTimKiem.contains(suKien.target)) {
        if (khungKetQua) khungKetQua.style.display = 'none';
    }
}); 
// =========================================================chức năng sách yêu thích ==========================================================
// ========================================================
// CHỨC NĂNG: CẬP NHẬT TRẠNG THÁI YÊU THÍCH (PDO + PHP THUẦN)
// ========================================================

function thayDoiYeuThich(su_kien, nut_bam) {
    if (su_kien) {
        su_kien.stopPropagation();
        su_kien.preventDefault();
    }

    let the_sach = nut_bam.closest('.book-card');
    if (!the_sach) return;
    
    let ma_sach = the_sach.dataset.id;
    let bieu_tuong_trai_tim = nut_bam.querySelector('i');

    let du_lieu_gui_di = new FormData();
    du_lieu_gui_di.append('ma_sach', ma_sach);

    // CẬP NHẬT ĐƯỜNG DẪN MỚI TẠI ĐÂY
    fetch(DUONG_DAN_GOC_JS + 'CuaHang/TrangBanHang/GiaoDien/xuly_yeuthich.php', {
        method: 'POST',
        body: du_lieu_gui_di
    })
    .then(phan_hoi => phan_hoi.text())
    .then(ket_qua_tra_ve => {
        let trang_thai = ket_qua_tra_ve.trim();
        
        if (trang_thai === 'CHUA_DANG_NHAP') {
            alert('Bạn vui lòng đăng nhập để sử dụng tính năng yêu thích!');
            if (typeof openLogin === "function") openLogin();
        } 
        else if (trang_thai === 'DA_THEM') {
            bieu_tuong_trai_tim.className = 'fas fa-heart';
            bieu_tuong_trai_tim.style.color = '#ef4444'; // Hiện màu đỏ
        } 
        else if (trang_thai === 'DA_XOA') {
            bieu_tuong_trai_tim.className = 'far fa-heart';
            bieu_tuong_trai_tim.style.color = ''; // Trở về mặc định
        }
    })
    .catch(loi => {
        console.error('Lỗi khi cập nhật danh sách yêu thích:', loi);
    });
}