// The event form's two date-and-time boxes: the same picker as the news form (12-hour clock with
// AM/PM, 8:00 AM by default, a Done button), plus a small x to empty a box. If the picker cannot
// load, the browser's own boxes keep working.

document.addEventListener('DOMContentLoaded', function () {
  if (typeof window.flatpickr !== 'function') return;

  document.querySelectorAll('.dt-pick').forEach((input) => {
    const picker = window.flatpickr(input, {
      enableTime: true,
      dateFormat: 'Y-m-d\\TH:i',
      altInput: true,
      altFormat: 'M j, Y  h:i K',
      allowInput: true,
      time_24hr: false,
      defaultHour: 8,
      defaultMinute: 0,
      minuteIncrement: 5,
      disableMobile: true,
      plugins: [new window.confirmDatePlugin({ confirmText: 'Done', showAlways: false })],
    });
    picker.altInput.placeholder = 'Choose a date and time';
    if (window.lockPickerTyping) window.lockPickerTyping(picker); // pick from the calendar; the x empties it

    const wrap = document.createElement('span');
    wrap.className = 'dt-wrap';
    picker.altInput.parentNode.insertBefore(wrap, picker.altInput);
    wrap.appendChild(picker.altInput);
    const clear = document.createElement('button');
    clear.type = 'button';
    clear.className = 'dt-clear';
    clear.title = 'Clear';
    clear.setAttribute('aria-label', 'Clear date and time');
    clear.innerHTML = '&times;';
    clear.addEventListener('click', () => picker.clear());
    wrap.appendChild(clear);
    const showClear = () => { clear.hidden = !input.value; };
    picker.config.onChange.push(showClear);
    picker.config.onClose.push(showClear);
    showClear();
  });

  // Days and times that have passed are grayed out, and stopping cannot come before starting. A box that
  // already holds an old time (an event being edited) keeps it as the earliest it can have.
  const opens = document.getElementById('evOpens');
  const closes = document.getElementById('evCloses');
  if (opens && closes && opens._flatpickr && closes._flatpickr) {
    const initial = { evOpens: opens.value, evCloses: closes.value };
    const sync = () => {
      const now = new Date();
      const startOfToday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
      const floor = (id) => (initial[id] && new Date(initial[id]) < now ? initial[id] : (id === 'evOpens' ? startOfToday : now));
      opens._flatpickr.set('minDate', floor('evOpens'));
      let min = floor('evCloses');
      if (opens.value && new Date(opens.value) > new Date(min)) min = opens.value;
      closes._flatpickr.set('minDate', min);
    };
    opens.addEventListener('change', sync);
    closes.addEventListener('change', sync);
    sync();
  }
});
