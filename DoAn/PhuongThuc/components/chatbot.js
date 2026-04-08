// ==================== CHATBOT COMPONENT ====================

class Chatbot {
  constructor(options = {}) {
    this.isOpen = false;
    this.messages = options.messages || [];
    this.onMessage = options.onMessage || (() => {});
    this.isTyping = false;
    
    this.elements = {
      container: null,
      toggle: null,
      messagesContainer: null,
      input: null,
      sendBtn: null
    };
    
    this.responses = [
      { keywords: ['xin chào', 'hello', 'hi', 'chào'], response: 'Xin chào! Rất vui được hỗ trợ bạn. Bạn cần tìm sách gì hôm nay?' },
      { keywords: ['tìm sách', 'tìm', 'search', 'kiếm'], response: 'Bạn có thể tìm kiếm sách theo tên, tác giả hoặc thể loại. Ví dụ: "sách kinh tế" hoặc "Dale Carnegie"' },
      { keywords: ['giỏ hàng', 'cart', 'xem giỏ'], response: 'Để xem giỏ hàng, bạn nhấn vào biểu tượng giỏ hàng ở góc trên bên phải nhé!' },
      { keywords: ['khuyến mãi', 'sale', 'giảm giá', 'ưu đãi'], response: 'Chúng tôi đang có chương trình Flash Sale với giảm giá lên đến 50%! Hãy nhanh tay đặt hàng ngay.' },
      { keywords: ['đăng nhập', 'login', 'đăng ký'], response: 'Bạn có thể đăng nhập bằng cách nhấn vào nút "Đăng nhập" ở góc trên bên phải. Đăng nhập để lưu giỏ hàng và theo dõi đơn hàng dễ dàng hơn!' },
      { keywords: ['liên hệ', 'hỗ trợ', 'contact', 'hotline'], response: 'Bạn có thể liên hệ với chúng tôi qua hotline 1900-xxxx hoặc email support@booksales.vn' },
      { keywords: ['vận chuyển', 'ship', 'delivery', 'giao hàng'], response: 'Chúng tôi miễn phí vận chuyển cho đơn hàng từ 250.000đ. Thời gian giao hàng từ 2-5 ngày tùy khu vực.' },
      { keywords: ['đổi trả', 'return', 'hoàn tiền'], response: 'Chúng tôi hỗ trợ đổi trả sản phẩm trong vòng 7 ngày nếu sách có lỗi hoặc không đúng mô tả.' },
      { keywords: ['sách mới', 'mới nhất', 'update'], response: 'Chúng tôi cập nhật sách mới liên tục! Bạn có thể xem mục "Sách Mới Phát Hành" trên trang chủ.' },
      { keywords: ['doanh thu', 'thống kê', 'báo cáo'], response: 'Để xem doanh thu và báo cáo, bạn vui lòng chuyển sang giao diện Admin và truy cập mục "Quản lý doanh thu" nhé!' }
    ];
    
    this.init();
  }

  init() {
    this.elements.container = document.getElementById('chatbot');
    this.elements.toggle = document.getElementById('chatbot-toggle');
    this.elements.messagesContainer = document.getElementById('chatbot-messages');
    this.elements.input = document.getElementById('chatbot-input');
    this.elements.sendBtn = document.getElementById('chatbot-send');
    
    // Create toggle button if not exists
    if (!this.elements.toggle) {
      this.createToggleButton();
    }
    
    // Create chat window if not exists
    if (!this.elements.container) {
      this.createChatWindow();
    }
    
    this.bindEvents();
  }

  createToggleButton() {
    const toggle = document.createElement('div');
    toggle.id = 'chatbot-toggle';
    toggle.className = 'chatbot-toggle';
    toggle.innerHTML = '<i class="fas fa-comments"></i>';
    document.body.appendChild(toggle);
    this.elements.toggle = toggle;
  }

  createChatWindow() {
    const chatWindow = document.createElement('div');
    chatWindow.id = 'chatbot';
    chatWindow.className = 'chatbot';
    chatWindow.innerHTML = `
      <div class="chatbot-header">
        <div class="chatbot-header-info">
          <i class="fas fa-robot"></i>
          <div>
            <h4>Trợ lý AI</h4>
            <p>Luôn sẵn sàng hỗ trợ</p>
          </div>
        </div>
        <button id="chatbot-close"><i class="fas fa-times"></i></button>
      </div>
      <div class="chatbot-messages" id="chatbot-messages">
        <div class="chatbot-message bot">
          <div class="message-avatar bot"><i class="fas fa-robot"></i></div>
          <div class="message-content">
            <p>Xin chào! Tôi là trợ lý AI của Book Sales Management. Tôi có thể giúp gì cho bạn hôm nay?</p>
          </div>
        </div>
      </div>
      <div class="typing-indicator" id="typing-indicator" style="display: none;">
        <div class="typing-dot"></div>
        <div class="typing-dot"></div>
        <div class="typing-dot"></div>
      </div>
      <div class="chatbot-input">
        <input type="text" id="chatbot-input" placeholder="Nhập câu hỏi của bạn...">
        <button id="chatbot-send"><i class="fas fa-paper-plane"></i></button>
      </div>
    `;
    document.body.appendChild(chatWindow);
    
    // Add CSS for enhanced chatbot
    this.addEnhancedStyles();
    
    this.elements.container = chatWindow;
    this.elements.messagesContainer = chatWindow.querySelector('#chatbot-messages');
  }

  addEnhancedStyles() {
    const style = document.createElement('style');
    style.textContent = `
      .chatbot-toggle {
        position: fixed;
        bottom: 24px;
        right: 24px;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        cursor: pointer;
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.4);
        z-index: 1000;
        transition: all 0.3s ease;
        animation: pulse-chat 2s infinite;
      }
      
      @keyframes pulse-chat {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
      }
      
      .chatbot-toggle:hover {
        transform: scale(1.1);
        box-shadow: 0 15px 35px rgba(37, 99, 235, 0.5);
      }
      
      .chatbot {
        position: fixed;
        bottom: 100px;
        right: 24px;
        width: 400px;
        height: 600px;
        max-height: calc(100vh - 120px);
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        display: none;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.05);
      }
      
      .chatbot.active {
        display: flex;
        animation: slideUp 0.3s ease;
      }
      
      @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
      }
      
      .chatbot-header {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
      }
      
      .chatbot-header-info {
        display: flex;
        align-items: center;
        gap: 12px;
      }
      
      .chatbot-header-info i {
        font-size: 28px;
      }
      
      .chatbot-header-info h4 {
        font-weight: 700;
        font-size: 16px;
      }
      
      .chatbot-header-info p {
        font-size: 12px;
        opacity: 0.8;
      }
      
      .chatbot-header button {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
      }
      
      .chatbot-header button:hover {
        background: rgba(255, 255, 255, 0.3);
      }
      
      .chatbot-messages {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 16px;
        background: #f8fafc;
      }
      
      .chatbot-message {
        display: flex;
        gap: 12px;
        max-width: 85%;
        animation: fadeIn 0.3s ease;
      }
      
      @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
      }
      
      .chatbot-message.user {
        flex-direction: row-reverse;
        align-self: flex-end;
      }
      
      .message-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
      }
      
      .message-avatar.bot {
        background: white;
        border: 2px solid #e5e7eb;
        color: #2563eb;
      }
      
      .message-avatar.user {
        background: #2563eb;
        color: white;
      }
      
      .message-content {
        padding: 14px 18px;
        border-radius: 18px;
        font-size: 14px;
        line-height: 1.5;
      }
      
      .chatbot-message.bot .message-content {
        background: white;
        border: 1px solid #e5e7eb;
        color: #1f2937;
        border-bottom-left-radius: 4px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      }
      
      .chatbot-message.user .message-content {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
        border-bottom-right-radius: 4px;
      }
      
      .typing-indicator {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 12px 18px;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        border-bottom-left-radius: 4px;
        margin-left: 48px;
        margin-bottom: 8px;
      }
      
      .typing-dot {
        width: 8px;
        height: 8px;
        background: #2563eb;
        border-radius: 50%;
        animation: typing 1.4s infinite;
      }
      
      .typing-dot:nth-child(2) { animation-delay: 0.2s; }
      .typing-dot:nth-child(3) { animation-delay: 0.4s; }
      
      @keyframes typing {
        0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
        30% { transform: translateY(-4px); opacity: 1; }
      }
      
      .chatbot-input {
        padding: 16px 20px;
        display: flex;
        gap: 12px;
        background: white;
        border-top: 1px solid #e5e7eb;
      }
      
      .chatbot-input input {
        flex: 1;
        padding: 14px 20px;
        border: 2px solid #e5e7eb;
        border-radius: 25px;
        outline: none;
        font-size: 14px;
        transition: all 0.3s;
      }
      
      .chatbot-input input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
      }
      
      .chatbot-input button {
        background: #2563eb;
        color: white;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
      }
      
      .chatbot-input button:hover {
        background: #1d4ed8;
        transform: scale(1.05);
      }
      
      @media (max-width: 480px) {
        .chatbot {
          width: 100%;
          height: 100%;
          max-height: 100%;
          bottom: 0;
          right: 0;
          border-radius: 0;
        }
        
        .chatbot-toggle {
          bottom: 16px;
          right: 16px;
        }
      }
    `;
    document.head.appendChild(style);
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
    this.elements.toggle?.style.display = 'none';
    this.elements.input?.focus();
  }

  close() {
    this.isOpen = false;
    this.elements.container?.classList.remove('active');
    this.elements.toggle?.style.display = 'flex';
  }

  sendMessage() {
    const message = this.elements.input?.value.trim();
    if (!message || this.isTyping) return;

    // Add user message
    this.addMessage(message, 'user');
    this.elements.input.value = '';

    // Show typing indicator
    this.showTyping();

    // Simulate bot response with delay
    setTimeout(() => {
      this.hideTyping();
      const response = this.getResponse(message);
      this.addMessage(response, 'bot');
    }, 1000 + Math.random() * 1000);
  }

  showTyping() {
    this.isTyping = true;
    let indicator = document.getElementById('typing-indicator');
    if (!indicator) {
      indicator = document.createElement('div');
      indicator.id = 'typing-indicator';
      indicator.className = 'typing-indicator';
      indicator.innerHTML = `
        <div class="message-avatar bot"><i class="fas fa-robot"></i></div>
        <div class="typing-dots">
          <div class="typing-dot"></div>
          <div class="typing-dot"></div>
          <div class="typing-dot"></div>
        </div>
      `;
      this.elements.messagesContainer?.appendChild(indicator);
    }
    indicator.style.display = 'flex';
    this.elements.messagesContainer.scrollTop = this.elements.messagesContainer.scrollHeight;
  }

  hideTyping() {
    this.isTyping = false;
    const indicator = document.getElementById('typing-indicator');
    if (indicator) {
      indicator.style.display = 'none';
    }
  }

  addMessage(text, sender) {
    if (!this.elements.messagesContainer) return;
    
    // Remove typing indicator if exists
    const indicator = document.getElementById('typing-indicator');
    if (indicator) {
      indicator.style.display = 'none';
    }
    
    const messageEl = document.createElement('div');
    messageEl.className = `chatbot-message ${sender}`;
    messageEl.innerHTML = `
      <div class="message-avatar ${sender}">
        <i class="fas fa-${sender === 'user' ? 'user' : 'robot'}"></i>
      </div>
      <div class="message-content">
        <p>${text}</p>
      </div>
    `;
    
    this.elements.messagesContainer.appendChild(messageEl);
    this.elements.messagesContainer.scrollTop = this.elements.messagesContainer.scrollHeight;
    
    this.messages.push({ text, sender, timestamp: new Date() });
  }

  getResponse(userMessage) {
    const lowerMessage = userMessage.toLowerCase();
    
    // Find matching response
    for (const item of this.responses) {
      for (const keyword of item.keywords) {
        if (lowerMessage.includes(keyword)) {
          return item.response;
        }
      }
    }
    
    // Default random response
    const defaults = [
      'Cảm ơn bạn đã liên hệ! Bạn có thể hỏi về sách, đơn hàng, hoặc các chương trình khuyến mãi nhé!',
      'Tôi có thể giúp bạn tìm sách theo tên, tác giả hoặc thể loại. Bạn cần tìm gì?',
      'Nếu bạn cần hỗ trợ về đơn hàng, vui lòng liên hệ hotline 1900-xxxx để được hỗ trợ nhanh nhất!',
      'Chúng tôi có rất nhiều sách mới cập nhật mỗi tuần. Bạn có muốn tôi giới thiệu một số sách hay không?'
    ];
    
    return defaults[Math.floor(Math.random() * defaults.length)];
  }

  clearMessages() {
    if (!this.elements.messagesContainer) return;
    
    this.elements.messagesContainer.innerHTML = `
      <div class="chatbot-message bot">
        <div class="message-avatar bot"><i class="fas fa-robot"></i></div>
        <div class="message-content">
          <p>Xin chào! Tôi là trợ lý AI của Book Sales Management. Tôi có thể giúp gì cho bạn hôm nay?</p>
        </div>
      </div>
    `;
    this.messages = [];
  }
}

// Create global instance
const chatbot = new Chatbot();

// // Export
// if (typeof module !== 'undefined' && module.exports) {
//   module.exports = { Chatbot, chatbot };
// }

