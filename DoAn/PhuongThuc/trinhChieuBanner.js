/**
 * TrinhChieuBanner - Điều khiển slideshow banner hero
 * Hỗ trợ: tự động chuyển slide, nút prev/next, chấm chỉ báo
 */
class TrinhChieuBanner {
    constructor(sliderId, options = {}) {
        this.slider = document.getElementById(sliderId);
        if (!this.slider) {
            console.warn(`TrinhChieuBanner: Không tìm thấy phần tử #${sliderId}`);
            return;
        }

        this.slides = this.slider.querySelectorAll('.hero-slide');
        this.thuTuHienTai = 0;
        this.tongSoSlide = this.slides.length;
        this.thoiGianTuDong = options.autoplay ?? 5000; // ms
        this.dangChay = null;

        if (this.tongSoSlide <= 0) {
            console.warn('TrinhChieuBanner: Không có slide nào!');
            return;
        }

        this._khoiTaoNutDieuHuong();
        this._khoiTaoChamChiBao();
        this._batDauTuDong();

        // Dừng auto khi hover vào banner
        const heroBanner = this.slider.closest('.hero-banner');
        if (heroBanner) {
            heroBanner.addEventListener('mouseenter', () => this._dungTuDong());
            heroBanner.addEventListener('mouseleave', () => this._batDauTuDong());
        }

        console.log(`✅ TrinhChieuBanner: Đã khởi tạo với ${this.tongSoSlide} slide.`);
    }

    /**
     * Chuyển tới slide theo chỉ số
     */
    _chuyenDen(index) {
        // Xóa class active khỏi slide hiện tại
        this.slides[this.thuTuHienTai].classList.remove('active');

        // Cập nhật chỉ số (vòng tròn)
        this.thuTuHienTai = (index + this.tongSoSlide) % this.tongSoSlide;

        // Thêm class active cho slide mới
        this.slides[this.thuTuHienTai].classList.add('active');

        // Cập nhật chấm chỉ báo
        this._capNhatChamChiBao();
    }

    /**
     * Slide tiếp theo
     */
    tiepTheo() {
        this._chuyenDen(this.thuTuHienTai + 1);
    }

    /**
     * Slide trước
     */
    truoc() {
        this._chuyenDen(this.thuTuHienTai - 1);
    }

    /**
     * Khởi tạo nút prev / next
     */
    _khoiTaoNutDieuHuong() {
        const nutTruoc = document.getElementById('hero-prev');
        const nutTiep = document.getElementById('hero-next');

        if (nutTruoc) {
            nutTruoc.addEventListener('click', () => {
                this.truoc();
                this._dungTuDong();
                this._batDauTuDong();
            });
        }

        if (nutTiep) {
            nutTiep.addEventListener('click', () => {
                this.tiepTheo();
                this._dungTuDong();
                this._batDauTuDong();
            });
        }
    }

    /**
     * Khởi tạo chấm chỉ báo (indicators)
     */
    _khoiTaoChamChiBao() {
        const container = document.getElementById('hero-indicators');
        if (!container) return;

        container.innerHTML = '';

        for (let i = 0; i < this.tongSoSlide; i++) {
            const cham = document.createElement('button');
            cham.classList.add('hero-indicator');
            if (i === 0) cham.classList.add('active');
            cham.setAttribute('aria-label', `Slide ${i + 1}`);

            cham.addEventListener('click', () => {
                this._chuyenDen(i);
                this._dungTuDong();
                this._batDauTuDong();
            });

            container.appendChild(cham);
        }
    }

    /**
     * Cập nhật chấm chỉ báo theo slide hiện tại
     */
    _capNhatChamChiBao() {
        const container = document.getElementById('hero-indicators');
        if (!container) return;

        const cacs = container.querySelectorAll('.hero-indicator');
        cacs.forEach((c, i) => {
            c.classList.toggle('active', i === this.thuTuHienTai);
        });
    }

    /**
     * Bắt đầu tự động chuyển slide
     */
    _batDauTuDong() {
        if (this.thoiGianTuDong <= 0) return;
        this.dangChay = setInterval(() => this.tiepTheo(), this.thoiGianTuDong);
    }

    /**
     * Dừng tự động chuyển slide
     */
    _dungTuDong() {
        if (this.dangChay) {
            clearInterval(this.dangChay);
            this.dangChay = null;
        }
    }
}
