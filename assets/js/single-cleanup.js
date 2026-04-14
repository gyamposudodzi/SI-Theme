document.addEventListener("DOMContentLoaded", function () {
  const stickySidebar = document.querySelector(".single-layout .sidebar");
  if (stickySidebar) {
    stickySidebar.classList.remove("sidebar-stop");
    stickySidebar.style.removeProperty("--nwm-sidebar-stop-top");
  }
});
