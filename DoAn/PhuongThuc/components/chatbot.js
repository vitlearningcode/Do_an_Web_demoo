/**
 * ============================================================
 * LUỒNG: CHATBOT TRỢ LÝ ẢO (chatbot.js)
 *
 * GỌI BỞI: footer.php hoặc layout.php (render HTML chatbot + load script)
 *   <div id="chatbot">...</div>
 *   <script src="...chatbot.js"></script>
 *
 * HTML YÊU CẦU (PHP render sẵn):
 *   <div id="chatbot" class="chatbot-panel">
 *     <div id="chatbot-messages">
 *       <div id="typing-indicator" style="display:none">...</div>
 *     </div>
 *     <input id="chatbot-input" type="text" />
 *     <button id="chatbot-send">Gửi</button>
 *     <button id="chatbot-close">✕</button>
 *   </div>
 *   <button id="chatbot-toggle">💬</button>  ← Nút mở chatbot (floating)
 *
 * BACKEND:
 *   POST → xuly_chatbot.php (GiaoDien/)
 *   Body: JSON { message: "..." } hoặc { action: "ping" }
 *   Response: JSON từ Gemini API (proxied qua PHP)
 *     { candidates: [{ content: { parts: [{ text: "..." }] } }] }
 *
 * LUỒNG HOẠT ĐỘNG:
 *
 *   KHỞI ĐỘNG (1 lần/phiên):
 *     DOMContentLoaded → hienThongBaoChaoMung()
 *       → SessionStorage check: chỉ hiện bong bóng lần đầu mở trang
 *       → Tạo <div class="chatbot-welcome-bubble"> → tự xóa sau 5 giây
 *
 *   MỞ CHATBOT (lần đầu):
 *     User click nút 💬 → mo()
 *       → Disable input (chờ ping)
 *       → hienHieuUngGoPhim() — hiện "..." typing indicator
 *       → fetch POST { action:'ping' } → xuly_chatbot.php
 *       → Nếu OK:  enable input + hiện lời chào từ bot
 *       → Nếu lỗi: hiện thông báo hệ thống quá tải
 *     Lần thứ 2+: chỉ focus input (không ping lại)
 *
 *   GỬI TIN NHẮN:
 *     User nhập + Enter/Click Gửi → guiTinNhan()
 *       → Hiện tin nhắn user (themTinNhanVaoKhung)
 *       → hienHieuUngGoPhim() — "bot đang gõ..."
 *       → fetch POST { message: "..." } → xuly_chatbot.php
 *         → PHP gọi Gemini API với system prompt
 *       → Nhận JSON response → hiện phản hồi bot
 *       → Nếu lỗi mạng/API → hiện thông báo bảo trì
 *
 * LƯU Ý QUAN TRỌNG:
 *   - File này là exception với nguyên tắc no-innerHTML:
 *     themTinNhanVaoKhung() dùng innerHTML để render tin nhắn
 *     Lý do: cần xử lý markdown/HTML từ Gemini API response
 *     Rủi ro XSS: thấp vì nội dung user được echo thẳng —
 *     TODO: sanitize noiDung trước khi inject nếu muốn an toàn hơn
 *
 *   - Dùng fetch() (AJAX) — ngoại lệ so với kiến trúc no-AJAX
 *     Lý do: chatbot cần real-time response, không thể redirect form
 *
 * BIẾN TOÀN CỤC:
 *   chatbot — instance duy nhất
 *   DUONG_DAN_GOC_JS — inject từ PHP trong <head>
 * ============================================================
 */

class TroLyAo {
  constructor(tuyChon = {}) {
    this.dangMo           = false;                    // Trạng thái panel chatbot
    this.danhSachTinNhan  = tuyChon.messages || [];   // Lịch sử tin nhắn (tùy chọn)
    this.dangGoPhim       = false;                    // Bot đang "gõ" — khoá gửi tiếp

    // --- Flag kiểm tra hệ thống (chỉ ping 1 lần/phiên) ---
    this.daKiemTraHeThong = false;  // true sau lần ping đầu tiên
    this.heThongBinhThuong = false; // true nếu xuly_chatbot.php phản hồi OK

    // Tham chiếu DOM — tìm trong khoiTao()
    this.thanhPhan = {
      khungChinh:    null, // <div id="chatbot"> — panel chứa toàn bộ chatbot
      nutMo:         null, // <button id="chatbot-toggle"> — nút floating 💬
      khuVucTinNhan: null, // <div id="chatbot-messages"> — vùng hiển thị chat
      oNhapLieu:     null, // <input id="chatbot-input">
      nutGui:        null, // <button id="chatbot-send">
    };

    // Ngân hàng trả lời offline (dự phòng khi không có API)
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

    // Đợi DOM sẵn sàng trước khi tìm phần tử
    document.addEventListener("DOMContentLoaded", () => this.khoiTao());
  }

  khoiTao() {
    // TÌM phần tử do PHP render
    this.thanhPhan.khungChinh    = document.getElementById("chatbot");
    this.thanhPhan.nutMo         = document.getElementById("chatbot-toggle");
    this.thanhPhan.khuVucTinNhan = document.getElementById("chatbot-messages");
    this.thanhPhan.oNhapLieu     = document.getElementById("chatbot-input");
    this.thanhPhan.nutGui        = document.getElementById("chatbot-send");

    // Chỉ gắn sự kiện nếu chatbot tồn tại trong trang
    if (this.thanhPhan.khungChinh && this.thanhPhan.nutMo) {
      this.ganSuKien();
    }

    // Bong bóng chào mừng: chỉ hiện 1 lần mỗi phiên (sessionStorage check)
    this.hienThongBaoChaoMung();
  }

  // ============================================================
  // BONG BÓNG CHÀO MỪNG
  // Hiện 1 lần khi load trang lần đầu trong phiên làm việc
  // Tự xóa sau 5 giây (fade out + removeChild)
  // ============================================================
  hienThongBaoChaoMung() {
    // sessionStorage: tồn tại trong tab hiện tại, mất khi đóng tab
    // → Tránh bong bóng hiện lại mỗi lần navigate trong site
    if (sessionStorage.getItem("daHienChaoMungChatbot")) {
      return; // Đã hiện rồi → bỏ qua
    }

    sessionStorage.setItem("daHienChaoMungChatbot", "true");

    // Tạo <div> bong bóng — gắn vào body (vị trí fixed bởi CSS)
    const bubble = document.createElement("div");
    bubble.className = "chatbot-welcome-bubble";
    bubble.innerText = "Đạo hữu xin dừng bước, ví tiền của của đạo hữu có duyên với cửa hàng của chúng tôi !!!";

    document.body.appendChild(bubble);

    // Tự ẩn sau 5 giây: thêm class fade-out → CSS transition opacity:0
    setTimeout(() => {
      bubble.classList.add("bubble-fade-out");
      // Sau khi transition kết thúc (500ms): xóa khỏi DOM hoàn toàn
      setTimeout(() => {
        if (bubble.parentNode) {
          document.body.removeChild(bubble);
        }
      }, 500);
    }, 5000);
  }

  ganSuKien() {
    // Nút toggle 💬: click → đóng nếu đang mở, mở nếu đang đóng
    this.thanhPhan.nutMo.addEventListener("click", () => this.dongMoKhungChat());

    // Nút ✕ đóng panel
    const nutDong = document.getElementById("chatbot-close");
    if (nutDong) nutDong.addEventListener("click", () => this.dong());

    // Nút "Gửi"
    if (this.thanhPhan.nutGui) {
      this.thanhPhan.nutGui.addEventListener("click", () => this.guiTinNhan());
    }

    // Phím Enter trong ô nhập liệu → gửi tin nhắn
    if (this.thanhPhan.oNhapLieu) {
      this.thanhPhan.oNhapLieu.addEventListener("keypress", (suKien) => {
        if (suKien.key === "Enter") this.guiTinNhan();
      });
    }
  }

  /**
   * Toggle đóng/mở panel chatbot
   */
  dongMoKhungChat() {
    if (this.dangMo) this.dong();
    else this.mo();
  }

  // ============================================================
  // KIỂM TRA HỆ THỐNG CHATBOT (PING)
  // Gửi { action: 'ping' } → xuly_chatbot.php
  // Trả về: true nếu kết nối OK, false nếu lỗi
  // ============================================================
  async kiemTraHeThong() {
    // DUONG_DAN_GOC_JS: được PHP inject vào <head>
    // Fallback '/DoAn-Web/DoAn/': dùng khi biến chưa được set (hiếm)
    const duongDanAPI = (typeof DUONG_DAN_GOC_JS !== 'undefined'
      ? DUONG_DAN_GOC_JS
      : '/DoAn-Web/DoAn/')
      + 'CuaHang/TrangBanHang/GiaoDien/xuly_chatbot.php';

    try {
      const phanHoi = await fetch(duongDanAPI, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'ping' }) // PHP kiểm tra và trả 200 OK
      });
      return phanHoi.ok; // true nếu HTTP 200, false nếu 4xx/5xx
    } catch (error) {
      return false; // Không thể kết nối mạng
    }
  }

  /**
   * Mở panel chatbot
   * Lần đầu: ping server kiểm tra sống/chết → enable/disable input
   * Lần sau:  chỉ focus input (không ping lại)
   */
  async mo() {
    this.dangMo = true;
    this.thanhPhan.khungChinh?.classList.add("active");

    // Ẩn nút toggle 💬 khi panel đang mở (tránh overlap)
    if (this.thanhPhan.nutMo) this.thanhPhan.nutMo.style.display = "none";

    // --- LẦN ĐẦU MỞ: Ping kiểm tra hệ thống ---
    if (!this.daKiemTraHeThong) {
      // 1. Khoá input trong lúc chờ phản hồi (tránh user gõ khi bot chưa sẵn sàng)
      if (this.thanhPhan.oNhapLieu) this.thanhPhan.oNhapLieu.disabled = true;
      if (this.thanhPhan.nutGui)    this.thanhPhan.nutGui.disabled    = true;

      // 2. Hiện "..." gợi ý đang kết nối
      this.hienHieuUngGoPhim();

      // 3. Gửi ping — await: chờ kết quả trước khi tiếp tục
      this.heThongBinhThuong = await this.kiemTraHeThong();
      this.daKiemTraHeThong  = true; // Đánh dấu đã kiểm tra (không ping lại lần sau)

      // 4. Ẩn "..." sau khi có kết quả
      this.anHieuUngGoPhim();

      if (this.heThongBinhThuong) {
        // HỆ THỐNG SỐNG → Mở khóa input + hiện lời chào
        if (this.thanhPhan.oNhapLieu) {
          this.thanhPhan.oNhapLieu.disabled = false;
          this.thanhPhan.oNhapLieu.focus();
        }
        if (this.thanhPhan.nutGui) this.thanhPhan.nutGui.disabled = false;

        this.themTinNhanVaoKhung("Mình có thể tư vấn sách gì cho bạn?", "bot");
      } else {
        // HỆ THỐNG KHÔNG PHẢN HỒI → Input vẫn bị khóa + thông báo
        this.themTinNhanVaoKhung(
          "Hệ thống đang quá tải, bạn vui lòng thử lại sau ít phút!!!",
          "bot"
        );
      }
    } else {
      // --- LẦN SAU: Không ping lại, chỉ focus nếu hệ thống bình thường ---
      if (this.heThongBinhThuong && this.thanhPhan.oNhapLieu) {
        this.thanhPhan.oNhapLieu.focus();
      }
    }
  }

  /**
   * Đóng panel chatbot + hiện lại nút toggle
   */
  dong() {
    this.dangMo = false;
    this.thanhPhan.khungChinh?.classList.remove("active");
    // Hiện lại nút 💬 (dùng flex vì CSS nút này là display:flex)
    if (this.thanhPhan.nutMo) this.thanhPhan.nutMo.style.display = "flex";
  }

  /**
   * Gửi tin nhắn user đến API chatbot
   * Luồng: lấy nội dung → hiện user msg → fetch POST → hiện bot msg
   */
  async guiTinNhan() {
    const noiDung = this.thanhPhan.oNhapLieu?.value.trim();

    // Guard: không gửi rỗng, không gửi khi bot đang trả lời
    if (!noiDung || this.dangGoPhim) return;

    // 1. Hiện tin nhắn user ngay lập tức (UX: không chờ server)
    this.themTinNhanVaoKhung(noiDung, "user");
    this.thanhPhan.oNhapLieu.value = ""; // Xóa ô nhập

    // 2. Hiện "..." chờ bot trả lời
    this.hienHieuUngGoPhim();

    try {
      const duongDanAPI = (typeof DUONG_DAN_GOC_JS !== 'undefined'
        ? DUONG_DAN_GOC_JS
        : '/DoAn-Web/DoAn/')
        + 'CuaHang/TrangBanHang/GiaoDien/xuly_chatbot.php';

      // 3. POST tin nhắn lên PHP → PHP gọi Gemini API
      const response = await fetch(duongDanAPI, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: noiDung })
      });

      if (!response.ok) throw new Error("Lỗi mạng HTTP");

      // 4. Parse JSON response từ Gemini (qua PHP proxy)
      const data = await response.json();
      this.anHieuUngGoPhim();

      // Nếu PHP trả về lỗi từ Gemini API
      if (data.error) {
        this.themTinNhanVaoKhung("⚠️ Hệ thống báo: " + data.error, "bot");
        return;
      }

      // Lấy text từ cấu trúc Gemini response:
      // data.candidates[0].content.parts[0].text
      const cauTraLoi = data.candidates[0]?.content?.parts[0]?.text
        || "Xin lỗi, tôi gặp chút trục trặc.";

      // 5. Hiện phản hồi bot
      this.themTinNhanVaoKhung(cauTraLoi, "bot");

    } catch (error) {
      // Lỗi mạng hoặc JSON parse lỗi
      console.error("Lỗi chatbot:", error);
      this.anHieuUngGoPhim();
      this.themTinNhanVaoKhung(
        "Hệ thống trợ lý ảo đang bảo trì, bạn vui lòng thử lại sau!",
        "bot"
      );
    }
  }

  /**
   * Hiện typing indicator "..." (bot đang gõ)
   * Đồng thời khoá flag dangGoPhim để tránh gửi 2 message cùng lúc
   */
  hienHieuUngGoPhim() {
    this.dangGoPhim = true;
    let hieuUng = document.getElementById("typing-indicator");
    if (hieuUng) {
      hieuUng.style.display = "flex";
      // Auto scroll xuống cuối để typing indicator luôn nhìn thấy
      this.thanhPhan.khuVucTinNhan.scrollTop =
        this.thanhPhan.khuVucTinNhan.scrollHeight;
    }
  }

  /**
   * Ẩn typing indicator + mở khóa gửi tiếp
   */
  anHieuUngGoPhim() {
    this.dangGoPhim = false;
    const hieuUng = document.getElementById("typing-indicator");
    if (hieuUng) hieuUng.style.display = "none";
  }

  /**
   * Thêm tin nhắn vào khu vực chat
   *
   * @param {string} noiDung   - Nội dung tin nhắn
   * @param {string} nguoiGui  - 'user' | 'bot'
   *
   * LƯU Ý: Dùng innerHTML — NGOẠI LỆ với nguyên tắc no-innerHTML
   *   Lý do: Gemini có thể trả về markdown/HTML formatting
   *   Rủi ro XSS: tin nhắn user được inject thẳng (TODO: sanitize)
   */
  themTinNhanVaoKhung(noiDung, nguoiGui) {
    if (!this.thanhPhan.khuVucTinNhan) return;

    // Ẩn typing indicator nếu đang hiện (bot chuẩn bị trả lời)
    this.anHieuUngGoPhim();

    const theTinNhan = document.createElement("div");
    theTinNhan.className = `chatbot-message ${nguoiGui}`;

    // Cấu trúc: avatar icon + nội dung text
    // nguoiGui === 'user': icon fa-user | 'bot': icon fa-robot
    theTinNhan.innerHTML = `
      <div class="message-avatar ${nguoiGui}">
        <i class="fas fa-${nguoiGui === "user" ? "user" : "robot"}"></i>
      </div>
      <div class="message-content">
        <p>${noiDung}</p>
      </div>
    `;

    this.thanhPhan.khuVucTinNhan.appendChild(theTinNhan);

    // Tự động scroll xuống tin nhắn mới nhất
    this.thanhPhan.khuVucTinNhan.scrollTop =
      this.thanhPhan.khuVucTinNhan.scrollHeight;
  }
}

// Singleton toàn cục
const chatbot = new TroLyAo();