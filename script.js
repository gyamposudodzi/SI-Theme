document.addEventListener("DOMContentLoaded", function () {
  const body = document.body;
  const navToggle = document.querySelector(".nav-toggle");
  const searchToggle = document.querySelector(".search-toggle");
  const megaPanel = document.querySelector(".mega-panel");
  const megaClose = megaPanel ? megaPanel.querySelector(".mega-panel__close") : null;
  const searchPanel = document.querySelector(".search-panel");
  const searchDialog = searchPanel ? searchPanel.querySelector(".search-panel__dialog") : null;
  const searchClose = searchPanel ? searchPanel.querySelector(".search-panel__close") : null;
  const searchInput = searchPanel ? searchPanel.querySelector(".search-field") : null;

  function closeNav() {
    if (navToggle) {
      navToggle.setAttribute("aria-expanded", "false");
    }
    body.classList.remove("nav-open");
    if (megaPanel) {
      megaPanel.setAttribute("aria-hidden", "true");
    }
  }

  function closeSearch() {
    if (searchToggle) {
      searchToggle.setAttribute("aria-expanded", "false");
    }
    body.classList.remove("search-open");
    if (searchPanel) {
      searchPanel.setAttribute("aria-hidden", "true");
    }
  }

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

  if (megaClose) {
    megaClose.addEventListener("click", function () {
      closeNav();
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
      if (!expanded && searchInput) {
        window.setTimeout(function () {
          searchInput.focus();
        }, 40);
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
      closeNav();
    }

    if (
      body.classList.contains("search-open") &&
      searchToggle &&
      searchPanel &&
      searchDialog &&
      !searchDialog.contains(target) &&
      !searchToggle.contains(target)
    ) {
      closeSearch();
    }
  });

  if (searchClose) {
    searchClose.addEventListener("click", function () {
      closeSearch();
    });
  }

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape" && body.classList.contains("nav-open")) {
      closeNav();
    }
    if (event.key === "Escape" && body.classList.contains("search-open")) {
      closeSearch();
    }
  });

  window.addEventListener("resize", function () {
    if (window.innerWidth > 820 && body.classList.contains("nav-open")) {
      closeNav();
    }

    if (window.innerWidth <= 560 && body.classList.contains("search-open")) {
      if (searchPanel) {
        searchPanel.setAttribute("aria-hidden", "false");
      }
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
