<?php
/**
 * khungChatbot.php — HTML khung chatbot
 * Bao gồm: nút bong bóng chat (chatbot-toggle) + panel chatbot
 * Logic JS nằm trong PhuongThuc/components/chatbot.js
 */
?>
<div id="chatbot-toggle" class="chatbot-toggle">
    <i class="fas fa-comments"></i>
</div>
<div id="chatbot" class="chatbot">
    <div class="chatbot-header">
        <div class="chatbot-header-info">
            <i class="fas fa-robot"></i>
            <div>
                <h4>Trợ lý AI</h4>
                <p>Luôn sẵn sàng hỗ trợ</p>
            </div>
        </div>
        <button id="chatbot-close"><i class="fas fa-times"></i></button>
    </div>
    <div class="chatbot-messages" id="chatbot-messages">
        <div class="chatbot-message bot">
            <div class="message-avatar bot"><i class="fas fa-robot"></i></div>
            <div class="message-content">
                <p>Xin chào! Tôi là trợ lý AI của Cửa Hàng. Tôi có thể giúp gì cho bạn hôm nay?</p>
            </div>
        </div>
    </div>
    <div class="typing-indicator" id="typing-indicator" style="display: none;">
        <div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>
    </div>
    <div class="chatbot-input">
        <input type="text" id="chatbot-input" placeholder="Nhập câu hỏi của bạn...">
        <button id="chatbot-send"><i class="fas fa-paper-plane"></i></button>
    </div>
</div>
