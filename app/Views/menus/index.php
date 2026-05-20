// app/Views/menus/index.php

<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="rounded-2xl
            bg-white
            p-6
            shadow-sm
            dark:bg-slate-900">

  <!-- HEADER -->
  <div class="mb-6 flex items-center justify-between">

    <div>
      <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
        Menu Management
      </h1>

      <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
        List data menu system
      </p>
    </div>

    <a
      href="<?= site_url('menus/create') ?>"
      class="rounded-xl bg-blue-600 px-4 py-3 text-sm font-medium text-white hover:bg-blue-700">

      Tambah Menu

    </a>

  </div>

  <!-- TABLE -->
  <table
    id="tableMenu"
    class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">

    <thead class="bg-slate-50 dark:bg-slate-800">
      <tr>

        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700 dark:text-slate-200">
          Parent
        </th>

        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700 dark:text-slate-200">
          Menu
        </th>

        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700 dark:text-slate-200">
          URL
        </th>

        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700 dark:text-slate-200">
          Icon
        </th>

        <th class="px-4 py-3 text-center text-sm font-semibold  text-slate-700 dark:text-slate-200">
          Status
        </th>

        <th class="px-4 py-3 text-center text-sm font-semibold  text-slate-700 dark:text-slate-200">
          Action
        </th>

      </tr>

    </thead>

  </table>

</div>

<script>
  document.addEventListener("DOMContentLoaded", () => {

    initDataTable("#tableMenu", {

      ajax: {
        url: "<?= site_url('menus/datatable') ?>",
        type: "POST",
      },

      columns: [{
          data: "parent"
        },
        {
          data: "name"
        },
        {
          data: "url"
        },
        {
          data: "icon"
        },
        {
          data: "status",
          orderable: false,
          searchable: false,
          className: "text-center",
        },
        {
          data: "action",
          orderable: false,
          searchable: false,
          className: "text-center",
        },
      ],
    });
  });
</script>

<?= $this->endSection() ?>