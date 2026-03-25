document.addEventListener("DOMContentLoaded", function () {
  const body = document.body;
  const navToggle = document.querySelector(".nav-toggle");
  const searchToggle = document.querySelector(".search-toggle");
  const megaPanel = document.querySelector(".mega-panel");
  const searchPanel = document.querySelector(".search-panel");

  if (navToggle) {
    navToggle.addEventListener("click", function () {
      const expanded = navToggle.getAttribute("aria-expanded") === "true";
      navToggle.setAttribute("aria-expanded", String(!expanded));
      body.classList.toggle("nav-open", !expanded);
      if (megaPanel) {
        megaPanel.setAttribute("aria-hidden", String(expanded));
      }
      if (!expanded && searchToggle) {
        searchToggle.setAttribute("aria-expanded", "false");
        body.classList.remove("search-open");
        if (searchPanel) {
          searchPanel.setAttribute("aria-hidden", "true");
        }
      }
    });
  }

  if (searchToggle) {
    searchToggle.addEventListener("click", function () {
      const expanded = searchToggle.getAttribute("aria-expanded") === "true";
      searchToggle.setAttribute("aria-expanded", String(!expanded));
      body.classList.toggle("search-open", !expanded);
      if (searchPanel) {
        searchPanel.setAttribute("aria-hidden", String(expanded));
      }
      if (!expanded && navToggle) {
        navToggle.setAttribute("aria-expanded", "false");
        body.classList.remove("nav-open");
        if (megaPanel) {
          megaPanel.setAttribute("aria-hidden", "true");
        }
      }
    });
  }

  document.addEventListener("click", function (event) {
    const target = event.target;
    if (
      body.classList.contains("nav-open") &&
      navToggle &&
      megaPanel &&
      !megaPanel.contains(target) &&
      !navToggle.contains(target)
    ) {
      navToggle.setAttribute("aria-expanded", "false");
      megaPanel.setAttribute("aria-hidden", "true");
      body.classList.remove("nav-open");
    }

    if (
      body.classList.contains("search-open") &&
      searchToggle &&
      searchPanel &&
      !searchPanel.contains(target) &&
      !searchToggle.contains(target)
    ) {
      searchToggle.setAttribute("aria-expanded", "false");
      searchPanel.setAttribute("aria-hidden", "true");
      body.classList.remove("search-open");
    }
  });

  document.querySelectorAll("[data-featured-slider]").forEach(function (slider) {
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
