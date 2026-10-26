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

  // Stopping cannot come before starting: grey those days out.
  const opens = document.getElementById('evOpens');
  const closes = document.getElementById('evCloses');
  if (opens && closes && opens._flatpickr && closes._flatpickr) {
    const sync = () => closes._flatpickr.set('minDate', opens.value || null);
    opens.addEventListener('change', sync);
    sync();
  }
});
