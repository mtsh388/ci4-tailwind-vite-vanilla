<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">

  <!-- HEADER -->
  <div class="flex items-center justify-between border-b border-slate-200 p-6 dark:border-slate-800">

    <div>

      <h1 class="text-2xl font-bold text-slate-800 dark:text-white">
        Levels
      </h1>

      <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
        Kelola data level
      </p>

    </div>

    <?php if (hasPermission('levels', 'create')): ?>

      <a
        href="<?= site_url('levels/create') ?>"
        class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-medium text-white transition hover:bg-blue-700">

        Tambah Level

      </a>

    <?php endif; ?>

  </div>

  <!-- TABLE -->
  <div class="p-6 overflow-x-auto">

    <table
      id="tableLevels"
      class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">

      <thead class="bg-slate-50 dark:bg-slate-800">

        <tr>

          <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 dark:text-slate-200">
            No
          </th>

          <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 dark:text-slate-200">
            Nama Level
          </th>

          <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 dark:text-slate-200">
            Action
          </th>

        </tr>

      </thead>

    </table>

  </div>

</div>

<script>
  document.addEventListener("DOMContentLoaded", () => {

    initDataTable("#tableLevels", {

      ajax: {
        url: "<?= site_url('levels/datatable') ?>",
        type: "POST",
      },

      columns: [{
          data: "no",
          orderable: false,
          searchable: false,
          width: "5%",
        },
        {
          data: "name",
        },
        {
          data: "action",
          orderable: false,
          searchable: false,
        },
      ],
    });

  });
</script>

<?= $this->endSection() ?>