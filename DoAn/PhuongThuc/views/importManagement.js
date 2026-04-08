// ==================== IMPORT MANAGEMENT VIEW ====================

const importManagementView = {
  title: 'Quản lý nhập sách',
  
  imports: [
    { id: 'PN001', date: '20/01/2024', supplier: 'Công ty ABC', quantity: 500, total: '50,000,000đ', status: 'completed' },
    { id: 'PN002', date: '22/01/2024', supplier: 'Công ty XYZ', quantity: 300, total: '30,000,000đ', status: 'pending' },
    { id: 'PN003', date: '25/01/2024', supplier: 'Nhà xuất bản Alpha', quantity: 200, total: '25,000,000đ', status: 'processing' },
    { id: 'PN004', date: '28/01/2024', supplier: 'Công ty Books Co', quantity: 450, total: '45,000,000đ', status: 'completed' }
  ],
  
  suppliers: ['Công ty ABC', 'Công ty XYZ', 'Nhà xuất bản Alpha', 'Công ty Books Co'],
  
  render: function() {
    return `
      <div class="view-container">
        <div class="view-header">
          <div>
            <h2 class="view-title">Quản lý nhập sách</h2>
            <p class="view-subtitle">Theo dõi và quản lý các đơn nhập hàng</p>
          </div>
          <div class="view-actions">
            <button class="btn btn-primary" onclick="importManagementView.openAddModal()">
              <i class="fas fa-plus"></i> Thêm đơn nhập
            </button>
          </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-file-invoice"></i></div>
            <div class="stat-info">
              <h4>${this.imports.length}</h4>
              <p>Tổng đơn nhập</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
              <h4>${this.imports.filter(i => i.status === 'completed').length}</h4>
              <p>Hoàn thành</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
            <div class="stat-info">
              <h4>${this.imports.filter(i => i.status === 'pending').length}</h4>
              <p>Chờ xử lý</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-book"></i></div>
            <div class="stat-info">
              <h4>${this.imports.reduce((sum, i) => sum + i.quantity, 0)}</h4>
              <p>Tổng sách nhập</p>
            </div>
          </div>
        </div>

        <!-- Table -->
        <div class="view-card">
          <table class="data-table">
            <thead>
              <tr>
                <th>Mã phiếu</th>
                <th>Ngày nhập</th>
                <th>Nhà cung cấp</th>
                <th>Số lượng</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              ${this.imports.map(imp => `
                <tr>
                  <td><strong>#${imp.id}</strong></td>
                  <td>${imp.date}</td>
                  <td>${imp.supplier}</td>
                  <td>${imp.quantity}</td>
                  <td>${imp.total}</td>
                  <td>
                    <span class="status-badge ${imp.status}">
                      ${imp.status === 'completed' ? 'Hoàn thành' : 
                        imp.status === 'pending' ? 'Chờ xử lý' : 'Đang xử lý'}
                    </span>
                  </td>
                  <td>
                    <div class="action-buttons">
                      <button class="btn-icon" title="Xem chi tiết"><i class="fas fa-eye"></i></button>
                      <button class="btn-icon" title="Sửa"><i class="fas fa-edit"></i></button>
                      <button class="btn-icon btn-icon-danger" title="Xóa"><i class="fas fa-trash"></i></button>
                    </div>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
  },
  
  openAddModal: function() {
    toast.info('Chức năng thêm đơn nhập');
  }
};

// Export
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { importManagementView };
}

