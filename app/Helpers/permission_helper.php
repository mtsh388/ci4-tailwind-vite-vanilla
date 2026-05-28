<?php

use App\Models\MenuAccessModel;

if (!function_exists('hasPermission')) {

  function hasPermission($url, $action = 'view')
  {
    $db = db_connect();

    $menu = $db
      ->table('menus')
      ->where('url', $url)
      ->get()
      ->getRowArray();
    if (!$menu) {
      return false;
    }

    $permissionField = match ($action) {

      'view'   => 'can_view',
      'create' => 'can_create',
      'update' => 'can_update',
      'delete' => 'can_delete',

      default => 'can_view',
    };

    $access = $db
      ->table('menu_access')
      ->where('level_id', session('level_id'))
      ->where('menu_id', $menu['id'])
      ->get()
      ->getRowArray();
    if (!$access) {
      return false;
    }

    return (bool) $access[$permissionField];
  }
}
