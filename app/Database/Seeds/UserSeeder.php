<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $password = env('ADMIN_DEFAULT_PASSWORD', 'ChangeMe!2024#Secure');

        $data = [
            [
                'level_id'  => 1,
                'nama'      => 'Administrator',
                'email'    => 'admin@example.com',
                'username'  => 'admin',
                'password'  => password_hash($password, PASSWORD_DEFAULT),
                'is_active' => 1,
                'change_password' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
