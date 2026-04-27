document.addEventListener("DOMContentLoaded", function () {
  const stickySidebar = document.querySelector(".single-layout .sidebar");
  if (stickySidebar) {
    stickySidebar.classList.remove("sidebar-stop");
    stickySidebar.style.removeProperty("--nwm-sidebar-stop-top");
  }

  const copyLinks = document.querySelectorAll("[data-copy-link]");
  const shareOpeners = document.querySelectorAll("[data-share-open]");
  const shareModals = document.querySelectorAll("[data-share-modal]");
  const nativeShareButtons = document.querySelectorAll("[data-native-share]");
  let activeModal = null;
  let activeTrigger = null;

  function fallbackCopy(text) {
    const input = document.createElement("input");
    input.type = "text";
    input.value = text;
    input.setAttribute("readonly", "readonly");
    input.style.position = "absolute";
    input.style.left = "-9999px";
    document.body.appendChild(input);
    input.select();
    document.execCommand("copy");
    document.body.removeChild(input);
  }

  function closeShareModal(modal) {
    if (!modal) {
      return;
    }

    modal.hidden = true;
    modal.setAttribute("aria-hidden", "true");
    document.body.classList.remove("share-modal-open");

    if (activeTrigger) {
      activeTrigger.focus();
    }

    activeModal = null;
    activeTrigger = null;
  }

  function openShareModal(trigger) {
    const modalId = trigger.getAttribute("aria-controls");
    const modal = modalId ? document.getElementById(modalId) : null;

    if (!modal) {
      return;
    }

    activeModal = modal;
    activeTrigger = trigger;
    modal.hidden = false;
    modal.setAttribute("aria-hidden", "false");
    document.body.classList.add("share-modal-open");

    const firstFocusable = modal.querySelector("[data-copy-input], [data-native-share], a, button");
    if (firstFocusable) {
      window.setTimeout(function () {
        firstFocusable.focus();
      }, 40);
    }
  }

  copyLinks.forEach(function (link) {
    link.addEventListener("click", function (event) {
      event.preventDefault();

      const url = link.getAttribute("data-copy-url");
      if (!url) {
        return;
      }

      const originalLabel = link.getAttribute("aria-label") || "Copy link";

      const markCopied = function () {
        link.setAttribute("aria-label", "Link copied");
        window.setTimeout(function () {
          link.setAttribute("aria-label", originalLabel);
        }, 1600);
      };

      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(markCopied).catch(function () {
          fallbackCopy(url);
          markCopied();
        });
        return;
      }

      fallbackCopy(url);
      markCopied();
    });
  });

  shareOpeners.forEach(function (trigger) {
    trigger.addEventListener("click", function () {
      openShareModal(trigger);
    });
  });

  shareModals.forEach(function (modal) {
    modal.querySelectorAll("[data-share-close]").forEach(function (closer) {
      closer.addEventListener("click", function () {
        closeShareModal(modal);
      });
    });
  });

  nativeShareButtons.forEach(function (button) {
    if (navigator.share) {
      button.hidden = false;
    }

    button.addEventListener("click", function () {
      if (!navigator.share) {
        return;
      }

      navigator.share({
        title: button.getAttribute("data-share-title") || document.title,
        url: button.getAttribute("data-share-url") || window.location.href
      }).catch(function () {
        return null;
      });
    });
  });

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape" && activeModal) {
      closeShareModal(activeModal);
    }
  });
});
