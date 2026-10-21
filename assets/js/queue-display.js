// The waiting-room screen: asks the server every 3 seconds who is being called and redraws.
// A newly called number flashes and (if sound is switched on) rings a short chime, then a voice
// says "Now serving D 0 0 3. Please go to Ms. Reyes." The voice is the browser's own (speechSynthesis),
// so it works offline; which voices exist depends on the TV or PC.

(function () {
  const tv = document.getElementById('tv');
  if (!tv) return;

  const queueId = tv.dataset.queue;
  const cards = document.getElementById('tvCards');
  const nextList = document.getElementById('tvNext');
  const more = document.getElementById('tvMore');
  const status = document.getElementById('tvStatus');
  const soundBtn = document.getElementById('tvSound');
  let soundOn = false;
  let audio = null;
  let known = null; // code -> called_at of what was on screen last time (null until the first draw)
  let failures = 0;

  function chime() {
    if (!soundOn || !audio) return;
    [[880, 0], [1175, 0.22]].forEach(([freq, at]) => {
      const osc = audio.createOscillator();
      const gain = audio.createGain();
      osc.type = 'sine';
      osc.frequency.value = freq;
      gain.gain.setValueAtTime(0.0001, audio.currentTime + at);
      gain.gain.exponentialRampToValueAtTime(0.35, audio.currentTime + at + 0.03);
      gain.gain.exponentialRampToValueAtTime(0.0001, audio.currentTime + at + 0.55);
      osc.connect(gain).connect(audio.destination);
      osc.start(audio.currentTime + at);
      osc.stop(audio.currentTime + at + 0.6);
    });
  }

  // ---- spoken announcement ----
  const speechOk = 'speechSynthesis' in window && typeof window.SpeechSynthesisUtterance === 'function';
  function pickVoice() {
    if (!speechOk) return null;
    const voices = window.speechSynthesis.getVoices();
    const english = voices.filter((v) => /^en[-_]/i.test(v.lang));
    // A Philippine English voice sounds most natural here; otherwise any English voice.
    return english.find((v) => /en[-_]PH/i.test(v.lang)) || english.find((v) => /en[-_](US|GB|AU)/i.test(v.lang)) || english[0] || null;
  }
  if (speechOk) window.speechSynthesis.onvoiceschanged = () => {};

  // "MO-D003" -> "D 0 0 3"; an event number "FA-007" -> "number 0 0 7". The office prefix is left out.
  function spokenNumber(code) {
    const m = /^(?:[A-Z0-9]+-)?([A-Z]?)(\d+)$/.exec(code);
    if (!m) return code.replace(/[-]/g, ' ');
    const digits = m[2].split('').join(' ');
    return m[1] ? m[1] + ', ' + digits : 'number ' + digits;
  }

  const speechQueue = [];
  let speaking = false;
  function speakNext() {
    if (speaking || !speechQueue.length || !soundOn || !speechOk) return;
    const text = speechQueue.shift();
    const u = new window.SpeechSynthesisUtterance(text);
    const voice = pickVoice();
    if (voice) { u.voice = voice; u.lang = voice.lang; } else { u.lang = 'en-US'; }
    u.rate = 0.9;
    speaking = true;
    u.onend = u.onerror = () => { speaking = false; setTimeout(speakNext, 400); };
    window.speechSynthesis.speak(u);
  }
  function announce(t) {
    if (!soundOn || !speechOk) return;
    const who = t.staff ? 'Please go to ' + t.staff + '.' : 'Please go to the counter.';
    speechQueue.push((t.priority ? 'Priority. ' : '') + 'Now serving ' + spokenNumber(t.code) + '. ' + who);
    // After the chime, one announcement after another.
    setTimeout(speakNext, 900);
  }

  function el(tag, cls, text) {
    const e = document.createElement(tag);
    if (cls) e.className = cls;
    if (text !== undefined) e.textContent = text;
    return e;
  }

  function draw(data) {
    // Fewer people being called means bigger cards.
    const n = data.serving.length;
    const cols = n <= 1 ? 1 : n <= 4 ? 2 : 3;
    const rows = Math.ceil(Math.max(n, 1) / cols);
    cards.style.setProperty('--cols', cols);
    cards.style.setProperty('--code', Math.max(5, Math.min(14, 46 / rows / (cols > 1 ? 1.3 : 1))) + 'vh');
    cards.style.setProperty('--to', Math.max(2.4, Math.min(4.6, 15 / rows)) + 'vh');
    cards.replaceChildren();

    const now = {};
    const fresh = [];
    if (n === 0) {
      cards.appendChild(el('div', 'tv-none', 'Please wait for your number to be called.'));
    }
    data.serving.forEach((t) => {
      now[t.code] = t.called_at;
      const card = el('div', 'tv-card ' + t.status);
      if (t.priority) card.appendChild(el('div', 'tv-priority', '★ Priority'));
      card.appendChild(el('div', 'code', t.code));
      const to = el('div', 'to');
      to.append(t.status === 'serving' ? 'Being served by ' : 'Please go to ');
      to.appendChild(el('b', '', t.staff || 'the staff'));
      card.appendChild(to);
      // New, or called again: draw attention.
      if (known !== null && known[t.code] !== t.called_at && t.status === 'called') {
        card.classList.add('fresh');
        fresh.push(t);
      }
      cards.appendChild(card);
    });
    if (fresh.length) {
      chime();
      fresh.forEach(announce);
    }
    known = now;

    nextList.replaceChildren();
    data.next.slice(0, 8).forEach((code) => nextList.appendChild(el('li', '', code)));
    more.textContent = data.waiting === 0 ? 'Nobody is waiting.' : (data.waiting > 8 ? '+ ' + (data.waiting - 8) + ' more waiting' : '');
  }

  async function refresh() {
    try {
      const res = await fetch('/LGU-Link/queue-display-data.php?q=' + encodeURIComponent(queueId), { cache: 'no-store' });
      if (!res.ok) throw new Error('bad response');
      draw(await res.json());
      failures = 0;
      status.hidden = true;
    } catch (e) {
      if (++failures >= 2) status.hidden = false;
    }
  }

  function tick() {
    const d = new Date();
    document.getElementById('tvClock').innerHTML = '';
    const time = d.toLocaleTimeString('en-PH', { hour: 'numeric', minute: '2-digit' });
    const date = d.toLocaleDateString('en-PH', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
    document.getElementById('tvClock').append(time, Object.assign(document.createElement('small'), { textContent: date }));
  }

  soundBtn.addEventListener('click', () => {
    soundOn = !soundOn;
    if (soundOn && !audio) audio = new (window.AudioContext || window.webkitAudioContext)();
    if (soundOn && audio.state === 'suspended') audio.resume();
    soundBtn.classList.toggle('on', soundOn);
    soundBtn.innerHTML = '<i class="fa-solid ' + (soundOn ? 'fa-volume-high' : 'fa-volume-xmark') + '"></i>';
    if (!soundOn && speechOk) { speechQueue.length = 0; window.speechSynthesis.cancel(); speaking = false; }
    if (soundOn) {
      chime();
      // Speaking once now also unlocks the voice, which browsers allow only after a click.
      if (speechOk) { speechQueue.push('Sound is on.'); setTimeout(speakNext, 700); }
    }
  });
  document.getElementById('tvFull').addEventListener('click', () => {
    if (document.fullscreenElement) document.exitFullscreen();
    else document.documentElement.requestFullscreen().catch(() => {});
  });

  draw(window.TV_INITIAL);
  tick();
  setInterval(tick, 15000);
  setInterval(refresh, 3000);
})();
