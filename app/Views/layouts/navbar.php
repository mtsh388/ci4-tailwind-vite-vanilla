<header class="sticky top-0 z-30 w-full border-b border-slate-200 bg-white/90 backdrop-blur dark:border-slate-800 dark:bg-slate-900/90">

  <div class="flex w-full items-center justify-between gap-3 px-4 py-4">

    <!-- LEFT -->
    <div class="flex min-w-0 items-center gap-3">

      <!-- MOBILE BUTTON -->
      <button
        id="sidebarToggle"
        type="button"
        class="flex-shrink-0 rounded-xl p-2 hover:bg-slate-100 dark:hover:bg-slate-800 md:hidden">

        <i
          data-lucide="menu"
          class="h-6 w-6">
        </i>

      </button>

      <!-- TITLE -->
      <div class="min-w-0">

        <h2 class="truncate text-lg font-bold text-slate-800 dark:text-white sm:text-xl">
          <?= esc($title ?? 'Dashboard') ?>
        </h2>

      </div>

    </div>

    <!-- RIGHT -->
    <div class="ml-auto flex flex-shrink-0 items-center gap-2">

      <!-- DARK MODE -->
      <button
        id="theme-toggle"
        type="button"
        class="inline-flex items-center gap-2 rounded-xl
               bg-slate-100 px-3 py-2
               text-sm font-medium
               text-slate-700
               transition-all duration-300
               hover:bg-slate-200
               dark:bg-slate-800
               dark:text-white
               dark:hover:bg-slate-700">

        <!-- SUN -->
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

        <!-- MOON -->
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

      <!-- USER INFO -->
      <div class="hidden text-right lg:block">

        <p class="font-semibold text-slate-800 dark:text-white">
          <?= session('name') ?>
        </p>

        <p class="text-sm text-slate-500 dark:text-slate-400">
          <?= session('username') ?>
        </p>

      </div>

      <!-- DROPDOWN -->
      <div class="relative">

        <button
          id="userDropdownButton"
          class="rounded-xl bg-slate-100 px-3 py-2 text-sm
                 text-slate-700
                 hover:bg-slate-200
                 dark:bg-slate-800
                 dark:text-white
                 dark:hover:bg-slate-700 sm:px-4">

          Account

        </button>

        <div
          id="userDropdown"
          class="absolute right-0 mt-2 hidden w-48 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg dark:border-slate-700 dark:bg-slate-900">

          <a
            href="<?= site_url('change-password') ?>"
            class="block px-4 py-3 text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800">

            Change Password

          </a>

          <a
            href="<?= site_url('logout') ?>"
            class="block px-4 py-3 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">

            Logout

          </a>

        </div>

      </div>

    </div>

  </div>

</header>