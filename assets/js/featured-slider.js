document.addEventListener("DOMContentLoaded", function () {
  const sliders = document.querySelectorAll("[data-featured-slider]");
  if (!sliders.length) {
    return;
  }

  sliders.forEach(function (slider) {
    const slides = Array.from(slider.querySelectorAll("[data-featured-slide]"));
    const dots = Array.from(slider.querySelectorAll("[data-featured-dot]"));
    const prev = slider.querySelector("[data-featured-prev]");
    const next = slider.querySelector("[data-featured-next]");

    if (!slides.length) {
      return;
    }

    let index = slides.findIndex(function (slide) {
      return slide.classList.contains("is-active");
    });

    if (index < 0) {
      index = 0;
    }

    function renderFeaturedSlider(nextIndex) {
      index = (nextIndex + slides.length) % slides.length;

      slides.forEach(function (slide, slideIndex) {
        slide.classList.toggle("is-active", slideIndex === index);
      });

      dots.forEach(function (dot, dotIndex) {
        dot.classList.toggle("is-active", dotIndex === index);
      });
    }

    if (prev) {
      prev.addEventListener("click", function () {
        renderFeaturedSlider(index - 1);
      });
    }

    if (next) {
      next.addEventListener("click", function () {
        renderFeaturedSlider(index + 1);
      });
    }

    dots.forEach(function (dot, dotIndex) {
      dot.addEventListener("click", function () {
        renderFeaturedSlider(dotIndex);
      });
    });
  });
});
