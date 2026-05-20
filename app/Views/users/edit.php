<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="mx-auto max-w-4xl rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">

  <!-- HEADER -->
  <div class="border-b border-slate-200 p-6 dark:border-slate-800">

    <h1 class="text-2xl font-bold text-slate-800 dark:text-white">
      Edit User
    </h1>

    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
      Update data user
    </p>

  </div>

  <!-- FORM -->
  <form
    action="<?= site_url('users/update/' . $user['id']) ?>"
    method="post"
    class="p-6">

    <?= csrf_field() ?>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

      <!-- LEVEL -->
      <div>

        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
          Level
        </label>

        <select
          name="level_id"
          required
          class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-700 transition
                 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100
                 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:ring-blue-900">

          <option value="">
            -- Pilih Level --
          </option>

          <?php foreach ($levels as $level): ?>

            <option
              value="<?= $level['id'] ?>"
              <?= $user['level_id'] == $level['id'] ? 'selected' : '' ?>>

              <?= esc($level['name']) ?>

            </option>

          <?php endforeach; ?>

        </select>

      </div>

      <!-- NAMA -->
      <div>

        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
          Nama
        </label>

        <input
          type="text"
          name="nama"
          value="<?= esc($user['nama']) ?>"
          required
          class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-700 transition
                 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100
                 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-400 dark:focus:ring-blue-900">

      </div>

      <!-- EMAIL -->
      <div>

        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
          Email
        </label>

        <input
          type="email"
          name="email"
          value="<?= esc($user['email']) ?>"
          class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-700 transition
                 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100
                 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-400 dark:focus:ring-blue-900">

      </div>

      <!-- USERNAME -->
      <div>

        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
          Username
        </label>

        <input
          type="text"
          name="username"
          value="<?= esc($user['username']) ?>"
          required
          class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-700 transition
                 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100
                 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-400 dark:focus:ring-blue-900">

      </div>

      <!-- PASSWORD -->
      <div>

        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
          Password Baru
        </label>

        <input
          type="password"
          name="password"
          placeholder="Kosongkan jika tidak diubah"
          class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-700 transition
                 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100
                 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-400 dark:focus:ring-blue-900">

      </div>

      <!-- STATUS -->
      <div>

        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
          Status
        </label>

        <select
          name="is_active"
          class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-700 transition
                 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100
                 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:ring-blue-900">

          <option
            value="1"
            <?= $user['is_active'] == 1 ? 'selected' : '' ?>>

            Active

          </option>

          <option
            value="0"
            <?= $user['is_active'] == 0 ? 'selected' : '' ?>>

            Inactive

          </option>

        </select>

      </div>

      <!-- CHANGE PASSWORD -->
      <div>

        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
          Force Change Password
        </label>

        <select
          name="change_password"
          class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-700 transition
                 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100
                 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:ring-blue-900">

          <option
            value="0"
            <?= $user['change_password'] == 0 ? 'selected' : '' ?>>

            No

          </option>

          <option
            value="1"
            <?= $user['change_password'] == 1 ? 'selected' : '' ?>>

            Yes

          </option>

        </select>

      </div>

    </div>

    <!-- BUTTON -->
    <div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-200 pt-6 dark:border-slate-800">

      <a
        href="<?= site_url('users') ?>"
        class="rounded-xl bg-slate-200 px-5 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-300
               dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600">

        Kembali

      </a>

      <button
        type="submit"
        class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-medium text-white transition hover:bg-blue-700">

        Update User

      </button>

    </div>

  </form>

</div>

<?= $this->endSection() ?>