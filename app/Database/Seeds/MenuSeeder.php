<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run()
    {
        $menus = [

            [
                'name'       => 'Dashboard',
                'url'        => '/dashboard',
                'icon'       => 'layout-dashboard',
                'sort_order' => 1,
            ],

            [
                'name'       => 'Users',
                'url'        => '/users',
                'icon'       => 'users',
                'sort_order' => 2,
            ],

            [
                'name'       => 'Levels',
                'url'        => '/levels',
                'icon'       => 'shield',
                'sort_order' => 3,
            ],

            [
                'name'       => 'Menus',
                'url'        => '/menus',
                'icon'       => 'menu',
                'sort_order' => 4,
            ],

            [
                'name'       => 'Change Password',
                'url'        => '/change-password',
                'icon'       => 'key-round',
                'sort_order' => 5,
            ],

        ];

        $this->db->table('menus')->insertBatch($menus);
    }
}
