<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="max-w-2xl">

  <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">

    <!-- TITLE -->
    <h1 class="mb-6 text-2xl font-bold text-slate-800 dark:text-white">
      Tambah Level
    </h1>

    <!-- FORM -->
    <form action="<?= site_url('levels/store') ?>" method="post">

      <?= csrf_field() ?>

      <!-- INPUT -->
      <div class="mb-6">

        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
          Nama Level
        </label>

        <input
          type="text"
          name="name"
          value="<?= old('name') ?>"
          placeholder="Contoh: Administrator"
          class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-700 transition
                 placeholder:text-slate-400
                 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100
                 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-500 dark:focus:ring-blue-900">

      </div>

      <!-- BUTTON -->
      <div class="flex items-center gap-3">

        <button
          type="submit"
          class="rounded-xl bg-blue-600 px-6 py-3 text-white transition hover:bg-blue-700">

          Simpan

        </button>

        <a
          href="<?= site_url('levels') ?>"
          class="rounded-xl bg-slate-200 px-6 py-3 text-slate-700 transition hover:bg-slate-300
                 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600">

          Kembali

        </a>

      </div>

    </form>

  </div>

</div>

<?= $this->endSection() ?>