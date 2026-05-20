<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="mx-auto max-w-3xl rounded-2xl bg-white shadow-sm dark:bg-slate-900">

  <!-- VALIDATION -->
  <?php if (session()->getFlashdata('errors')): ?>

    <script>
      document.addEventListener('DOMContentLoaded', function() {

        Swal.fire({
          icon: 'error',
          title: 'Validasi Gagal',
          html: `
            <ul style="text-align:left;">
              <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
              <?php endforeach; ?>
            </ul>
          `,
          confirmButtonText: 'OK'
        });

      });
    </script>

  <?php endif; ?>

  <!-- HEADER -->
  <div class="border-b border-slate-200 p-6 dark:border-slate-800">

    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
      Tambah User
    </h1>

    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
      Tambah data user baru
    </p>

  </div>

  <!-- FORM -->
  <form
    action="<?= site_url('users/store') ?>"
    method="post"
    class="p-6">

    <?= csrf_field() ?>

    <div class="grid gap-6">

      <!-- NAME -->
      <div>

        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
          Nama
        </label>

        <input
          type="text"
          name="nama"
          value="<?= old('nama') ?>"
          required
          class="w-full rounded-xl
                 border border-slate-300
                 bg-white
                 px-4 py-3
                 text-sm text-slate-700
                 transition

                 focus:border-blue-500
                 focus:outline-none
                 focus:ring-2
                 focus:ring-blue-200

                 dark:border-slate-700
                 dark:bg-slate-800
                 dark:text-white
                 dark:focus:border-blue-500
                 dark:focus:ring-blue-500/20">

      </div>

      <!-- EMAIL -->
      <div>

        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
          Email
        </label>

        <input
          type="email"
          name="email"
          value="<?= old('email') ?>"
          required
          class="w-full rounded-xl
                 border border-slate-300
                 bg-white
                 px-4 py-3
                 text-sm text-slate-700
                 transition

                 focus:border-blue-500
                 focus:outline-none
                 focus:ring-2
                 focus:ring-blue-200

                 dark:border-slate-700
                 dark:bg-slate-800
                 dark:text-white
                 dark:focus:border-blue-500
                 dark:focus:ring-blue-500/20">

      </div>

      <!-- USERNAME -->
      <div>

        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
          Username
        </label>

        <input
          type="text"
          name="username"
          value="<?= old('username') ?>"
          required
          class="w-full rounded-xl
                 border border-slate-300
                 bg-white
                 px-4 py-3
                 text-sm text-slate-700
                 transition

                 focus:border-blue-500
                 focus:outline-none
                 focus:ring-2
                 focus:ring-blue-200

                 dark:border-slate-700
                 dark:bg-slate-800
                 dark:text-white
                 dark:focus:border-blue-500
                 dark:focus:ring-blue-500/20">

      </div>

      <!-- PASSWORD -->
      <div>

        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
          Password
        </label>

        <input
          type="password"
          name="password"
          required
          class="w-full rounded-xl
                 border border-slate-300
                 bg-white
                 px-4 py-3
                 text-sm text-slate-700
                 transition

                 focus:border-blue-500
                 focus:outline-none
                 focus:ring-2
                 focus:ring-blue-200

                 dark:border-slate-700
                 dark:bg-slate-800
                 dark:text-white
                 dark:focus:border-blue-500
                 dark:focus:ring-blue-500/20">

      </div>

      <!-- LEVEL -->
      <div>

        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
          Level
        </label>

        <select
          name="level_id"
          required
          class="w-full rounded-xl
                 border border-slate-300
                 bg-white
                 px-4 py-3
                 text-sm text-slate-700
                 transition

                 focus:border-blue-500
                 focus:outline-none
                 focus:ring-2
                 focus:ring-blue-200

                 dark:border-slate-700
                 dark:bg-slate-800
                 dark:text-white
                 dark:focus:border-blue-500
                 dark:focus:ring-blue-500/20">

          <option value="">
            -- Pilih Level --
          </option>

          <?php foreach ($levels as $level): ?>

            <option
              value="<?= $level['id'] ?>"
              <?= old('level_id') == $level['id']
                ? 'selected'
                : '' ?>>

              <?= esc($level['name']) ?>

            </option>

          <?php endforeach; ?>

        </select>

      </div>

    </div>

    <!-- FOOTER -->
    <div class="mt-8 flex flex-wrap items-center justify-end gap-3 border-t border-slate-200 pt-6 dark:border-slate-800">

      <!-- BACK -->
      <a
        href="<?= site_url('users') ?>"
        class="rounded-xl
               bg-slate-200
               px-6 py-3
               text-sm font-medium
               text-slate-700
               transition
               hover:bg-slate-300

               dark:bg-slate-800
               dark:text-slate-200
               dark:hover:bg-slate-700">

        Kembali

      </a>

      <!-- SUBMIT -->
      <button
        type="submit"
        class="rounded-xl
               bg-blue-600
               px-6 py-3
               text-sm font-medium
               text-white
               transition
               hover:bg-blue-700">

        Simpan User

      </button>

    </div>

  </form>

</div>

<?= $this->endSection() ?>