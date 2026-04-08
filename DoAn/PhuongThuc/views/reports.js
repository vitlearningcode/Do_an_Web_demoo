// ==================== REPORTS VIEW ====================

const reportsView = {
  title: 'Báo cáo',
  
  render: function() {
    return `
      <div class="view-container">
        <div class="view-header">
          <div>
            <h2 class="view-title">Báo cáo tổng hợp</h2>
            <p class="view-subtitle">Xem và xuất các báo cáo của hệ thống</p>
          </div>
          <div class="view-actions">
            <button class="btn btn-outline">
              <i class="fas fa-download"></i> Xuất PDF
            </button>
            <button class="btn btn-outline">
              <i class="fas fa-file-excel"></i> Xuất Excel
            </button>
          </div>
        </div>

        <!-- Report Types -->
        <div class="reports-grid">
          <div class="report-card" onclick="reportsView.selectReport('revenue')">
            <div class="report-icon blue">
              <i class="fas fa-chart-line"></i>
            </div>
            <h3>Báo cáo doanh thu</h3>
            <p>Doanh thu theo ngày, tuần, tháng</p>
            <span class="report-arrow"><i class="fas fa-arrow-right"></i></span>
          </div>
          
          <div class="report-card" onclick="reportsView.selectReport('sales')">
            <div class="report-icon green">
              <i class="fas fa-shopping-cart"></i>
            </div>
            <h3>Báo cáo bán hàng</h3>
            <p>Thống kê đơn hàng và sản phẩm</p>
            <span class="report-arrow"><i class="fas fa-arrow-right"></i></span>
          </div>
          
          <div class="report-card" onclick="reportsView.selectReport('inventory')">
            <div class="report-icon orange">
              <i class="fas fa-boxes"></i>
            </div>
            <h3>Báo cáo tồn kho</h3>
            <p>Tình trạng kho hàng hiện tại</p>
            <span class="report-arrow"><i class="fas fa-arrow-right"></i></span>
          </div>
          
          <div class="report-card" onclick="reportsView.selectReport('customer')">
            <div class="report-icon purple">
              <i class="fas fa-users"></i>
            </div>
            <h3>Báo cáo khách hàng</h3>
            <p>Thống kê và phân tích khách hàng</p>
            <span class="report-arrow"><i class="fas fa-arrow-right"></i></span>
          </div>
        </div>

        <!-- Quick Stats -->
        <div class="view-card">
          <div class="card-header">
            <h3>Tổng quan nhanh</h3>
            <select class="view-select">
              <option>Tháng này</option>
              <option>Tháng trước</option>
              <option>Quý này</option>
            </select>
          </div>
          
          <div class="quick-stats">
            <div class="quick-stat">
              <span class="quick-stat-label">Tổng doanh thu</span>
              <span class="quick-stat-value">890,500,000 ₫</span>
              <span class="quick-stat-change positive">+18.2%</span>
            </div>
            <div class="quick-stat">
              <span class="quick-stat-label">Tổng đơn hàng</span>
              <span class="quick-stat-value">1,234</span>
              <span class="quick-stat-change positive">+5.4%</span>
            </div>
            <div class="quick-stat">
              <span class="quick-stat-label">Khách hàng mới</span>
              <span class="quick-stat-value">856</span>
              <span class="quick-stat-change positive">+15.3%</span>
            </div>
            <div class="quick-stat">
              <span class="quick-stat-label">Sách đã bán</span>
              <span class="quick-stat-value">3,456</span>
              <span class="quick-stat-change negative">-2.1%</span>
            </div>
          </div>
        </div>
      </div>
    `;
  },
  
  selectReport: function(type) {
    toast.info(`Đang tải báo cáo ${type}...`);
  }
};

// Export
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { reportsView };
}

