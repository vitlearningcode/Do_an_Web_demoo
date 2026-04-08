// ==================== INVENTORY MANAGEMENT VIEW ====================

const inventoryManagementView = {
  title: 'Quản lý kho',
  
  inventory: [
    { id: '1', name: 'Đắc Nhân Tâm', stock: 120, sold: 80, remaining: 40, status: 'in-stock' },
    { id: '2', name: 'Nhà Giả Kim', stock: 45, sold: 30, remaining: 15, status: 'low-stock' },
    { id: '3', name: 'Nghĩ Giàu Làm Giàu', stock: 8, sold: 12, remaining: -4, status: 'out-of-stock' },
    { id: '4', name: 'Tuổi Trẻ Đáng Giá Bao Nhiêu', stock: 210, sold: 90, remaining: 120, status: 'in-stock' },
    { id: '5', name: 'Sapiens: Lược Sử Loài Người', stock: 32, sold: 18, remaining: 14, status: 'low-stock' }
  ],
  
  render: function() {
    return `
      <div class="view-container">
        <div class="view-header">
          <div>
            <h2 class="view-title">Quản lý kho</h2>
            <p class="view-subtitle">Theo dõi tồn kho và cảnh báo hết hàng</p>
          </div>
          <div class="view-actions">
            <button class="btn btn-outline">
              <i class="fas fa-download"></i> Xuất Excel
            </button>
            <button class="btn btn-primary">
              <i class="fas fa-plus"></i> Nhập kho
            </button>
          </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-book"></i></div>
            <div class="stat-info">
              <h4>3,456</h4>
              <p>Tổng sách tồn kho</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
              <h4>2,890</h4>
              <p>Còn hàng</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="stat-info">
              <h4>24</h4>
              <p>Sắp hết hàng</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-times-circle"></i></div>
            <div class="stat-info">
              <h4>5</h4>
              <p>Đã hết hàng</p>
            </div>
          </div>
        </div>

        <!-- Filter -->
        <div class="view-card">
          <div class="search-filter-row">
            <div class="search-box">
              <i class="fas fa-search"></i>
              <input type="text" placeholder="Tìm theo tên sách, mã sách...">
            </div>
            <select class="filter-select">
              <option>Tất cả trạng thái</option>
              <option>Còn hàng</option>
              <option>Sắp hết hàng</option>
              <option>Đã hết hàng</option>
            </select>
            <select class="filter-select">
              <option>Tất cả danh mục</option>
              <option>Văn học</option>
              <option>Kinh tế</option>
              <option>Kỹ năng sống</option>
            </select>
          </div>
        </div>

        <!-- Table -->
        <div class="view-card">
          <table class="data-table">
            <thead>
              <tr>
                <th>Mã sách</th>
                <th>Tên sách</th>
                <th>Tổng nhập</th>
                <th>Đã bán</th>
                <th>Còn lại</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              ${this.inventory.map(item => `
                <tr>
                  <td><strong>#${item.id}</strong></td>
                  <td>${item.name}</td>
                  <td>${item.stock}</td>
                  <td>${item.sold}</td>
                  <td class="${item.remaining < 0 ? 'text-danger' : item.remaining < 10 ? 'text-warning' : ''}">
                    ${item.remaining}
                  </td>
                  <td>
                    <span class="status-badge ${item.status}">
                      ${item.status === 'in-stock' ? 'Còn hàng' : 
                        item.status === 'low-stock' ? 'Sắp hết' : 'Hết hàng'}
                    </span>
                  </td>
                  <td>
                    <div class="action-buttons">
                      <button class="btn-icon" title="Nhập thêm"><i class="fas fa-plus"></i></button>
                      <button class="btn-icon" title="Xem chi tiết"><i class="fas fa-eye"></i></button>
                    </div>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
          
          <div class="table-pagination">
            <p class="pagination-info">Hiển thị <span>1-5</span> trong <span>124</span> kết quả</p>
            <div class="pagination-buttons">
              <button class="btn btn-sm" disabled>Trước</button>
              <button class="btn btn-sm btn-primary">1</button>
              <button class="btn btn-sm">2</button>
              <button class="btn btn-sm">3</button>
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
  module.exports = { inventoryManagementView };
}

