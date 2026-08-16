// Floating FAQ chatbot widget - talks to user/chatbot-api.php

document.addEventListener('DOMContentLoaded', function () {
  const toggle = document.getElementById('chatbotToggle');
  const panel = document.getElementById('chatbotPanel');
  const closeBtn = document.getElementById('chatbotClose');
  const form = document.getElementById('chatbotForm');
  const input = document.getElementById('chatbotInput');
  const messages = document.getElementById('chatbotMessages');

  if (!toggle || !panel) return;

  let greeted = false;

  toggle.addEventListener('click', function () {
    panel.classList.toggle('open');
    if (panel.classList.contains('open')) {
      if (!greeted) {
        addBubble('bot', "Hi! I'm the LGU Norzagaray virtual assistant. I know the Citizen's Charter — ask me the requirements, fees, or processing time for any document or service, e.g. \"requirements for a mayor's permit\" or \"senior citizen ID\".");
        greeted = true;
      }
      input.focus();
    }
  });

  closeBtn.addEventListener('click', function () {
    panel.classList.remove('open');
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const text = input.value.trim();
    if (!text) return;

    addBubble('user', text);
    input.value = '';
    input.disabled = true;
    const typingEl = addTyping();

    fetch('/LGU-Link/user/chatbot-api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ message: text }),
    })
      .then((res) => res.json())
      .then((data) => {
        typingEl.remove();
        addBubble('bot', data.reply || "Sorry, I couldn't process that.");
      })
      .catch(() => {
        typingEl.remove();
        addBubble('bot', "I'm having trouble responding right now. Please try again in a moment.");
      })
      .finally(() => {
        input.disabled = false;
        input.focus();
      });
  });

  function addBubble(sender, text) {
    const bubble = document.createElement('div');
    bubble.className = `chatbot-bubble ${sender}`;
    bubble.textContent = text;
    messages.appendChild(bubble);
    messages.scrollTop = messages.scrollHeight;
    return bubble;
  }

  function addTyping() {
    const bubble = document.createElement('div');
    bubble.className = 'chatbot-bubble bot typing';
    bubble.innerHTML = '<span></span><span></span><span></span>';
    messages.appendChild(bubble);
    messages.scrollTop = messages.scrollHeight;
    return bubble;
  }
});
