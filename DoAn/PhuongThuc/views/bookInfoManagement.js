// ==================== BOOK INFO MANAGEMENT VIEW ====================

const bookInfoManagementView = {
  title: 'Quản lý thông tin sách',
  
  // Sample books data
  books: [
    { id: '1', name: 'Đắc Nhân Tâm', author: 'Dale Carnegie', category: 'Kỹ năng sống', price: 86000, originalPrice: 120000, stock: 120, image: 'https://picsum.photos/seed/book1/60/80' },
    { id: '2', name: 'Nhà Giả Kim', author: 'Paulo Coelho', category: 'Văn học', price: 79000, originalPrice: 95000, stock: 45, image: 'https://picsum.photos/seed/book2/60/80' },
    { id: '3', name: 'Nghĩ Giàu Làm Giàu', author: 'Napoleon Hill', category: 'Kinh tế', price: 110000, originalPrice: null, stock: 8, image: 'https://picsum.photos/seed/book3/60/80' },
    { id: '4', name: 'Tuổi Trẻ Đáng Giá Bao Nhiêu', author: 'Rosie Nguyễn', category: 'Kỹ năng sống', price: 80000, originalPrice: null, stock: 210, image: 'https://picsum.photos/seed/book4/60/80' },
    { id: '5', name: 'Sapiens: Lược Sử Loài Người', author: 'Yuval Noah Harari', category: 'Khoa học', price: 250000, originalPrice: 300000, stock: 32, image: 'https://picsum.photos/seed/book5/60/80' }
  ],
  
  categories: ['Văn học', 'Kinh tế', 'Kỹ năng sống', 'Khoa học', 'Tâm lý học', 'Thiếu nhi', 'Tôn giáo'],
  
  render: function() {
    return `
      <div class="view-container">
        <!-- Header -->
        <div class="view-header">
          <div>
            <h2 class="view-title">Quản lý thông tin sách chi tiết</h2>
            <p class="view-subtitle">Thêm, sửa, xóa và cập nhật thông tin sách</p>
          </div>
          <div class="view-actions">
            <button class="btn btn-primary" onclick="bookInfoManagementView.openAddModal()">
              <i class="fas fa-plus"></i> Thêm sách mới
            </button>
          </div>
        </div>

        <!-- Search & Filter -->
        <div class="view-card">
          <div class="search-filter-row">
            <div class="search-box">
              <i class="fas fa-search"></i>
              <input type="text" placeholder="Tìm kiếm tên sách, tác giả, ISBN..." id="book-search-input">
            </div>
            <select class="filter-select" id="category-filter">
              <option value="">Tất cả danh mục</option>
              ${this.categories.map(cat => `<option value="${cat}">${cat}</option>`).join('')}
            </select>
            <button class="btn btn-outline">
              <i class="fas fa-filter"></i> Lọc
            </button>
          </div>
        </div>

        <!-- Books Table -->
        <div class="view-card">
          <table class="data-table">
            <thead>
              <tr>
                <th class="th-book">Sách</th>
                <th>Tác Giả</th>
                <th>Danh Mục</th>
                <th>Giá Bán</th>
                <th>Tồn Kho</th>
                <th class="th-actions">Thao Tác</th>
              </tr>
            </thead>
            <tbody id="books-table-body">
              ${this.renderBooksTable()}
            </tbody>
          </table>
          
          <div class="table-pagination">
            <p class="pagination-info">Hiển thị <span>1-${this.books.length}</span> trong <span>${this.books.length}</span> kết quả</p>
            <div class="pagination-buttons">
              <button class="btn btn-sm" disabled>Trước</button>
              <button class="btn btn-sm btn-primary">1</button>
              <button class="btn btn-sm">2</button>
              <button class="btn btn-sm">3</button>
              <button class="btn btn-sm">Sau</button>
            </div>
          </div>
        </div>

        <!-- Add/Edit Modal -->
        ${this.renderAddEditModal()}
      </div>
    `;
  },
  
  renderBooksTable: function() {
    return this.books.map((book, idx) => `
      <tr>
        <td>
          <div class="book-cell">
            <img src="${book.image}" alt="${escapeHtml(book.name)}" referrerPolicy="no-referrer">
            <div>
              <p class="book-name">${escapeHtml(book.name)}</p>
              <p class="book-isbn">ISBN: 978-604-${1000 + idx}</p>
            </div>
          </div>
        </td>
        <td>${escapeHtml(book.author)}</td>
        <td><span class="category-badge">${escapeHtml(book.category)}</span></td>
        <td>
          <div class="price-cell">
            <span class="current-price">${formatPrice(book.price)}</span>
            ${book.originalPrice ? `<span class="original-price">${formatPrice(book.originalPrice)}</span>` : ''}
          </div>
        </td>
        <td>
          <div class="stock-cell">
            <span class="stock-dot ${book.stock < 10 ? 'danger' : book.stock < 50 ? 'warning' : 'success'}"></span>
            <span class="${book.stock < 10 ? 'text-danger' : ''}">${book.stock}</span>
          </div>
        </td>
        <td>
          <div class="action-buttons">
            <button class="btn-icon" title="Tạo ảnh bìa AI" onclick="bookInfoManagementView.openImageGenerator('${escapeHtml(book.name)}')">
              <i class="fas fa-image"></i>
            </button>
            <button class="btn-icon" title="Sửa" onclick="bookInfoManagementView.editBook('${book.id}')">
              <i class="fas fa-edit"></i>
            </button>
            <button class="btn-icon btn-icon-danger" title="Xóa" onclick="bookInfoManagementView.deleteBook('${book.id}')">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </td>
      </tr>
    `).join('');
  },
  
  renderAddEditModal: function() {
    return `
      <div class="modal-overlay" id="book-add-modal">
        <div class="modal book-form-modal">
          <button class="modal-close" onclick="bookInfoManagementView.closeAddModal()">
            <i class="fas fa-times"></i>
          </button>
          <h2 id="book-form-title">Thêm sách mới</h2>
          
          <form id="book-form" onsubmit="bookInfoManagementView.saveBook(event)">
            <!-- Image Upload -->
            <div class="form-image-upload" id="image-upload-area">
              <input type="file" id="book-image-input" accept="image/*" style="display: none;">
              <div class="image-preview" id="image-preview">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>Kéo thả ảnh bìa hoặc click để tải lên</p>
                <span>PNG, JPG, GIF up to 5MB</span>
              </div>
            </div>
            
            <div class="form-grid">
              <div class="form-group">
                <label>Tên sách <span class="required">*</span></label>
                <input type="text" name="name" required placeholder="Nhập tên sách">
              </div>
              
              <div class="form-group">
                <label>Tác giả <span class="required">*</span></label>
                <input type="text" name="author" required placeholder="Nhập tên tác giả">
              </div>
              
              <div class="form-group">
                <label>Danh mục</label>
                <select name="category">
                  ${this.categories.map(cat => `<option value="${cat}">${cat}</option>`).join('')}
                </select>
              </div>
              
              <div class="form-group">
                <label>Giá bán (VNĐ) <span class="required">*</span></label>
                <input type="number" name="price" required placeholder="0">
              </div>
              
              <div class="form-group">
                <label>Giá gốc (VNĐ)</label>
                <input type="number" name="originalPrice" placeholder="0">
              </div>
              
              <div class="form-group">
                <label>Số lượng tồn kho</label>
                <input type="number" name="stock" placeholder="0">
              </div>
              
              <div class="form-group full-width">
                <label>Mô tả ngắn</label>
                <textarea name="description" rows="3" placeholder="Nhập mô tả nội dung sách..."></textarea>
              </div>
            </div>
            
            <div class="form-actions">
              <button type="button" class="btn btn-outline" onclick="bookInfoManagementView.closeAddModal()">Hủy bỏ</button>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Lưu thông tin
              </button>
            </div>
          </form>
        </div>
      </div>
    `;
  },
  
  openAddModal: function() {
    document.getElementById('book-add-modal').classList.add('active');
    document.getElementById('book-form-title').textContent = 'Thêm sách mới';
    document.getElementById('book-form').reset();
    document.getElementById('image-preview').innerHTML = `
      <i class="fas fa-cloud-upload-alt"></i>
      <p>Kéo thả ảnh bìa hoặc click để tải lên</p>
      <span>PNG, JPG, GIF up to 5MB</span>
    `;
  },
  
  closeAddModal: function() {
    document.getElementById('book-add-modal').classList.remove('active');
  },
  
  openImageGenerator: function(bookName) {
    imageGeneratorModal.open(bookName);
    imageGeneratorModal.onImageSelect = (imageUrl) => {
      window.selectedBookImage = imageUrl;
      // Update preview if modal is open
      const preview = document.getElementById('image-preview');
      if (preview) {
        preview.innerHTML = `<img src="${imageUrl}" alt="Preview">`;
      }
    };
  },
  
  editBook: function(id) {
    const book = this.books.find(b => b.id === id);
    if (!book) return;
    
    document.getElementById('book-add-modal').classList.add('active');
    document.getElementById('book-form-title').textContent = 'Sửa thông tin sách';
    
    // Fill form
    const form = document.getElementById('book-form');
    form.name.value = book.name;
    form.author.value = book.author;
    form.category.value = book.category;
    form.price.value = book.price;
    form.originalPrice.value = book.originalPrice || '';
    form.stock.value = book.stock;
    
    // Update image preview
    const preview = document.getElementById('image-preview');
    preview.innerHTML = `<img src="${book.image}" alt="Preview">`;
    
    window.editingBookId = id;
  },
  
  deleteBook: function(id) {
    if (confirm('Bạn có chắc chắn muốn xóa sách này?')) {
      this.books = this.books.filter(b => b.id !== id);
      document.getElementById('books-table-body').innerHTML = this.renderBooksTable();
      toast.success('Xóa sách thành công');
    }
  },
  
  saveBook: function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    
    const bookData = {
      id: window.editingBookId || 'new_' + Date.now(),
      name: formData.get('name'),
      author: formData.get('author'),
      category: formData.get('category'),
      price: parseInt(formData.get('price')),
      originalPrice: formData.get('originalPrice') ? parseInt(formData.get('originalPrice')) : null,
      stock: parseInt(formData.get('stock')) || 0,
      image: window.selectedBookImage || 'https://picsum.photos/seed/book' + Date.now() + '/60/80'
    };
    
    if (window.editingBookId) {
      // Update existing
      const idx = this.books.findIndex(b => b.id === window.editingBookId);
      if (idx !== -1) {
        this.books[idx] = { ...this.books[idx], ...bookData };
      }
      toast.success('Cập nhật sách thành công');
    } else {
      // Add new
      this.books.unshift(bookData);
      toast.success('Thêm sách mới thành công');
    }
    
    document.getElementById('books-table-body').innerHTML = this.renderBooksTable();
    this.closeAddModal();
    window.editingBookId = null;
    window.selectedBookImage = null;
  }
};

// Export
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { bookInfoManagementView };
}

