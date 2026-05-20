<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="rounded-2xl bg-white shadow-sm dark:bg-slate-900">

  <!-- HEADER -->
  <div class="flex flex-col gap-4 border-b border-slate-200 p-6 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">

    <div>

      <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
        Users
      </h1>

      <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
        Kelola data users
      </p>

    </div>

    <?php if (hasPermission('users', 'create')): ?>

      <a
        href="<?= site_url('users/create') ?>"
        class="inline-flex items-center justify-center rounded-xl
               bg-blue-600 px-5 py-3
               text-sm font-medium text-white
               transition hover:bg-blue-700">

        Tambah User

      </a>

    <?php endif; ?>

  </div>

  <!-- TABLE -->
  <div class="overflow-x-auto p-6">

    <table
      id="tableUsers"
      class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">

      <thead class="bg-slate-50 dark:bg-slate-800">

        <tr>

          <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 dark:text-slate-200">
            No
          </th>

          <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 dark:text-slate-200">
            Nama
          </th>

          <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 dark:text-slate-200">
            Username
          </th>

          <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 dark:text-slate-200">
            Email
          </th>

          <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 dark:text-slate-200">
            Level
          </th>

          <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 dark:text-slate-200">
            Status
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

    initDataTable("#tableUsers", {

      ajax: {
        url: "<?= site_url('users/datatable') ?>",
        type: "POST",
      },

      columns: [{
          data: "no",
          orderable: false,
          searchable: false,
          className: "text-center",
        },
        {
          data: "nama",
        },
        {
          data: "username",
        },
        {
          data: "email",
        },
        {
          data: "level",
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