// Public homepage hero carousel: auto-fades between slides, with
// click-to-navigate left/right arrows and dot indicators.

document.addEventListener('DOMContentLoaded', function () {
  const carousel = document.getElementById('heroCarousel');
  if (!carousel) return;

  const slides = carousel.querySelectorAll('.carousel-slide');
  const dots = document.querySelectorAll('#carouselDots .carousel-dot');
  const prevBtn = document.getElementById('carouselPrev');
  const nextBtn = document.getElementById('carouselNext');

  if (slides.length <= 1) return;

  const AUTO_ADVANCE_MS = 6000;
  let current = 0;
  let timer = null;

  function showSlide(index) {
    slides[current].classList.remove('active');
    dots[current] && dots[current].classList.remove('active');

    current = (index + slides.length) % slides.length;

    slides[current].classList.add('active');
    dots[current] && dots[current].classList.add('active');
  }

  function next() {
    showSlide(current + 1);
  }

  function prev() {
    showSlide(current - 1);
  }

  function restartAutoAdvance() {
    clearInterval(timer);
    timer = setInterval(next, AUTO_ADVANCE_MS);
  }

  prevBtn.addEventListener('click', function () {
    prev();
    restartAutoAdvance();
  });

  nextBtn.addEventListener('click', function () {
    next();
    restartAutoAdvance();
  });

  dots.forEach((dot) => {
    dot.addEventListener('click', function () {
      showSlide(parseInt(dot.dataset.index, 10));
      restartAutoAdvance();
    });
  });

  restartAutoAdvance();
});
