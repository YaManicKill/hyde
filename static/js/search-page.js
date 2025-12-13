// Search page rendering of episode cards
(function () {
  const input = document.getElementById("site-search");
  const resultsEl = document.getElementById("search-results");
  let fuse = null;
  let data = [];

  function viewEpisode(item) {
    const post = document.createElement("div");
    post.className = "post";
    const h1 = document.createElement("h1");
    h1.className = "post-title";
    const a = document.createElement("a");
    a.href = item.url;
    a.textContent = item.title;
    h1.appendChild(a);
    const dateSpan = document.createElement("span");
    dateSpan.className = "post-date";
    dateSpan.textContent = item.date || "";
    const infoSpan = document.createElement("span");
    infoSpan.className = "post-date";
    // We may not have year/seasonname/episode in index; show tags if present
    if (item.tags && item.tags.length) {
      infoSpan.textContent = `[${item.tags.join(", ")}]`;
    } else {
      infoSpan.textContent = "";
    }
    const desc = document.createElement("p");
    desc.textContent = item.description || "";
    post.appendChild(h1);
    post.appendChild(dateSpan);
    post.appendChild(infoSpan);
    post.appendChild(desc);
    return post;
  }

  function renderResults(items) {
    if (!resultsEl) return;
    resultsEl.innerHTML = "";
    if (!items || items.length === 0) {
      const empty = document.createElement("p");
      empty.textContent = "No results.";
      resultsEl.appendChild(empty);
      return;
    }
    const frag = document.createDocumentFragment();
    items.slice(0, 30).forEach(({ item }) => {
      frag.appendChild(viewEpisode(item));
    });
    resultsEl.appendChild(frag);
  }

  async function init() {
    if (!input) return;
    try {
      const res = await fetch("/index.json");
      data = await res.json();
    } catch (e) {
      console.error("Search index load failed", e);
      return;
    }
    try {
      if (!window.Fuse) {
        const script = document.createElement("script");
        script.src =
          "https://cdn.jsdelivr.net/npm/fuse.js@6.6.2/dist/fuse.min.js";
        document.head.appendChild(script);
        await new Promise((resolve) => {
          script.onload = resolve;
        });
      }
      fuse = new Fuse(data, {
        keys: ["title", "description", "tags", "content"],
        threshold: 0.4,
        ignoreLocation: true,
        includeScore: true,
        minMatchCharLength: 2,
      });
    } catch (e) {
      console.error("Fuse load failed", e);
      return;
    }

    input.addEventListener("input", function () {
      const q = input.value.trim();
      if (!q) {
        renderResults([]);
        return;
      }
      const res = fuse.search(q);
      renderResults(res);
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
