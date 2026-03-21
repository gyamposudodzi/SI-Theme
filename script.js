document.addEventListener("DOMContentLoaded", function () {
  const body = document.body;
  const navToggle = document.querySelector(".nav-toggle");
  const searchToggle = document.querySelector(".search-toggle");

  if (navToggle) {
    navToggle.addEventListener("click", function () {
      const expanded = navToggle.getAttribute("aria-expanded") === "true";
      navToggle.setAttribute("aria-expanded", String(!expanded));
      body.classList.toggle("nav-open", !expanded);
    });
  }

  if (searchToggle) {
    searchToggle.addEventListener("click", function () {
      const expanded = searchToggle.getAttribute("aria-expanded") === "true";
      searchToggle.setAttribute("aria-expanded", String(!expanded));
      body.classList.toggle("search-open", !expanded);
    });
  }
});
