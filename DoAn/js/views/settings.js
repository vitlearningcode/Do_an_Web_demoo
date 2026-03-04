// ==================== SETTINGS VIEW ====================

const settingsView = {
  title: 'Cài đặt hệ thống',
  
  settings: {
    storeName: 'Book Sales Store',
    email: 'contact@booksales.vn',
    phone: '1900-xxxx',
    address: '123 Đường ABC, Quận 1, TP.HCM',
    freeShipping: 250000,
    taxRate: 10
  },
  
  render: function() {
    return `
      <div class="view-container">
        <div class="view-header">
          <div>
            <h2 class="view-title">Cài đặt hệ thống</h2>
            <p class="view-subtitle">Quản lý cấu hình cửa hàng</p>
          </div>
        </div>

        <div class="settings-grid">
          <!-- General Settings -->
          <div class="view-card">
            <div class="card-header">
              <h3><i class="fas fa-cog"></i> Cài đặt chung</h3>
            </div>
            <form class="settings-form" onsubmit="settingsView.saveSettings(event)">
              <div class="form-group">
                <label>Tên cửa hàng</label>
                <input type="text" name="storeName" value="${this.settings.storeName}">
              </div>
              <div class="form-group">
                <label>Email liên hệ</label>
                <input type="email" name="email" value="${this.settings.email}">
              </div>
              <div class="form-group">
                <label>Điện thoại</label>
                <input type="tel" name="phone" value="${this.settings.phone}">
              </div>
              <div class="form-group">
                <label>Địa chỉ</label>
                <input type="text" name="address" value="${this.settings.address}">
              </div>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Lưu thay đổi
              </button>
            </form>
          </div>

          <!-- Shipping Settings -->
          <div class="view-card">
            <div class="card-header">
              <h3><i class="fas fa-truck"></i> Cài đặt vận chuyển</h3>
            </div>
            <form class="settings-form">
              <div class="form-group">
                <label>Miễn phí vận chuyển từ (VNĐ)</label>
                <input type="number" value="${this.settings.freeShipping}">
              </div>
              <div class="form-group">
                <label>Phí vận chuyển mặc định (VNĐ)</label>
                <input type="number" value="25000">
              </div>
              <div class="form-group">
                <label>Thời gian giao hàng (ngày)</label>
                <input type="number" value="3">
              </div>
              <button type="button" class="btn btn-primary">
                <i class="fas fa-save"></i> Lưu thay đổi
              </button>
            </form>
          </div>

          <!-- Tax Settings -->
          <div class="view-card">
            <div class="card-header">
              <h3><i class="fas fa-percent"></i> Cài đặt thuế</h3>
            </div>
            <form class="settings-form">
              <div class="form-group">
                <label>Tỷ lệ thuế (%)</label>
                <input type="number" value="${this.settings.taxRate}">
              </div>
              <div class="form-group">
                <label class="checkbox-label">
                  <input type="checkbox" checked>
                  <span>Hiển thị thuế trong giá sản phẩm</span>
                </label>
              </div>
              <button type="button" class="btn btn-primary">
                <i class="fas fa-save"></i> Lưu thay đổi
              </button>
            </form>
          </div>

          <!-- Notification Settings -->
          <div class="view-card">
            <div class="card-header">
              <h3><i class="fas fa-bell"></i> Cài đặt thông báo</h3>
            </div>
            <form class="settings-form">
              <div class="form-group">
                <label class="checkbox-label">
                  <input type="checkbox" checked>
                  <span>Thông báo đơn hàng mới</span>
                </label>
              </div>
              <div class="form-group">
                <label class="checkbox-label">
                  <input type="checkbox" checked>
                  <span>Thông báo tồn kho thấp</span>
                </label>
              </div>
              <div class="form-group">
                <label class="checkbox-label">
                  <input type="checkbox" checked>
                  <span>Email báo cáo tuần</span>
                </label>
              </div>
              <button type="button" class="btn btn-primary">
                <i class="fas fa-save"></i> Lưu thay đổi
              </button>
            </form>
          </div>
        </div>
      </div>
    `;
  },
  
  saveSettings: function(e) {
    e.preventDefault();
    toast.success('Lưu cài đặt thành công');
  }
};

// Export
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { settingsView };
}

