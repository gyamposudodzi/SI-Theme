document.addEventListener("DOMContentLoaded", function () {
  const body = document.body;
  const searchToggle = document.querySelector(".search-toggle");
  const searchPanel = document.querySelector(".search-panel");
  const searchDialog = searchPanel ? searchPanel.querySelector(".search-panel__dialog") : null;
  const searchClose = searchPanel ? searchPanel.querySelector(".search-panel__close") : null;
  const searchInput = searchPanel ? searchPanel.querySelector(".search-field") : null;
  const searchForm = searchPanel ? searchPanel.querySelector(".search-form") : null;
  const searchFilters = searchPanel ? searchPanel.querySelector(".search-panel__filters") : null;
  const searchSuggested = searchPanel ? searchPanel.querySelector(".search-panel__suggested") : null;
  const searchStatus = searchPanel ? searchPanel.querySelector("[data-search-status]") : null;
  const searchResults = searchPanel ? searchPanel.querySelector("[data-search-results]") : null;
  const navToggle = document.querySelector(".nav-toggle");
  const megaPanel = document.querySelector(".mega-panel");
  const config = window.nwmSearchModalConfig || {};

  if (!searchToggle || !searchPanel) {
    return;
  }

  let activeController = null;
  let debounceId = 0;
  let activeResultIndex = -1;
  const fallbackFiltersMarkup = searchFilters ? searchFilters.innerHTML : "";

  function closeSearch() {
    searchToggle.setAttribute("aria-expanded", "false");
    body.classList.remove("search-open");
    searchPanel.setAttribute("aria-hidden", "true");
  }

  function showDefaultState() {
    if (searchFilters) {
      searchFilters.hidden = false;
    }

    if (searchSuggested) {
      searchSuggested.hidden = false;
    }

    if (searchResults) {
      searchResults.hidden = true;
      searchResults.innerHTML = "";
    }

    if (searchStatus) {
      searchStatus.hidden = false;
      searchStatus.textContent =
        config.emptyLabel || "Start typing to search posts, categories, and tools.";
    }

    activeResultIndex = -1;
  }

  function showLiveState() {
    if (searchFilters) {
      searchFilters.hidden = true;
    }

    if (searchSuggested) {
      searchSuggested.hidden = true;
    }

    if (searchResults) {
      searchResults.hidden = false;
    }
  }

  function buildGroup(title, items) {
    if (!Array.isArray(items) || !items.length) {
      return "";
    }

    return (
      '<section class="search-panel__group">' +
      '<h3 class="search-panel__group-title">' +
      title +
      "</h3>" +
      '<div class="search-panel__group-list">' +
      items
        .map(function (item) {
          return (
            '<a class="search-panel__result" href="' +
            item.url +
            '">' +
            '<strong class="search-panel__result-title">' +
            item.title +
            "</strong>" +
            (item.meta
              ? '<span class="search-panel__result-meta">' + item.meta + "</span>"
              : "") +
            "</a>"
          );
        })
        .join("") +
      "</div>" +
      "</section>"
    );
  }

  function buildEmptyState(query) {
    const formAction = searchForm ? searchForm.getAttribute("action") || "/" : "/";
    const searchUrl = new URL(formAction, window.location.origin);
    searchUrl.searchParams.set("s", query);

    return (
      '<section class="search-panel__empty-state">' +
      '<p class="search-panel__empty-copy">' +
      (config.noResultsLabel || "No matching results yet. Try a broader keyword.") +
      "</p>" +
      '<div class="search-panel__empty-actions">' +
      '<a class="search-panel__empty-button" href="' +
      searchUrl.toString() +
      '">' +
      (config.viewAllLabel || "See full search results") +
      "</a>" +
      "</div>" +
      (fallbackFiltersMarkup
        ? '<div class="search-panel__empty-filters"><h3 class="search-panel__group-title">' +
          (config.exploreLabel || "Explore categories") +
          "</h3>" +
          '<div class="search-panel__filters search-panel__filters--recovery">' +
          fallbackFiltersMarkup +
          "</div></div>"
        : "") +
      "</section>"
    );
  }

  function renderResults(payload, query) {
    const posts = payload.posts || [];
    const categories = payload.categories || [];
    const tools = payload.tools || [];
    const hasResults = posts.length || categories.length || tools.length;

    showLiveState();

    if (searchStatus) {
      searchStatus.hidden = hasResults;
      searchStatus.textContent = hasResults
        ? ""
        : config.noResultsLabel || "No matching results yet. Try a broader keyword.";
    }

    if (!searchResults) {
      return;
    }

    if (!hasResults) {
      searchResults.hidden = false;
      searchResults.innerHTML = buildEmptyState(query);
      activeResultIndex = -1;
      return;
    }

    searchResults.innerHTML =
      buildGroup(config.postsLabel || "Posts", posts) +
      buildGroup(config.categoriesLabel || "Categories", categories) +
      buildGroup(config.toolsLabel || "Tools", tools);

    activeResultIndex = -1;
  }

  function getResultLinks() {
    return searchResults ? Array.from(searchResults.querySelectorAll(".search-panel__result")) : [];
  }

  function setActiveResult(index) {
    const links = getResultLinks();

    links.forEach(function (link, linkIndex) {
      link.classList.toggle("is-active", linkIndex === index);
    });

    activeResultIndex = index;

    if (links[index]) {
      links[index].focus();
      links[index].scrollIntoView({
        block: "nearest"
      });
    }
  }

  function fetchResults(query) {
    if (!searchResults || !searchStatus) {
      return;
    }

    if (activeController) {
      activeController.abort();
    }

    activeController = new AbortController();
    showLiveState();
    searchResults.hidden = true;
    searchResults.innerHTML = "";
    searchStatus.hidden = false;
    searchStatus.textContent = config.loadingLabel || "Searching...";

    const url = new URL(config.ajaxUrl || window.ajaxurl || "/wp-admin/admin-ajax.php", window.location.origin);
    url.searchParams.set("action", "nerdywithme_live_search");
    url.searchParams.set("nonce", config.nonce || "");
    url.searchParams.set("q", query);

    fetch(url.toString(), {
      method: "GET",
      credentials: "same-origin",
      signal: activeController.signal
    })
      .then(function (response) {
        if (!response.ok) {
          throw new Error("Request failed");
        }

        return response.json();
      })
      .then(function (data) {
        if (!data || !data.success) {
          throw new Error("Search failed");
        }

        renderResults(data.data || {}, query);
      })
      .catch(function (error) {
        if (error.name === "AbortError") {
          return;
        }

        showDefaultState();
      });
  }

  searchToggle.addEventListener("click", function () {
    const expanded = searchToggle.getAttribute("aria-expanded") === "true";
    searchToggle.setAttribute("aria-expanded", String(!expanded));
    body.classList.toggle("search-open", !expanded);
    searchPanel.setAttribute("aria-hidden", String(expanded));

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

  document.addEventListener("click", function (event) {
    const target = event.target;
    if (
      body.classList.contains("search-open") &&
      searchDialog &&
      !searchDialog.contains(target) &&
      !searchToggle.contains(target)
    ) {
      closeSearch();
    }
  });

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape" && body.classList.contains("search-open")) {
      closeSearch();
      return;
    }

    if (!body.classList.contains("search-open")) {
      return;
    }

    const links = getResultLinks();
    if (!links.length) {
      return;
    }

    if (event.key === "ArrowDown") {
      event.preventDefault();
      setActiveResult(Math.min(activeResultIndex + 1, links.length - 1));
    } else if (event.key === "ArrowUp") {
      event.preventDefault();
      if (activeResultIndex <= 0) {
        activeResultIndex = -1;
        searchInput?.focus();
        links.forEach(function (link) {
          link.classList.remove("is-active");
        });
      } else {
        setActiveResult(activeResultIndex - 1);
      }
    } else if (event.key === "Enter" && activeResultIndex >= 0 && links[activeResultIndex]) {
      event.preventDefault();
      window.location.href = links[activeResultIndex].href;
    }
  });

  if (searchClose) {
    searchClose.addEventListener("click", function () {
      closeSearch();
    });
  }

  if (searchInput) {
    searchInput.setAttribute("autocomplete", "off");

    searchInput.addEventListener("keydown", function (event) {
      if (event.key === "ArrowDown") {
        const links = getResultLinks();
        if (links.length) {
          event.preventDefault();
          setActiveResult(0);
        }
      }
    });

    searchInput.addEventListener("input", function () {
      const query = searchInput.value.trim();
      const minChars = Number(config.minChars || 2);

      window.clearTimeout(debounceId);

      if (query.length < minChars) {
        if (activeController) {
          activeController.abort();
        }
        showDefaultState();
        return;
      }

      debounceId = window.setTimeout(function () {
        fetchResults(query);
      }, 220);
    });
  }

  window.addEventListener("resize", function () {
    if (window.innerWidth <= 560 && body.classList.contains("search-open")) {
      searchPanel.setAttribute("aria-hidden", "false");
    }
  });

  showDefaultState();
});
