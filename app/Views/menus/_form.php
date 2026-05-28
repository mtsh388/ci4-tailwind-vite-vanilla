<div class="mb-6">

  <label class="mb-2 block text-sm font-medium">
    Parent Menu
  </label>

  <select
    name="parent_id"
    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">

    <option value="">
      Parent Menu
    </option>

    <?php foreach ($parents as $parent): ?>

      <option
        value="<?= $parent['id'] ?>"

        <?= old('parent_id', $menu['parent_id'] ?? '') == $parent['id']
          ? 'selected'
          : '' ?>>

        <?= esc($parent['name']) ?>

      </option>

    <?php endforeach; ?>

  </select>

</div>

<div class="mb-6">

  <label class="mb-2 block text-sm font-medium">
    Nama Menu
  </label>

  <input
    type="text"
    name="name"
    value="<?= old('name', $menu['name'] ?? '') ?>"
    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">

</div>

<div class="mb-6">

  <label class="mb-2 block text-sm font-medium">
    Icon
  </label>

  <div class="rounded-2xl border border-slate-300 bg-white p-4">

    <!-- TOP -->
    <div class="flex items-center gap-3">

      <!-- PREVIEW -->
      <div
        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl border border-slate-300 bg-slate-50">

        <iconify-icon
          id="preview-icon"
          icon="lucide:<?= old('icon', $menu['icon'] ?? 'layout-dashboard') ?>"
          width="28">
        </iconify-icon>

      </div>

      <!-- SEARCH -->
      <div class="relative flex-1">

        <input
          type="text"
          id="icon-input"
          name="icon"
          value="<?= old('icon', $menu['icon'] ?? 'layout-dashboard') ?>"
          placeholder="Cari icon..."
          autocomplete="off"
          class="w-full rounded-2xl border border-slate-300 py-3 pl-11 pr-4
                 focus:border-blue-500 focus:outline-none">

        <!-- SEARCH ICON -->
        <div class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2">

          <iconify-icon
            icon="lucide:search"
            width="18"
            class="text-slate-400">
          </iconify-icon>

        </div>

      </div>

    </div>

    <!-- RESULT INFO -->
    <div
      id="icon-count"
      class="mt-4 text-sm text-slate-500">
    </div>

    <!-- ICON LIST -->
    <div
      id="icon-list"
      class="mt-3 grid max-h-80 grid-cols-4 gap-3 overflow-y-auto md:grid-cols-6 lg:grid-cols-8">

    </div>

    <!-- EMPTY -->
    <div
      id="icon-empty"
      class="hidden py-10 text-center text-sm text-slate-500">

      Icon tidak ditemukan

    </div>

  </div>

</div>

<div class="mb-6">

  <label class="mb-2 block text-sm font-medium">
    URL
  </label>

  <input
    type="text"
    name="url"
    value="<?= old('url', $menu['url'] ?? '') ?>"
    placeholder="Contoh: users"
    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">

</div>

<div class="mb-6">

  <label class="mb-2 block text-sm font-medium">
    Sort Order
  </label>

  <input
    type="number"
    name="sort_order"
    value="<?= old('sort_order', $menu['sort_order'] ?? 0) ?>"
    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">

</div>

<div class="mb-6">

  <label class="mb-2 block text-sm font-medium">
    Status
  </label>

  <select
    name="is_active"
    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">

    <option value="1">Active</option>
    <option value="0"
      <?= old('is_active', $menu['is_active'] ?? 1) == 0
        ? 'selected'
        : '' ?>>

      Inactive
    </option>

  </select>

</div>

<script>
  const input = document.getElementById('icon-input');
  const preview = document.getElementById('icon-preview');

  input.addEventListener('input', function() {

    preview.innerHTML = `
      <iconify-icon
        icon="lucide:${this.value}"
        width="24">
      </iconify-icon>
    `;

  });
</script>