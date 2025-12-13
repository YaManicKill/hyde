// Dark mode toggle functionality
(function () {
  const DARK_MODE_KEY = "theme-mode";
  const html = document.documentElement;

  // Check for saved preference or system preference
  function initTheme() {
    let theme = localStorage.getItem(DARK_MODE_KEY);

    if (!theme) {
      // Check system preference
      if (
        window.matchMedia &&
        window.matchMedia("(prefers-color-scheme: dark)").matches
      ) {
        theme = "dark";
      } else {
        theme = "light";
      }
    }

    applyTheme(theme);
  }

  // Apply theme to document
  function applyTheme(theme) {
    const checkbox = document.getElementById("theme-toggle-btn");
    if (theme === "dark") {
      html.classList.add("dark-mode");
      html.classList.remove("light-mode");
      localStorage.setItem(DARK_MODE_KEY, "dark");
      if (checkbox) checkbox.checked = true;
    } else {
      html.classList.remove("dark-mode");
      html.classList.add("light-mode");
      localStorage.setItem(DARK_MODE_KEY, "light");
      if (checkbox) checkbox.checked = false;
    }
  }

  // Toggle theme
  function toggleTheme() {
    const isDark = html.classList.contains("dark-mode");
    applyTheme(isDark ? "light" : "dark");
  }

  // Initialize on load
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      initTheme();
      const checkbox = document.getElementById("theme-toggle-btn");
      if (checkbox) {
        checkbox.addEventListener("change", toggleTheme);
      }
    });
  } else {
    initTheme();
    const checkbox = document.getElementById("theme-toggle-btn");
    if (checkbox) {
      checkbox.addEventListener("change", toggleTheme);
    }
  }

  // Listen for system theme changes
  if (window.matchMedia) {
    window
      .matchMedia("(prefers-color-scheme: dark)")
      .addEventListener("change", (e) => {
        if (!localStorage.getItem(DARK_MODE_KEY)) {
          applyTheme(e.matches ? "dark" : "light");
        }
      });
  }
})();
