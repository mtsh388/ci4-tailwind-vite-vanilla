<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MenuAccessSeeder extends Seeder
{
    public function run()
    {
        $menus = $this->db
            ->table('menus')
            ->get()
            ->getResultArray();

        $data = [];

        foreach ($menus as $menu) {

            $data[] = [
                'level_id'   => 1,
                'menu_id'    => $menu['id'],
                'can_view'   => 1,
                'can_create' => 1,
                'can_update' => 1,
                'can_delete' => 1,
            ];
        }

        $this->db
            ->table('menu_access')
            ->insertBatch($data);
    }
}
