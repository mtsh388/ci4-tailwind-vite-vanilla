<?php

namespace App\Controllers;

use App\Models\LevelModel;
use App\Models\MenuModel;
use App\Models\MenuAccessModel;

class MenuAccess extends BaseController
{
    protected $levelModel;
    protected $menuModel;
    protected $menuAccessModel;

    public function __construct()
    {
        $this->levelModel = new LevelModel();

        $this->menuModel = new MenuModel();

        $this->menuAccessModel = new MenuAccessModel();
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index($levelId)
    {
        $level = $this->levelModel->find($levelId);

        if (!$level) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $menus = $this->menuModel
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        $accessMenuIds = $this->menuAccessModel
            ->where('level_id', $levelId)
            ->findAll();

        return $this->render('menu-access/index', [
            'title' => 'Menu Access',

            'level' => $level,

            'menus' => $menus,

            'accessMenuIds' => $accessMenuIds,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update($levelId)
    {
        $permissions =
            $this->request->getPost('permissions') ?? [];

        /*
        |--------------------------------------------------------------------------
        | DELETE OLD ACCESS
        |--------------------------------------------------------------------------
        */
        $this->menuAccessModel
            ->where('level_id', $levelId)
            ->delete();

        /*
        |--------------------------------------------------------------------------
        | INSERT NEW ACCESS
        |--------------------------------------------------------------------------
        */
        foreach ($permissions as $menuId => $permission) {

            $this->menuAccessModel->insert([

                'level_id' => $levelId,

                'menu_id' => $menuId,

                'can_view' =>
                isset($permission['view']) ? 1 : 0,

                'can_create' =>
                isset($permission['create']) ? 1 : 0,

                'can_update' =>
                isset($permission['update']) ? 1 : 0,

                'can_delete' =>
                isset($permission['delete']) ? 1 : 0,
            ]);
        }

        return redirect()
            ->to('/levels')
            ->with(
                'success',
                'Permission berhasil diupdate'
            );
    }

    public function datatable($levelId)
    {
        $request = service('request');

        $draw   = $request->getPost('draw');
        $start  = $request->getPost('start');
        $length = $request->getPost('length');
        $search = $request->getPost('search')['value'] ?? '';

        /*
    |--------------------------------------------------------------------------
    | QUERY
    |--------------------------------------------------------------------------
    */
        $builder = $this->menuModel
            ->select('
            menus.*,
            parent.name as parent_name,
            menu_access.can_view,
            menu_access.can_create,
            menu_access.can_update,
            menu_access.can_delete
        ')
            ->join(
                'menus parent',
                'parent.id = menus.parent_id',
                'left'
            )
            ->join(
                'menu_access',
                'menu_access.menu_id = menus.id
            AND menu_access.level_id = ' . (int)$levelId,
                'left'
            )
            ->where('menus.url !=', '')
            ->where('menus.url !=', '#');

        /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */
        if (!empty($search)) {

            $builder->groupStart()
                ->like('menus.name', $search)
                ->orLike('parent.name', $search)
                ->groupEnd();
        }

        /*
    |--------------------------------------------------------------------------
    | FILTERED
    |--------------------------------------------------------------------------
    */
        $filtered = $builder->countAllResults(false);

        /*
    |--------------------------------------------------------------------------
    | ORDER
    |--------------------------------------------------------------------------
    */
        $builder
            ->orderBy('parent.name', 'ASC')
            ->orderBy('menus.name', 'ASC');

        /*
    |--------------------------------------------------------------------------
    | GET DATA
    |--------------------------------------------------------------------------
    */
        $menus = $builder->findAll($length, $start);

        $data = [];

        foreach ($menus as $menu) {

            $data[] = [

                'parent' => $menu['parent_name'] ?? '-',

                'menu' => '
                <div class="' . (!empty($menu['parent_id']) ? 'pl-6' : '') . '">
                    ' . esc($menu['name']) . '
                </div>
            ',

                'view' => $this->permissionCheckbox(
                    $levelId,
                    $menu['id'],
                    'view',
                    $menu['can_view']
                ),

                'create' => $this->permissionCheckbox(
                    $levelId,
                    $menu['id'],
                    'create',
                    $menu['can_create']
                ),

                'update' => $this->permissionCheckbox(
                    $levelId,
                    $menu['id'],
                    'update',
                    $menu['can_update']
                ),

                'delete' => $this->permissionCheckbox(
                    $levelId,
                    $menu['id'],
                    'delete',
                    $menu['can_delete']
                ),
            ];
        }

        /*
    |--------------------------------------------------------------------------
    | TOTAL
    |--------------------------------------------------------------------------
    */
        $total = $this->menuModel
            ->where('url !=', '')
            ->where('url !=', '#')
            ->countAllResults();

        return $this->response->setJSON([
            'draw'            => intval($draw),
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $data,
        ]);
    }
    private function permissionCheckbox(
        $levelId,
        $menuId,
        $permission,
        $checked = 0
    ) {
        return '
        <input
            type="checkbox"

            class="permission-checkbox
                   h-5 w-5 rounded border-slate-300"

            data-level="' . $levelId . '"
            data-menu="' . $menuId . '"
            data-permission="' . $permission . '"

            ' . ($checked ? 'checked' : '') . '
        >
    ';
    }
    public function updatePermission()
    {
        $request = service('request');

        $data = $request->getJSON(true);

        if (
            empty($data['level_id']) || !is_numeric($data['level_id'])
            || empty($data['menu_id']) || !is_numeric($data['menu_id'])
            || empty($data['permission'])
            || !in_array($data['permission'], ['view', 'create', 'update', 'delete'], true)
            || !isset($data['value']) || !in_array((int) $data['value'], [0, 1], true)
        ) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Invalid input',
            ]);
        }

        $levelId    = (int) $data['level_id'];
        $menuId     = (int) $data['menu_id'];
        $permission = $data['permission'];
        $value      = (int) $data['value'];

        /*
    |--------------------------------------------------------------------------
    | CHECK EXIST
    |--------------------------------------------------------------------------
    */
        $access = $this->menuAccessModel
            ->where('level_id', $levelId)
            ->where('menu_id', $menuId)
            ->first();

        if (!$access) {

            $this->menuAccessModel->insert([
                'level_id'   => $levelId,
                'menu_id'    => $menuId,
                'can_view'   => 0,
                'can_create' => 0,
                'can_update' => 0,
                'can_delete' => 0,
            ]);

            $access = $this->menuAccessModel
                ->where('level_id', $levelId)
                ->where('menu_id', $menuId)
                ->first();
        }

        /*
    |--------------------------------------------------------------------------
    | FIELD
    |--------------------------------------------------------------------------
    */
        $field = match ($permission) {
            'view'   => 'can_view',
            'create' => 'can_create',
            'update' => 'can_update',
            'delete' => 'can_delete',
        };

        /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
        $this->menuAccessModel->update($access['id'], [
            $field => $value
        ]);

        return $this->response->setJSON([
            'success' => true,
        ]);
    }
}
