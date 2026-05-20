<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="max-w-xl">

  <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">

    <!-- TITLE -->
    <h1 class="mb-2 text-2xl font-bold text-slate-800 dark:text-white">
      Change Password
    </h1>

    <p class="mb-6 text-sm text-slate-500 dark:text-slate-400">
      Update password akun Anda
    </p>

    <!-- FORM -->
    <form action="<?= site_url('change-password') ?>" method="post">

      <?= csrf_field() ?>

      <!-- CURRENT PASSWORD -->
      <div class="mb-5">

        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
          Password Lama
        </label>

        <input
          type="password"
          name="current_password"
          placeholder="Masukkan password lama"
          class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-700 transition
                 placeholder:text-slate-400
                 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100
                 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-500 dark:focus:ring-blue-900">

      </div>
      <div class="mb-5 text-right">

        <a
          href="<?= site_url('forgot-password') ?>"
          class="text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">

          Lupa Password?

        </a>

      </div>

      <!-- NEW PASSWORD -->
      <div class="mb-5">

        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
          Password Baru
        </label>

        <input
          type="password"
          name="new_password"
          placeholder="Masukkan password baru"
          class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-700 transition
                 placeholder:text-slate-400
                 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100
                 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-500 dark:focus:ring-blue-900">

      </div>

      <!-- CONFIRM PASSWORD -->
      <div class="mb-6">

        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
          Konfirmasi Password
        </label>

        <input
          type="password"
          name="confirm_password"
          placeholder="Ulangi password baru"
          class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-700 transition
                 placeholder:text-slate-400
                 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100
                 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-500 dark:focus:ring-blue-900">

      </div>

      <!-- BUTTON -->
      <div class="flex items-center gap-3">

        <button
          type="submit"
          class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-medium text-white transition hover:bg-blue-700">

          Update Password

        </button>

      </div>

    </form>

  </div>

</div>

<?= $this->endSection() ?>