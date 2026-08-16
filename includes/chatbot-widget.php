<?php
/**
 * Floating FAQ chatbot widget. Paired with assets/css/chatbot.css,
 * assets/js/chatbot.js, and user/chatbot-api.php.
 */
?>
  <div class="chatbot-widget">
    <div class="chatbot-panel" id="chatbotPanel">
      <div class="chatbot-panel-header">
        <div class="chatbot-panel-title">
          <i class="fa-solid fa-robot"></i>
          <div>
            <strong>LGU Assistant</strong>
            <span>Ask about documents, appointments & offices</span>
          </div>
        </div>
        <i class="fa-solid fa-xmark chatbot-close" id="chatbotClose"></i>
      </div>

      <div class="chatbot-messages" id="chatbotMessages"></div>

      <form class="chatbot-input-row" id="chatbotForm">
        <input type="text" id="chatbotInput" placeholder="Type your question..." autocomplete="off" required>
        <button type="submit" aria-label="Send"><i class="fa-solid fa-paper-plane"></i></button>
      </form>
    </div>

    <button class="chatbot-toggle" id="chatbotToggle" aria-label="Open LGU Assistant chat">
      <i class="fa-solid fa-comment-dots"></i>
    </button>
  </div>
