// ==================== THÀNH PHẦN CHATBOT TRỢ LÝ ẢO ====================
class TroLyAo {
  constructor(tuyChon = {}) {
    this.dangMo = false;
    this.danhSachTinNhan = tuyChon.messages || [];
    this.dangGoPhim = false;
    
    // Đưa 2 biến kiểm tra vào làm thuộc tính của class
    this.daKiemTraHeThong = false; 
    this.heThongBinhThuong = false;

    this.thanhPhan = {
      khungChinh: null,
      nutMo: null,
      khuVucTinNhan: null,
      oNhapLieu: null,
      nutGui: null,
    };

    this.nganHangTraLoi = [
      {
        tuKhoa: ["xin chào", "hello", "hi", "chào"],
        cauTraLoi: "Xin chào! Rất vui được hỗ trợ bạn. Bạn cần tìm sách gì hôm nay?",
      },
      {
        tuKhoa: ["tìm sách", "tìm"],
        cauTraLoi: "Bạn có thể tìm kiếm sách theo tên, tác giả hoặc thể loại.",
      }
    ];

    document.addEventListener("DOMContentLoaded", () => this.khoiTao());
  }

  khoiTao() {
    this.thanhPhan.khungChinh = document.getElementById("chatbot");
    this.thanhPhan.nutMo = document.getElementById("chatbot-toggle");
    this.thanhPhan.khuVucTinNhan = document.getElementById("chatbot-messages");
    this.thanhPhan.oNhapLieu = document.getElementById("chatbot-input");
    this.thanhPhan.nutGui = document.getElementById("chatbot-send");

    if (this.thanhPhan.khungChinh && this.thanhPhan.nutMo) {
      this.ganSuKien();
    }

    //gọi bong bóng khi load xong trang 
    this.hienThongBaoChaoMung();
  }
  //===================================================== hàm bong bóng chat=====================================================
  hienThongBaoChaoMung() {
    // 1. Kiểm tra xem bong bóng đã được hiển thị trong phiên này chưa
    if (sessionStorage.getItem("daHienChaoMungChatbot")) {
        return; // Nếu có rồi thì thoát luôn, không hiển thị nữa
    }

    // 2. Nếu chưa có, đánh dấu vào bộ nhớ là đã hiển thị
    sessionStorage.setItem("daHienChaoMungChatbot", "true");

    // 3. Tạo phần tử bong bóng (Code cũ của bạn giữ nguyên)
    const bubble = document.createElement("div");
    bubble.className = "chatbot-welcome-bubble";
    bubble.innerText = "Đạo hữu xin dừng bước, ví tiền của của đạo hữu có duyên với cửa hàng của chúng tôi !!!";
    
    document.body.appendChild(bubble);

    // Sau 5 giây thì biến mất
    setTimeout(() => {
      bubble.classList.add("bubble-fade-out");
      setTimeout(() => {
          if (bubble.parentNode) {
              document.body.removeChild(bubble);
          }
      }, 500); 
    }, 5000);
  }
  // ========================================================================================================

  ganSuKien() {
    this.thanhPhan.nutMo.addEventListener("click", () => this.dongMoKhungChat());

    const nutDong = document.getElementById("chatbot-close");
    if (nutDong) nutDong.addEventListener("click", () => this.dong());

    if (this.thanhPhan.nutGui) {
      this.thanhPhan.nutGui.addEventListener("click", () => this.guiTinNhan());
    }

    if (this.thanhPhan.oNhapLieu) {
      this.thanhPhan.oNhapLieu.addEventListener("keypress", (suKien) => {
        if (suKien.key === "Enter") this.guiTinNhan();
      });
    }
  }

  dongMoKhungChat() {
    if (this.dangMo) this.dong();
    else this.mo();
  }

  // ================================================================= TÍNH NĂNG MỚI: KIỂM TRA CHAT SỐNG KHÔNG =================
  async kiemTraHeThong() {
    // Tự động nhận diện đường dẫn để không lỗi ở trang con
    const duongDanAPI = (typeof DUONG_DAN_GOC_JS !== 'undefined' ? DUONG_DAN_GOC_JS : '/DoAn-Web/DoAn/') + 'CuaHang/TrangBanHang/GiaoDien/xuly_chatbot.php';
    try {
        const phanHoi = await fetch(duongDanAPI, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'ping' }) // Gửi lệnh ping
        });
        return phanHoi.ok;
    } catch (error) {
        return false;
    }
  }
  // ============================================================================================================================

  async mo() {
    this.dangMo = true;
    this.thanhPhan.khungChinh?.classList.add("active");
    if (this.thanhPhan.nutMo) this.thanhPhan.nutMo.style.display = "none";

    // KỊCH BẢN KIỂM TRA LẦN ĐẦU TIÊN
    if (!this.daKiemTraHeThong) {
      // 1. Khóa ô nhập liệu tránh khách gõ khi chưa kết nối xong
      if (this.thanhPhan.oNhapLieu) this.thanhPhan.oNhapLieu.disabled = true;
      if (this.thanhPhan.nutGui) this.thanhPhan.nutGui.disabled = true;

      // 2. Hiện hiệu ứng ba chấm (Giả lập đang kết nối)
      this.hienHieuUngGoPhim();

      // 3. Chờ phản hồi Ping từ PHP
      this.heThongBinhThuong = await this.kiemTraHeThong();
      this.daKiemTraHeThong = true; // Đánh dấu là đã kiểm tra xong

      // 4. Có kết quả, ẩn hiệu ứng
      this.anHieuUngGoPhim();

      if (this.heThongBinhThuong) {
        // HỆ THỐNG SỐNG -> Mở khóa ô nhập liệu
        if (this.thanhPhan.oNhapLieu) {
            this.thanhPhan.oNhapLieu.disabled = false;
            this.thanhPhan.oNhapLieu.focus();
        }
        if (this.thanhPhan.nutGui) this.thanhPhan.nutGui.disabled = false;
        
        // (Tuỳ chọn) In câu chào
        this.themTinNhanVaoKhung("Mình có thể tư vấn sách gì cho bạn?", "bot");
      } else {
        // HỆ THỐNG CHẾT -> In thông báo, ô nhập liệu vẫn bị khóa
        this.themTinNhanVaoKhung("Hệ thống đang quá tải, bạn vui lòng thử lại sau ít phút!!!", "bot");
      }
    } else {
      // Những lần mở sau, nếu hệ thống sống thì chỉ cần focus con trỏ
      if (this.heThongBinhThuong && this.thanhPhan.oNhapLieu) {
          this.thanhPhan.oNhapLieu.focus();
      }
    }
  }

  dong() {
    this.dangMo = false;
    this.thanhPhan.khungChinh?.classList.remove("active");
    if (this.thanhPhan.nutMo) this.thanhPhan.nutMo.style.display = "flex";
  }

  async guiTinNhan() {
    const noiDung = this.thanhPhan.oNhapLieu?.value.trim();
    if (!noiDung || this.dangGoPhim) return;

    this.themTinNhanVaoKhung(noiDung, "user");
    this.thanhPhan.oNhapLieu.value = "";
    this.hienHieuUngGoPhim();

    try {
      // Dùng đường dẫn linh hoạt để chạy được ở mọi trang
      const duongDanAPI = (typeof DUONG_DAN_GOC_JS !== 'undefined' ? DUONG_DAN_GOC_JS : '/DoAn-Web/DoAn/') + 'CuaHang/TrangBanHang/GiaoDien/xuly_chatbot.php';
      
      const response = await fetch(duongDanAPI, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: noiDung })
      });

      if (!response.ok) throw new Error("Lỗi mạng HTTP");

      const data = await response.json();
      this.anHieuUngGoPhim();

      if (data.error) {
         this.themTinNhanVaoKhung("⚠️ Hệ thống báo: " + data.error, "bot");
         return;
      }

      const cauTraLoi = data.candidates[0]?.content?.parts[0]?.text || "Xin lỗi, tôi gặp chút trục trặc.";
      this.themTinNhanVaoKhung(cauTraLoi, "bot");

    } catch (error) {
      console.error("Lỗi:", error);
      this.anHieuUngGoPhim();
      this.themTinNhanVaoKhung("Hệ thống trợ lý ảo đang bảo trì, bạn vui lòng thử lại sau!", "bot");
    }
  }

  hienHieuUngGoPhim() {
    this.dangGoPhim = true;
    let hieuUng = document.getElementById("typing-indicator");
    if (hieuUng) {
      hieuUng.style.display = "flex";
      this.thanhPhan.khuVucTinNhan.scrollTop = this.thanhPhan.khuVucTinNhan.scrollHeight;
    }
  }

  anHieuUngGoPhim() {
    this.dangGoPhim = false;
    const hieuUng = document.getElementById("typing-indicator");
    if (hieuUng) hieuUng.style.display = "none";
  }

  themTinNhanVaoKhung(noiDung, nguoiGui) {
    if (!this.thanhPhan.khuVucTinNhan) return;
    this.anHieuUngGoPhim();

    const theTinNhan = document.createElement("div");
    theTinNhan.className = `chatbot-message ${nguoiGui}`;
    // Nếu là bot, dùng innerHTML để có thể xử lý các icon hoặc định dạng (nếu cần), user thì in chữ bình thường
    theTinNhan.innerHTML = `
      <div class="message-avatar ${nguoiGui}">
        <i class="fas fa-${nguoiGui === "user" ? "user" : "robot"}"></i>
      </div>
      <div class="message-content">
        <p>${noiDung}</p>
      </div>
    `;

    this.thanhPhan.khuVucTinNhan.appendChild(theTinNhan);
    this.thanhPhan.khuVucTinNhan.scrollTop = this.thanhPhan.khuVucTinNhan.scrollHeight;
  }
}

const chatbot = new TroLyAo();