// Floating FAQ chatbot widget - talks to user/chatbot-api.php

document.addEventListener('DOMContentLoaded', function () {
  const toggle = document.getElementById('chatbotToggle');
  const panel = document.getElementById('chatbotPanel');
  const closeBtn = document.getElementById('chatbotClose');
  const form = document.getElementById('chatbotForm');
  const input = document.getElementById('chatbotInput');
  const messages = document.getElementById('chatbotMessages');

  if (!toggle || !panel) return;

  const switchButtons = document.querySelectorAll('.chatbot-lang-btn');

  const TEXT = {
    en: {
      greeting: "Hi! I'm the LGU Norzagaray virtual assistant. I know the Citizen's Charter — ask me the requirements, fees, or processing time for any document or service, e.g. \"requirements for a mayor's permit\" or \"senior citizen ID\". Please don't type personal details such as ID numbers or addresses here.",
      switched: 'Okay, I will reply in English.',
      placeholder: 'Type your question...',
      error: "I'm having trouble responding right now. Please try again in a moment.",
      sorry: "Sorry, I couldn't process that.",
    },
    tl: {
      greeting: "Kumusta po! Ako ang virtual assistant ng LGU Norzagaray. Alam ko ang Citizen's Charter — itanong lang ang mga kailangan, bayad, o tagal ng proseso ng anumang dokumento o serbisyo, hal. \"requirements para sa mayor's permit\" o \"senior citizen ID\". Huwag po sanang mag-type dito ng personal na detalye tulad ng ID number o address.",
      switched: 'Sige po, sasagot ako sa Tagalog.',
      placeholder: 'I-type ang tanong mo...',
      error: 'May problema po sa pagsagot ngayon. Pakisubukan ulit mamaya.',
      sorry: 'Paumanhin po, hindi ko ito naproseso.',
    },
  };

  let lang = 'en';
  // A page with no earlier conversation starts with the welcome message, which asks for the language.
  // The last choice is only used to highlight the header switch until the citizen picks. A page that
  // has a conversation (see restoreConversation below) skips the welcome and shows it again.
  let langChosen = false;
  try {
    if (localStorage.getItem('chatbotLang') === 'tl') lang = 'tl';
  } catch (e) {
    // Storage can be blocked (private mode); the default language is fine.
  }
  applyLangUi();

  function applyLangUi() {
    switchButtons.forEach((btn) => {
      const active = btn.dataset.lang === lang;
      btn.classList.toggle('active', active);
      btn.setAttribute('aria-pressed', String(active));
    });
    input.placeholder = TEXT[lang].placeholder;
  }

  /** Applies a language choice from the header switch or the in-chat buttons. */
  function setLanguage(next) {
    const wasChosen = langChosen;
    const changed = next !== lang;
    lang = next;
    langChosen = true;
    try {
      localStorage.setItem('chatbotLang', lang);
    } catch (e) {
      // Not fatal: the choice just won't be remembered next visit.
    }
    applyLangUi();
    removePicker();

    if (!greeted) return;
    if (!wasChosen) {
      // First pick, made on the welcome buttons or the header switch.
      addBubble('bot', TEXT[lang].greeting);
    } else if (changed) {
      addBubble('bot', TEXT[lang].switched);
    }
  }

  switchButtons.forEach((btn) => {
    btn.addEventListener('click', function () {
      setLanguage(btn.dataset.lang);
    });
  });

  /** Welcome message with two big buttons; shown until a language has been picked. */
  function showLanguagePicker() {
    const bubble = addBubble('bot', 'Welcome to the LGU Norzagaray assistant! Please choose your language.\nMaligayang pagdating! Pumili po ng wika.');
    const row = document.createElement('div');
    row.className = 'chatbot-lang-picker';
    [['en', 'English'], ['tl', 'Tagalog']].forEach(([code, label]) => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'chatbot-lang-choice';
      btn.textContent = label;
      btn.addEventListener('click', () => setLanguage(code));
      row.appendChild(btn);
    });
    bubble.appendChild(row);
  }

  function removePicker() {
    document.querySelectorAll('.chatbot-lang-picker').forEach((row) => row.remove());
  }

  let greeted = false;

  toggle.addEventListener('click', function () {
    panel.classList.toggle('open');
    rememberOpen(panel.classList.contains('open'));
    if (panel.classList.contains('open')) {
      messages.scrollTop = messages.scrollHeight;
      if (!greeted) {
        greeted = true;
        if (langChosen) {
          addBubble('bot', TEXT[lang].greeting);
        } else {
          showLanguagePicker();
        }
      }
      input.focus();
    }
  });

  closeBtn.addEventListener('click', function () {
    panel.classList.remove('open');
    rememberOpen(false);
  });

  /**
   * The conversation lives on the server (in the login session), so every page can draw it again.
   * The page hands it over in data-history. Whether the chat window was open is remembered for this
   * browser tab, so moving between pages feels like one continuous chat.
   */
  function rememberOpen(open) {
    try {
      if (open) sessionStorage.setItem('chatbotOpen', '1');
      else sessionStorage.removeItem('chatbotOpen');
    } catch (e) {
      // Storage blocked: the window simply starts closed on the next page.
    }
  }

  function restoreConversation() {
    let saved = [];
    try {
      saved = JSON.parse(document.querySelector('.chatbot-widget').dataset.history || '[]');
    } catch (e) {
      saved = [];
    }
    if (!Array.isArray(saved) || saved.length === 0) return;

    greeted = true;
    langChosen = true;
    applyLangUi();
    addBubble('bot', TEXT[lang].greeting);
    saved.forEach((turn) => addBubble(turn.role === 'user' ? 'user' : 'bot', String(turn.content)));

    let wasOpen = false;
    try {
      wasOpen = sessionStorage.getItem('chatbotOpen') === '1';
    } catch (e) {
      wasOpen = false;
    }
    if (wasOpen) {
      panel.classList.add('open');
      messages.scrollTop = messages.scrollHeight;
    }
  }
  restoreConversation();

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const text = input.value.trim();
    if (!text) return;

    removePicker();
    addBubble('user', text);
    input.value = '';
    input.disabled = true;
    const typingEl = addTyping();

    fetch('/LGU-Link/user/chatbot-api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': (document.querySelector('meta[name="csrf-token"]') || {}).content || '' },
      body: JSON.stringify({ message: text, lang }),
    })
      .then((res) => res.json())
      .then((data) => {
        typingEl.remove();
        addBubble('bot', data.reply || TEXT[lang].sorry);
      })
      .catch(() => {
        typingEl.remove();
        addBubble('bot', TEXT[lang].error);
      })
      .finally(() => {
        input.disabled = false;
        input.focus();
      });
  });

  function addBubble(sender, text) {
    const bubble = document.createElement('div');
    bubble.className = `chatbot-bubble ${sender}`;
    appendWithLinks(bubble, text);
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

/**
 * Puts the text in the bubble as plain text, except that a link to a news post on this same site
 * (a full address, or a path like /LGU-Link/news-post.php?id=12) becomes a clickable link. A link
 * to any other site is left as plain text, so the bot can never send a citizen elsewhere.
 */
function appendWithLinks(bubble, text) {
  const pattern = /(https?:\/\/[^\s)]+|\/[A-Za-z0-9_\-\/]*(?:news-post|news)\.php(?:\?id=\d+)?)/g;
  let last = 0;
  let match;
  while ((match = pattern.exec(text)) !== null) {
    bubble.appendChild(document.createTextNode(text.slice(last, match.index)));
    let url = null;
    try { url = new URL(match[0], window.location.origin); } catch (e) { url = null; }
    if (url && url.origin === window.location.origin && /news(-post)?\.php$/.test(url.pathname)) {
      const link = document.createElement('a');
      link.href = url.pathname + url.search;
      link.textContent = match[0].startsWith('/') ? (url.pathname.endsWith('news.php') ? 'News page' : 'Read the post') : match[0];
      link.target = '_blank';
      link.rel = 'noopener';
      bubble.appendChild(link);
    } else {
      bubble.appendChild(document.createTextNode(match[0]));
    }
    last = match.index + match[0].length;
  }
  bubble.appendChild(document.createTextNode(text.slice(last)));
}
