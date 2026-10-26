// News/announcement form.
//  - The four date-and-time boxes use a calendar picker (flatpickr): 12-hour time with AM/PM, a
//    default time of 8:00 AM when you pick a day, and a "Done" button. The value sent to the server
//    keeps the same "2026-11-05T09:00" format the browser's own box used.
//  - The activity section only shows when "This is an activity" is ticked, and its fields are
//    required then. The server checks everything again.
// If the picker library cannot load, the plain browser boxes keep working.

document.addEventListener('DOMContentLoaded', function () {
  const hasPicker = typeof window.flatpickr === 'function';
  const ids = ['nfGoLive', 'nfShowUntil', 'nfStarts', 'nfEnds'];

  if (hasPicker) {
    ids.forEach((id) => {
      const input = document.getElementById(id);
      if (!input) return;

      const picker = window.flatpickr(input, {
        enableTime: true,
        dateFormat: 'Y-m-d\\TH:i', // what is sent: same as a datetime-local box
        altInput: true,
        altFormat: 'M j, Y  h:i K', // what people see: "Nov 5, 2026  08:00 AM"
        allowInput: true, // keeps "required" working: browsers skip it on read-only boxes
        time_24hr: false,
        defaultHour: 8,
        defaultMinute: 0,
        minuteIncrement: 5,
        disableMobile: true, // the phone's own picker has none of the above
        plugins: [new window.confirmDatePlugin({ confirmText: 'Done', showAlways: false })],
      });
      picker.altInput.placeholder = 'Choose a date and time';
      if (window.lockPickerTyping) window.lockPickerTyping(picker); // pick from the calendar; the x empties it

      // A small "x" to empty the box again.
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
      picker.altInput.setAttribute('aria-label', input.closest('.form-group').querySelector('label').textContent.trim());
    });

    // An activity usually ends the same day it starts, so open "Ends" on the start date.
    const starts = document.getElementById('nfStarts');
    const ends = document.getElementById('nfEnds');
    if (starts && ends) {
      ends._flatpickr.config.onOpen.push(() => {
        if (!ends.value && starts.value) ends._flatpickr.jumpToDate(starts.value);
      });
    }
  }

  // A date cannot be picked before the one it must follow (grayed out in the calendar).
  const limits = [['nfShowUntil', 'nfGoLive'], ['nfEnds', 'nfStarts']];
  if (hasPicker) {
    limits.forEach(([later, earlier]) => {
      const sync = () => {
        const e = document.getElementById(earlier).value;
        document.getElementById(later)._flatpickr.set('minDate', e || null);
      };
      document.getElementById(earlier).addEventListener('change', sync);
      sync();
    });
  }

  // The visible box (the picker's, when there is one) is what the browser validates and focuses.
  const box = (id) => {
    const el = document.getElementById(id);
    return el && el._flatpickr ? el._flatpickr.altInput : el;
  };

  const toggle = document.getElementById('nfIsActivity');
  const section = document.getElementById('nfActivityFields');
  if (!toggle || !section) return;

  const venue = section.querySelector('[name="venue"]');
  function syncActivity() {
    section.hidden = !toggle.checked;
    ['nfStarts', 'nfEnds'].forEach((id) => { box(id).required = toggle.checked; });
    if (venue) venue.required = toggle.checked;
  }
  toggle.addEventListener('change', syncActivity);
  syncActivity();

  // Picking when an activity ends before it starts is the most common slip; catch it early.
  const value = (id) => document.getElementById(id).value;
  function checkOrder() {
    box('nfEnds').setCustomValidity(value('nfStarts') && value('nfEnds') && value('nfEnds') <= value('nfStarts') ? 'The activity must end after it starts.' : '');
  }
  function checkShowUntil() {
    box('nfShowUntil').setCustomValidity(value('nfShowUntil') && value('nfGoLive') && value('nfShowUntil') <= value('nfGoLive') ? 'This must be after the time it is posted.' : '');
  }
  const recheck = () => { checkOrder(); checkShowUntil(); };
  ids.forEach((id) => document.getElementById(id).addEventListener('change', recheck));
  const form = toggle.closest('form');
  if (form) form.addEventListener('submit', recheck, true);
  if (form) form.addEventListener('click', () => setTimeout(recheck, 0));
  checkOrder();
  checkShowUntil();
});
