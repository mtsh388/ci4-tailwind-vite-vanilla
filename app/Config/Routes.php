<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');

$routes->get('/login', 'Auth::login');
$routes->post('/login/process', 'Auth::process');
$routes->get('/logout', 'Auth::logout');

$routes->group('', ['filter' => 'auth'], function ($routes) {

  $routes->get('/dashboard', 'Dashboard::index');
  $routes->get('/change-password', 'Auth::changePassword');
  $routes->post('/change-password', 'Auth::updatePassword');
  $routes->get('/forgot-password', 'Auth::forgotPassword');
  // User Routes
  $routes->group('users', function ($routes) {

    $routes->get('/', 'Users::index');

    $routes->get('create', 'Users::create');
    $routes->post('store', 'Users::store');

    $routes->get('edit/(:num)', 'Users::edit/$1');
    $routes->post('update/(:num)', 'Users::update/$1');

    $routes->post('delete/(:num)', 'Users::delete/$1');

    $routes->post('datatable', 'Users::datatable');
    $routes->post('toggle-status/(:num)', 'Users::toggleStatus/$1');
  });

  // Level Routes
  $routes->group('levels', function ($routes) {
    $routes->get('/', 'Level::index');
    $routes->get('create', 'Level::create');
    $routes->post('store', 'Level::store');
    $routes->get('edit/(:num)', 'Level::edit/$1');
    $routes->post('update/(:num)', 'Level::update/$1');
    $routes->post('delete/(:num)', 'Level::delete/$1');
    $routes->post('datatable', 'Level::datatable');
  });
  // Menu Routes
  $routes->group('menus', function ($routes) {
    $routes->get('/', 'Menus::index');
    $routes->get('create', 'Menus::create');
    $routes->post('store', 'Menus::store');
    $routes->get('edit/(:num)', 'Menus::edit/$1');
    $routes->post('update/(:num)', 'Menus::update/$1');
    $routes->post('delete/(:num)', 'Menus::delete/$1');
    $routes->post('datatable', 'Menus::datatable');
    $routes->post('toggle-status/(:num)', 'Menus::toggleStatus/$1');
  });

  // Menu Access Routes
  $routes->group('menu-access', function ($routes) {
    $routes->get(
      '(:num)',
      'MenuAccess::index/$1'
    );

    $routes->post(
      'update/(:num)',
      'MenuAccess::update/$1'
    );
    $routes->post(
      'datatable/(:num)',
      'MenuAccess::datatable/$1'
    );

    $routes->post(
      'update-permission',
      'MenuAccess::updatePermission'
    );
  });
});
