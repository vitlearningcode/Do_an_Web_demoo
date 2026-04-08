// ==================== THÀNH PHẦN CHATBOT TRỢ LÝ ẢO ====================

class TroLyAo {
  constructor(tuyChon = {}) {
    this.dangMo = false;
    this.danhSachTinNhan = tuyChon.messages || [];
    this.dangGoPhim = false;

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
        cauTraLoi:
          "Xin chào! Rất vui được hỗ trợ bạn. Bạn cần tìm sách gì hôm nay?",
      },
      {
        tuKhoa: ["tìm sách", "tìm"],
        cauTraLoi: "Bạn có thể tìm kiếm sách theo tên, tác giả hoặc thể loại.",
      },
      // ... (Giữ nguyên các câu trả lời của ông ở đây cho gọn nhé)
    ];

    document.addEventListener("DOMContentLoaded", () => this.khoiTao());
  }

  khoiTao() {
    // CHỈ ĐI TÌM HTML DO PHP IN RA, KHÔNG TỰ VẼ NỮA
    this.thanhPhan.khungChinh = document.getElementById("chatbot");
    this.thanhPhan.nutMo = document.getElementById("chatbot-toggle");
    this.thanhPhan.khuVucTinNhan = document.getElementById("chatbot-messages");
    this.thanhPhan.oNhapLieu = document.getElementById("chatbot-input");
    this.thanhPhan.nutGui = document.getElementById("chatbot-send");

    // Nếu tìm thấy khung chatbot trên trang thì mới gắn sự kiện
    if (this.thanhPhan.khungChinh && this.thanhPhan.nutMo) {
      this.ganSuKien();
    }
  }

  ganSuKien() {
    this.thanhPhan.nutMo.addEventListener("click", () =>
      this.dongMoKhungChat(),
    );

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

  mo() {
    this.dangMo = true;
    this.thanhPhan.khungChinh?.classList.add("active");
    if (this.thanhPhan.nutMo) this.thanhPhan.nutMo.style.display = "none";
    this.thanhPhan.oNhapLieu?.focus();
  }

  dong() {
    this.dangMo = false;
    this.thanhPhan.khungChinh?.classList.remove("active");
    if (this.thanhPhan.nutMo) this.thanhPhan.nutMo.style.display = "flex";
  }

  guiTinNhan() {
    const noiDung = this.thanhPhan.oNhapLieu?.value.trim();
    if (!noiDung || this.dangGoPhim) return;

    this.themTinNhanVaoKhung(noiDung, "user");
    this.thanhPhan.oNhapLieu.value = "";
    this.hienHieuUngGoPhim();

    setTimeout(
      () => {
        this.anHieuUngGoPhim();
        const cauTraLoi = this.timCauTraLoi(noiDung);
        this.themTinNhanVaoKhung(cauTraLoi, "bot");
      },
      1000 + Math.random() * 1000,
    );
  }

  hienHieuUngGoPhim() {
    this.dangGoPhim = true;
    let hieuUng = document.getElementById("typing-indicator");
    if (hieuUng) {
      hieuUng.style.display = "flex";
      this.thanhPhan.khuVucTinNhan.scrollTop =
        this.thanhPhan.khuVucTinNhan.scrollHeight;
    }
  }

  anHieuUngGoPhim() {
    this.dangGoPhim = false;
    const hieuUng = document.getElementById("typing-indicator");
    if (hieuUng) hieuUng.style.display = "none";
  }

  // Chỗ này in HTML động cho từng dòng tin nhắn là hợp lý (giống giỏ hàng)
  themTinNhanVaoKhung(noiDung, nguoiGui) {
    if (!this.thanhPhan.khuVucTinNhan) return;
    this.anHieuUngGoPhim();

    const theTinNhan = document.createElement("div");
    theTinNhan.className = `chatbot-message ${nguoiGui}`;
    theTinNhan.innerHTML = `
      <div class="message-avatar ${nguoiGui}">
        <i class="fas fa-${nguoiGui === "user" ? "user" : "robot"}"></i>
      </div>
      <div class="message-content">
        <p>${noiDung}</p>
      </div>
    `;

    this.thanhPhan.khuVucTinNhan.appendChild(theTinNhan);
    this.thanhPhan.khuVucTinNhan.scrollTop =
      this.thanhPhan.khuVucTinNhan.scrollHeight;
  }

  timCauTraLoi(tinNhanCuaUser) {
    const chuThuong = tinNhanCuaUser.toLowerCase();
    for (const phanTu of this.nganHangTraLoi) {
      for (const tuKhoa of phanTu.tuKhoa) {
        if (chuThuong.includes(tuKhoa)) return phanTu.cauTraLoi;
      }
    }
    return "Xin lỗi, tôi chưa hiểu ý bạn. Bạn có thể hỏi về đơn hàng, sách hoặc khuyến mãi nhé!";
  }
}

const chatbot = new TroLyAo();
