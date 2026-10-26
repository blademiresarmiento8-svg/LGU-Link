// Working-calendar rules in the browser. On any <form data-schedule-rules>:
//   data-rule="date"  -> a calendar where closed days, holidays and past dates
//                        are greyed out and cannot be clicked (flatpickr)
//   data-rule="time"  -> a dropdown of the bookable start times only
//   data-rule="end"   -> a dropdown of the bookable end times only
// If the calendar library can't load, the plain date/time boxes are kept and
// bad values are reported with a message instead. Either way the server
// enforces the same rules (includes/schedule-helpers.php).

(function () {
  const rules = window.SCHEDULE_RULES;
  if (!rules) return;

  const SLOT = rules.slot || 30; // minutes between bookable times
  const DAY_NAMES = ['Sundays', 'Mondays', 'Tuesdays', 'Wednesdays', 'Thursdays', 'Fridays', 'Saturdays'];

  /* ---------- helpers ---------- */

  const toMin = (hhmm) => Number(hhmm.slice(0, 2)) * 60 + Number(hhmm.slice(3, 5));
  const fromMin = (m) => `${String(Math.floor(m / 60)).padStart(2, '0')}:${String(m % 60).padStart(2, '0')}`;

  function clock(hhmm) {
    const [h, m] = hhmm.split(':').map(Number);
    return `${h % 12 === 0 ? 12 : h % 12}:${String(m).padStart(2, '0')} ${h >= 12 ? 'PM' : 'AM'}`;
  }

  function nowHHMM() {
    const d = new Date();
    return `${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}`;
  }

  function isoOf(d) {
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
  }

  /* ---------- the rules (same checks the server makes) ---------- */

  function dateProblem(date) {
    if (!date) return '';
    if (date < rules.today) return 'The date cannot be in the past.';

    const weekday = new Date(`${date}T00:00:00`).getDay(); // 0 = Sunday
    if (!rules.workDays.includes(weekday === 0 ? 7 : weekday)) {
      return `Offices are closed on ${DAY_NAMES[weekday]}. Choose a working day (${rules.description}).`;
    }
    if (rules.holidays[date]) {
      return `That day is a holiday (${rules.holidays[date]}). Choose another day.`;
    }
    return '';
  }

  function timeProblem(date, time) {
    if (!time) return '';
    if (time < rules.open || time >= rules.close) {
      return `Choose a time within working hours (${clock(rules.open)} to ${clock(rules.close)}).`;
    }
    if (rules.lunch && time >= rules.lunch[0] && time < rules.lunch[1]) {
      return `That time falls in the lunch break (${clock(rules.lunch[0])} - ${clock(rules.lunch[1])}).`;
    }
    if (date && date === rules.today && time <= nowHHMM()) {
      return 'That time has already passed today.';
    }
    return '';
  }

  function endProblem(start, end) {
    if (!end) return '';
    if (start && end <= start) return 'The end time must be after the start time.';
    if (end > rules.close) return `The meeting must end by closing time (${clock(rules.close)}).`;
    if (rules.lunch && start && start < rules.lunch[1] && end > rules.lunch[0] && !(start >= rules.lunch[0] && start < rules.lunch[1])) {
      return `The meeting overlaps the lunch break (${clock(rules.lunch[0])} - ${clock(rules.lunch[1])}).`;
    }
    return '';
  }

  /* ---------- time dropdowns ---------- */

  function startSlots(date) {
    const slots = [];
    for (let m = toMin(rules.open); m < toMin(rules.close); m += SLOT) {
      const t = fromMin(m);
      if (!timeProblem(date, t)) slots.push(t);
    }
    return slots;
  }

  function endSlots(start) {
    const slots = [];
    const from = start ? toMin(start) + SLOT : toMin(rules.open) + SLOT;
    for (let m = from; m <= toMin(rules.close); m += SLOT) {
      const t = fromMin(m);
      if (!endProblem(start, t)) slots.push(t);
    }
    return slots;
  }

  function fillSelect(select, times) {
    const keep = select.value;
    // A time that was set from code (editing an existing meeting) stays selectable
    // even if it isn't on the 30-minute grid; the server still has the final say.
    if (select.dataset.keep && !times.includes(select.dataset.keep)) {
      times = times.concat(select.dataset.keep).sort();
    }
    select.textContent = '';
    const blank = document.createElement('option');
    blank.value = '';
    blank.textContent = select.dataset.blank || 'Select a time';
    select.appendChild(blank);
    times.forEach((t) => {
      const opt = document.createElement('option');
      opt.value = t;
      opt.textContent = clock(t);
      select.appendChild(opt);
    });
    select.value = times.includes(keep) ? keep : '';
  }

  /** Turns <input type="time" data-rule=...> into a <select> that carries over its name, id and required flag. */
  function toSelect(input) {
    const select = document.createElement('select');
    ['id', 'name'].forEach((a) => input.getAttribute(a) && select.setAttribute(a, input.getAttribute(a)));
    select.required = input.required;
    select.dataset.rule = input.dataset.rule;
    select.dataset.blank = input.dataset.blank || '';
    input.replaceWith(select);
    return select;
  }

  /* ---------- the date can be picked, not typed ---------- */

  /**
   * Makes the visible date box pick-only: clicking opens the calendar, but typing, pasting, cutting,
   * dragging text in, and Backspace/Delete do nothing. The box is not made read-only, because browsers
   * skip the "required" check on read-only fields (an empty date would be submitted); this stops the
   * editing instead and leaves that check working. The server checks the date again anyway.
   */
  function lockTyping(picker) {
    const box = picker.altInput;
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
  }

  /* ---------- per-form setup ---------- */

  function revalidate(form) {
    const dateEl = form.querySelector('[data-rule="date"]');
    const timeEl = form.querySelector('[data-rule="time"]');
    const endEl = form.querySelector('[data-rule="end"]');
    const date = dateEl ? dateEl.value : '';
    const alt = dateEl && dateEl._flatpickr ? dateEl._flatpickr.altInput : dateEl;

    if (alt) alt.setCustomValidity(dateProblem(date));
    if (timeEl) timeEl.setCustomValidity(timeProblem(date, timeEl.value));
    if (endEl) endEl.setCustomValidity(endProblem(timeEl ? timeEl.value : '', endEl.value));
  }

  function refreshTimes(form) {
    const dateEl = form.querySelector('[data-rule="date"]');
    const timeEl = form.querySelector('[data-rule="time"]');
    const endEl = form.querySelector('[data-rule="end"]');

    if (timeEl && timeEl.tagName === 'SELECT') fillSelect(timeEl, startSlots(dateEl ? dateEl.value : ''));
    if (endEl && endEl.tagName === 'SELECT') fillSelect(endEl, endSlots(timeEl ? timeEl.value : ''));
    revalidate(form);
  }

  function setup(form) {
    const dateEl = form.querySelector('[data-rule="date"]');
    const hasCalendar = typeof window.flatpickr === 'function';

    if (dateEl) {
      dateEl.min = rules.today;
      if (hasCalendar) {
        window.flatpickr(dateEl, {
          dateFormat: 'Y-m-d',
          altInput: true,
          altFormat: 'D, F j, Y',
          minDate: rules.today,
          disableMobile: true, // the phone's own picker can't grey out days
          // The visible box must not be read-only: browsers skip "required" checks on read-only
          // fields, so an empty date would be submitted and bounce back from the server.
          allowInput: true,
          disable: [(d) => dateProblem(isoOf(d)) !== ''],
          onChange: () => refreshTimes(form),
        });
        dateEl._flatpickr.altInput.placeholder = 'Choose a working day';
        dateEl._flatpickr.altInput.required = dateEl.required; // the hidden real input can't enforce it
        lockTyping(dateEl._flatpickr);
      }
    }

    // Time boxes become dropdowns of valid slots only.
    form.querySelectorAll('[data-rule="time"], [data-rule="end"]').forEach((el) => {
      if (el.tagName === 'INPUT') toSelect(el);
    });
    const timeEl = form.querySelector('[data-rule="time"]');
    const endEl = form.querySelector('[data-rule="end"]');
    if (timeEl) timeEl.addEventListener('change', () => refreshTimes(form));
    if (endEl) endEl.addEventListener('change', () => revalidate(form));
    if (dateEl) dateEl.addEventListener('change', () => refreshTimes(form));

    // One hint line telling people when bookings are possible.
    const fields = form.querySelectorAll('[data-rule]');
    const last = fields[fields.length - 1];
    if (last && !form.querySelector('.schedule-hint')) {
      const hint = document.createElement('small');
      hint.className = 'row-note schedule-hint';
      hint.style.display = 'block';
      hint.style.marginTop = '4px';
      hint.textContent = `Open: ${rules.description}.`;
      (last.closest('.form-group') || last.parentNode).appendChild(hint);
    }

    // Runs right before the browser resets the fields, so code that calls form.reset()
    // and then fills values in (prefilling, editing) is not undone afterwards.
    form.addEventListener('reset', () => {
      if (dateEl && dateEl._flatpickr) dateEl._flatpickr.clear();
      refreshTimes(form);
    });
    refreshTimes(form);
  }

  /**
   * Sets a field's value from code (e.g. when editing an existing meeting).
   * Works for the calendar and the dropdowns; an existing time that isn't on the
   * 30-minute grid is added as an extra option so it isn't lost.
   */
  function setValue(el, value) {
    if (el._flatpickr) {
      el._flatpickr.setDate(value || null, true);
      return;
    }
    if (el.tagName === 'SELECT') {
      el.dataset.keep = value || '';
      if (value && !Array.from(el.options).some((o) => o.value === value)) {
        const opt = document.createElement('option');
        opt.value = value;
        opt.textContent = clock(value);
        el.appendChild(opt);
      }
    }
    el.value = value || '';
    el.dispatchEvent(new Event('change', { bubbles: true }));
  }

  document.querySelectorAll('form[data-schedule-rules]').forEach(setup);
  /** Is this date (and time of day on that date) bookable? Used to prefill a form from a preference. */
  function canUse(date, time) {
    const dateOk = !dateProblem(date);
    return { date: dateOk, time: dateOk && !!time && startSlots(date).includes(String(time).slice(0, 5)) };
  }

  window.ScheduleRules = { revalidate, setValue, refreshTimes, canUse };
})();
