<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'level_id'  => 1,
                'nama'      => 'Administrator',
                'email'    => 'mtsh388@gmail.com',
                'username'  => 'admin',
                'password'  => password_hash('admin123', PASSWORD_DEFAULT),
                'is_active' => 1,
                'change_password' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
