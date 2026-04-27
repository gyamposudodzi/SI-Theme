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
    const input = document.createElement("textarea");
    input.value = text;
    input.setAttribute("readonly", "readonly");
    input.style.position = "fixed";
    input.style.top = "0";
    input.style.left = "-9999px";
    input.style.opacity = "0";
    document.body.appendChild(input);
    input.focus();
    input.select();
    input.setSelectionRange(0, input.value.length);

    let copied = false;

    try {
      copied = document.execCommand("copy");
    } catch (error) {
      copied = false;
    }

    document.body.removeChild(input);
    return copied;
  }

  function markCopied(link) {
    const originalLabel = link.getAttribute("aria-label") || "Copy link";
    const originalText = link.textContent;

    link.setAttribute("aria-label", "Link copied");
    if (link.tagName === "BUTTON") {
      link.textContent = "Copied";
    }

    window.setTimeout(function () {
      link.setAttribute("aria-label", originalLabel);
      if (link.tagName === "BUTTON") {
        link.textContent = originalText;
      }
    }, 1600);
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

      const modal = link.closest("[data-share-modal]");
      const input = modal ? modal.querySelector("[data-copy-input]") : null;
      const url = link.getAttribute("data-copy-url") || (input ? input.value : "");
      if (!url) {
        return;
      }

      if (input) {
        input.focus();
        input.select();
        input.setSelectionRange(0, input.value.length);
      }

      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(function () {
          markCopied(link);
        }).catch(function () {
          if (fallbackCopy(url)) {
            markCopied(link);
          }
        });
        return;
      }

      if (fallbackCopy(url)) {
        markCopied(link);
      }
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
