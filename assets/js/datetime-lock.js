// Makes a flatpickr date-and-time box pick-only: clicking opens the calendar, but typing, pasting,
// cutting, dragging text in, and Backspace/Delete do nothing. (Use the small x beside a box to empty it.)
//
// The box is not made read-only on purpose: browsers skip the "required" check on read-only fields,
// so an empty required date would be submitted. Stopping the editing instead keeps that check working.
// The server checks every date again when the form is saved.
//
// Used by the news form and the queue events form: window.lockPickerTyping(picker).

window.lockPickerTyping = function (picker) {
  const box = picker.altInput;
  if (!box) return;
  box.setAttribute('inputmode', 'none'); // no on-screen keyboard on phones
  box.setAttribute('autocomplete', 'off');
  box.style.caretColor = 'transparent';
  box.style.cursor = 'pointer';
  // beforeinput covers every way text can change: typing, paste, cut, drop, Backspace, Delete.
  box.addEventListener('beforeinput', (e) => e.preventDefault());
  box.addEventListener('keydown', (e) => {
    // Enter or Space opens the calendar (keyboard users); Tab and Escape keep working as usual.
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      picker.open();
    }
  });
};
