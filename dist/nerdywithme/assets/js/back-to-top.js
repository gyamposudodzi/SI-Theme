(function () {
  var button = document.querySelector("[data-back-to-top]");

  if (!button) {
    return;
  }

  var revealAfter = 360;

  function syncVisibility() {
    var shouldShow = window.scrollY > revealAfter;
    button.hidden = !shouldShow;
    button.classList.toggle("is-visible", shouldShow);
  }

  button.addEventListener("click", function () {
    window.scrollTo({
      top: 0,
      behavior: "smooth"
    });
  });

  window.addEventListener("scroll", syncVisibility, { passive: true });
  syncVisibility();
})();
