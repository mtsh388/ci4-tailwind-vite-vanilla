<?php

namespace Tests\Unit\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class AuthControllerTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate = false;

    protected function setUp(): void
    {
        parent::setUp();

        $forge = \Config\Database::forge('tests');

        $forge->dropTable('menu_access', true);
        $forge->dropTable('menus', true);
        $forge->dropTable('users', true);
        $forge->dropTable('levels', true);

        $forge->addField([
            'id' => ['type' => 'INTEGER', 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $forge->addKey('id', true);
        $forge->createTable('levels');

        $forge->addField([
            'id' => ['type' => 'INTEGER', 'auto_increment' => true],
            'level_id' => ['type' => 'INTEGER', 'null' => true],
            'nama' => ['type' => 'VARCHAR', 'constraint' => 255],
            'username' => ['type' => 'VARCHAR', 'constraint' => 255],
            'email' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'password' => ['type' => 'VARCHAR', 'constraint' => 255],
            'is_active' => ['type' => 'TINYINT', 'default' => 1],
            'change_password' => ['type' => 'TINYINT', 'default' => 0, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $forge->addKey('id', true);
        $forge->createTable('users');

        $forge->addField([
            'id' => ['type' => 'INTEGER', 'auto_increment' => true],
            'parent_id' => ['type' => 'INTEGER', 'null' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'icon' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'url' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'sort_order' => ['type' => 'INTEGER', 'default' => 0],
            'is_active' => ['type' => 'TINYINT', 'default' => 1],
        ]);
        $forge->addKey('id', true);
        $forge->createTable('menus');

        $forge->addField([
            'id' => ['type' => 'INTEGER', 'auto_increment' => true],
            'level_id' => ['type' => 'INTEGER'],
            'menu_id' => ['type' => 'INTEGER'],
            'can_view' => ['type' => 'TINYINT', 'default' => 0],
            'can_create' => ['type' => 'TINYINT', 'default' => 0],
            'can_update' => ['type' => 'TINYINT', 'default' => 0],
            'can_delete' => ['type' => 'TINYINT', 'default' => 0],
        ]);
        $forge->addKey('id', true);
        $forge->createTable('menu_access');

        $db = \Config\Database::connect('tests');
        $db->table('levels')->insert(['id' => 1, 'name' => 'Admin']);

        $db->table('users')->insert([
            'id' => 1,
            'level_id' => 1,
            'nama' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'is_active' => 1,
        ]);

        $db->table('users')->insert([
            'id' => 2,
            'level_id' => 1,
            'nama' => 'Inactive User',
            'username' => 'inactive',
            'email' => 'inactive@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'is_active' => 0,
        ]);
    }

    public function testLoginPageRendersSuccessfully(): void
    {
        $result = $this->get('/login');

        $result->assertOK();
        $result->assertSee('Login');
    }

    public function testLoginRedirectsTodashboardWhenAlreadyLoggedIn(): void
    {
        $session = session();
        $session->set('logged_in', true);

        $result = $this->withSession([
            'logged_in' => true,
        ])->get('/login');

        $result->assertRedirectTo('/dashboard');
    }

    public function testProcessLoginValidationFails(): void
    {
        $result = $this->post('/login/process', [
            'username' => '',
            'password' => '',
        ]);

        $result->assertRedirect();
        $result->assertSessionHas('error');
    }

    public function testProcessLoginUsernameNotFound(): void
    {
        $result = $this->post('/login/process', [
            'username' => 'nonexistent',
            'password' => 'password123',
        ]);

        $result->assertRedirect();
        $result->assertSessionHas('error', 'Username tidak ditemukan');
    }

    public function testProcessLoginInactiveUser(): void
    {
        $result = $this->post('/login/process', [
            'username' => 'inactive',
            'password' => 'password123',
        ]);

        $result->assertRedirect();
        $result->assertSessionHas('error', 'User tidak aktif');
    }

    public function testProcessLoginWrongPassword(): void
    {
        $result = $this->post('/login/process', [
            'username' => 'admin',
            'password' => 'wrongpassword',
        ]);

        $result->assertRedirect();
        $result->assertSessionHas('error', 'Password salah');
    }

    public function testProcessLoginSuccess(): void
    {
        $result = $this->post('/login/process', [
            'username' => 'admin',
            'password' => 'password123',
        ]);

        $result->assertRedirectTo('/dashboard');
        $result->assertSessionHas('logged_in', true);
        $result->assertSessionHas('username', 'admin');
    }

    public function testLogout(): void
    {
        $result = $this->withSession([
            'logged_in' => true,
            'user_id' => 1,
        ])->get('/logout');

        $result->assertRedirectTo('/login');
    }

    public function testChangePasswordPageRequiresAuth(): void
    {
        $result = $this->get('/change-password');

        $result->assertRedirectTo('/login');
    }

    public function testChangePasswordPageRendersWhenAuthenticated(): void
    {
        $result = $this->withSession([
            'logged_in' => true,
            'user_id' => 1,
            'level_id' => 1,
            'username' => 'admin',
            'nama' => 'Admin',
        ])->get('/change-password');

        $result->assertOK();
        $result->assertSee('Change Password');
    }

    public function testUpdatePasswordValidationFails(): void
    {
        $result = $this->withSession([
            'logged_in' => true,
            'user_id' => 1,
            'level_id' => 1,
            'username' => 'admin',
            'nama' => 'Admin',
        ])->post('/change-password', [
            'current_password' => '',
            'new_password' => '',
            'confirm_password' => '',
        ]);

        $result->assertRedirect();
        $result->assertSessionHas('error');
    }

    public function testUpdatePasswordWrongCurrentPassword(): void
    {
        $result = $this->withSession([
            'logged_in' => true,
            'user_id' => 1,
            'level_id' => 1,
            'username' => 'admin',
            'nama' => 'Admin',
        ])->post('/change-password', [
            'current_password' => 'wrongpassword',
            'new_password' => 'newpass123',
            'confirm_password' => 'newpass123',
        ]);

        $result->assertRedirect();
        $result->assertSessionHas('error', 'Password lama salah');
    }

    public function testUpdatePasswordSuccess(): void
    {
        $result = $this->withSession([
            'logged_in' => true,
            'user_id' => 1,
            'level_id' => 1,
            'username' => 'admin',
            'nama' => 'Admin',
        ])->post('/change-password', [
            'current_password' => 'password123',
            'new_password' => 'newpass123',
            'confirm_password' => 'newpass123',
        ]);

        $result->assertRedirect();
        $result->assertSessionHas('success', 'Password berhasil diubah');
    }
}
