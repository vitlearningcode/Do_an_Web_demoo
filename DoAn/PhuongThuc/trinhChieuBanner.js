/**
 * ============================================================
 * LUỒNG: SLIDESHOW BANNER HERO (trinhChieuBanner.js)
 *
 * GỌI BỞI: index.php sau khi render HTML banner
 *   <script src="...trinhChieuBanner.js"></script>
 *   <script>
 *     document.addEventListener('DOMContentLoaded', function() {
 *       new TrinhChieuBanner('hero-slider', { autoplay: 5000 });
 *     });
 *   </script>
 *
 * YÊU CẦU HTML (PHP render sẵn từ bannerSach.php / index.php):
 *   <div id="hero-slider">
 *     <div class="hero-slide active">...</div>
 *     <div class="hero-slide">...</div>
 *   </div>
 *   <button id="hero-prev">&#8592;</button>
 *   <button id="hero-next">&#8594;</button>
 *   <div id="hero-indicators"></div>  ← Sẽ được tạo động bởi JS
 *
 * LUỒNG HOẠT ĐỘNG:
 *   Khởi tạo → tìm slides → tạo indicators → bắt đầu autoplay
 *   Autoplay: setInterval → tiepTheo() mỗi N ms
 *   User nhấn nút/chấm → dừng + chuyển slide + khởi động lại autoplay
 *   Hover vào banner → dừng autoplay; hover ra → bắt đầu lại
 *
 * PATTERN: Class ES6 thuần (không dùng thư viện Swiper/Slick)
 *   Lý do: dự án thuần PHP/JS, không có bundler
 *   Private methods: đặt tên với _ prefix (quy ước, không phải #private thật)
 * ============================================================
 */
class TrinhChieuBanner {
    /**
     * @param {string} sliderId - ID của phần tử chứa các slide
     * @param {object} options  - Tuỳ chọn: { autoplay: 5000 } (ms)
     */
    constructor(sliderId, options = {}) {
        // Tìm phần tử slider bằng ID (do PHP render trong index.php)
        this.slider = document.getElementById(sliderId);
        if (!this.slider) {
            // Guard: banner có thể không có ở một số trang (trang admin, chi tiết sách...)
            console.warn(`TrinhChieuBanner: Không tìm thấy phần tử #${sliderId}`);
            return;
        }

        // Lấy tất cả slide con có class .hero-slide
        this.slides          = this.slider.querySelectorAll('.hero-slide');
        this.thuTuHienTai    = 0;                         // Index slide đang hiển thị
        this.tongSoSlide     = this.slides.length;
        this.thoiGianTuDong  = options.autoplay ?? 5000;  // ms giữa các lần chuyển (mặc định 5s)
        this.dangChay        = null;                       // ID của setInterval (dùng để clearInterval)

        if (this.tongSoSlide <= 0) {
            console.warn('TrinhChieuBanner: Không có slide nào!');
            return;
        }

        // Khởi tạo các thành phần UI
        this._khoiTaoNutDieuHuong(); // Gắn event cho nút ◀ ▶
        this._khoiTaoChamChiBao();   // Tạo động các chấm indicator
        this._batDauTuDong();        // Bắt đầu slideshow tự động

        // Tạm dừng khi user hover vào banner (tránh slide bị chuyển khi đang xem)
        // Khởi động lại khi hover ra
        const heroBanner = this.slider.closest('.hero-banner');
        if (heroBanner) {
            heroBanner.addEventListener('mouseenter', () => this._dungTuDong());
            heroBanner.addEventListener('mouseleave', () => this._batDauTuDong());
        }

        console.log(`✅ TrinhChieuBanner: Đã khởi tạo với ${this.tongSoSlide} slide.`);
    }

    /**
     * Chuyển tới slide theo chỉ số (vòng tròn: slide cuối → slide đầu)
     * @param {number} index - Chỉ số slide muốn chuyển tới
     */
    _chuyenDen(index) {
        // Bước 1: Bỏ class 'active' khỏi slide đang hiển thị
        // CSS dùng .hero-slide { display:none } và .hero-slide.active { display:block }
        this.slides[this.thuTuHienTai].classList.remove('active');

        // Bước 2: Tính chỉ số mới theo kiểu vòng tròn
        // Ví dụ: slide cuối (index=2), tongSo=3 → (2+1) % 3 = 0 (quay về đầu)
        // Ví dụ: slide đầu (index=0), nhấn prev → (0-1+3) % 3 = 2 (về cuối)
        this.thuTuHienTai = (index + this.tongSoSlide) % this.tongSoSlide;

        // Bước 3: Hiển thị slide mới bằng class 'active'
        this.slides[this.thuTuHienTai].classList.add('active');

        // Bước 4: Đồng bộ chấm chỉ báo (indicator) theo slide hiện tại
        this._capNhatChamChiBao();
    }

    /**
     * Chuyển sang slide tiếp theo (gọi bởi autoplay và nút ▶)
     */
    tiepTheo() {
        this._chuyenDen(this.thuTuHienTai + 1); // +1: chuyển tiếp; _chuyenDen xử lý vòng tròn
    }

    /**
     * Chuyển về slide trước (gọi bởi nút ◀)
     */
    truoc() {
        this._chuyenDen(this.thuTuHienTai - 1); // -1: về trước; _chuyenDen xử lý vòng tròn
    }

    /**
     * Khởi tạo nút điều hướng prev / next
     * IDs cố định: hero-prev, hero-next (PHP render trong bannerSach.php)
     *
     * PATTERN restart autoplay: khi user nhấn nút → dừng → chuyển → bắt đầu lại
     * Tránh tình huống: user nhấn nút, ngay sau đó autoplay cũng chuyển (double skip)
     */
    _khoiTaoNutDieuHuong() {
        const nutTruoc = document.getElementById('hero-prev');
        const nutTiep  = document.getElementById('hero-next');

        if (nutTruoc) {
            nutTruoc.addEventListener('click', () => {
                this.truoc();          // Chuyển slide về trước
                this._dungTuDong();    // Dừng timer cũ
                this._batDauTuDong();  // Đặt lại timer từ đầu (tránh double-skip)
            });
        }

        if (nutTiep) {
            nutTiep.addEventListener('click', () => {
                this.tiepTheo();       // Chuyển slide tiếp
                this._dungTuDong();
                this._batDauTuDong();
            });
        }
    }

    /**
     * Khởi tạo chấm chỉ báo (dot indicators) — TẠO ĐỘNG bằng JS
     * PHP không render chấm này (số lượng chấm = số slide, không hardcode)
     *
     * Mỗi chấm:
     *   - Là <button> (accessible: có thể focus bằng bàn phím)
     *   - aria-label="Slide N" (screen reader đọc được)
     *   - Click → chuyển tới slide tương ứng + restart autoplay
     */
    _khoiTaoChamChiBao() {
        const container = document.getElementById('hero-indicators');
        if (!container) return; // Trang không có indicator container → bỏ qua

        container.innerHTML = ''; // Xóa indicators cũ nếu có (tránh duplicate khi init lại)

        for (let i = 0; i < this.tongSoSlide; i++) {
            const cham = document.createElement('button');
            cham.classList.add('hero-indicator');
            if (i === 0) cham.classList.add('active'); // Slide đầu tiên active mặc định
            cham.setAttribute('aria-label', `Slide ${i + 1}`); // Accessibility

            // Closure: mỗi chấm giữ index riêng của nó
            cham.addEventListener('click', () => {
                this._chuyenDen(i);    // Nhảy đến slide i
                this._dungTuDong();    // Dừng timer cũ
                this._batDauTuDong(); // Khởi động lại timer
            });

            container.appendChild(cham);
        }
    }

    /**
     * Cập nhật trạng thái active của chấm chỉ báo
     * Gọi mỗi khi _chuyenDen() được thực hiện
     */
    _capNhatChamChiBao() {
        const container = document.getElementById('hero-indicators');
        if (!container) return;

        // Xóa active khỏi tất cả chấm, rồi thêm vào chấm tại vị trí hiện tại
        const cacs = container.querySelectorAll('.hero-indicator');
        cacs.forEach((c, i) => {
            // classList.toggle(class, condition): thêm nếu true, xóa nếu false
            c.classList.toggle('active', i === this.thuTuHienTai);
        });
    }

    /**
     * Bắt đầu tự động chuyển slide (setInterval)
     * Gọi trong constructor và sau mỗi lần user tương tác
     */
    _batDauTuDong() {
        if (this.thoiGianTuDong <= 0) return; // Tắt autoplay nếu = 0

        // setInterval: gọi tiepTheo() mỗi thoiGianTuDong ms
        // Lưu ID vào this.dangChay để có thể dừng bằng clearInterval
        this.dangChay = setInterval(() => this.tiepTheo(), this.thoiGianTuDong);
    }

    /**
     * Dừng tự động chuyển slide (clearInterval)
     * Gọi khi: user hover, user nhấn nút/chấm, hoặc trước khi restart
     */
    _dungTuDong() {
        if (this.dangChay) {
            clearInterval(this.dangChay); // Hủy timer đang chạy
            this.dangChay = null;         // Reset về null để tránh gọi clearInterval 2 lần
        }
    }
}
