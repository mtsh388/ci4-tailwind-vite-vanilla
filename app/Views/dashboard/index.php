<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="grid gap-6 md:grid-cols-3">

  <!-- TOTAL USERS -->
  <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-colors duration-300 dark:border-slate-800 dark:bg-slate-900">

    <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">
      Total Users
    </h3>

    <p class="mt-2 text-3xl font-bold text-slate-800 dark:text-white">
      25
    </p>

  </div>

  <!-- TOTAL MENU -->
  <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-colors duration-300 dark:border-slate-800 dark:bg-slate-900">

    <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">
      Total Menu
    </h3>

    <p class="mt-2 text-3xl font-bold text-slate-800 dark:text-white">
      10
    </p>

  </div>

  <!-- TOTAL LEVEL -->
  <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-colors duration-300 dark:border-slate-800 dark:bg-slate-900">

    <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">
      Total Level
    </h3>

    <p class="mt-2 text-3xl font-bold text-slate-800 dark:text-white">
      3
    </p>

  </div>

</div>

<?= $this->endSection() ?>