<?php

namespace App\Controllers;

use App\Models\LevelModel;

class Level extends BaseController
{
    protected $levelModel;

    public function __construct()
    {
        $this->levelModel = new LevelModel();
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        if (!hasPermission('levels', 'view')) {
            throw \CodeIgniter\Exceptions\PageForbiddenException::forPageForbidden();
        }
        return $this->render('levels/index', [
            'title'  => 'Levels',
            'levels' => $this->levelModel
                ->orderBy('id', 'DESC')
                ->findAll(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        if (!hasPermission('levels', 'create')) {
            throw \CodeIgniter\Exceptions\PageForbiddenException::forPageForbidden();
        }
        return $this->render('levels/create', [
            'title' => 'Tambah Level',
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
            'name' => 'required|min_length[3]',
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

        $inserted = $this->levelModel->insert([
            'name' => $this->request->getPost('name'),
        ]);

        if (!$inserted) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan level');
        }

        return redirect()
            ->to('/levels')
            ->with(
                'success',
                'Level berhasil ditambahkan'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $level = $this->levelModel->find($id);

        if (!$level) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->render('levels/edit', [
            'title' => 'Edit Level',
            'level' => $level,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update($id)
    {
        if (!hasPermission('levels', 'update')) {
            throw \CodeIgniter\Exceptions\PageForbiddenException::forPageForbidden();
        }
        $rules = [
            'name' => 'required|min_length[3]',
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

        $level = $this->levelModel->find($id);

        if (!$level) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $updated = $this->levelModel->update($id, [
            'name' => $this->request->getPost('name'),
        ]);

        if (!$updated) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate level');
        }

        return redirect()
            ->to('/levels')
            ->with(
                'success',
                'Level berhasil diupdate'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function delete($id)
    {
        if (!hasPermission('levels', 'delete')) {
            throw \CodeIgniter\Exceptions\PageForbiddenException::forPageForbidden();
        }

        $level = $this->levelModel->find($id);

        if (!$level) {
            return redirect()
                ->to('/levels')
                ->with('error', 'Level tidak ditemukan');
        }

        $deleted = $this->levelModel->delete($id);

        if (!$deleted) {
            return redirect()
                ->to('/levels')
                ->with('error', 'Gagal menghapus level');
        }

        return redirect()
            ->to('/levels')
            ->with(
                'success',
                'Level berhasil dihapus'
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
        $builder = $this->levelModel;

        /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */
        if (!empty($search)) {

            $builder = $builder
                ->groupStart()
                ->like('name', $search)
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
    | ORDER
    |--------------------------------------------------------------------------
    */
        $columns = [
            0 => 'id',
            1 => 'name',
        ];

        $orderColumnIndex = $request->getPost('order')[0]['column'] ?? 1;
        $orderDir         = $request->getPost('order')[0]['dir'] ?? 'asc';

        $orderColumn = $columns[$orderColumnIndex] ?? 'name';

        $builder->orderBy($orderColumn, $orderDir);

        /*
    |--------------------------------------------------------------------------
    | GET DATA
    |--------------------------------------------------------------------------
    */
        $levels = $builder->findAll($length, $start);

        $data = [];

        foreach ($levels as $key => $level) {

            $action = '<div class="flex items-center gap-2">';

            /*
        |--------------------------------------------------------------------------
        | ACCESS
        |--------------------------------------------------------------------------
        */
            $action .= '
            <a
                href="' . site_url('menu-access/' . $level['id']) . '"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">

                Access
            </a>
        ';

            /*
        |--------------------------------------------------------------------------
        | EDIT
        |--------------------------------------------------------------------------
        */
            if (hasPermission('levels', 'update')) {

                $action .= '
                <a
                    href="' . site_url('levels/edit/' . $level['id']) . '"
                    class="rounded-lg bg-yellow-500 px-4 py-2 text-sm text-white hover:bg-yellow-600">

                    Edit
                </a>
            ';
            }

            /*
        |--------------------------------------------------------------------------
        | DELETE
        |--------------------------------------------------------------------------
        */
            if (hasPermission('levels', 'delete')) {

                $action .= '
                <a
                    href="' . site_url('levels/delete/' . $level['id']) . '"
                    onclick="return confirm(\'Hapus data ini?\')"
                    class="rounded-lg bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700">

                    Delete
                </a>
            ';
            }

            $action .= '</div>';

            $data[] = [
                'no'     => $start + $key + 1,
                'name'   => esc($level['name']),
                'action' => $action,
            ];
        }

        /*
    |--------------------------------------------------------------------------
    | TOTAL
    |--------------------------------------------------------------------------
    */
        $total = $this->levelModel->countAll();

        return $this->response->setJSON([
            'draw'            => intval($draw),
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $data,
        ]);
    }
}
