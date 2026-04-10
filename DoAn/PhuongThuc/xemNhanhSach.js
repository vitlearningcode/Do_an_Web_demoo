/**
 * xemNhanhSach.js — v2
 * Modal "Xem Nhanh Sách"
 *
 * ✅ KHÔNG innerHTML — chỉ textContent / setAttribute / className / style
 * ✅ KHÔNG AJAX — KHÔNG JSON
 *    → Mọi dữ liệu đọc từ data-* của .book-card (PHP render ra trang)
 * ✅Gắn click lên .book-card, bỏ qua các nút: .btn-add-to-cart .btn-add-quick .btn-action-icon
 */

(function () {
  'use strict';

  // ── Phần tử gốc ──────────────────────────────────────────────────────
  var overlay = document.getElementById('modal-xem-nhanh');
  if (!overlay) return;

  var nutDong   = document.getElementById('mn-dong');
  var sachHienTai = null; // Lưu thông tin sách đang mở (để thêm giỏ hàng)
  var soLuong   = 1;

  // ── Mở / Đóng modal ──────────────────────────────────────────────────
  function moModal() {
    // Tính chiều rộng scrollbar để bù vào padding, tránh giật trang
    var scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
    document.body.style.paddingRight = scrollbarWidth + 'px';
    document.body.style.overflow = 'hidden';
    overlay.classList.add('active');
  }

  function dongModal() {
    overlay.classList.remove('active');
    document.body.style.paddingRight = '';
    document.body.style.overflow = '';
    sachHienTai = null;
  }

  // Click ngoài hộp → đóng
  overlay.addEventListener('click', function (e) {
    if (e.target === overlay) dongModal();
  });

  if (nutDong) nutDong.addEventListener('click', dongModal);

  // Phím Escape → đóng
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && overlay.classList.contains('active')) dongModal();
  });

  // ── Gắn click cho tất cả .book-card ──────────────────────────────────
  document.querySelectorAll('.book-card').forEach(function (card) {
    card.addEventListener('click', function (e) {

      // Bỏ qua: nút thêm giỏ, thêm nhanh, icon tim/mắt
      if (
        e.target.closest('.btn-add-to-cart') ||
        e.target.closest('.btn-add-quick')   ||
        e.target.closest('.btn-action-icon')
      ) {
        return;
      }

      // Đọc toàn bộ dữ liệu từ data-* của card (PHP đã render sẵn)
      var chiTiet = {
        maSach      : card.getAttribute('data-id')         || '',
        tenSach     : card.getAttribute('data-name')       || 'Chưa có tên',
        hinhAnh     : card.getAttribute('data-image')      || '',
        giaHienTai  : card.getAttribute('data-price')      || '0',   // giá hiện tại (đã giảm hoặc gốc)
        giaBan      : card.getAttribute('data-gia-ban')    || '0',   // giá gốc trước giảm
        phanTramGiam: card.getAttribute('data-giam')       || '',    // % giảm, rỗng = không giảm
        tacGia      : card.getAttribute('data-tac-gia')   || 'Đang cập nhật',
        theLoai     : card.getAttribute('data-the-loai')  || '',
        diemTB      : card.getAttribute('data-diem')       || '0',
        soReview    : card.getAttribute('data-reviews')   || '0',
        tongBan     : card.getAttribute('data-da-ban')     || '0',
        moTa        : card.getAttribute('data-mo-ta')      || '',
        nhaXuatBan  : card.getAttribute('data-nxb')        || '',
        namSX       : card.getAttribute('data-nam-sx')     || '',
        hinhThucBia : card.getAttribute('data-bia')        || '',
        tonKho      : card.getAttribute('data-ton-kho')    || '',
      };

      // Nhãn từ card (BÁN CHẠY / FLASH SALE / MỚI…)
      var nhanLoaiEl = card.querySelector('.book-badge.label-type');
      var nhanGiamEl = card.querySelector('.book-badge.label-discount');
      chiTiet.nhanLoai = nhanLoaiEl ? nhanLoaiEl.textContent.trim() : '';
      chiTiet.nhanGiam = nhanGiamEl ? nhanGiamEl.textContent.trim() : '';

      sachHienTai = chiTiet;
      soLuong = 1;

      moModal();
      dienDuLieu(chiTiet);
    });
  });

  // ── Điền dữ liệu vào modal (không innerHTML) ─────────────────────────
  function dienDuLieu(d) {

    // Ảnh bìa
    datThuocTinh('mn-anh', 'src', d.hinhAnh || 'https://placehold.co/300x400/eff6ff/2563eb?text=📚');
    datThuocTinh('mn-anh', 'alt', d.tenSach);

    // Nhãn góc ảnh
    if (d.nhanLoai) {
      datVan('mn-nhan-loai', d.nhanLoai);
      hien('mn-nhan-loai');
    } else {
      an('mn-nhan-loai');
    }
    if (d.nhanGiam) {
      datVan('mn-nhan-giam', d.nhanGiam);
      hien('mn-nhan-giam');
    } else {
      an('mn-nhan-giam');
    }

    // Thể loại, tên sách, tác giả
    datVan('mn-the-loai',   d.theLoai);
    datVan('mn-ten-sach',   d.tenSach);
    datVan('mn-tac-gia-ten', d.tacGia);

    // Sao đánh giá
    var diem = parseFloat(d.diemTB) || 0;
    datSao(diem);
    datVan('mn-diem-tb', diem > 0 ? diem.toFixed(1) : '');

    // Số đánh giá
    var soReview = parseInt(d.soReview) || 0;
    if (soReview > 0) {
      datVan('mn-so-review', soReview + ' đánh giá');
      hien('mn-dg-sep-1');
    } else {
      datVan('mn-so-review', 'Chưa có đánh giá');
      an('mn-dg-sep-1');  // ẩn dấu · trước nếu không có điểm
    }

    // Tổng đã bán
    var tongBan = parseInt(d.tongBan) || 0;
    if (tongBan > 0) {
      var banHienThi = tongBan >= 1000
        ? (tongBan / 1000).toFixed(1).replace('.0', '') + 'k+'
        : tongBan + '+';
      datVan('mn-tong-ban', 'Đã bán ' + banHienThi);
      hien('mn-dg-sep-2');
    } else {
      datVan('mn-tong-ban', '');
      an('mn-dg-sep-2');
    }

    // Giá
    var giaHT  = parseFloat(d.giaHienTai) || 0;
    var giaBan = parseFloat(d.giaBan)     || 0;
    var giam   = parseInt(d.phanTramGiam) || 0;

    datVan('mn-gia-hien-tai', dinhDangGia(giaHT) + ' ₫');

    if (giam > 0 && giaBan > giaHT) {
      datVan('mn-gia-goc', dinhDangGia(giaBan) + ' ₫');
      hien('mn-gia-goc');
      datVan('mn-badge-giam', '-' + giam + '%');
      hien('mn-badge-giam');
    } else {
      an('mn-gia-goc');
      an('mn-badge-giam');
    }

    // Thông tin xuất bản
    datVan('mn-nxb',            d.nhaXuatBan  || 'Đang cập nhật');
    datVan('mn-nam-sx',         d.namSX       || 'Đang cập nhật');
    datVan('mn-hinh-thuc-bia',  d.hinhThucBia || 'Đang cập nhật');
    datVan('mn-ton-kho',        d.tonKho      || 'Đang cập nhật');

    // Mô tả sách
    var moTa   = d.moTa || 'Chưa có mô tả cho sách này.';
    var elMoTa = document.getElementById('mn-mo-ta');
    if (elMoTa) {
      elMoTa.textContent = moTa;
      elMoTa.classList.remove('mo-rong');
    }
    var nutXemThem = document.getElementById('mn-xem-them');
    if (nutXemThem) {
      nutXemThem.textContent = 'Xem thêm';
      nutXemThem.style.display = moTa.length > 160 ? '' : 'none';
    }

    // Reset số lượng
    soLuong = 1;
    datVanInput('mn-so-luong', 1);
  }

  // ── Cập nhật sao (chỉ đổi className, không innerHTML) ────────────────
  function datSao(diem) {
    var saos = document.querySelectorAll('#mn-sao i');
    saos.forEach(function (el, i) {
      if (i < Math.floor(diem)) {
        el.className = 'fas fa-star';            // sao đầy
      } else if (i < diem) {
        el.className = 'fas fa-star-half-alt';   // sao nửa
      } else {
        el.className = 'far fa-star';             // sao rỗng
      }
    });
  }

  // ── Số lượng ─────────────────────────────────────────────────────────
  var nutGiam = document.getElementById('mn-giam-sl');
  var nutTang = document.getElementById('mn-tang-sl');

  if (nutGiam) {
    nutGiam.addEventListener('click', function () {
      if (soLuong > 1) { soLuong--; datVanInput('mn-so-luong', soLuong); }
    });
  }
  if (nutTang) {
    nutTang.addEventListener('click', function () {
      soLuong++; datVanInput('mn-so-luong', soLuong);
    });
  }

  // ── Xem thêm / Thu gọn mô tả ─────────────────────────────────────────
  var nutXemThem = document.getElementById('mn-xem-them');
  if (nutXemThem) {
    nutXemThem.addEventListener('click', function () {
      var elMoTa = document.getElementById('mn-mo-ta');
      if (!elMoTa) return;
      var daMoRong = elMoTa.classList.toggle('mo-rong');
      nutXemThem.textContent = daMoRong ? 'Thu gọn' : 'Xem thêm';
    });
  }

  // ── Thêm vào giỏ hàng từ modal ───────────────────────────────────────
  var nutThemGio = document.getElementById('mn-them-vao-gio');
  if (nutThemGio) {
    nutThemGio.addEventListener('click', function () {
      if (!sachHienTai) return;

      // Kiểm tra đăng nhập bắt buộc
      if (typeof dangDangNhap === 'undefined' || !dangDangNhap) {
        alert('Bạn cần đăng nhập để thêm sách vào giỏ hàng!');
        dongModal();
        if (typeof openLogin === 'function') {
          openLogin();
        }
        return;
      }

      var thongTin = {
        maSach : sachHienTai.maSach,
        tenSach: sachHienTai.tenSach,
        giaBan : parseFloat(sachHienTai.giaHienTai),
        hinhAnh: sachHienTai.hinhAnh,
        tacGia : sachHienTai.tacGia,
      };

      if (typeof cartDrawer !== 'undefined') {
        cartDrawer.addItem(thongTin, soLuong);
        dongModal();
      } else {
        console.warn('[xemNhanhSach] cartDrawer chưa khởi tạo.');
      }
    });
  }

  // ── Yêu thích (toggle tim) ────────────────────────────────────────────
  var nutYeuThich = document.getElementById('mn-yeu-thich');
  if (nutYeuThich) {
    nutYeuThich.addEventListener('click', function () {
      // Toggle fill cho SVG heart (không innerHTML — dùng setAttribute)
      var svg = nutYeuThich.querySelector('svg');
      if (!svg) return;
      var daThich = nutYeuThich.classList.toggle('dang-thich');
      svg.setAttribute('fill', daThich ? '#ef4444' : 'none');
      svg.setAttribute('stroke', daThich ? '#ef4444' : 'currentColor');
      nutYeuThich.style.color = daThich ? '#ef4444' : '';
    });
  }

  // ── Chia sẻ (Web Share API) ───────────────────────────────────────────
  var nutChiaSe = document.getElementById('mn-chia-se');
  if (nutChiaSe) {
    nutChiaSe.addEventListener('click', function () {
      if (sachHienTai && navigator.share) {
        navigator.share({
          title: sachHienTai.tenSach,
          text : 'Xem sách: ' + sachHienTai.tenSach,
          url  : window.location.href,
        });
      }
    });
  }

  // ── Panel Đánh Giá (click số review → hiện iframe PHP) ──────────────────────
  // JS chỉ gán src iframe + toggle display — KHÔNG innerHTML, KHÔNG fetch/AJAX
  var nutSoReview  = document.getElementById('mn-so-review');
  var panelDanhGia = document.getElementById('mn-panel-danh-gia');
  var iframeDG     = document.getElementById('mn-iframe-danh-gia');
  var nutDongPanel = document.getElementById('mn-dong-panel');

  if (nutSoReview && panelDanhGia && iframeDG) {
    function moPanel() {
      if (!sachHienTai || !sachHienTai.maSach) return;
      // Gán src iframe → PHP layDanhGia.php render HTML danh sách review
      iframeDG.setAttribute(
        'src',
        'CuaHang/TrangBanHang/ChiTietSach/layDanhGia.php?maSach=' + encodeURIComponent(sachHienTai.maSach)
      );
      panelDanhGia.style.display = '';
    }

    nutSoReview.addEventListener('click', moPanel);
    nutSoReview.addEventListener('keydown', function(e) {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); moPanel(); }
    });

    // Nút đóng panel
    if (nutDongPanel) {
      nutDongPanel.addEventListener('click', function() {
        panelDanhGia.style.display = 'none';
        iframeDG.setAttribute('src', 'about:blank');
      });
    }

    // Khi đóng modal chính → đóng cả panel và reset iframe
    var _dongModalGoc = dongModal;
    dongModal = function() {
      panelDanhGia.style.display = 'none';
      iframeDG.setAttribute('src', 'about:blank');
      _dongModalGoc();
    };
  }

  // ── Helpers (không innerHTML) ───────────────────────────────────

  /** Gán textContent */
  function datVan(id, van) {
    var el = document.getElementById(id);
    if (el) el.textContent = van;
  }

  /** Gán value cho input */
  function datVanInput(id, val) {
    var el = document.getElementById(id);
    if (el) el.value = val;
  }

  /** setAttribute */
  function datThuocTinh(id, attr, val) {
    var el = document.getElementById(id);
    if (el) el.setAttribute(attr, val);
  }

  /** Hiện phần tử */
  function hien(id) {
    var el = document.getElementById(id);
    if (el) el.style.display = '';
  }

  /** Ẩn phần tử */
  function an(id) {
    var el = document.getElementById(id);
    if (el) el.style.display = 'none';
  }

  /** Format số tiền kiểu Việt Nam */
  function dinhDangGia(so) {
    return Number(so).toLocaleString('vi-VN');
  }

})();
