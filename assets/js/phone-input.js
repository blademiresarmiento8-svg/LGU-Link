// Phone number boxes (type="tel") accept only what a mobile number is made of: digits, "+", spaces,
// dashes and brackets. Letters are dropped as they are typed or pasted. The server checks the number again.
document.addEventListener('input', function (event) {
  const input = event.target;
  if (!(input instanceof HTMLInputElement) || input.type !== 'tel') return;
  const cleaned = input.value.replace(/[^\d+\s\-()]/g, '');
  if (cleaned !== input.value) input.value = cleaned;
});
