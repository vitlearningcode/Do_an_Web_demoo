// ==================== IMAGE GENERATOR MODAL COMPONENT ====================

class ImageGeneratorModal {
  constructor(options = {}) {
    this.isOpen = false;
    this.bookName = '';
    this.onImageSelect = options.onImageSelect || (() => {});
    this.generatedImages = [];
    this.isGenerating = false;
    
    this.elements = {
      modal: null,
      bookTitle: null,
      promptInput: null,
      generateBtn: null,
      imagesContainer: null,
      loadingIndicator: null
    };
    
    // Sample AI-generated images (in real app, these would come from an AI service)
    this.sampleImages = [
      'https://picsum.photos/seed/ai1/300/400',
      'https://picsum.photos/seed/ai2/300/400',
      'https://picsum.photos/seed/ai3/300/400',
      'https://picsum.photos/seed/ai4/300/400',
      'https://picsum.photos/seed/ai5/300/400',
      'https://picsum.photos/seed/ai6/300/400'
    ];
    
    this.init();
  }

  init() {
    // Create modal HTML if not exists
    if (!document.getElementById('image-generator-modal')) {
      this.createModalHTML();
    }
    
    this.elements.modal = document.getElementById('image-generator-modal');
    this.elements.bookTitle = document.getElementById('ai-book-title');
    this.elements.promptInput = document.getElementById('ai-prompt');
    this.elements.generateBtn = document.getElementById('generate-btn');
    this.elements.imagesContainer = document.getElementById('generated-images');
    this.elements.loadingIndicator = document.getElementById('ai-loading');
    
    this.bindEvents();
  }

  createModalHTML() {
    const modalHTML = `
      <div class="modal-overlay" id="image-generator-modal">
        <div class="modal image-generator-modal">
          <button class="modal-close" id="ai-modal-close"><i class="fas fa-times"></i></button>
          <h2><i class="fas fa-robot"></i> Tạo ảnh bìa sách AI</h2>
          <p class="modal-subtitle" id="ai-book-title">Tạo ảnh bìa cho: </p>
          
          <div class="ai-prompt-section">
            <label>Mô tả bìa sách</label>
            <textarea id="ai-prompt" placeholder="Ví dụ: Cuốn sách về kinh doanh, bìa màu xanh dương, phong cách hiện đại..."></textarea>
          </div>
          
          <button class="submit-btn" id="generate-btn">
            <i class="fas fa-magic"></i> Tạo ảnh
          </button>
          
          <div class="ai-loading" id="ai-loading" style="display: none;">
            <div class="spinner"></div>
            <p>Đang tạo ảnh...</p>
          </div>
          
          <div class="generated-images" id="generated-images"></div>
        </div>
      </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Add CSS for this modal
    const style = document.createElement('style');
    style.textContent = `
      .image-generator-modal {
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
      }
      .modal-subtitle {
        color: var(--gray-500);
        margin-bottom: 20px;
        font-size: 14px;
      }
      .ai-prompt-section {
        margin-bottom: 20px;
      }
      .ai-prompt-section label {
        display: block;
        font-weight: 500;
        margin-bottom: 8px;
        color: var(--gray-700);
      }
      .ai-prompt-section textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--gray-300);
        border-radius: 8px;
        resize: vertical;
        min-height: 80px;
        font-family: inherit;
      }
      .ai-prompt-section textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
      }
      .generated-images {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-top: 20px;
      }
      .generated-images .ai-image {
        position: relative;
        aspect-ratio: 3/4;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        border: 3px solid transparent;
        transition: all 0.2s;
      }
      .generated-images .ai-image:hover {
        border-color: var(--primary);
        transform: scale(1.02);
      }
      .generated-images .ai-image.selected {
        border-color: var(--success);
      }
      .generated-images .ai-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
      .generated-images .ai-image .select-icon {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 24px;
        height: 24px;
        background: var(--success);
        color: white;
        border-radius: 50%;
        display: none;
        align-items: center;
        justify-content: center;
      }
      .generated-images .ai-image.selected .select-icon {
        display: flex;
      }
      .ai-loading {
        text-align: center;
        padding: 40px;
      }
      .ai-loading .spinner {
        width: 40px;
        height: 40px;
        border: 3px solid var(--gray-200);
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 16px;
      }
      @keyframes spin {
        to { transform: rotate(360deg); }
      }
    `;
    document.head.appendChild(style);
  }

  bindEvents() {
    const closeBtn = document.getElementById('ai-modal-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', () => this.close());
    }
    
    if (this.elements.modal) {
      this.elements.modal.addEventListener('click', (e) => {
        if (e.target === this.elements.modal) this.close();
      });
    }
    
    if (this.elements.generateBtn) {
      this.elements.generateBtn.addEventListener('click', () => this.generateImages());
    }
  }

  open(bookName) {
    this.bookName = bookName;
    this.generatedImages = [];
    
    if (this.elements.bookTitle) {
      this.elements.bookTitle.textContent = `Tạo ảnh bìa cho: ${bookName}`;
    }
    
    if (this.elements.promptInput) {
      this.elements.promptInput.value = '';
    }
    
    if (this.elements.imagesContainer) {
      this.elements.imagesContainer.innerHTML = '';
    }
    
    this.isOpen = true;
    this.elements.modal?.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  close() {
    this.isOpen = false;
    this.elements.modal?.classList.remove('active');
    document.body.style.overflow = '';
  }

  async generateImages() {
    if (this.isGenerating) return;
    
    this.isGenerating = true;
    this.elements.generateBtn.disabled = true;
    this.elements.generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tạo...';
    this.elements.loadingIndicator.style.display = 'block';
    this.elements.imagesContainer.innerHTML = '';
    
    // Simulate AI generation delay
    await new Promise(resolve => setTimeout(resolve, 2000));
    
    // Shuffle and select 6 random images
    const shuffled = [...this.sampleImages].sort(() => Math.random() - 0.5);
    this.generatedImages = shuffled.slice(0, 6);
    
    this.renderImages();
    
    this.isGenerating = false;
    this.elements.generateBtn.disabled = false;
    this.elements.generateBtn.innerHTML = '<i class="fas fa-magic"></i> Tạo ảnh';
    this.elements.loadingIndicator.style.display = 'none';
  }

  renderImages() {
    if (!this.elements.imagesContainer) return;
    
    this.elements.imagesContainer.innerHTML = this.generatedImages.map((img, idx) => `
      <div class="ai-image" data-index="${idx}" onclick="imageGeneratorModal.selectImage(${idx})">
        <img src="${img}" alt="Generated ${idx + 1}" referrerPolicy="no-referrer">
        <div class="select-icon"><i class="fas fa-check"></i></div>
      </div>
    `).join('');
  }

  selectImage(index) {
    // Remove previous selection
    document.querySelectorAll('.generated-images .ai-image').forEach(el => {
      el.classList.remove('selected');
    });
    
    // Add selection to clicked image
    const selectedEl = document.querySelector(`.generated-images .ai-image[data-index="${index}"]`);
    if (selectedEl) {
      selectedEl.classList.add('selected');
    }
    
    const selectedImage = this.generatedImages[index];
    this.onImageSelect(selectedImage);
    toast.success('Đã chọn ảnh bìa');
    setTimeout(() => this.close(), 500);
  }
}

// Create global instance
const imageGeneratorModal = new ImageGeneratorModal({
  onImageSelect: (imageUrl) => {
    console.log('Selected image:', imageUrl);
    // This would be handled by the parent component
    window.selectedBookImage = imageUrl;
  }
});

// Export
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { ImageGeneratorModal, imageGeneratorModal };
}

