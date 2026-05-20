/*
|--------------------------------------------------------------------------
| USER DROPDOWN
|--------------------------------------------------------------------------
*/

const dropdownButton = document.getElementById("userDropdownButton");
const dropdownMenu = document.getElementById("userDropdown");

if (dropdownButton && dropdownMenu) {
  /*
  |--------------------------------------------------------------------------
  | TOGGLE DROPDOWN
  |--------------------------------------------------------------------------
  */
  dropdownButton.addEventListener("click", (e) => {
    e.stopPropagation();

    dropdownMenu.classList.toggle("hidden");
  });

  /*
  |--------------------------------------------------------------------------
  | CLOSE WHEN CLICK OUTSIDE
  |--------------------------------------------------------------------------
  */
  document.addEventListener("click", (e) => {
    if (
      !dropdownButton.contains(e.target) &&
      !dropdownMenu.contains(e.target)
    ) {
      dropdownMenu.classList.add("hidden");
    }
  });
}
