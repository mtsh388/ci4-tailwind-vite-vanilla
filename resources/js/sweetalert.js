import Swal from "sweetalert2";

/*
|--------------------------------------------------------------------------
| SWEET ALERT THEME
|--------------------------------------------------------------------------
*/
const isDarkMode = () => {
  return document.documentElement.classList.contains("dark");
};

const swalTheme = () => {
  return {
    background: isDarkMode() ? "#0f172a" : "#ffffff",

    color: isDarkMode() ? "#f8fafc" : "#0f172a",

    customClass: {
      popup: `
        rounded-2xl
        ${isDarkMode() ? "border border-slate-700" : ""}
      `,
    },
  };
};

/*
|--------------------------------------------------------------------------
| FLASH MESSAGE
|--------------------------------------------------------------------------
*/
document.addEventListener("DOMContentLoaded", () => {
  const successMessage = document.body.dataset.success;

  const errorMessage = document.body.dataset.error;

  /*
  |--------------------------------------------------------------------------
  | SUCCESS
  |--------------------------------------------------------------------------
  */
  if (successMessage) {
    Swal.fire({
      ...swalTheme(),

      icon: "success",

      title: "Berhasil",

      text: successMessage,

      timer: 2000,

      showConfirmButton: false,
    });
  }

  /*
  |--------------------------------------------------------------------------
  | ERROR
  |--------------------------------------------------------------------------
  */
  if (errorMessage) {
    Swal.fire({
      ...swalTheme(),

      icon: "error",

      title: "Validasi Gagal",

      html: errorMessage,
    });
  }
});

/*
|--------------------------------------------------------------------------
| DELETE CONFIRM
|--------------------------------------------------------------------------
*/
document.addEventListener("click", async (e) => {
  const button = e.target.closest(".btn-delete");

  if (!button) return;

  e.preventDefault();

  const url = button.getAttribute("href");

  /*
  |--------------------------------------------------------------------------
  | CONFIRM
  |--------------------------------------------------------------------------
  */
  const result = await Swal.fire({
    ...swalTheme(),

    title: "Hapus Data?",

    text: "Data yang dihapus tidak dapat dikembalikan.",

    icon: "warning",

    showCancelButton: true,

    confirmButtonText: "Ya, Hapus",

    cancelButtonText: "Batal",

    reverseButtons: true,

    customClass: {
      popup: `
        rounded-2xl
        ${isDarkMode() ? "border border-slate-700" : ""}
      `,

      confirmButton:
        "rounded-xl bg-red-600 px-5 py-2 text-sm font-medium text-white hover:bg-red-700",

      cancelButton: `
        ml-2 rounded-xl
        px-5 py-2 text-sm font-medium
        bg-slate-200 text-slate-700 hover:bg-slate-300
        dark:bg-slate-700 dark:text-white dark:hover:bg-slate-600
      `,
    },

    buttonsStyling: false,
  });

  /*
  |--------------------------------------------------------------------------
  | CANCEL
  |--------------------------------------------------------------------------
  */
  if (!result.isConfirmed) {
    return;
  }

  /*
  |--------------------------------------------------------------------------
  | LOADING
  |--------------------------------------------------------------------------
  */
  Swal.fire({
    ...swalTheme(),

    title: "Menghapus Data...",

    text: "Mohon tunggu sebentar",

    allowOutsideClick: false,

    allowEscapeKey: false,

    didOpen: () => {
      Swal.showLoading();
    },
  });

  /*
  |--------------------------------------------------------------------------
  | REDIRECT
  |--------------------------------------------------------------------------
  */
  showLoader();

  const form = document.createElement("form");

  form.method = "POST";

  form.action = url;

  /*
  |--------------------------------------------------------------------------
  | CSRF TOKEN
  |--------------------------------------------------------------------------
  */
  const csrfName = document.querySelector('meta[name="csrf-name"]')?.content;

  const csrfHash = document.querySelector('meta[name="csrf-hash"]')?.content;

  if (csrfName && csrfHash) {
    const csrfInput = document.createElement("input");

    csrfInput.type = "hidden";

    csrfInput.name = csrfName;

    csrfInput.value = csrfHash;

    form.appendChild(csrfInput);
  }

  document.body.appendChild(form);

  form.submit();
});

/*
|--------------------------------------------------------------------------
| GLOBAL LOADING
|--------------------------------------------------------------------------
*/
window.swalLoading = (title = "Loading...") => {
  Swal.fire({
    ...swalTheme(),

    title,

    text: "Mohon tunggu sebentar",

    allowOutsideClick: false,

    allowEscapeKey: false,

    didOpen: () => {
      Swal.showLoading();
    },
  });
};

/*
|--------------------------------------------------------------------------
| CLOSE LOADING
|--------------------------------------------------------------------------
*/
window.swalClose = () => {
  Swal.close();
};

/*
|--------------------------------------------------------------------------
| TOAST
|--------------------------------------------------------------------------
*/
window.swalToast = ({
  icon = "success",

  title = "Berhasil",
}) => {
  Swal.fire({
    ...swalTheme(),

    toast: true,

    position: "top-end",

    icon,

    title,

    showConfirmButton: false,

    timer: 3000,

    timerProgressBar: true,
  });
};
