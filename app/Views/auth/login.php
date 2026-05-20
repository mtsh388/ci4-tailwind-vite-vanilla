<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title) ?></title>

  <!-- PREVENT FOUC -->
  <script>
    (() => {

      const theme = localStorage.getItem('theme')

      if (
        theme === 'dark' ||
        (
          !theme &&
          window.matchMedia('(prefers-color-scheme: dark)').matches
        )
      ) {
        document.documentElement.classList.add('dark')
      }

    })()
  </script>

  <?php if (ENVIRONMENT === 'development'): ?>

    <script type="module" src="http://localhost:5173/@vite/client"></script>

    <script type="module" src="http://localhost:5173/resources/js/app.js"></script>

  <?php else: ?>

    <link rel="stylesheet" href="<?= base_url('build/assets/app.css') ?>">

    <script type="module" src="<?= base_url('build/assets/app.js') ?>"></script>

  <?php endif; ?>

  <style>
    body.loading {
      overflow: hidden;
    }

    body.loading #app {
      opacity: 0;
      pointer-events: none;
    }

    #pageLoader {
      position: fixed;
      inset: 0;
      z-index: 99999;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(6px);
      transition: all .3s ease;
    }

    .dark #pageLoader {
      background: rgba(15, 23, 42, 0.9);
    }

    .loader-hidden {
      display: none !important;
    }

    .loader-spinner {
      width: 56px;
      height: 56px;
      border-radius: 9999px;
      border: 4px solid #bfdbfe;
      border-top-color: #2563eb;
      animation: spin .8s linear infinite;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }
  </style>

</head>

<body class="loading flex min-h-screen items-center justify-center bg-slate-100 px-4 text-slate-800 transition-colors duration-300 dark:bg-slate-950 dark:text-white">

  <!-- PAGE LOADER -->
  <div id="pageLoader">

    <div class="flex flex-col items-center">

      <div class="loader-spinner"></div>

      <p class="mt-4 text-sm font-medium text-slate-600 dark:text-slate-300">
        Loading...
      </p>

    </div>

  </div>

  <!-- LOGIN CARD -->
  <div
    id="app"
    class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-8 shadow-xl transition-colors duration-300 dark:border-slate-800 dark:bg-slate-900">

    <!-- THEME TOGGLE -->
    <div class="mb-6 flex justify-end">

      <button
        id="theme-toggle"
        type="button"
        class="inline-flex items-center gap-2 rounded-xl bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-200 dark:bg-slate-800 dark:text-white dark:hover:bg-slate-700">

        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="hidden h-5 w-5 dark:block"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor">

          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364 6.364l-1.414-1.414M7.05 7.05 5.636 5.636m12.728 0-1.414 1.414M7.05 16.95l-1.414 1.414M12 8a4 4 0 100 8 4 4 0 000-8" />

        </svg>

        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="block h-5 w-5 dark:hidden"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor">

          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M20.354 15.354A9 9 0 018.646 3.646
               9.003 9.003 0 0012 21a9.003
               9.003 0 008.354-5.646z" />

        </svg>

        <span id="theme-text">
          Dark
        </span>

      </button>

    </div>

    <!-- HEADER -->
    <div class="mb-8 text-center">

      <h1 class="text-3xl font-bold text-slate-800 dark:text-white">
        Login
      </h1>

      <p class="mt-2 text-slate-500 dark:text-slate-400">
        Silakan login untuk melanjutkan
      </p>

    </div>

    <!-- ERROR -->
    <?php if (session()->getFlashdata('error')): ?>

      <div class="mb-4 rounded-xl border border-red-200 bg-red-100 p-4 text-sm text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300">

        <?= session()->getFlashdata('error') ?>

      </div>

    <?php endif; ?>

    <!-- SUCCESS -->
    <?php if (session()->getFlashdata('success')): ?>

      <div class="mb-4 rounded-xl border border-green-200 bg-green-100 p-4 text-sm text-green-700 dark:border-green-900 dark:bg-green-950/40 dark:text-green-300">

        <?= session()->getFlashdata('success') ?>

      </div>

    <?php endif; ?>

    <!-- FORM -->
    <form action="<?= site_url('login/process') ?>" method="post">

      <?= csrf_field() ?>

      <!-- USERNAME -->
      <div class="mb-5">

        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
          Username
        </label>

        <input
          type="text"
          name="username"
          value="<?= old('username') ?>"
          class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 transition focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:ring-blue-900"
          placeholder="Masukkan username">

      </div>

      <!-- PASSWORD -->
      <!-- PASSWORD -->
      <div class="mb-6">

        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
          Password
        </label>

        <div class="relative">

          <input
            id="password"
            type="password"
            name="password"
            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 pr-12 text-sm text-slate-800 transition focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:ring-blue-900"
            placeholder="Masukkan password">

          <button
            type="button"
            id="togglePassword"
            class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white">

            <!-- EYE OPEN -->
            <svg
              id="iconEye"
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor">

              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M2.458 12C3.732 7.943 7.523 5 12 5
             c4.478 0 8.268 2.943 9.542 7
             -1.274 4.057-5.064 7-9.542 7
             -4.477 0-8.268-2.943-9.542-7z" />

            </svg>

          </button>

        </div>

      </div>

      <!-- BUTTON -->
      <button
        type="submit"
        class="w-full rounded-xl bg-blue-600 px-4 py-3 font-semibold text-white transition hover:bg-blue-700">

        Login

      </button>

    </form>

  </div>

  <!-- THEME SCRIPT -->
  <script>
    /*
  |--------------------------------------------------------------------------
  | SHOW / HIDE PASSWORD
  |--------------------------------------------------------------------------
  */
    const passwordInput =
      document.getElementById('password')

    const togglePassword =
      document.getElementById('togglePassword')

    const iconEye =
      document.getElementById('iconEye')

    togglePassword.addEventListener('click', () => {

      const isPassword =
        passwordInput.type === 'password'

      passwordInput.type =
        isPassword ? 'text' : 'password'

      iconEye.innerHTML = isPassword ?

        `
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M13.875 18.825A10.05 10.05 0 0112 19
           c-4.478 0-8.268-2.943-9.542-7
           a9.956 9.956 0 012.293-3.95m3.1-2.382
           A9.953 9.953 0 0112 5c4.478 0 8.268 2.943
           9.542 7a9.97 9.97 0 01-4.043 5.132M15 12
           a3 3 0 11-6 0 3 3 0 016 0zm6 6L3 3"
      />
      `

        :

        `
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
      />

      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M2.458 12C3.732 7.943 7.523 5 12 5
           c4.478 0 8.268 2.943 9.542 7
           -1.274 4.057-5.064 7-9.542 7
           -4.477 0-8.268-2.943-9.542-7z"
      />
      `
    })
    document.addEventListener('DOMContentLoaded', () => {

      /*
      |--------------------------------------------------------------------------
      | REMOVE LOADER
      |--------------------------------------------------------------------------
      */
      document.body.classList.remove('loading')

      document
        .getElementById('pageLoader')
        .classList.add('loader-hidden')

      /*
      |--------------------------------------------------------------------------
      | ELEMENT
      |--------------------------------------------------------------------------
      */
      const body = document.body

      const themeToggle =
        document.getElementById('theme-toggle')

      const themeText =
        document.getElementById('theme-text')

      /*
      |--------------------------------------------------------------------------
      | APPLY THEME
      |--------------------------------------------------------------------------
      */
      const applyTheme = (theme) => {

        if (theme === 'dark') {

          body.classList.add('dark')

          themeText.innerText = 'Light'

        } else {

          body.classList.remove('dark')

          themeText.innerText = 'Dark'

        }

      }

      /*
      |--------------------------------------------------------------------------
      | INIT THEME
      |--------------------------------------------------------------------------
      */
      const savedTheme =
        localStorage.getItem('theme') || 'light'

      applyTheme(savedTheme)

      /*
      |--------------------------------------------------------------------------
      | TOGGLE THEME
      |--------------------------------------------------------------------------
      */
      themeToggle.addEventListener('click', () => {

        const isDark =
          body.classList.contains('dark')

        const newTheme =
          isDark ? 'light' : 'dark'

        localStorage.setItem('theme', newTheme)

        applyTheme(newTheme)

      })

    })
  </script>

</body>

</html>