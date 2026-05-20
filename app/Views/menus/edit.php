<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="max-w-3xl">

  <!-- CARD -->
  <div class="rounded-2xl bg-white p-6 shadow-sm dark:bg-slate-900">

    <!-- TITLE -->
    <h1 class="mb-6 text-2xl font-bold text-slate-900 dark:text-white">
      Edit Menu
    </h1>

    <!-- FORM -->
    <form
      action="<?= site_url('menus/update/' . $menu['id']) ?>"
      method="post"
      class="space-y-6">

      <?= csrf_field() ?>

      <?= $this->include('menus/_form') ?>

      <!-- ACTION -->
      <div class="flex flex-wrap items-center gap-3">

        <!-- SUBMIT -->
        <button
          type="submit"
          class="rounded-xl
                 bg-blue-600
                 px-6 py-3
                 font-medium
                 text-white
                 transition
                 hover:bg-blue-700">

          Update

        </button>

        <!-- BACK -->
        <a
          href="<?= site_url('menus') ?>"
          class="rounded-xl
                 bg-slate-200
                 px-6 py-3
                 font-medium
                 text-slate-700
                 transition
                 hover:bg-slate-300

                 dark:bg-slate-800
                 dark:text-slate-200
                 dark:hover:bg-slate-700">

          Kembali

        </a>

      </div>

    </form>

  </div>

</div>

<?= $this->endSection() ?>