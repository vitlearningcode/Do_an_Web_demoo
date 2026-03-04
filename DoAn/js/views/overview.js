// ==================== OVERVIEW VIEW ====================

const overviewView = {
  title: 'Tổng quan',
  
  render: () => {
    const statsData = [
      { title: 'Doanh thu', value: '12,450,000 ₫', change: '+15.2%', isUp: true, icon: 'fa-money-bill-wave', color: 'blue' },
      { title: 'Đơn hàng mới', value: '142', change: '+5.4%', isUp: true, icon: 'fa-shopping-bag', color: 'emerald' },
      { title: 'Khách hàng', value: '1,245', change: '-1.2%', isUp: false, icon: 'fa-users', color: 'purple' },
      { title: 'Cảnh báo tồn kho', value: '24', change: '+12', isUp: false, icon: 'fa-exclamation-triangle', color: 'orange' }
    ];

    const lowStockData = [
      { name: 'Nghĩ Giàu Làm Giàu', stock: 8, min: 10, category: 'Kinh tế' },
      { name: 'Nhà Giả Kim', stock: 12, min: 20, category: 'Văn học' },
      { name: 'Tâm Lý Học Tội Phạm', stock: 5, min: 15, category: 'Tâm lý học' },
      { name: 'Sapiens: Lược Sử Loài Người', stock: 15, min: 20, category: 'Khoa học' },
      { name: 'Muôn Kiếp Nhân Sinh', stock: 9, min: 15, category: 'Tôn giáo' }
    ];

    const recentOrders = [
      { id: 'DH001', customer: 'Nguyễn Văn A', product: 'Đắc Nhân Tâm', total: '86,000đ', status: 'completed' },
      { id: 'DH002', customer: 'Trần Thị B', product: 'Nhà Giả Kim', total: '79,000đ', status: 'processing' },
      { id: 'DH003', customer: 'Lê Văn C', product: 'Atomic Habits', total: '145,000đ', status: 'pending' },
      { id: 'DH004', customer: 'Phạm Thị D', product: 'Tư Duy Nhanh Và Chậm', total: '185,000đ', status: 'completed' }
    ];

    return `
      <div class="view-container">
        <!-- Header -->
        <div class="view-header">
          <div>
            <h2 class="view-title">Tổng quan hệ thống</h2>
            <p class="view-subtitle">Theo dõi các chỉ số quan trọng trong ngày</p>
          </div>
          <div class="view-actions">
            <select class="view-select">
              <option>Hôm nay</option>
              <option>7 ngày qua</option>
              <option>Tháng này</option>
            </select>
          </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
          ${statsData.map(stat => `
            <div class="stat-card">
              <div class="stat-icon ${stat.color}">
                <i class="fas ${stat.icon}"></i>
              </div>
              <div class="stat-info">
                <h4>${stat.value}</h4>
                <p>${stat.title}</p>
                <span class="stat-change ${stat.isUp ? 'positive' : 'negative'}">
                  <i class="fas fa-arrow-${stat.isUp ? 'up' : 'down'}"></i>
                  ${stat.change}
                </span>
              </div>
            </div>
          `).join('')}
        </div>

        <div class="view-grid">
          <!-- Revenue Chart -->
          <div class="view-card chart-card">
            <div class="card-header">
              <h3>Biểu đồ doanh thu</h3>
              <button class="card-action">Xem chi tiết</button>
            </div>
            <div class="chart-placeholder">
              <div class="chart-bars">
                ${[40, 65, 45, 80, 55, 70, 90].map(height => `
                  <div class="chart-bar" style="height: ${height}%">
                    <span class="bar-tooltip">${height * 150000}₫</span>
                  </div>
                `).join('')}
              </div>
              <div class="chart-labels">
                <span>T2</span><span>T3</span><span>T4</span><span>T5</span><span>T6</span><span>T7</span><span>CN</span>
              </div>
            </div>
          </div>

          <!-- Low Stock -->
          <div class="view-card">
            <div class="card-header">
              <h3>Sắp hết hàng</h3>
              <button class="card-action-icon">
                <i class="fas fa-box"></i>
              </button>
            </div>
            <div class="stock-list">
              ${lowStockData.map(item => `
                <div class="stock-item">
                  <div class="stock-indicator ${item.stock <= 5 ? 'danger' : 'warning'}"></div>
                  <div class="stock-info">
                    <h4>${item.name}</h4>
                    <p>${item.category}</p>
                  </div>
                  <div class="stock-count">
                    <span class="${item.stock <= 5 ? 'text-danger' : 'text-warning'}">${item.stock}</span>
                    <span class="text-muted">/ ${item.min}</span>
                  </div>
                </div>
              `).join('')}
            </div>
            <button class="create-order-btn">
              <i class="fas fa-plus"></i> Tạo đơn nhập hàng
            </button>
          </div>
        </div>

        <!-- Recent Orders -->
        <div class="view-card">
          <div class="card-header">
            <h3>Đơn hàng gần đây</h3>
            <a href="#" class="view-all-link">Xem tất cả</a>
          </div>
          <table class="data-table">
            <thead>
              <tr>
                <th>Mã đơn</th>
                <th>Khách hàng</th>
                <th>Sản phẩm</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
              </tr>
            </thead>
            <tbody>
              ${recentOrders.map(order => `
                <tr>
                  <td>#${order.id}</td>
                  <td>${order.customer}</td>
                  <td>${order.product}</td>
                  <td>${order.total}</td>
                  <td>
                    <span class="status-badge ${order.status}">
                      ${order.status === 'completed' ? 'Hoàn thành' : 
                        order.status === 'processing' ? 'Đang xử lý' : 'Chờ thanh toán'}
                    </span>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
  }
};

// Export
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { overviewView };
}

