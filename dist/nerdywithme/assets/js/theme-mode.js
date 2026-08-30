(function () {
  var root = document.documentElement;
  var storageKey = "nwmTheme";
  var mediaQuery = window.matchMedia ? window.matchMedia("(prefers-color-scheme: dark)") : null;
  var toggles = document.querySelectorAll("[data-theme-toggle]");

  function readStoredTheme() {
    try {
      var stored = window.localStorage.getItem(storageKey) || "";
      return stored === "dark" || stored === "light" ? stored : "";
    } catch (error) {
      return "";
    }
  }

  function writeStoredTheme(theme) {
    try {
      window.localStorage.setItem(storageKey, theme);
    } catch (error) {
      // Keep the current theme for this visit if storage is unavailable.
    }
  }

  function updateToggle(theme) {
    var isDark = theme === "dark";

    toggles.forEach(function (toggle) {
      var label = toggle.querySelector("[data-theme-toggle-label]");
      var labelText = isDark ? "Switch to light mode" : "Switch to dark mode";

      toggle.setAttribute("aria-pressed", isDark ? "true" : "false");
      toggle.setAttribute("title", labelText);

      if (label) {
        label.textContent = labelText;
      }
    });
  }

  function applyTheme(theme, persist) {
    var nextTheme = theme === "dark" ? "dark" : "light";

    root.setAttribute("data-nwm-theme", nextTheme);
    updateToggle(nextTheme);

    if (persist) {
      writeStoredTheme(nextTheme);
    }
  }

  function getSystemTheme() {
    return mediaQuery && mediaQuery.matches ? "dark" : "light";
  }

  applyTheme(readStoredTheme() || root.getAttribute("data-nwm-theme") || getSystemTheme(), false);

  toggles.forEach(function (toggle) {
    toggle.addEventListener("click", function () {
      var currentTheme = root.getAttribute("data-nwm-theme");
      applyTheme(currentTheme === "dark" ? "light" : "dark", true);
    });
  });

  if (mediaQuery) {
    var handleSystemThemeChange = function () {
      if (!readStoredTheme()) {
        applyTheme(getSystemTheme(), false);
      }
    };

    if (mediaQuery.addEventListener) {
      mediaQuery.addEventListener("change", handleSystemThemeChange);
    } else if (mediaQuery.addListener) {
      mediaQuery.addListener(handleSystemThemeChange);
    }
  }
})();
