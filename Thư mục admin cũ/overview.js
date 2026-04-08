// ==================== OVERVIEW VIEW ====================

const overviewView = {
  title: 'Tổng quan',
  chart: null,
  
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
                <h4>${escapeHtml(stat.value)}</h4>
                <p>${escapeHtml(stat.title)}</p>
                <span class="stat-change ${stat.isUp ? 'positive' : 'negative'}">
                  <i class="fas fa-arrow-${stat.isUp ? 'up' : 'down'}"></i>
                  ${escapeHtml(stat.change)}
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
            <div class="chart-container" style="height: 280px; position: relative;">
              <canvas id="revenueChart"></canvas>
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
                    <h4>${escapeHtml(item.name)}</h4>
                    <p>${escapeHtml(item.category)}</p>
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
                  <td>#${escapeHtml(order.id)}</td>
                  <td>${escapeHtml(order.customer)}</td>
                  <td>${escapeHtml(order.product)}</td>
                  <td>${escapeHtml(order.total)}</td>
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
  },
  
  // Initialize Chart.js after render
  initChart: () => {
    // Wait for DOM to be ready
    setTimeout(() => {
      const ctx = document.getElementById('revenueChart');
      if (!ctx) return;
      
      // Destroy existing chart if any
      if (overviewView.chart) {
        overviewView.chart.destroy();
      }
      
      const data = {
        labels: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'],
        datasets: [{
          label: 'Doanh thu',
          data: [4000000, 3000000, 2000000, 2780000, 1890000, 2390000, 3490000],
          fill: true,
          borderColor: '#3b82f6',
          backgroundColor: (context) => {
            const chart = context.chart;
            const {ctx, chartArea} = chart;
            if (!chartArea) return null;
            const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0)');
            gradient.addColorStop(1, 'rgba(59, 130, 246, 0.3)');
            return gradient;
          },
          borderWidth: 3,
          tension: 0.4,
          pointBackgroundColor: '#3b82f6',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 5,
          pointHoverRadius: 7
        }]
      };
      
      overviewView.chart = new Chart(ctx, {
        type: 'line',
        data: data,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          interaction: {
            intersect: false,
            mode: 'index'
          },
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              backgroundColor: '#1f2937',
              titleColor: '#fff',
              bodyColor: '#fff',
              padding: 12,
              cornerRadius: 12,
              displayColors: false,
              callbacks: {
                label: function(context) {
                  return new Intl.NumberFormat('vi-VN', { 
                    style: 'currency', 
                    currency: 'VND' 
                  }).format(context.raw);
                }
              }
            }
          },
          scales: {
            x: {
              grid: {
                display: false
              },
              ticks: {
                color: '#64748b',
                font: {
                  size: 12
                }
              }
            },
            y: {
              grid: {
                color: '#f1f5f9'
              },
              ticks: {
                color: '#64748b',
                font: {
                  size: 12
                },
                callback: function(value) {
                  if (value >= 1000000) {
                    return (value / 1000000).toFixed(1) + 'M';
                  }
                  return value.toLocaleString();
                }
              }
            }
          }
        }
      });
    }, 100);
  }
};

// Export
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { overviewView };
}

