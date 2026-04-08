// ==================== HERO CAROUSEL COMPONENT ====================

// No import - use global heroSlides from data.js
class HeroCarousel {
  constructor(containerId, options = {}) {
    this.container = document.getElementById(containerId);
    this.heroSlidesData = window.heroSlides;
    this.slides = [];
    this.currentIndex = 0;
    this.autoPlay = options.autoPlay !== false;
    this.autoPlayInterval = options.autoPlayInterval || 5000;
    this.intervalId = null;
    
    if (this.container) {
      this.populateSlides();
      this.init();
    }
  }

  populateSlides() {
    this.container.innerHTML = '';
    this.heroSlidesData.forEach((slideData, index) => {
      const slide = document.createElement('div');
      slide.className = `hero-slide ${slideData.gradient} ${index === 0 ? 'active' : ''}`;
      slide.innerHTML = `
        <div class="hero-slide-bg">
          <img src="${slideData.image}" alt="Banner" loading="lazy">
          <div class="gradient-overlay"></div>
        </div>
        <div class="hero-content">
          <span class="hero-badge">${slideData.badge}</span>
          <h2>${slideData.title}</h2>
          <p>${slideData.description}</p>
          <button class="hero-btn">${slideData.buttonText} <i class="fas fa-arrow-right"></i></button>
        </div>
      `;
      this.container.appendChild(slide);
    });
  }

  init() {
    this.slides = Array.from(this.container.querySelectorAll('.hero-slide'));
    this.createIndicators();
    this.bindEvents();
    
    if (this.autoPlay) {
      this.startAutoPlay();
    }
  }

  createIndicators() {
    const indicatorsContainer = document.getElementById('hero-indicators');
    if (!indicatorsContainer) return;
    
    indicatorsContainer.innerHTML = '';
    
    this.slides.forEach((_, index) => {
      const indicator = document.createElement('div');
      indicator.className = `hero-indicator ${index === 0 ? 'active' : ''}`;
      indicator.addEventListener('click', () => this.goToSlide(index));
      indicatorsContainer.appendChild(indicator);
    });
  }

  bindEvents() {
    const prevBtn = document.getElementById('hero-prev');
    const nextBtn = document.getElementById('hero-next');
    
    if (prevBtn) {
      prevBtn.addEventListener('click', () => this.prev());
    }
    
    if (nextBtn) {
      nextBtn.addEventListener('click', () => this.next());
    }
    
    // Pause on hover
    if (this.container) {
      this.container.addEventListener('mouseenter', () => this.stopAutoPlay());
      this.container.addEventListener('mouseleave', () => this.startAutoPlay());
    }
  }

  goToSlide(index) {
    this.slides.forEach((slide, i) => {
      slide.classList.toggle('active', i === index);
    });
    
    const indicators = document.querySelectorAll('.hero-indicator');
    indicators.forEach((indicator, i) => {
      indicator.classList.toggle('active', i === index);
    });
    
    this.currentIndex = index;
  }

  next() {
    const nextIndex = (this.currentIndex + 1) % this.slides.length;
    this.goToSlide(nextIndex);
  }

  prev() {
    const prevIndex = (this.currentIndex - 1 + this.slides.length) % this.slides.length;
    this.goToSlide(prevIndex);
  }

  startAutoPlay() {
    this.stopAutoPlay();
    this.intervalId = setInterval(() => this.next(), this.autoPlayInterval);
  }

  stopAutoPlay() {
    if (this.intervalId) {
      clearInterval(this.intervalId);
      this.intervalId = null;
    }
  }

  destroy() {
    this.stopAutoPlay();
  }
}

// Global init function
function initHeroCarousel() {
  return new HeroCarousel('hero-slider');
}

// Export for app.js
window.initHeroCarousel = initHeroCarousel;

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { HeroCarousel, initHeroCarousel };
}


