<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">

  <!-- HEADER -->
  <div class="border-b border-slate-200 p-6 dark:border-slate-800">

    <h1 class="text-2xl font-bold text-slate-800 dark:text-white">
      Menu Access
    </h1>

    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
      Kelola hak akses level:
      <span class="font-semibold text-slate-700 dark:text-slate-200">
        <?= esc($level['name']) ?>
      </span>
    </p>

  </div>

  <!-- TABLE -->
  <div class="overflow-x-auto p-6">

    <table
      id="tableMenuAccess"
      class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">

      <thead class="bg-slate-50 dark:bg-slate-800">

        <tr>

          <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 dark:text-slate-200">
            Parent
          </th>

          <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 dark:text-slate-200">
            Menu
          </th>

          <th class="px-6 py-4 text-center text-sm font-semibold text-slate-700 dark:text-slate-200">
            View
          </th>

          <th class="px-6 py-4 text-center text-sm font-semibold text-slate-700 dark:text-slate-200">
            Create
          </th>

          <th class="px-6 py-4 text-center text-sm font-semibold text-slate-700 dark:text-slate-200">
            Update
          </th>

          <th class="px-6 py-4 text-center text-sm font-semibold text-slate-700 dark:text-slate-200">
            Delete
          </th>

        </tr>

      </thead>

    </table>

  </div>

</div>

<script>
  document.addEventListener("DOMContentLoaded", () => {

    /*
    |--------------------------------------------------------------------------
    | DATATABLE
    |--------------------------------------------------------------------------
    */
    initDataTable("#tableMenuAccess", {

      processing: true,
      serverSide: true,

      ajax: {
        url: "<?= site_url('menu-access/datatable/' . $level['id']) ?>",
        type: "POST",
      },

      order: [
        [0, "asc"],
        [1, "asc"]
      ],

      columns: [{
          data: "parent",
        },
        {
          data: "menu",
        },
        {
          data: "view",
          orderable: false,
          searchable: false,
          className: "text-center",
        },
        {
          data: "create",
          orderable: false,
          searchable: false,
          className: "text-center",
        },
        {
          data: "update",
          orderable: false,
          searchable: false,
          className: "text-center",
        },
        {
          data: "delete",
          orderable: false,
          searchable: false,
          className: "text-center",
        },
      ],
    });

    /*
    |--------------------------------------------------------------------------
    | AUTO UPDATE
    |--------------------------------------------------------------------------
    */
    document.addEventListener("change", async (e) => {

      if (!e.target.classList.contains("permission-checkbox")) {
        return;
      }

      const checkbox = e.target;

      const levelId = checkbox.dataset.level;
      const menuId = checkbox.dataset.menu;
      const permission = checkbox.dataset.permission;
      const value = checkbox.checked ? 1 : 0;

      try {

        const response = await fetch(
          "<?= site_url('menu-access/update-permission') ?>", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-Requested-With": "XMLHttpRequest",
            },

            body: JSON.stringify({
              level_id: levelId,
              menu_id: menuId,
              permission: permission,
              value: value,
            }),
          }
        );

        const result = await response.json();

        if (!result.success) {

          Swal.fire({
            icon: "error",
            title: "Gagal",
            text: result.message || "Gagal update permission",
          });

          checkbox.checked = !checkbox.checked;
        }

      } catch (error) {

        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Terjadi kesalahan",
        });

        checkbox.checked = !checkbox.checked;
      }
    });
  });
</script>

<?= $this->endSection() ?>