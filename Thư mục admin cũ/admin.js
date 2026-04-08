// ==================== ADMIN VIEWS ====================

let currentAdminView = 'overview';

// Admin view templates
const adminViews = {
  overview: `
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-shopping-bag"></i></div>
        <div class="stat-info">
          <h4>1,234</h4>
          <p>Đơn hàng hôm nay</p>
          <span class="stat-change positive">+12.5%</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-money-bill-wave"></i></div>
        <div class="stat-info">
          <h4>125.5M</h4>
          <p>Doanh thu hôm nay</p>
          <span class="stat-change positive">+8.2%</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-users"></i></div>
        <div class="stat-info">
          <h4>856</h4>
          <p>Khách hàng mới</p>
          <span class="stat-change positive">+15.3%</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-book"></i></div>
        <div class="stat-info">
          <h4>3,456</h4>
          <p>Sản phẩm tồn kho</p>
          <span class="stat-change negative">-2.1%</span>
        </div>
      </div>
    </div>
    <div class="admin-card">
      <div class="admin-card-header">
        <h3>Đơn hàng gần đây</h3>
        <a href="#" class="view-all-btn">Xem tất cả</a>
      </div>
      <table class="admin-table">
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
          <tr>
            <td>#DH001</td>
            <td>Nguyễn Văn A</td>
            <td>Đắc Nhân Tâm</td>
            <td>86,000đ</td>
            <td><span class="status-badge success">Hoàn thành</span></td>
          </tr>
          <tr>
            <td>#DH002</td>
            <td>Trần Thị B</td>
            <td>Nhà Giả Kim</td>
            <td>79,000đ</td>
            <td><span class="status-badge warning">Đang xử lý</span></td>
          </tr>
          <tr>
            <td>#DH003</td>
            <td>Lê Văn C</td>
            <td>Atomic Habits</td>
            <td>145,000đ</td>
            <td><span class="status-badge danger">Chờ thanh toán</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  `,
  
  import: `
    <div class="admin-card">
      <div class="admin-card-header">
        <h3>Quản lý nhập hàng</h3>
        <button class="submit-btn" style="width: auto; padding: 10px 20px;">+ Thêm đơn nhập</button>
      </div>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Mã phiếu</th>
            <th>Ngày nhập</th>
            <th>Nhà cung cấp</th>
            <th>Số lượng</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>#PN001</td>
            <td>20/01/2024</td>
            <td>Công ty ABC</td>
            <td>500</td>
            <td>50,000,000đ</td>
            <td><span class="status-badge success">Hoàn thành</span></td>
          </tr>
          <tr>
            <td>#PN002</td>
            <td>22/01/2024</td>
            <td>Công ty XYZ</td>
            <td>300</td>
            <td>30,000,000đ</td>
            <td><span class="status-badge warning">Đang chờ</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  `,
  
  'book-info': `
    <div class="admin-card">
      <div class="admin-card-header">
        <h3>Quản lý thông tin sách</h3>
        <button class="submit-btn" style="width: auto; padding: 10px 20px;">+ Thêm sách mới</button>
      </div>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Mã sách</th>
            <th>Tên sách</th>
            <th>Tác giả</th>
            <th>Thể loại</th>
            <th>Giá bán</th>
            <th>Tồn kho</th>
            <th>Thao tác</th>
          </tr>
        </thead>
        <tbody>
          ${featuredBooks.map(book => `
            <tr>
              <td>${book.id}</td>
              <td>${book.name}</td>
              <td>${book.author}</td>
              <td>${book.category}</td>
              <td>${formatPrice(book.price)}</td>
              <td>${Math.floor(Math.random() * 100) + 10}</td>
              <td>
                <button style="color: var(--primary); background: none; margin-right: 8px;"><i class="fas fa-edit"></i></button>
                <button style="color: var(--danger); background: none;"><i class="fas fa-trash"></i></button>
              </td>
            </tr>
          `).join('')}
        </tbody>
      </table>
    </div>
  `,
  
  revenue: `
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-calendar-check"></i></div>
        <div class="stat-info">
          <h4>890.5M</h4>
          <p>Doanh thu tháng này</p>
          <span class="stat-change positive">+18.2%</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-chart-line"></i></div>
        <div class="stat-info">
          <h4>1.2M</h4>
          <p>Trung bình/ngày</p>
          <span class="stat-change positive">+5.4%</span>
        </div>
      </div>
    </div>
    <div class="admin-card">
      <div class="admin-card-header">
        <h3>Doanh thu theo ngày</h3>
      </div>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Ngày</th>
            <th>Số đơn</th>
            <th>Doanh thu</th>
            <th>Lợi nhuận</th>
          </tr>
        </thead>
        <tbody>
          <tr><td>20/01/2024</td><td>45</td><td>4,500,000đ</td><td>1,350,000đ</td></tr>
          <tr><td>19/01/2024</td><td>52</td><td>5,200,000đ</td><td>1,560,000đ</td></tr>
          <tr><td>18/01/2024</td><td>38</td><td>3,800,000đ</td><td>1,140,000đ</td></tr>
        </tbody>
      </table>
    </div>
  `,
  
  sales: `
    <div class="admin-card">
      <div class="admin-card-header">
        <h3>Quản lý bán hàng</h3>
      </div>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Mã đơn</th>
            <th>Khách hàng</th>
            <th>Sản phẩm</th>
            <th>Số lượng</th>
            <th>Tổng tiền</th>
            <th>Ngày đặt</th>
            <th>Trạng thái</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>#DH001</td>
            <td>Nguyễn Văn A</td>
            <td>Đắc Nhân Tâm</td>
            <td>2</td>
            <td>172,000đ</td>
            <td>20/01/2024</td>
            <td><span class="status-badge success">Hoàn thành</span></td>
          </tr>
          <tr>
            <td>#DH002</td>
            <td>Trần Thị B</td>
            <td>Nhà Giả Kim</td>
            <td>1</td>
            <td>79,000đ</td>
            <td>20/01/2024</td>
            <td><span class="status-badge warning">Đng xử lý</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  `,
  
  inventory: `
    <div class="admin-card">
      <div class="admin-card-header">
        <h3>Quản lý tồn kho</h3>
      </div>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Mã sách</th>
            <th>Tên sách</th>
            <th>Tồn kho</th>
            <th>Đã bán</th>
            <th>Còn lại</th>
            <th>Trạng thái</th>
          </tr>
        </thead>
        <tbody>
          ${featuredBooks.slice(0, 5).map(book => {
            const sold = Math.floor(Math.random() * 50);
            const stock = Math.floor(Math.random() * 100) + 20;
            return `
              <tr>
                <td>${book.id}</td>
                <td>${book.name}</td>
                <td>${stock}</td>
                <td>${sold}</td>
                <td>${stock - sold}</td>
                <td><span class="status-badge ${stock - sold < 20 ? 'danger' : 'success'}">${stock - sold < 20 ? 'Sắp hết' : 'Còn hàng'}</span></td>
              </tr>
            `;
          }).join('')}
        </tbody>
      </table>
    </div>
  `,
  
  reports: `
    <div class="admin-card">
      <div class="admin-card-header">
        <h3>Báo cáo tổng hợp</h3>
      </div>
      <div style="padding: 40px; text-align: center; color: var(--gray-500);">
        <i class="fas fa-chart-bar" style="font-size: 48px; margin-bottom: 16px;"></i>
        <p>Biểu đồ và báo cáo chi tiết sẽ được hiển thị tại đây</p>
      </div>
    </div>
  `,
  
  settings: `
    <div class="admin-card">
      <div class="admin-card-header">
        <h3>Cài đặt hệ thống</h3>
      </div>
      <div class="form-group">
        <label>Tên cửa hàng</label>
        <input type="text" value="Book Sales Store">
      </div>
      <div class="form-group">
        <label>Email liên hệ</label>
        <input type="email" value="contact@booksales.vn">
      </div>
      <div class="form-group">
        <label>Điện thoại</label>
        <input type="tel" value="1900 xxxx">
      </div>
      <div class="form-group">
        <label>Địa chỉ</label>
        <input type="text" value="123 Đường ABC, Quận 1, TP.HCM">
      </div>
      <button class="submit-btn">Lưu thay đổi</button>
    </div>
  `,
  
  contact: `
    <div class="admin-card">
      <div class="admin-card-header">
        <h3>Quản lý liên hệ</h3>
      </div>
      <table class="admin-table">
        <thead>
          <tr>
            <th>STT</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Tiêu đề</th>
            <th>Nội dung</th>
            <th>Ngày gửi</th>
            <th>Trạng thái</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>Nguyễn Văn A</td>
            <td>a@gmail.com</td>
            <td>Hỏi về sách</td>
            <td>Tôi muốn hỏi về...</td>
            <td>20/01/2024</td>
            <td><span class="status-badge warning">Chưa đọc</span></td>
          </tr>
          <tr>
            <td>2</td>
            <td>Trần Thị B</td>
            <td>b@gmail.com</td>
            <td>Góp ý</td>
            <td>Cửa hàng nên...</td>
            <td>19/01/2024</td>
            <td><span class="status-badge success">Đã trả lời</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  `
};

// View titles
const viewTitles = {
  overview: 'Tổng quan',
  import: 'Nhập hàng',
  'book-info': 'Thông tin sách',
  revenue: 'Doanh thu',
  sales: 'Bán hàng',
  inventory: 'Tồn kho',
  reports: 'Báo cáo',
  settings: 'Cài đặt',
  contact: 'Liên hệ'
};

// Load admin view
function loadAdminView(view) {
  currentAdminView = view;
  const adminContent = document.getElementById('admin-content');
  const viewTitle = document.getElementById('admin-view-title');
  
  viewTitle.textContent = viewTitles[view] || 'Tổng quan';
  adminContent.innerHTML = adminViews[view] || adminViews.overview;
  
  // Update active nav item
  document.querySelectorAll('.nav-item').forEach(item => {
    item.classList.toggle('active', item.dataset.view === view);
  });
}

// Initialize admin sidebar
function initAdminSidebar() {
  document.querySelectorAll('.nav-item[data-view]').forEach(item => {
    item.addEventListener('click', (e) => {
      e.preventDefault();
      loadAdminView(item.dataset.view);
    });
  });

  document.getElementById('btn-logout').addEventListener('click', (e) => {
    e.preventDefault();
    document.getElementById('btn-customer').click();
  });
}

// Export
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { loadAdminView, initAdminSidebar, currentAdminView, adminViews };
}

