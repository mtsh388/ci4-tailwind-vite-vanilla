<?php

namespace App\Controllers;

use App\Models\MenuModel;
use App\Models\MenuAccessModel;
use App\Models\LevelModel;

class Menus extends BaseController
{
    protected $menuModel;
    protected $menuAccessModel;
    protected $levelModel;
    public function __construct()
    {
        $this->menuModel = new MenuModel();
        $this->menuAccessModel = new MenuAccessModel();
        $this->levelModel = new LevelModel();
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

        if (!$this->validate($rules)) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    validation_list_errors()
                );
        }

        $parentId = $this->request->getPost('parent_id');

        /*
            |--------------------------------------------------------------------------
            | INSERT MENU
            |--------------------------------------------------------------------------
        */
        $inserted = $this->menuModel->insert([
            'parent_id'  => $parentId ?: null,
            'name'       => $this->request->getPost('name'),
            'icon'       => $this->request->getPost('icon'),
            'url'        => $this->request->getPost('url'),
            'sort_order' => $this->request->getPost('sort_order'),
            'is_active'  => $this->request->getPost('is_active'),
        ]);

        if (!$inserted) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan menu');
        }

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

        if (!$this->validate($rules)) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    validation_list_errors()
                );
        }

        $menu = $this->menuModel->find($id);

        if (!$menu) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $parentId = $this->request->getPost('parent_id');

        $updated = $this->menuModel->update($id, [
            'parent_id' => $parentId ?: null,
            'name'      => $this->request->getPost('name'),
            'icon'      => $this->request->getPost('icon'),
            'url'       => $this->request->getPost('url'),
            'sort_order' => $this->request->getPost('sort_order'),
            'is_active' => $this->request->getPost('is_active'),
        ]);

        if (!$updated) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate menu');
        }

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
        if (!empty($search)) {

            $builder->groupStart()
                ->like('menus.name', $search)
                ->orLike('menus.url', $search)
                ->orLike('menus.icon', $search)
                ->orLike('parent.name', $search)
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

        $orderColumnIndex = $request->getPost('order')[0]['column'] ?? 1;
        $orderDir         = $request->getPost('order')[0]['dir'] ?? 'asc';

        $orderColumn = $columns[$orderColumnIndex] ?? 'menus.sort_order';

        /*
        |--------------------------------------------------------------------------
        | FIX ORDER PARENT + CHILD
        |--------------------------------------------------------------------------
        */
        $builder
            ->orderBy('IFNULL(menus.parent_id, menus.id)', '', false)
            ->orderBy('(menus.parent_id IS NULL)', 'DESC', false)
            ->orderBy($orderColumn, $orderDir)
            ->orderBy('menus.sort_order', 'ASC');

        /*
        |--------------------------------------------------------------------------
        | GET DATA
        |--------------------------------------------------------------------------
        */
        $menus = $builder->findAll($length, $start);

        /*
        |--------------------------------------------------------------------------
        | GROUPING MENU
        |--------------------------------------------------------------------------
        */
        $groupedMenus = [];

        foreach ($menus as $menu) {

            /*
            |--------------------------------------------------------------------------
            | STATUS BADGE
            |--------------------------------------------------------------------------
            */
            $badge = '
            <label class="relative inline-flex cursor-pointer items-center">

                <input
                    type="checkbox"
                    value="' . $menu['id'] . '"
                    class="toggle-status peer sr-only"
                    data-url="' . site_url('menus/toggle-status') . '"
                    ' . ($menu['is_active'] ? 'checked' : '') . '>

                <div
                    class="peer h-6 w-11 rounded-full bg-slate-300 transition

                    dark:bg-slate-700

                    after:absolute
                    after:left-[2px]
                    after:top-[2px]
                    after:h-5
                    after:w-5
                    after:rounded-full
                    after:bg-white
                    after:transition-all

                    peer-checked:bg-green-500
                    peer-checked:after:translate-x-full">
                </div>

            </label>
        ';

            /*
            |--------------------------------------------------------------------------
            | ACTION BUTTONS
            |--------------------------------------------------------------------------
            */
            $actionButtons = [];

            if (hasPermission('menus', 'update')) {

                $actionButtons[] = '
                <a
                    href="' . site_url('menus/edit/' . $menu['id']) . '"
                    class="rounded-lg
                           bg-yellow-500
                           px-3 py-2
                           text-xs
                           font-medium
                           text-white
                           transition
                           hover:bg-yellow-600">

                    Edit

                </a>
            ';
            }

            if (hasPermission('menus', 'delete')) {

                $actionButtons[] = '
                <button
                    type="button"
                    data-id="' . $menu['id'] . '"
                    class="btn-delete
                           rounded-lg
                           bg-red-600
                           px-3 py-2
                           text-xs
                           font-medium
                           text-white
                           transition
                           hover:bg-red-700">

                    Delete

                </button>
            ';
            }

            $action = '
            <div class="flex items-center justify-center gap-2">
                ' . implode('', $actionButtons) . '
            </div>
        ';

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

                /*
                |--------------------------------------------------------------------------
                | STATUS BADGE
                |--------------------------------------------------------------------------
                */
                $badge = '
                <label class="relative inline-flex cursor-pointer items-center">

                    <input
                        type="checkbox"
                        value="' . $menu['id'] . '"
                        class="toggle-status peer sr-only"
                        data-url="' . site_url('menus/toggle-status') . '"
                        ' . ($menu['is_active'] ? 'checked' : '') . '>

                    <div
                        class="peer h-6 w-11 rounded-full bg-slate-300 transition

                        dark:bg-slate-700

                        after:absolute
                        after:left-[2px]
                        after:top-[2px]
                        after:h-5
                        after:w-5
                        after:rounded-full
                        after:bg-white
                        after:transition-all

                        peer-checked:bg-green-500
                        peer-checked:after:translate-x-full">
                    </div>

                </label>
            ';

                /*
                |--------------------------------------------------------------------------
                | ACTION BUTTONS
                |--------------------------------------------------------------------------
                */
                $actionButtons = [];

                if (hasPermission('menus', 'update')) {

                    $actionButtons[] = '
                    <a
                        href="' . site_url('menus/edit/' . $menu['id']) . '"
                        class="rounded-lg
                               bg-yellow-500
                               px-3 py-2
                               text-xs
                               font-medium
                               text-white
                               transition
                               hover:bg-yellow-600">

                        Edit

                    </a>
                ';
                }

                if (hasPermission('menus', 'delete')) {

                    $actionButtons[] = '
                    <button
                        type="button"
                        data-id="' . $menu['id'] . '"
                        class="btn-delete
                               rounded-lg
                               bg-red-600
                               px-3 py-2
                               text-xs
                               font-medium
                               text-white
                               transition
                               hover:bg-red-700">

                        Delete

                    </button>
                ';
                }

                $action = '
                <div class="flex items-center justify-center gap-2">
                    ' . implode('', $actionButtons) . '
                </div>
            ';

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

        return $this->response->setJSON([
            'draw'            => intval($draw),
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $data,
        ]);
    }
    public function toggleStatus($id)
    {
        /*
            |--------------------------------------------------------------------------
            | GET MENU
            |--------------------------------------------------------------------------
        */
        $menu = $this->menuModel->find($id);

        if (!$menu) {

            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Menu tidak ditemukan',
            ]);
        }

        /*
            |--------------------------------------------------------------------------
            | TOGGLE STATUS
            |--------------------------------------------------------------------------
        */
        $newStatus = $menu['is_active'] ? 0 : 1;

        $updated = $this->menuModel->update($id, [
            'is_active' => $newStatus,
        ]);

        if (!$updated) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Gagal mengupdate status',
            ]);
        }

        return $this->response->setJSON([
            'status'    => true,
            'message'   => 'Status berhasil diupdate',
            'is_active' => $newStatus,
        ]);
    }
}
