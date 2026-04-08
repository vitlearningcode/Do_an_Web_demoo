// ==================== SALES MANAGEMENT VIEW ====================

const salesManagementView = {
  title: 'Quản lý bán hàng',
  
  sales: [
    { id: 'DH001', customer: 'Nguyễn Văn A', product: 'Đắc Nhân Tâm', quantity: 2, total: '172,000đ', date: '20/01/2024', status: 'completed' },
    { id: 'DH002', customer: 'Trần Thị B', product: 'Nhà Giả Kim', quantity: 1, total: '79,000đ', date: '20/01/2024', status: 'processing' },
    { id: 'DH003', customer: 'Lê Văn C', product: 'Atomic Habits', quantity: 1, total: '145,000đ', date: '19/01/2024', status: 'pending' },
    { id: 'DH004', customer: 'Phạm Thị D', product: 'Tư Duy Nhanh Và Chậm', quantity: 2, total: '370,000đ', date: '19/01/2024', status: 'completed' },
    { id: 'DH005', customer: 'Vũ Văn E', product: 'Sapiens', quantity: 1, total: '250,000đ', date: '18/01/2024', status: 'cancelled' }
  ],
  
  render: function() {
    return `
      <div class="view-container">
        <div class="view-header">
          <div>
            <h2 class="view-title">Quản lý bán hàng</h2>
            <p class="view-subtitle">Theo dõi và quản lý đơn hàng</p>
          </div>
          <div class="view-actions">
            <button class="btn btn-outline">
              <i class="fas fa-download"></i> Xuất Excel
            </button>
          </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-shopping-cart"></i></div>
            <div class="stat-info">
              <h4>1,234</h4>
              <p>Tổng đơn hàng</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
              <h4>1,089</h4>
              <p>Hoàn thành</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-spinner"></i></div>
            <div class="stat-info">
              <h4>98</h4>
              <p>Đang xử lý</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-times-circle"></i></div>
            <div class="stat-info">
              <h4>47</h4>
              <p>Đã hủy</p>
            </div>
          </div>
        </div>

        <!-- Filter -->
        <div class="view-card">
          <div class="search-filter-row">
            <div class="search-box">
              <i class="fas fa-search"></i>
              <input type="text" placeholder="Tìm theo mã đơn, tên khách hàng...">
            </div>
            <select class="filter-select">
              <option>Tất cả trạng thái</option>
              <option>Hoàn thành</option>
              <option>Đang xử lý</option>
              <option>Chờ thanh toán</option>
              <option>Đã hủy</option>
            </select>
            <select class="filter-select">
              <option>Hôm nay</option>
              <option>7 ngày qua</option>
              <option>Tháng này</option>
            </select>
          </div>
        </div>

        <!-- Table -->
        <div class="view-card">
          <table class="data-table">
            <thead>
              <tr>
                <th>Mã đơn</th>
                <th>Khách hàng</th>
                <th>Sản phẩm</th>
                <th>Số lượng</th>
                <th>Tổng tiền</th>
                <th>Ngày đặt</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              ${this.sales.map(sale => `
                <tr>
                  <td><strong>#${sale.id}</strong></td>
                  <td>${sale.customer}</td>
                  <td>${sale.product}</td>
                  <td>${sale.quantity}</td>
                  <td><strong>${sale.total}</strong></td>
                  <td>${sale.date}</td>
                  <td>
                    <span class="status-badge ${sale.status}">
                      ${sale.status === 'completed' ? 'Hoàn thành' : 
                        sale.status === 'processing' ? 'Đang xử lý' : 
                        sale.status === 'pending' ? 'Chờ thanh toán' : 'Đã hủy'}
                    </span>
                  </td>
                  <td>
                    <div class="action-buttons">
                      <button class="btn-icon" title="Xem chi tiết"><i class="fas fa-eye"></i></button>
                      <button class="btn-icon" title="Sửa"><i class="fas fa-edit"></i></button>
                    </div>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
          
          <div class="table-pagination">
            <p class="pagination-info">Hiển thị <span>1-5</span> trong <span>1,234</span> kết quả</p>
            <div class="pagination-buttons">
              <button class="btn btn-sm" disabled>Trước</button>
              <button class="btn btn-sm btn-primary">1</button>
              <button class="btn btn-sm">2</button>
              <button class="btn btn-sm">3</button>
              <button class="btn btn-sm">...</button>
              <button class="btn btn-sm">247</button>
              <button class="btn btn-sm">Sau</button>
            </div>
          </div>
        </div>
      </div>
    `;
  }
};

// Export
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { salesManagementView };
}

