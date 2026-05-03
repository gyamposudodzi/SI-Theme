document.addEventListener("DOMContentLoaded", function () {
  const mobileBreakpoint = 560;
  const titleNodes = Array.from(document.querySelectorAll("[data-mobile-trim-title]"));

  if (!titleNodes.length) {
    return;
  }

  function trimText(value, limit) {
    const text = String(value || "").trim();

    if (!text || text.length <= limit) {
      return text;
    }

    return text.slice(0, Math.max(0, limit - 3)).trimEnd() + "...";
  }

  function syncTitles() {
    const shouldTrim = window.innerWidth <= mobileBreakpoint;

    titleNodes.forEach(function (node) {
      const fullTitle = node.getAttribute("data-full-title") || node.textContent || "";
      const limit = Number(node.getAttribute("data-mobile-trim-limit") || 65);
      node.textContent = shouldTrim ? trimText(fullTitle, limit) : fullTitle;
    });
  }

  window.addEventListener("resize", syncTitles);
  syncTitles();
});
