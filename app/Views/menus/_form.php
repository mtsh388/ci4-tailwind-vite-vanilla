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

  <input
    type="text"
    name="icon"
    value="<?= old('icon', $menu['icon'] ?? '') ?>"
    placeholder="Contoh: users"
    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">

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