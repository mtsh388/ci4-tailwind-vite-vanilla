<?php

namespace App\Controllers;

use App\Models\LevelModel;
use App\Traits\DatatableTrait;

class Level extends BaseController
{
    use DatatableTrait;

    protected $levelModel;

    public function __construct()
    {
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
        $this->checkPermission('levels', 'view');

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
        $this->checkPermission('levels', 'create');

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

        if (!$this->validateOrRedirect($rules)) {

            return redirect()
                ->back()
                ->withInput();
        }

        $this->levelModel->insert([
            'name' => $this->request->getPost('name'),
        ]);

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
        $this->checkPermission('levels', 'update');

        $rules = [
            'name' => 'required|min_length[3]',
        ];

        if (!$this->validateOrRedirect($rules)) {

            return redirect()
                ->back()
                ->withInput();
        }

        $this->levelModel->update($id, [
            'name' => $this->request->getPost('name'),
        ]);

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
        $this->checkPermission('levels', 'delete');

        $this->levelModel->delete($id);

        return redirect()
            ->to('/levels')
            ->with(
                'success',
                'Level berhasil dihapus'
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
        $builder = $this->levelModel;

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */
        if (!empty($dt['search'])) {

            $builder = $builder
                ->groupStart()
                ->like('name', $dt['search'])
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

        $orderColumn = $this->getDatatableOrderColumn(
            $columns,
            $dt['orderColumnIndex'],
            'name'
        );

        $builder->orderBy($orderColumn, $dt['orderDir']);

        /*
        |--------------------------------------------------------------------------
        | GET DATA
        |--------------------------------------------------------------------------
        */
        $levels = $builder->findAll($dt['length'], $dt['start']);

        $data = [];

        foreach ($levels as $key => $level) {

            $actionButtons = [];

            $actionButtons[] = '
            <a
                href="' . site_url('menu-access/' . $level['id']) . '"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">

                Access
            </a>
        ';

            if (hasPermission('levels', 'update')) {
                $actionButtons[] = renderEditButton('levels/edit/' . $level['id']);
            }

            if (hasPermission('levels', 'delete')) {
                $actionButtons[] = renderDeleteButton('levels/delete/' . $level['id']);
            }

            $data[] = [
                'no'     => $dt['start'] + $key + 1,
                'name'   => esc($level['name']),
                'action' => renderActionButtons($actionButtons),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */
        $total = $this->levelModel->countAll();

        return $this->formatDatatableResponse($dt['draw'], $total, $filtered, $data);
    }
}
