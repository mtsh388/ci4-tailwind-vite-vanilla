import "iconify-icon";
import * as icons from "lucide";

document.addEventListener("DOMContentLoaded", () => {

  const iconInput = document.getElementById("icon-input");
  const iconPreview = document.getElementById("preview-icon");
  const iconList = document.getElementById("icon-list");
  const iconCount = document.getElementById("icon-count");
  const iconEmpty = document.getElementById("icon-empty");

  if (!iconInput || !iconPreview || !iconList) {
    return;
  }

  // GET ICONS
  const lucideIcons = Object.keys(icons)
    .filter(name => /^[A-Z]/.test(name))
    .map(name =>
      name
        .replace(/([a-z])([A-Z])/g, "$1-$2")
        .toLowerCase()
    );

  // RENDER
  function renderIcons(keyword = "") {

    const filteredIcons = lucideIcons
      .filter(icon => icon.includes(keyword))
      .slice(0, 80); // BATASI

    iconCount.textContent =
      `Menampilkan ${filteredIcons.length} icon`;

    if (filteredIcons.length === 0) {

      iconEmpty.classList.remove("hidden");
      iconList.classList.add("hidden");

      return;
    }

    iconEmpty.classList.add("hidden");
    iconList.classList.remove("hidden");

    // BUILD HTML SEKALI
    let html = "";

    filteredIcons.forEach(icon => {

      const active =
        iconInput.value === icon
          ? "border-blue-500 bg-blue-50 text-blue-600"
          : "";

      html += `
        <button
          type="button"
          title="${icon}"
          data-icon="${icon}"
          class="icon-item group flex flex-col items-center
                 justify-center gap-2 rounded-2xl
                 border border-slate-200 bg-white p-3
                 text-slate-700 transition
                 hover:border-blue-500
                 hover:bg-blue-50
                 hover:text-blue-600
                 ${active}">

          <iconify-icon
            icon="lucide:${icon}"
            width="22">
          </iconify-icon>

          <span class="line-clamp-1 w-full text-center text-[11px]">
            ${icon}
          </span>

        </button>
      `;
    });

    // SET SEKALI
    iconList.innerHTML = html;
  }

  // CLICK EVENT (EVENT DELEGATION)
  iconList.addEventListener("click", (e) => {

    const button = e.target.closest(".icon-item");

    if (!button) return;

    const icon = button.dataset.icon;

    iconInput.value = icon;

    iconPreview.setAttribute(
      "icon",
      `lucide:${icon}`
    );

    renderIcons(icon);

  });

  // DEBOUNCE
  let debounce;

  iconInput.addEventListener("input", function () {

    clearTimeout(debounce);

    debounce = setTimeout(() => {

      const keyword = this.value.toLowerCase();

      iconPreview.setAttribute(
        "icon",
        `lucide:${keyword}`
      );

      renderIcons(keyword);

    }, 200);

  });

  // INITIAL
  renderIcons(iconInput.value);

});