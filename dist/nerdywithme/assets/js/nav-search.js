document.addEventListener("DOMContentLoaded", function () {
  const body = document.body;
  const root = document.documentElement;
  const navToggle = document.querySelector(".nav-toggle");
  const megaPanel = document.querySelector(".mega-panel");
  const megaClose = megaPanel ? megaPanel.querySelector(".mega-panel__close") : null;
  const menuSlot = document.querySelector("[data-mega-menu-slot]");
  const menuTemplates = document.querySelectorAll(".mega-panel__menu-templates .mega-panel__links");
  let lockedScrollY = 0;

  function lockPageScroll() {
    if (window.matchMedia("(max-width: 1100px)").matches || body.classList.contains("nav-scroll-locked")) {
      return;
    }

    lockedScrollY = window.scrollY || window.pageYOffset || 0;
    body.classList.add("nav-scroll-locked");
    body.style.position = "fixed";
    body.style.top = "-" + lockedScrollY + "px";
    body.style.left = "0";
    body.style.right = "0";
    body.style.width = "100%";
    body.style.overflow = "hidden";
    root.style.scrollBehavior = "auto";
  }

  function unlockPageScroll() {
    if (!body.classList.contains("nav-scroll-locked")) {
      return;
    }

    body.classList.remove("nav-scroll-locked");
    body.style.position = "";
    body.style.top = "";
    body.style.left = "";
    body.style.right = "";
    body.style.width = "";
    body.style.overflow = "";
    root.style.scrollBehavior = "";
    window.scrollTo(0, lockedScrollY);
  }

  function renderDrawerMenu() {
    if (!menuSlot || !menuTemplates.length) {
      return;
    }

    const isCompactDrawer = window.matchMedia("(max-width: 1100px)").matches;
    const desiredIndex = isCompactDrawer ? 1 : 0;
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
    unlockPageScroll();
    if (megaPanel) {
      megaPanel.setAttribute("aria-hidden", "true");
    }
  }

  renderDrawerMenu();

  if (navToggle) {
    navToggle.addEventListener("click", function () {
      renderDrawerMenu();
      const expanded = navToggle.getAttribute("aria-expanded") === "true";
      navToggle.setAttribute("aria-expanded", String(!expanded));
      body.classList.toggle("nav-open", !expanded);
      if (!expanded) {
        lockPageScroll();
      } else {
        unlockPageScroll();
      }
      if (megaPanel) {
        megaPanel.setAttribute("aria-hidden", String(expanded));
      }
    });
  }

  if (megaClose) {
    megaClose.addEventListener("click", function () {
      closeNav();
    });
  }

  if (navToggle) {
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
    });

    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape" && body.classList.contains("nav-open")) {
        closeNav();
      }
    });
  }

  if (menuSlot || navToggle) {
    window.addEventListener("resize", function () {
      renderDrawerMenu();
      if (window.innerWidth > 1100 && body.classList.contains("nav-open")) {
        lockPageScroll();
      } else if (window.innerWidth <= 1100) {
        unlockPageScroll();
      }
    });
  }
});
