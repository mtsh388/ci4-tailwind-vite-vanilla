<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\LevelModel;
use App\Traits\DatatableTrait;
use App\Traits\ToggleStatusTrait;

class Users extends BaseController
{
    use DatatableTrait;
    use ToggleStatusTrait;

    protected $userModel;
    protected $levelModel;

    public function __construct()
    {
        $this->userModel  = new UsersModel();
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
        return $this->render('users/index', [
            'title' => 'Users',
        ]);
    }

    /*
        |--------------------------------------------------------------------------
        | DATATABLE
        |--------------------------------------------------------------------------
    */
    public function datatable()
    {
        $dt = $this->getDatatableRequest();

        /*
        |--------------------------------------------------------------------------
        | QUERY
        |--------------------------------------------------------------------------
        */
        $builder = $this->userModel
            ->select('users.*, levels.name as level_name')
            ->join('levels', 'levels.id = users.level_id', 'left');

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */
        if (!empty($dt['search'])) {

            $builder->groupStart()
                ->like('users.nama', $dt['search'])
                ->orLike('users.username', $dt['search'])
                ->orLike('levels.name', $dt['search'])
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
        $columns = [
            0 => 'users.id',
            1 => 'users.nama',
            2 => 'users.username',
            3 => 'levels.name',
        ];

        $orderColumn = $this->getDatatableOrderColumn(
            $columns,
            $dt['orderColumnIndex'],
            'users.nama'
        );

        $builder->orderBy($orderColumn, $dt['orderDir']);

        /*
        |--------------------------------------------------------------------------
        | GET DATA
        |--------------------------------------------------------------------------
        */
        $users = $builder->findAll($dt['length'], $dt['start']);

        $data = [];

        foreach ($users as $key => $user) {
            $status = renderToggleSwitch(
                $user['id'],
                'users/toggle-status',
                (bool) $user['is_active']
            );

            $actionButtons = [];

            if (hasPermission('users', 'update')) {
                $actionButtons[] = renderEditButton('users/edit/' . $user['id']);
            }

            if (hasPermission('users', 'delete')) {
                $actionButtons[] = renderDeleteButton('users/delete/' . $user['id']);
            }

            $data[] = [
                'no'       => $dt['start'] + $key + 1,
                'nama'     => esc($user['nama']),
                'username' => esc($user['username']),
                'email'    => esc($user['email']),
                'level'    => esc($user['level_name'] ?? '-'),
                'status'   => $status,
                'action'   => renderActionButtons($actionButtons),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */
        $total = $this->userModel->countAll();

        return $this->formatDatatableResponse($dt['draw'], $total, $filtered, $data);
    }

    /*
        |--------------------------------------------------------------------------
        | CREATE
        |--------------------------------------------------------------------------
    */
    public function create()
    {
        return $this->render('users/create', [
            'title'  => 'Tambah User',
            'levels' => $this->levelModel->findAll(),
        ]);
    }

    /*
        |--------------------------------------------------------------------------
        | STORE
        |--------------------------------------------------------------------------
    */
    public function store()
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */
        $rules = [
            'nama' => [
                'label' => 'Nama',
                'rules' => 'required|min_length[3]',
            ],

            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email|is_unique[users.email]',
            ],

            'username' => [
                'label' => 'Username',
                'rules' => 'required|min_length[3]|is_unique[users.username]',
            ],

            'password' => [
                'label' => 'Password',
                'rules' => 'required|min_length[6]',
            ],

            'level_id' => [
                'label' => 'Level',
                'rules' => 'required|numeric',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | VALIDATE
        |--------------------------------------------------------------------------
        */
        if (!$this->validateOrRedirect($rules)) {

            return redirect()
                ->back()
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | INSERT
        |--------------------------------------------------------------------------
        */
        $this->userModel->insert([
            'nama'      => $this->request->getPost('nama'),
            'email'     => strtolower($this->request->getPost('email')),
            'username'  => trim($this->request->getPost('username')),
            'password'  => password_hash(
                $this->request->getPost('password'),
                PASSWORD_DEFAULT
            ),
            'level_id'  => $this->request->getPost('level_id'),
            'is_active' => 1,
        ]);

        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */
        return redirect()
            ->to(site_url('users'))
            ->with('success', 'User berhasil ditambahkan');
    }

    /*
        |--------------------------------------------------------------------------
        | EDIT
        |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        return $this->render('users/edit', [
            'title'  => 'Edit User',
            'user'   => $this->userModel->find($id),
            'levels' => $this->levelModel->findAll(),
        ]);
    }

    /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
    */
    public function update($id)
    {
        $data = [
            'name'     => $this->request->getPost('name'),
            'username' => $this->request->getPost('username'),
            'level_id' => $this->request->getPost('level_id'),
        ];

        if ($this->request->getPost('password')) {

            $data['password'] = password_hash(
                $this->request->getPost('password'),
                PASSWORD_DEFAULT
            );
        }

        $this->userModel->update($id, $data);

        return redirect()
            ->to('/users')
            ->with('success', 'User berhasil diupdate');
    }

    public function toggleStatus($id)
    {
        return $this->handleToggleStatus($this->userModel, $id, 'User');
    }

    /*
        |--------------------------------------------------------------------------
        | DELETE
        |--------------------------------------------------------------------------
    */
    public function delete($id)
    {
        $this->userModel->delete($id);

        return redirect()
            ->to('/users')
            ->with('success', 'User berhasil dihapus');
    }
}
