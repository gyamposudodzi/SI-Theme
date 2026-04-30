(function () {
  var banner = document.querySelector("[data-cookie-banner]");
  var modal = document.querySelector("[data-cookie-modal]");
  var modalTriggers = document.querySelectorAll("[data-cookie-modal-open]");
  var modalClosers = document.querySelectorAll("[data-cookie-modal-close]");
  var statusValue = document.querySelector("[data-cookie-status]");
  var optionalToggle = document.querySelector("[data-cookie-optional-toggle]");
  var savePreferencesButton = document.querySelector("[data-cookie-save-preferences]");
  var resetButtons = document.querySelectorAll("[data-cookie-reset]");

  if (!banner) {
    return;
  }

  var consentVersion = "v2";
  var storageKey = "nwmCookieConsent_" + consentVersion;
  var cookieName = "nwm_cookie_consent_" + consentVersion;
  var expiryDays = 180;

  function readCookie(name) {
    var escapedName = name.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    var match = document.cookie.match(new RegExp("(?:^|; )" + escapedName + "=([^;]*)"));
    return match ? decodeURIComponent(match[1]) : "";
  }

  function readStoredConsent() {
    try {
      var storedValue = window.localStorage.getItem(storageKey) || "";

      if (storedValue) {
        return storedValue;
      }
    } catch (error) {
      // Ignore storage issues and fall back to cookies.
    }

    return readCookie(cookieName);
  }

  function writeStoredConsent(value) {
    try {
      window.localStorage.setItem(storageKey, value);
    } catch (error) {
      // Ignore storage failures and still fall back to a cookie.
    }

    var expires = new Date();
    expires.setTime(expires.getTime() + expiryDays * 24 * 60 * 60 * 1000);
    document.cookie =
      cookieName +
      "=" +
      encodeURIComponent(value) +
      "; expires=" +
      expires.toUTCString() +
      "; path=/; SameSite=Lax";
  }

  function applyConsentState(value) {
    document.documentElement.setAttribute("data-nwm-cookie-consent", value || "unset");
    document.body.classList.toggle("nwm-cookie-consent-set", Boolean(value));

    if (statusValue) {
      statusValue.textContent = getConsentLabel(value);
    }

    if (optionalToggle) {
      optionalToggle.checked = value === "accepted";
    }
  }

  function getConsentLabel(value) {
    if (value === "accepted") {
      return "Optional cookies accepted";
    }

    if (value === "necessary") {
      return "Necessary cookies only";
    }

    return "Not set yet";
  }

  function closeBanner() {
    banner.setAttribute("hidden", "");
    banner.setAttribute("aria-hidden", "true");
    document.body.classList.remove("nwm-cookie-banner-visible");
  }

  function openBanner() {
    banner.removeAttribute("hidden");
    banner.setAttribute("aria-hidden", "false");
    document.body.classList.add("nwm-cookie-banner-visible");
  }

  function saveConsent(value) {
    writeStoredConsent(value);
    applyConsentState(value);
    closeBanner();
    closeModal();

    document.dispatchEvent(
      new CustomEvent("nwm:cookie-consent-updated", {
        detail: {
          consent: value
        }
      })
    );
  }

  function clearConsent() {
    try {
      window.localStorage.removeItem(storageKey);
    } catch (error) {
      // Ignore storage failures.
    }

    document.cookie =
      cookieName +
      "=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; SameSite=Lax";

    applyConsentState("");
    openBanner();
    closeModal();
  }

  function openModal() {
    if (!modal) {
      return;
    }

    modal.removeAttribute("hidden");
    modal.setAttribute("aria-hidden", "false");
    document.body.classList.add("nwm-cookie-modal-open");
  }

  function closeModal() {
    if (!modal) {
      return;
    }

    modal.setAttribute("hidden", "");
    modal.setAttribute("aria-hidden", "true");
    document.body.classList.remove("nwm-cookie-modal-open");
  }

  var storedConsent = readStoredConsent();
  applyConsentState(storedConsent);

  if (!storedConsent) {
    openBanner();
  } else {
    closeBanner();
  }

  banner.addEventListener("click", function (event) {
    var button = event.target.closest("[data-cookie-consent]");

    if (!button) {
      return;
    }

    saveConsent(button.getAttribute("data-cookie-consent"));
  });

  modalTriggers.forEach(function (trigger) {
    trigger.addEventListener("click", function () {
      openModal();
    });
  });

  modalClosers.forEach(function (closer) {
    closer.addEventListener("click", function () {
      closeModal();
    });
  });

  if (modal) {
    modal.addEventListener("click", function (event) {
      var consentButton = event.target.closest("[data-cookie-consent]");

      if (consentButton) {
        saveConsent(consentButton.getAttribute("data-cookie-consent"));
      }
    });
  }

  if (savePreferencesButton) {
    savePreferencesButton.addEventListener("click", function () {
      saveConsent(optionalToggle && optionalToggle.checked ? "accepted" : "necessary");
    });
  }

  resetButtons.forEach(function (button) {
    button.addEventListener("click", function () {
      clearConsent();
    });
  });

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
      closeModal();
    }
  });
})();
