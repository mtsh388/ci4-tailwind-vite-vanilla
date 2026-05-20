export function initTheme() {
  const html = document.documentElement;

  const themeToggle = document.getElementById("theme-toggle");

  const themeText = document.getElementById("theme-text");

  if (!themeToggle) return;

  /*
    |--------------------------------------------------------------------------
    | APPLY THEME
    |--------------------------------------------------------------------------
    */
  const applyTheme = (theme) => {
    if (theme === "dark") {
      html.classList.add("dark");

      if (themeText) {
        themeText.innerText = "Light";
      }
    } else {
      html.classList.remove("dark");

      if (themeText) {
        themeText.innerText = "Dark";
      }
    }
  };

  /*
    |--------------------------------------------------------------------------
    | INIT
    |--------------------------------------------------------------------------
    */
  const savedTheme = localStorage.getItem("theme") || "light";

  applyTheme(savedTheme);

  /*
    |--------------------------------------------------------------------------
    | TOGGLE
    |--------------------------------------------------------------------------
    */
  themeToggle.addEventListener("click", () => {
    const isDark = html.classList.contains("dark");

    const newTheme = isDark ? "light" : "dark";

    localStorage.setItem("theme", newTheme);

    applyTheme(newTheme);
  });
}
