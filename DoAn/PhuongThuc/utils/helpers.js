// ==================== UTILITY FUNCTIONS ====================

// Format price to Vietnamese Dong
function formatPrice(price) {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
}

// Show toast notification
function showToast(message, type = 'success') {
  const toast = document.getElementById('toast');
  const toastMessage = document.getElementById('toast-message');
  const toastIcon = toast.querySelector('i');
  
  toastMessage.textContent = message;
  
  // Update icon based on type
  if (type === 'success') {
    toastIcon.className = 'fas fa-check-circle';
    toastIcon.style.color = '#10b981';
  } else if (type === 'error') {
    toastIcon.className = 'fas fa-exclamation-circle';
    toastIcon.style.color = '#ef4444';
  } else if (type === 'warning') {
    toastIcon.className = 'fas fa-exclamation-triangle';
    toastIcon.style.color = '#f59e0b';
  }
  
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 3000);
}

// Debounce function
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Format date to Vietnamese
function formatDate(date) {
  return new Intl.DateTimeFormat('vi-VN', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
  }).format(new Date(date));
}

// Generate random ID
function generateId() {
  return 'id_' + Math.random().toString(36).substr(2, 9);
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
  if (!text) return '';
  const map = {
    '&': '&amp;',
    '<': '<',
    '>': '>',
    '"': '"',
    "'": '&#039;'
  };
  return String(text).replace(/[&<>"']/g, m => map[m]);
}

// Calculate discount percentage
function calculateDiscount(originalPrice, currentPrice) {
  if (!originalPrice) return 0;
  return Math.round((1 - currentPrice / originalPrice) * 100);
}

// Truncate text
function truncateText(text, maxLength) {
  if (text.length <= maxLength) return text;
  return text.substring(0, maxLength) + '...';
}

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { 
    formatPrice, 
    showToast, 
    debounce, 
    formatDate, 
    generateId,
    escapeHtml,
    calculateDiscount,
    truncateText
  };
}
