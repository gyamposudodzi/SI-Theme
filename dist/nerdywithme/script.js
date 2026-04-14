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
  const menuSlot = document.querySelector("[data-mega-menu-slot]");
  const menuTemplates = document.querySelectorAll(".mega-panel__menu-templates .mega-panel__links");

  function renderDrawerMenu() {
    if (!menuSlot || !menuTemplates.length) {
      return;
    }

    const isMobile = window.matchMedia("(max-width: 820px)").matches;
    const desiredIndex = isMobile ? 1 : 0;
    const template = menuTemplates[desiredIndex] || menuTemplates[0];

    while (menuSlot.firstChild) {
      menuSlot.removeChild(menuSlot.firstChild);
    }

    if (template) {
      menuSlot.appendChild(template.cloneNode(true));
    }
  }

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

  renderDrawerMenu();

  if (navToggle) {
    navToggle.addEventListener("click", function () {
      renderDrawerMenu();
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

  if (navToggle || searchToggle) {
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

    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape" && body.classList.contains("nav-open")) {
        closeNav();
      }
      if (event.key === "Escape" && body.classList.contains("search-open")) {
        closeSearch();
      }
    });
  }

  if (searchClose) {
    searchClose.addEventListener("click", function () {
      closeSearch();
    });
  }

  if (menuSlot || navToggle || searchToggle) {
    window.addEventListener("resize", function () {
      renderDrawerMenu();
      if (window.innerWidth > 820 && body.classList.contains("nav-open")) {
        closeNav();
      }

      if (window.innerWidth <= 560 && body.classList.contains("search-open")) {
        if (searchPanel) {
          searchPanel.setAttribute("aria-hidden", "false");
        }
      }
    });
  }

  const sliders = document.querySelectorAll("[data-featured-slider]");
  if (sliders.length) {
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
  }

  const readingBar = document.querySelector("[data-reading-bar]");
  if (readingBar && body.classList.contains("single-post")) {
    const readingArticle = document.querySelector("[data-reading-article]");
    const readingProgress = readingBar.querySelector("[data-reading-progress]");
    let lastY = window.scrollY || 0;
    let ticking = false;
    const threshold = 140;

    function updateReadingBar() {
      const currentY = window.scrollY || 0;
      const scrollingDown = currentY > lastY + 4;
      const scrollingUp = currentY < lastY - 4;

      if (currentY <= threshold) {
        body.classList.remove("is-reading-down");
      } else if (scrollingDown) {
        body.classList.add("is-reading-down");
      } else if (scrollingUp) {
        body.classList.remove("is-reading-down");
      }

      if (readingArticle && readingProgress) {
        const articleTop = readingArticle.offsetTop;
        const articleHeight = readingArticle.offsetHeight;
        const viewportHeight = window.innerHeight || document.documentElement.clientHeight || 1;
        const progressStart = articleTop;
        const progressDistance = Math.max(articleHeight - viewportHeight, 1);
        const rawProgress = ((currentY - progressStart) / progressDistance) * 100;
        const clampedProgress = Math.max(0, Math.min(100, rawProgress));
        readingProgress.style.width = clampedProgress + "%";
      }

      lastY = currentY;
      ticking = false;
    }

    window.addEventListener(
      "scroll",
      function () {
        if (!ticking) {
          window.requestAnimationFrame(updateReadingBar);
          ticking = true;
        }
      },
      { passive: true }
    );

    updateReadingBar();
  }

  const toc = document.querySelector("[data-toc]");
  if (toc) {
    const tocLinks = Array.from(toc.querySelectorAll('a[href^="#"]'));
    const tocMap = new Map();
    tocLinks.forEach(function (link) {
      const id = link.getAttribute("href").slice(1);
      const target = document.getElementById(id);
      if (target) {
        tocMap.set(target, link);
      }
    });

    if (tocMap.size) {
      const observer = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting && tocMap.has(entry.target)) {
              tocLinks.forEach(function (link) {
                link.classList.remove("is-active");
              });
              tocMap.get(entry.target).classList.add("is-active");
            }
          });
        },
        {
          rootMargin: "-20% 0px -65% 0px",
          threshold: 0.1,
        }
      );

      tocMap.forEach(function (_link, heading) {
        observer.observe(heading);
      });
    }
  }

  const stickySidebar = document.querySelector(".single-layout .sidebar");
  if (stickySidebar) {
    stickySidebar.classList.remove("sidebar-stop");
    stickySidebar.style.removeProperty("--nwm-sidebar-stop-top");
  }
});
