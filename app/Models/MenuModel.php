<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuModel extends Model
{
    protected $table = 'menus';

    protected $returnType = 'array';
    protected $allowedFields = [
        'parent_id',
        'name',
        'icon',
        'url',
        'sort_order',
        'is_active',
    ];
    /*
    |--------------------------------------------------------------------------
    | GET SIDEBAR MENU
    |--------------------------------------------------------------------------
    */
    public function getSidebarMenu($levelId)
    {
        /*
        |--------------------------------------------------------------------------
        | GET ACCESSIBLE MENUS
        |--------------------------------------------------------------------------
        */
        $menus = $this->db
            ->table('menus m')
            ->select('m.*')
            ->join(
                'menu_access ma',
                'ma.menu_id = m.id'
            )
            ->where('ma.level_id', $levelId)
            ->where('ma.can_view', 1)
            ->where('m.is_active', 1)
            ->orderBy('m.sort_order', 'ASC')
            ->get()
            ->getResultArray();

        /*
        |--------------------------------------------------------------------------
        | INCLUDE PARENT MENUS
        |--------------------------------------------------------------------------
        */
        $parentIds = [];

        foreach ($menus as $menu) {

            if (!empty($menu['parent_id'])) {
                $parentIds[] = $menu['parent_id'];
            }
        }

        $parentIds = array_unique($parentIds);

        /*
        |--------------------------------------------------------------------------
        | GET PARENT MENUS
        |--------------------------------------------------------------------------
        */
        $parents = [];

        if (!empty($parentIds)) {

            $parents = $this->db
                ->table('menus')
                ->whereIn('id', $parentIds)
                ->where('is_active', 1)
                ->get()
                ->getResultArray();
        }

        /*
        |--------------------------------------------------------------------------
        | MERGE
        |--------------------------------------------------------------------------
        */
        $menuIds = array_column($menus, 'id');

        $filteredParents = array_filter($parents, function ($parent) use ($menuIds) {
            return !in_array($parent['id'], $menuIds);
        });

        $result = array_merge($filteredParents, $menus);
        usort($result, function ($a, $b) {
            return $a['sort_order'] <=> $b['sort_order'];
        });

        return $result;
    }
}
