/*
|--------------------------------------------------------------------------
| MOBILE SIDEBAR
|--------------------------------------------------------------------------
*/

const sidebar = document.getElementById("sidebar");
const sidebarToggle = document.getElementById("sidebarToggle");
const sidebarOverlay = document.getElementById("sidebarOverlay");

if (sidebar && sidebarToggle && sidebarOverlay) {
  /*
  |--------------------------------------------------------------------------
  | OPEN SIDEBAR
  |--------------------------------------------------------------------------
  */
  sidebarToggle.addEventListener("click", () => {
    sidebar.classList.remove("-translate-x-full");

    sidebarOverlay.classList.remove("hidden");
  });

  /*
  |--------------------------------------------------------------------------
  | CLOSE SIDEBAR
  |--------------------------------------------------------------------------
  */
  sidebarOverlay.addEventListener("click", () => {
    sidebar.classList.add("-translate-x-full");

    sidebarOverlay.classList.add("hidden");
  });
}

/*
|--------------------------------------------------------------------------
| SUBMENU
|--------------------------------------------------------------------------
*/
document.querySelectorAll(".submenu-button").forEach((button) => {
  button.addEventListener("click", () => {
    const submenu = button.nextElementSibling;

    if (submenu) {
      submenu.classList.toggle("hidden");
    }

    const icon = button.querySelector(".submenu-arrow");

    if (icon) {
      icon.classList.toggle("rotate-90");
    }
  });
});
