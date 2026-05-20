<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuAccessModel extends Model
{
    protected $table = 'menu_access';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'level_id',
        'menu_id',

        'can_view',
        'can_create',
        'can_update',
        'can_delete',
    ];
}
