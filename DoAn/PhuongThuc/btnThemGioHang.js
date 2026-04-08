// ==========================================
// XỬ LÝ THÊM VÀO GIỎ HÀNG (SỬ DỤNG DATA ATTRIBUTE)
// ==========================================

window.addToCart = function(buttonElement) {
    // 1. Tìm ngược lên thẻ chứa nó (thẻ div có class book-card)
    const card = buttonElement.closest('.book-card');
    
    if (!card) {
        console.error("Lỗi: Không tìm thấy khung chứa sách!");
        return;
    }

    // 2. Móc dữ liệu trực tiếp từ các thuộc tính data-* mà ta đã gắn
    const cartItem = {
        id: card.dataset.id,
        name: card.dataset.name,
        price: parseFloat(card.dataset.price), 
        image: card.dataset.image,
        author: 'Đang cập nhật' 
    };

    // 3. Đưa vào giỏ hàng
    if (typeof cartDrawer !== 'undefined') {
        cartDrawer.addItem(cartItem, 1);
        
        if (typeof toast !== 'undefined') {
            toast.success(`Đã thêm "${cartItem.name}" vào giỏ hàng`);
        }
    } else {
        console.error("Lỗi: Không tìm thấy hệ thống giỏ hàng (cartDrawer).");
    }
};