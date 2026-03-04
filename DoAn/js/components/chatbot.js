// ==================== CHATBOT COMPONENT ====================

class Chatbot {
  constructor(options = {}) {
    this.isOpen = false;
    this.messages = options.messages || [];
    this.onMessage = options.onMessage || (() => {});
    
    this.elements = {
      container: null,
      toggle: null,
      messagesContainer: null,
      input: null,
      sendBtn: null
    };
    
    this.responses = [
      'Cảm ơn bạn đã liên hệ! Tôi có thể giúp gì cho bạn?',
      'Bạn có thể tìm kiếm sách theo tên, tác giả hoặc thể loại.',
      'Để xem giỏ hàng, hãy nhấn vào biểu tượng giỏ hàng ở góc trên.',
      'Chúng tôi có chương trình khuyến mãi đặc biệt vào cuối tuần này!',
      'Bạn cần hỗ trợ về đơn hàng nào? Tôi sẽ giúp bạn kiểm tra.',
      'Nếu bạn cần tìm sách theo thể loại, hãy vào mục "Danh mục sách" nhé!',
      'Chúng tôi có rất nhiều sách mới cập nhật liên tục, bạn có muốn tôi giới thiệu không?',
      'Đăng nhập để lưu lại giỏ hàng và theo dõi đơn hàng dễ dàng hơn!'
    ];
    
    this.init();
  }

  init() {
    this.elements.container = document.getElementById('chatbot');
    this.elements.toggle = document.getElementById('chatbot-toggle');
    this.elements.messagesContainer = document.getElementById('chatbot-messages');
    this.elements.input = document.getElementById('chatbot-input');
    this.elements.sendBtn = document.getElementById('chatbot-send');
    
    this.bindEvents();
  }

  bindEvents() {
    if (this.elements.toggle) {
      this.elements.toggle.addEventListener('click', () => this.toggle());
    }
    
    const closeBtn = document.getElementById('chatbot-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', () => this.close());
    }
    
    if (this.elements.sendBtn) {
      this.elements.sendBtn.addEventListener('click', () => this.sendMessage());
    }
    
    if (this.elements.input) {
      this.elements.input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') this.sendMessage();
      });
    }
  }

  toggle() {
    if (this.isOpen) {
      this.close();
    } else {
      this.open();
    }
  }

  open() {
    this.isOpen = true;
    this.elements.container?.classList.add('active');
  }

  close() {
    this.isOpen = false;
    this.elements.container?.classList.remove('active');
  }

  sendMessage() {
    const message = this.elements.input?.value.trim();
    if (!message) return;

    // Add user message
    this.addMessage(message, 'user');
    this.elements.input.value = '';

    // Simulate bot response
    setTimeout(() => {
      const response = this.getResponse(message);
      this.addMessage(response, 'bot');
    }, 500 + Math.random() * 1000);
  }

  addMessage(text, sender) {
    if (!this.elements.messagesContainer) return;
    
    const messageEl = document.createElement('div');
    messageEl.className = `chatbot-message ${sender}`;
    messageEl.innerHTML = `<p>${text}</p>`;
    
    this.elements.messagesContainer.appendChild(messageEl);
    this.elements.messagesContainer.scrollTop = this.elements.messagesContainer.scrollHeight;
    
    this.messages.push({ text, sender, timestamp: new Date() });
  }

  getResponse(userMessage) {
    const lowerMessage = userMessage.toLowerCase();
    
    // Simple keyword matching
    if (lowerMessage.includes('xin chào') || lowerMessage.includes('hello') || lowerMessage.includes('hi')) {
      return 'Xin chào! Rất vui được hỗ trợ bạn. Bạn cần tìm sách gì hôm nay?';
    }
    
    if (lowerMessage.includes('tìm sách') || lowerMessage.includes('tìm') || lowerMessage.includes('search')) {
      return 'Bạn có thể tìm kiếm sách theo tên, tác giả hoặc thể loại. Ví dụ: "sách kinh tế" hoặc "Dale Carnegie"';
    }
    
    if (lowerMessage.includes('giỏ hàng') || lowerMessage.includes('cart')) {
      return 'Để xem giỏ hàng, bạn nhấn vào biểu tượng giỏ hàng ở góc trên bên phải nhé!';
    }
    
    if (lowerMessage.includes('khuyến mãi') || lowerMessage.includes('sale') || lowerMessage.includes('giảm giá')) {
      return 'Chúng tôi đang có chương trình Flash Sale với giảm giá lên đến 50%! Hãy nhanh tay đặt hàng ngay.';
    }
    
    if (lowerMessage.includes('đăng nhập') || lowerMessage.includes('login')) {
      return 'Bạn có thể đăng nhập bằng cách nhấn vào nút "Đăng nhập" ở góc trên bên phải. Đăng nhập để lưu giỏ hàng và theo dõi đơn hàng dễ dàng hơn!';
    }
    
    if (lowerMessage.includes('liên hệ') || lowerMessage.includes('hỗ trợ') || lowerMessage.includes('contact')) {
      return 'Bạn có thể liên hệ với chúng tôi qua hotline 1900-xxxx hoặc email support@booksales.vn';
    }
    
    if (lowerMessage.includes('vận chuyển') || lowerMessage.includes('ship') || lowerMessage.includes('delivery')) {
      return 'Chúng tôi miễn phí vận chuyển cho đơn hàng từ 250.000đ. Thời gian giao hàng từ 2-5 ngày tùy khu vực.';
    }
    
    if (lowerMessage.includes('đổi trả') || lowerMessage.includes('return')) {
      return 'Chúng tôi hỗ trợ đổi trả sản phẩm trong vòng 7 ngày nếu sách có lỗi hoặc không đúng mô tả.';
    }
    
    // Default random response
    return this.responses[Math.floor(Math.random() * this.responses.length)];
  }

  clearMessages() {
    if (!this.elements.messagesContainer) return;
    
    this.elements.messagesContainer.innerHTML = `
      <div class="chatbot-message bot">
        <p>Xin chào! Tôi có thể giúp gì cho bạn?</p>
      </div>
    `;
    this.messages = [];
  }
}

// Create global instance
const chatbot = new Chatbot();

// Export
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { Chatbot, chatbot };
}

