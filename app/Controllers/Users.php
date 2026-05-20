<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\LevelModel;

class Users extends BaseController
{
    protected $userModel;
    protected $levelModel;

    public function __construct()
    {
        $this->userModel  = new UsersModel();
        $this->levelModel = new LevelModel();
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
        $builder = $this->userModel
            ->select('users.*, levels.name as level_name')
            ->join('levels', 'levels.id = users.level_id', 'left');

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */
        if (!empty($search)) {

            $builder->groupStart()
                ->like('users.nama', $search)
                ->orLike('users.username', $search)
                ->orLike('levels.name', $search)
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

        $orderColumnIndex = $request->getPost('order')[0]['column'] ?? 1;
        $orderDir         = $request->getPost('order')[0]['dir'] ?? 'asc';

        $orderColumn = $columns[$orderColumnIndex] ?? 'users.nama';

        $builder->orderBy($orderColumn, $orderDir);

        /*
        |--------------------------------------------------------------------------
        | GET DATA
        |--------------------------------------------------------------------------
        */
        $users = $builder->findAll($length, $start);

        $data = [];

        foreach ($users as $key => $user) {
            $status = '
                <label class="relative inline-flex cursor-pointer items-center">

                    <input
                        type="checkbox"
                        class="toggle-status peer sr-only"
                        value="' . $user['id'] . '"
                        ' . ($user['is_active'] ? 'checked' : '') . '
                        data-url="' . site_url('users/toggle-status') . '"
                        >

                    <div class="h-6 w-11 rounded-full bg-slate-300
                        transition
                        peer-checked:bg-green-500

                        after:absolute
                        after:left-[2px]
                        after:top-[2px]
                        after:h-5
                        after:w-5
                        after:rounded-full
                        after:bg-white
                        after:transition-all
                        peer-checked:after:translate-x-full">
                    </div>

                </label>';

            $actionButtons = [];

            /*
            |--------------------------------------------------------------------------
            | EDIT
            |--------------------------------------------------------------------------
            */
            if (hasPermission('users', 'update')) {

                $actionButtons[] = '
                    <a
                        href="' . site_url('users/edit/' . $user['id']) . '"
                        class="rounded-lg bg-yellow-500 px-3 py-2 text-xs font-medium text-white hover:bg-yellow-600">

                        Edit
                    </a>
                ';
            }

            /*
            |--------------------------------------------------------------------------
            | DELETE
            |--------------------------------------------------------------------------
            */
            if (hasPermission('users', 'delete')) {

                $actionButtons[] = '
                    <a
                        href="' . site_url('users/delete/' . $user['id']) . '"
                        class="btn-delete rounded-lg bg-red-600 px-3 py-2 text-xs font-medium text-white hover:bg-red-700">

                        Delete
                    </a>
                ';
            }

            $data[] = [
                'no'       => $start + $key + 1,
                'nama'     => esc($user['nama']),
                'username' => esc($user['username']),
                'email' => esc($user['email']),
                'level'    => esc($user['level_name'] ?? '-'),
                'status'    => $status,
                'action'   => '
                    <div class="flex items-center gap-2">
                        ' . implode('', $actionButtons) . '
                    </div>
                ',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */
        $total = $this->userModel->countAll();

        return $this->response->setJSON([
            'draw'            => intval($draw),
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $data,
        ]);
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
        if (!$this->validate($rules)) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    implode(
                        '<br>',
                        $this->validator->getErrors()
                    )
                );
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
        /*
            |--------------------------------------------------------------------------
            | GET User
            |--------------------------------------------------------------------------
        */
        $user = $this->userModel->find($id);

        if (!$user) {

            return $this->response->setJSON([
                'status'  => false,
                'message' => 'User tidak ditemukan',
            ]);
        }

        /*
            |--------------------------------------------------------------------------
            | TOGGLE STATUS
            |--------------------------------------------------------------------------
        */
        $newStatus = $user['is_active'] ? 0 : 1;

        $this->userModel->update($id, [
            'is_active' => $newStatus,
        ]);

        return $this->response->setJSON([
            'status'    => true,
            'message'   => 'Status berhasil diupdate',
            'is_active' => $newStatus,
        ]);
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
