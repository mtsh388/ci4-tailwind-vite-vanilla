<?php

namespace App\Controllers;

use App\Models\MenuModel;
use App\Models\MenuAccessModel;
use App\Models\LevelModel;
use App\Traits\DatatableTrait;
use App\Traits\ToggleStatusTrait;

class Menus extends BaseController
{
    use DatatableTrait;
    use ToggleStatusTrait;

    protected $menuModel;
    protected $menuAccessModel;
    protected $levelModel;
    public function __construct()
    {
        $this->menuModel = new MenuModel();
        $this->menuAccessModel = new MenuAccessModel();
        $this->levelModel = new LevelModel();
        helper('datatable_html');
    }

    /*
        |--------------------------------------------------------------------------
        | INDEX
        |--------------------------------------------------------------------------
    */
    public function index()
    {
        $menus = $this->menuModel
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        return $this->render('menus/index', [
            'title' => 'Menus',
            'menus' => $menus,
        ]);
    }

    /*
        |--------------------------------------------------------------------------
        | CREATE
        |--------------------------------------------------------------------------
    */
    public function create()
    {
        return $this->render('menus/create', [
            'title' => 'Tambah Menu',

            'parents' => $this->menuModel
                ->where('parent_id', null)
                ->findAll(),
        ]);
    }

    /*
        |--------------------------------------------------------------------------
        | STORE
        |--------------------------------------------------------------------------
    */
    public function store()
    {
        $rules = [
            'name' => 'required',
            'url'  => 'required',
        ];

        if (!$this->validateOrRedirect($rules)) {

            return redirect()
                ->back()
                ->withInput();
        }

        $parentId = $this->request->getPost('parent_id');

        /*
            |--------------------------------------------------------------------------
            | INSERT MENU
            |--------------------------------------------------------------------------
        */
        $this->menuModel->insert([
            'parent_id'  => $parentId ?: null,
            'name'       => $this->request->getPost('name'),
            'icon'       => $this->request->getPost('icon'),
            'url'        => $this->request->getPost('url'),
            'sort_order' => $this->request->getPost('sort_order'),
            'is_active'  => $this->request->getPost('is_active'),
        ]);

        /*
            |--------------------------------------------------------------------------
            | GET MENU ID
            |--------------------------------------------------------------------------
        */
        $menuId = $this->menuModel->getInsertID();

        /*
            |--------------------------------------------------------------------------
            | AUTO CREATE MENU ACCESS
            |--------------------------------------------------------------------------
        */
        $url = trim($this->request->getPost('url'));

        if (!empty($url)) {

            /*
                |--------------------------------------------------------------------------
                | GET ALL LEVEL
                |--------------------------------------------------------------------------
            */
            $levels = $this->levelModel->findAll();

            $insertData = [];

            foreach ($levels as $level) {
                if ($level['id'] == 1) {
                    $insertData[] = [
                        'level_id'   => $level['id'],
                        'menu_id'    => $menuId,
                        'can_view'   => 1,
                        'can_create' => 1,
                        'can_update' => 1,
                        'can_delete' => 1,
                    ];
                } else {
                    $insertData[] = [
                        'level_id'   => $level['id'],
                        'menu_id'    => $menuId,
                        'can_view'   => 0,
                        'can_create' => 0,
                        'can_update' => 0,
                        'can_delete' => 0,
                    ];
                }
            }

            /*
                |--------------------------------------------------------------------------
                | INSERT BATCH
                |--------------------------------------------------------------------------
            */
            if (!empty($insertData)) {
                $this->menuAccessModel->insertBatch($insertData);
            }
        }

        return redirect()
            ->to('/menus')
            ->with(
                'success',
                'Menu berhasil ditambahkan'
            );
    }

    /*
        |--------------------------------------------------------------------------
        | EDIT
        |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $menu = $this->menuModel->find($id);

        if (!$menu) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->render('menus/edit', [
            'title' => 'Edit Menu',
            'menu'  => $menu,

            'parents' => $this->menuModel
                ->where('parent_id', null)
                ->where('id !=', $id)
                ->findAll(),
        ]);
    }

    /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
    */
    public function update($id)
    {
        $rules = [
            'name' => 'required',
            'url'  => 'required',
        ];

        if (!$this->validateOrRedirect($rules)) {

            return redirect()
                ->back()
                ->withInput();
        }

        $parentId = $this->request->getPost('parent_id');

        $this->menuModel->update($id, [
            'parent_id' => $parentId ?: null,
            'name'      => $this->request->getPost('name'),
            'icon'      => $this->request->getPost('icon'),
            'url'       => $this->request->getPost('url'),
            'sort_order' => $this->request->getPost('sort_order'),
            'is_active' => $this->request->getPost('is_active'),
        ]);

        return redirect()
            ->to('/menus')
            ->with(
                'success',
                'Menu berhasil diupdate'
            );
    }

    /*
        |--------------------------------------------------------------------------
        | DELETE
        |--------------------------------------------------------------------------
    */
    public function delete($id)
    {
        $db = \Config\Database::connect();

        $db->transStart();

        /*
            |--------------------------------------------------------------------------
            | DELETE MENU ACCESS
            |--------------------------------------------------------------------------
        */
        $this->menuAccessModel
            ->where('menu_id', $id)
            ->delete();

        /*
            |--------------------------------------------------------------------------
            | DELETE MENU
            |--------------------------------------------------------------------------
        */
        $this->menuModel->delete($id);

        $db->transComplete();

        /*
            |--------------------------------------------------------------------------
            | CHECK TRANSACTION
            |--------------------------------------------------------------------------
        */
        if ($db->transStatus() === false) {

            return redirect()
                ->to('/menus')
                ->with(
                    'error',
                    'Menu gagal dihapus'
                );
        }

        return redirect()
            ->to('/menus')
            ->with(
                'success',
                'Menu berhasil dihapus'
            );
    }

    public function datatable()
    {
        $dt = $this->getDatatableRequest();

        /*
        |--------------------------------------------------------------------------
        | QUERY
        |--------------------------------------------------------------------------
        */
        $builder = $this->menuModel
            ->select('menus.*, parent.name as parent_name')
            ->join(
                'menus parent',
                'parent.id = menus.parent_id',
                'left'
            );

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */
        if (!empty($dt['search'])) {

            $builder->groupStart()
                ->like('menus.name', $dt['search'])
                ->orLike('menus.url', $dt['search'])
                ->orLike('menus.icon', $dt['search'])
                ->orLike('parent.name', $dt['search'])
                ->groupEnd();
        }

        /*
        |--------------------------------------------------------------------------
        | TOTAL FILTERED
        |--------------------------------------------------------------------------
        */
        $filtered = $builder->countAllResults(false);

        /*
        |--------------------------------------------------------------------------
        | ORDERING
        |--------------------------------------------------------------------------
        */
        $columns = [
            0 => 'parent.name',
            1 => 'menus.name',
            2 => 'menus.url',
            3 => 'menus.icon',
            4 => 'menus.is_active',
        ];

        $orderColumn = $this->getDatatableOrderColumn(
            $columns,
            $dt['orderColumnIndex'],
            'menus.sort_order'
        );

        /*
        |--------------------------------------------------------------------------
        | FIX ORDER PARENT + CHILD
        |--------------------------------------------------------------------------
        */
        $builder
            ->orderBy('IFNULL(menus.parent_id, menus.id)', '', false)
            ->orderBy('(menus.parent_id IS NULL)', 'DESC', false)
            ->orderBy($orderColumn, $dt['orderDir'])
            ->orderBy('menus.sort_order', 'ASC');

        /*
        |--------------------------------------------------------------------------
        | GET DATA
        |--------------------------------------------------------------------------
        */
        $menus = $builder->findAll($dt['length'], $dt['start']);

        /*
        |--------------------------------------------------------------------------
        | GROUPING MENU
        |--------------------------------------------------------------------------
        */
        $groupedMenus = [];

        foreach ($menus as $menu) {

            $badge = renderToggleSwitch(
                $menu['id'],
                'menus/toggle-status',
                (bool) $menu['is_active']
            );

            $actionButtons = [];

            if (hasPermission('menus', 'update')) {
                $actionButtons[] = renderEditButton('menus/edit/' . $menu['id']);
            }

            if (hasPermission('menus', 'delete')) {
                $actionButtons[] = renderDeleteButton('menus/delete/' . $menu['id'], 'button');
            }

            $action = renderActionButtons($actionButtons, 'center');

            /*
            |--------------------------------------------------------------------------
            | PARENT MENU
            |--------------------------------------------------------------------------
            */
            if (empty($menu['parent_id'])) {

                $groupedMenus[$menu['id']] = [
                    'parent_row' => [
                        'parent' => '
                        <span class="font-semibold text-slate-900 dark:text-white">
                            ' . esc($menu['name']) . '
                        </span>
                    ',

                        'name'   => '
                        <span class="text-slate-400 dark:text-slate-500">
                            -
                        </span>
                    ',

                        'url'    => '
                        <span class="text-slate-700 dark:text-slate-300">
                            ' . ($menu['url'] ?: '-') . '
                        </span>
                    ',

                        'icon'   => '
                        <span class="text-slate-700 dark:text-slate-300">
                            ' . ($menu['icon'] ?: '-') . '
                        </span>
                    ',

                        'status' => $badge,

                        'action' => $action,
                    ],

                    'children' => [],
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CHILD MENU
        |--------------------------------------------------------------------------
        */
        foreach ($menus as $menu) {

            if (!empty($menu['parent_id'])) {

                /*
                |--------------------------------------------------------------------------
                | PARENT BELUM ADA
                |--------------------------------------------------------------------------
                */
                if (!isset($groupedMenus[$menu['parent_id']])) {

                    $groupedMenus[$menu['parent_id']] = [
                        'parent_row' => [],
                        'children'   => [],
                    ];
                }

                $badge = renderToggleSwitch(
                    $menu['id'],
                    'menus/toggle-status',
                    (bool) $menu['is_active']
                );

                $actionButtons = [];

                if (hasPermission('menus', 'update')) {
                    $actionButtons[] = renderEditButton('menus/edit/' . $menu['id']);
                }

                if (hasPermission('menus', 'delete')) {
                    $actionButtons[] = renderDeleteButton('menus/delete/' . $menu['id'], 'button');
                }

                $action = renderActionButtons($actionButtons, 'center');

                $groupedMenus[$menu['parent_id']]['children'][] = [

                    'parent' => '',

                    'name'   => '
                    <div class="pl-6 text-slate-700 dark:text-slate-300">
                        └ ' . esc($menu['name']) . '
                    </div>
                ',

                    'url'    => '
                    <span class="text-slate-700 dark:text-slate-300">
                        ' . ($menu['url'] ?: '-') . '
                    </span>
                ',

                    'icon'   => '
                    <span class="text-slate-700 dark:text-slate-300">
                        ' . ($menu['icon'] ?: '-') . '
                    </span>
                ',

                    'status' => $badge,

                    'action' => $action,
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | FLATTEN
        |--------------------------------------------------------------------------
        */
        $data = [];

        foreach ($groupedMenus as $group) {

            if (!empty($group['parent_row'])) {
                $data[] = $group['parent_row'];
            }

            foreach ($group['children'] as $child) {
                $data[] = $child;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */
        $total = $this->menuModel->countAll();

        return $this->formatDatatableResponse($dt['draw'], $total, $filtered, $data);
    }

    public function toggleStatus($id)
    {
        return $this->handleToggleStatus($this->menuModel, $id, 'Menu');
    }
}
