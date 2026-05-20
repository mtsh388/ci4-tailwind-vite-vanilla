<?php

namespace App\Controllers;

use App\Models\UsersModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UsersModel();
        helper(['form']);
    }

    /*
    |--------------------------------------------------------------------------
    | Login Page
    |--------------------------------------------------------------------------
    */
    public function login()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login', [
            'title' => 'Login'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Process Login
    |--------------------------------------------------------------------------
    */
    public function process()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with(
                'error',
                'Username dan password wajib diisi'
            );
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->userModel
            ->where('username', $username)
            ->first();

        if (!$user) {
            return redirect()->back()->withInput()->with(
                'error',
                'Username tidak ditemukan'
            );
        }

        if (!$user['is_active']) {
            return redirect()->back()->withInput()->with(
                'error',
                'User tidak aktif'
            );
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with(
                'error',
                'Password salah'
            );
        }

        session()->regenerate();

        session()->set([
            'user_id'   => $user['id'],
            'level_id'  => $user['level_id'],
            'nama'      => $user['nama'],
            'username'  => $user['username'],
            'logged_in' => true,
        ]);

        return redirect()->to('/dashboard')
            ->with('success', 'Login berhasil');
    }

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */
    public function logout()
    {
        session()->destroy();

        return redirect()->to('/login')
            ->with('success', 'Logout berhasil');
    }

    /*
    |--------------------------------------------------------------------------
    | Change Password Page
    |--------------------------------------------------------------------------
    */
    public function changePassword()
    {
        return $this->render('auth/change_password', [
            'title' => 'Change Password'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Update Password
    |--------------------------------------------------------------------------
    */
    public function updatePassword()
    {
        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with(
                'error',
                implode(
                    '<br>',
                    $this->validator->getErrors()
                )
            );
        }

        $user = $this->userModel->find(session('user_id'));

        if (!password_verify(
            $this->request->getPost('current_password'),
            $user['password']
        )) {
            return redirect()->back()->with(
                'error',
                'Password lama salah'
            );
        }

        $this->userModel->update($user['id'], [
            'password' => password_hash(
                $this->request->getPost('new_password'),
                PASSWORD_DEFAULT
            )
        ]);

        return redirect()->back()->with(
            'success',
            'Password berhasil diubah'
        );
    }

    public function forgotPassword()
    {
        helper('text');

        $session = session();

        $iduser = $session->get('user_id');

        /*
    |--------------------------------------------------------------------------
    | GET USER
    |--------------------------------------------------------------------------
    */
        $user = $this->userModel
            ->where('id', $iduser)
            ->first();

        /*
    |--------------------------------------------------------------------------
    | USER NOT FOUND
    |--------------------------------------------------------------------------
    */
        if (!$user) {

            return redirect()
                ->back()
                ->with('error', 'User tidak ditemukan');
        }

        /*
    |--------------------------------------------------------------------------
    | GENERATE RANDOM PASSWORD
    |--------------------------------------------------------------------------
    */
        $randomPassword = random_string('alnum', 10);

        /*
    |--------------------------------------------------------------------------
    | UPDATE PASSWORD
    |--------------------------------------------------------------------------
    */
        $this->userModel->update($user['id'], [
            'password' => password_hash(
                $randomPassword,
                PASSWORD_DEFAULT
            ),
        ]);

        /*
    |--------------------------------------------------------------------------
    | SEND EMAIL
    |--------------------------------------------------------------------------
    */
        $emailService = \Config\Services::email();

        $emailService->setTo($user['email']);

        $emailService->setSubject('Reset Password');

        $emailService->setMessage("
        <h3>Password Baru Anda</h3>

        <p>Password: <b>{$randomPassword}</b></p>

        <p>Silakan login dan segera ubah password Anda.</p>
    ");

        /*
    |--------------------------------------------------------------------------
    | SEND EMAIL
    |--------------------------------------------------------------------------
    */
        if (!$emailService->send()) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    $emailService->printDebugger(['headers'])
                );
        }

        /*
    |--------------------------------------------------------------------------
    | SUCCESS
    |--------------------------------------------------------------------------
    */
        return redirect()
            ->back()
            ->with(
                'success',
                'Password baru berhasil dikirim ke email'
            );
    }
}
