import "../css/app.css";

/*
|--------------------------------------------------------------------------
| MODULES
|--------------------------------------------------------------------------
*/
import "./loader";
import "./sidebar";
import "./dropdown";
import "./datatables";
import "./lucide";
import "./sweetalert";
import "./menu-status";

import { initTheme } from "./theme";

document.addEventListener("DOMContentLoaded", () => {
  initTheme();
});
