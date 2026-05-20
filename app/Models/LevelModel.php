<?php

namespace App\Models;

use CodeIgniter\Model;

class LevelModel extends Model
{
    protected $table = 'levels';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'name',
    ];

    protected $useTimestamps = true;
}
