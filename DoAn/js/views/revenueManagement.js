// ==================== REVENUE MANAGEMENT VIEW ====================

const revenueManagementView = {
  title: 'Quản lý doanh thu',
  
  revenueData: [
    { date: '20/01/2024', orders: 45, revenue: '4,500,000đ', profit: '1,350,000đ' },
    { date: '19/01/2024', orders: 52, revenue: '5,200,000đ', profit: '1,560,000đ' },
    { date: '18/01/2024', orders: 38, revenue: '3,800,000đ', profit: '1,140,000đ' },
    { date: '17/01/2024', orders: 41, revenue: '4,100,000đ', profit: '1,230,000đ' },
    { date: '16/01/2024', orders: 55, revenue: '5,500,000đ', profit: '1,650,000đ' }
  ],
  
  render: function() {
    return `
      <div class="view-container">
        <div class="view-header">
          <div>
            <h2 class="view-title">Quản lý doanh thu</h2>
            <p class="view-subtitle">Theo dõi doanh thu và lợi nhuận</p>
          </div>
          <div class="view-actions">
            <select class="view-select">
              <option>Hôm nay</option>
              <option>7 ngày qua</option>
              <option>Tháng này</option>
              <option>Quý này</option>
            </select>
          </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-info">
              <h4>890.5M</h4>
              <p>Doanh thu tháng này</p>
              <span class="stat-change positive"><i class="fas fa-arrow-up"></i> +18.2%</span>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-chart-line"></i></div>
            <div class="stat-info">
              <h4>1.2M</h4>
              <p>Trung bình/ngày</p>
              <span class="stat-change positive"><i class="fas fa-arrow-up"></i> +5.4%</span>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-percentage"></i></div>
            <div class="stat-info">
              <h4>30%</h4>
              <p>Tỷ lệ lợi nhuận</p>
              <span class="stat-change positive"><i class="fas fa-arrow-up"></i> +2%</span>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-users"></i></div>
            <div class="stat-info">
              <h4>1,245</h4>
              <p>Khách hàng mua</p>
              <span class="stat-change negative"><i class="fas fa-arrow-down"></i> -1.2%</span>
            </div>
          </div>
        </div>

        <!-- Chart -->
        <div class="view-card">
          <div class="card-header">
            <h3>Doanh thu theo ngày</h3>
          </div>
          <div class="chart-placeholder">
            <div class="chart-bars">
              ${[65, 45, 80, 55, 70, 90, 75].map(height => `
                <div class="chart-bar" style="height: ${height}%">
                  <span class="bar-tooltip">${height * 15000}K</span>
                </div>
              `).join('')}
            </div>
            <div class="chart-labels">
              <span>T2</span><span>T3</span><span>T4</span><span>T5</span><span>T6</span><span>T7</span><span>CN</span>
            </div>
          </div>
        </div>

        <!-- Table -->
        <div class="view-card">
          <table class="data-table">
            <thead>
              <tr>
                <th>Ngày</th>
                <th>Số đơn</th>
                <th>Doanh thu</th>
                <th>Lợi nhuận</th>
              </tr>
            </thead>
            <tbody>
              ${this.revenueData.map(row => `
                <tr>
                  <td>${row.date}</td>
                  <td>${row.orders}</td>
                  <td><strong>${row.revenue}</strong></td>
                  <td class="text-success">${row.profit}</td>
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
  module.exports = { revenueManagementView };
}

