const pageLoader = document.getElementById("pageLoader");

function showLoader() {
  if (pageLoader) {
    pageLoader.classList.remove("loader-hidden");
  }
}

function hideLoader() {
  if (pageLoader) {
    pageLoader.classList.add("loader-hidden");
  }
}

/*
|--------------------------------------------------------------------------
| INITIAL LOAD
|--------------------------------------------------------------------------
*/
window.addEventListener("load", () => {
  document.body.classList.remove("loading");

  setTimeout(() => {
    hideLoader();
  }, 150);
});

/*
|--------------------------------------------------------------------------
| LINK CLICK
|--------------------------------------------------------------------------
*/
document.addEventListener("click", (e) => {
  /*
  |--------------------------------------------------------------------------
  | IGNORE SWEETALERT
  |--------------------------------------------------------------------------
  */
  if (e.target.closest(".swal2-container") || e.target.closest(".btn-delete")) {
    return;
  }

  const link = e.target.closest("a");

  if (!link) return;

  const href = link.getAttribute("href");

  if (
    !href ||
    href.startsWith("#") ||
    href.startsWith("javascript:") ||
    link.target === "_blank" ||
    link.hasAttribute("download")
  ) {
    return;
  }

  const url = new URL(href, window.location.origin);

  if (url.origin !== window.location.origin) {
    return;
  }

  showLoader();
});
/*
|--------------------------------------------------------------------------
| FORM SUBMIT
|--------------------------------------------------------------------------
*/
document.addEventListener("submit", () => {
  showLoader();
});

/*
|--------------------------------------------------------------------------
| BACK/FORWARD CACHE FIX
|--------------------------------------------------------------------------
*/
window.addEventListener("pageshow", () => {
  hideLoader();
});

window.showLoader = showLoader;
window.hideLoader = hideLoader;
