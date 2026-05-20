<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';

    protected $returnType       = 'array';

    protected $allowedFields = [
        'level_id',
        'nama',
        'username',
        'email',
        'password',
        'is_active',
        'change_password',
    ];

    protected $useTimestamps = true;
}
