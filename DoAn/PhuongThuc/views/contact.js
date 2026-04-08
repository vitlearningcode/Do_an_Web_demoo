// ==================== CONTACT VIEW ====================

const contactView = {
  title: 'Hỗ trợ & Liên hệ',
  
  contacts: [
    { id: 1, name: 'Nguyễn Văn A', email: 'a@gmail.com', subject: 'Hỏi về sách', message: 'Tôi muốn hỏi về...', date: '20/01/2024', status: 'unread' },
    { id: 2, name: 'Trần Thị B', email: 'b@gmail.com', subject: 'Góp ý', message: 'Cửa hàng nên...', date: '19/01/2024', status: 'replied' },
    { id: 3, name: 'Lê Văn C', email: 'c@gmail.com', subject: 'Khiếu nại', message: 'Đơn hàng của tôi...', date: '18/01/2024', status: 'unread' },
    { id: 4, name: 'Phạm Thị D', email: 'd@gmail.com', subject: 'Tư vấn', message: 'Tôi cần tìm sách...', date: '17/01/2024', status: 'replied' }
  ],
  
  render: function() {
    return `
      <div class="view-container">
        <div class="view-header">
          <div>
            <h2 class="view-title">Hỗ trợ & Liên hệ</h2>
            <p class="view-subtitle">Quản lý tin nhắn và phản hồi từ khách hàng</p>
          </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-envelope"></i></div>
            <div class="stat-info">
              <h4>${this.contacts.length}</h4>
              <p>Tổng tin nhắn</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-envelope-open"></i></div>
            <div class="stat-info">
              <h4>${this.contacts.filter(c => c.status === 'unread').length}</h4>
              <p>Chưa đọc</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
              <h4>${this.contacts.filter(c => c.status === 'replied').length}</h4>
              <p>Đã trả lời</p>
            </div>
          </div>
        </div>

        <!-- Filter -->
        <div class="view-card">
          <div class="search-filter-row">
            <div class="search-box">
              <i class="fas fa-search"></i>
              <input type="text" placeholder="Tìm theo tên, email...">
            </div>
            <select class="filter-select">
              <option>Tất cả trạng thái</option>
              <option>Chưa đọc</option>
              <option>Đã trả lời</option>
            </select>
          </div>
        </div>

        <!-- Contact List -->
        <div class="view-card">
          <table class="data-table">
            <thead>
              <tr>
                <th>STT</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Tiêu đề</th>
                <th>Nội dung</th>
                <th>Ngày gửi</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              ${this.contacts.map((contact, idx) => `
                <tr class="${contact.status === 'unread' ? 'unread-row' : ''}">
                  <td>${idx + 1}</td>
                  <td><strong>${contact.name}</strong></td>
                  <td>${contact.email}</td>
                  <td>${contact.subject}</td>
                  <td class="message-cell">${contact.message.substring(0, 50)}...</td>
                  <td>${contact.date}</td>
                  <td>
                    <span class="status-badge ${contact.status}">
                      ${contact.status === 'unread' ? 'Chưa đọc' : 'Đã trả lời'}
                    </span>
                  </td>
                  <td>
                    <div class="action-buttons">
                      <button class="btn-icon" title="Xem chi tiết" onclick="contactView.viewContact(${contact.id})">
                        <i class="fas fa-eye"></i>
                      </button>
                      <button class="btn-icon" title="Trả lời" onclick="contactView.replyContact(${contact.id})">
                        <i class="fas fa-reply"></i>
                      </button>
                      <button class="btn-icon btn-icon-danger" title="Xóa" onclick="contactView.deleteContact(${contact.id})">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>

        <!-- Contact Info -->
        <div class="view-card">
          <div class="card-header">
            <h3>Thông tin liên hệ</h3>
          </div>
          <div class="contact-info-grid">
            <div class="contact-info-item">
              <i class="fas fa-map-marker-alt"></i>
              <div>
                <h4>Địa chỉ</h4>
                <p>123 Đường ABC, Quận 1, TP.HCM</p>
              </div>
            </div>
            <div class="contact-info-item">
              <i class="fas fa-phone"></i>
              <div>
                <h4>Hotline</h4>
                <p>1900-xxxx</p>
              </div>
            </div>
            <div class="contact-info-item">
              <i class="fas fa-envelope"></i>
              <div>
                <h4>Email</h4>
                <p>contact@booksales.vn</p>
              </div>
            </div>
            <div class="contact-info-item">
              <i class="fas fa-clock"></i>
              <div>
                <h4>Giờ làm việc</h4>
                <p>8:00 - 22:00 (T2 - CN)</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
  },
  
  viewContact: function(id) {
    const contact = this.contacts.find(c => c.id === id);
    if (contact) {
      toast.info(`Xem tin nhắn từ ${contact.name}`);
    }
  },
  
  replyContact: function(id) {
    const contact = this.contacts.find(c => c.id === id);
    if (contact) {
      toast.success(`Đang trả lời ${contact.name}`);
    }
  },
  
  deleteContact: function(id) {
    if (confirm('Bạn có chắc chắn muốn xóa tin nhắn này?')) {
      this.contacts = this.contacts.filter(c => c.id !== id);
      // Re-render would be needed here
      toast.success('Xóa tin nhắn thành công');
    }
  }
};

// Export
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { contactView };
}

