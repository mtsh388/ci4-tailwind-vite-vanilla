<?php

$currentUrl = service('uri')->getSegment(1);

$parents = array_filter(
  $sidebarMenu,
  fn($m) => empty($m['parent_id'])
);

?>

<aside
  id="sidebar"
  class="fixed inset-y-0 left-0 z-50 w-64
           -translate-x-full
           border-r border-slate-200
           bg-white
           text-slate-800
           transition-transform duration-300

           dark:border-slate-800
           dark:bg-slate-900
           dark:text-white

           md:static
           md:translate-x-0
           md:flex
           md:flex-col">

  <!-- LOGO -->
  <div class="border-b border-slate-200 px-6 py-5 dark:border-slate-800">

    <h1 class="text-2xl font-bold">
      Starter Kit
    </h1>

    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
      CodeIgniter 4
    </p>

  </div>

  <!-- MENU -->
  <nav class="flex-1 overflow-y-auto px-4 py-6">

    <ul class="space-y-2">

      <?php foreach ($parents as $parent): ?>

        <?php

        /*
                |--------------------------------------------------------------------------
                | CHILDREN
                |--------------------------------------------------------------------------
                */
        $children = array_filter(
          $sidebarMenu,
          fn($m) => $m['parent_id'] == $parent['id']
        );

        $hasChildren = count($children) > 0;

        /*
                |--------------------------------------------------------------------------
                | ACTIVE SINGLE MENU
                |--------------------------------------------------------------------------
                */
        $isActive =
          url_is($parent['url']) ||
          url_is($parent['url'] . '/*');

        /*
                |--------------------------------------------------------------------------
                | ACTIVE PARENT
                |--------------------------------------------------------------------------
                */
        $isParentActive = false;

        foreach ($children as $child) {

          if (
            url_is($child['url']) ||
            url_is($child['url'] . '/*')
          ) {
            $isParentActive = true;
            break;
          }
        }

        ?>

        <!-- SINGLE MENU -->
        <?php if (!$hasChildren): ?>

          <li>

            <a
              href="<?= site_url($parent['url']) ?>"
              class="group flex items-center rounded-xl px-4 py-3 transition-all duration-200

                            <?= $isActive
                              ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20'
                              : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800' ?>">

              <div class="flex items-center gap-3">

                <?php if (!empty($parent['icon'])): ?>

                  <i
                    data-lucide="<?= esc($parent['icon']) ?>"
                    class="h-5 w-5 transition

                                        <?= $isActive
                                          ? 'text-white'
                                          : 'text-slate-500 group-hover:text-slate-900 dark:text-slate-400 dark:group-hover:text-white' ?>">
                  </i>

                <?php endif; ?>

                <span class="font-medium">
                  <?= esc($parent['name']) ?>
                </span>

              </div>

            </a>

          </li>

        <?php else: ?>

          <!-- MENU WITH SUBMENU -->
          <li>

            <button
              type="button"
              class="submenu-button group flex w-full items-center justify-between rounded-xl px-4 py-3 transition-all duration-200

                            <?= $isParentActive
                              ? 'bg-slate-100 text-slate-900 dark:bg-slate-800 dark:text-white'
                              : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800' ?>">

              <div class="flex items-center gap-3">

                <?php if (!empty($parent['icon'])): ?>

                  <i
                    data-lucide="<?= esc($parent['icon']) ?>"
                    class="h-5 w-5 transition

                                        <?= $isParentActive
                                          ? 'text-blue-600 dark:text-blue-400'
                                          : 'text-slate-500 group-hover:text-slate-900 dark:text-slate-400 dark:group-hover:text-white' ?>">
                  </i>

                <?php endif; ?>

                <span class="font-medium">
                  <?= esc($parent['name']) ?>
                </span>

              </div>

              <span
                class="submenu-arrow text-sm transition-transform duration-200

                                <?= $isParentActive
                                  ? 'rotate-90'
                                  : '' ?>">

                >

              </span>

            </button>

            <!-- SUBMENU -->
            <ul
              class="submenu mt-2 space-y-2 pl-4

                            <?= $isParentActive
                              ? ''
                              : 'hidden' ?>">

              <?php foreach ($children as $child): ?>

                <?php

                /*
                                |--------------------------------------------------------------------------
                                | ACTIVE CHILD ITEM
                                |--------------------------------------------------------------------------
                                */
                $isChildItemActive =
                  url_is($child['url']) ||
                  url_is($child['url'] . '/*');

                ?>

                <li>

                  <a
                    href="<?= site_url($child['url']) ?>"
                    class="group flex items-center rounded-xl px-4 py-3 text-sm transition-all duration-200

                                        <?= $isChildItemActive
                                          ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20'
                                          : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' ?>">

                    <div class="flex items-center gap-3">

                      <?php if (!empty($child['icon'])): ?>

                        <i
                          data-lucide="<?= esc($child['icon']) ?>"
                          class="h-4 w-4 transition

                                                    <?= $isChildItemActive
                                                      ? 'text-white'
                                                      : 'text-slate-400 group-hover:text-slate-700 dark:text-slate-500 dark:group-hover:text-white' ?>">
                        </i>

                      <?php endif; ?>

                      <span class="font-medium">
                        <?= esc($child['name']) ?>
                      </span>

                    </div>

                  </a>

                </li>

              <?php endforeach; ?>

            </ul>

          </li>

        <?php endif; ?>

      <?php endforeach; ?>

    </ul>

  </nav>

</aside>