<?php
/**
 * Floating FAQ chatbot widget. Paired with assets/css/chatbot.css,
 * assets/js/chatbot.js, and user/chatbot-api.php.
 *
 * The conversation so far is kept in the citizen's login session (user/chatbot-api.php), so every
 * page can draw it again: it is handed to the script in data-history. It ends with the session
 * (log out or idle time-out).
 */
$chatHistory = [];
foreach ((array) ($_SESSION['chatbot_history'] ?? []) as $turn) {
    if (is_array($turn) && in_array($turn['role'] ?? '', ['user', 'assistant'], true) && is_string($turn['content'] ?? null)) {
        $chatHistory[] = ['role' => $turn['role'], 'content' => $turn['content']];
    }
}
?>
  <meta name="csrf-token" content="<?= htmlspecialchars(csrfToken()) ?>">
  <div class="chatbot-widget" data-history="<?= htmlspecialchars(json_encode($chatHistory, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES) ?>">
    <div class="chatbot-panel" id="chatbotPanel">
      <div class="chatbot-panel-header">
        <div class="chatbot-panel-title">
          <i class="fa-solid fa-robot"></i>
          <div>
            <strong>LGU Assistant</strong>
            <span>Ask about documents, appointments & offices</span>
          </div>
        </div>
        <div class="chatbot-header-actions">
          <div class="chatbot-lang" role="group" aria-label="Chatbot language">
            <button type="button" class="chatbot-lang-btn active" data-lang="en" aria-pressed="true">EN</button>
            <button type="button" class="chatbot-lang-btn" data-lang="tl" aria-pressed="false">TL</button>
          </div>
          <i class="fa-solid fa-xmark chatbot-close" id="chatbotClose"></i>
        </div>
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
