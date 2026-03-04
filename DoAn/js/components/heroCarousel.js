// ==================== HERO CAROUSEL COMPONENT ====================

class HeroCarousel {
  constructor(containerId, options = {}) {
    this.container = document.getElementById(containerId);
    this.slides = [];
    this.currentIndex = 0;
    this.autoPlay = options.autoPlay !== false;
    this.autoPlayInterval = options.autoPlayInterval || 5000;
    this.intervalId = null;
    
    if (this.container) {
      this.init();
    }
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

// Export
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { HeroCarousel };
}

