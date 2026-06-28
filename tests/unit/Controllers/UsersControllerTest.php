<?php

namespace Tests\Unit\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class UsersControllerTest extends CIUnitTestCase
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
        $db->table('levels')->insert(['id' => 2, 'name' => 'User']);

        $db->table('menus')->insert([
            'id' => 1,
            'name' => 'Users',
            'url' => 'users',
            'sort_order' => 1,
            'is_active' => 1,
        ]);

        $db->table('menu_access')->insert([
            'level_id' => 1,
            'menu_id' => 1,
            'can_view' => 1,
            'can_create' => 1,
            'can_update' => 1,
            'can_delete' => 1,
        ]);

        $db->table('users')->insert([
            'id' => 1,
            'level_id' => 1,
            'nama' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'is_active' => 1,
        ]);
    }

    private function authenticatedSession(): array
    {
        return [
            'logged_in' => true,
            'user_id' => 1,
            'level_id' => 1,
            'username' => 'admin',
            'nama' => 'Admin',
        ];
    }

    public function testIndexRequiresAuth(): void
    {
        $result = $this->get('/users');
        $result->assertRedirectTo('/login');
    }

    public function testIndexRendersWhenAuthenticated(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->get('/users');

        $result->assertOK();
        $result->assertSee('Users');
    }

    public function testCreateRendersForm(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->get('/users/create');

        $result->assertOK();
        $result->assertSee('Tambah User');
    }

    public function testStoreValidationFails(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/users/store', [
                'nama' => 'ab',
                'username' => 'ab',
                'email' => 'invalid-email',
                'password' => '12345',
                'level_id' => '',
            ]);

        $result->assertRedirect();
        $result->assertSessionHas('error');
    }

    public function testStoreSuccess(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/users/store', [
                'nama' => 'New User',
                'username' => 'newuser',
                'email' => 'newuser@example.com',
                'password' => 'password123',
                'level_id' => 1,
            ]);

        $result->assertRedirect();
        $result->assertSessionHas('success', 'User berhasil ditambahkan');
    }

    public function testEditRendersForm(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->get('/users/edit/1');

        $result->assertOK();
        $result->assertSee('Edit User');
    }

    public function testUpdateSuccess(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/users/update/1', [
                'name' => 'Updated Admin',
                'username' => 'admin',
                'level_id' => 1,
            ]);

        $result->assertRedirectTo('/users');
        $result->assertSessionHas('success', 'User berhasil diupdate');
    }

    public function testUpdateWithPassword(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/users/update/1', [
                'name' => 'Admin',
                'username' => 'admin',
                'level_id' => 1,
                'password' => 'newpassword123',
            ]);

        $result->assertRedirectTo('/users');
        $result->assertSessionHas('success', 'User berhasil diupdate');
    }

    public function testToggleStatusSuccess(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/users/toggle-status/1');

        $result->assertOK();
        $json = json_decode($result->getJSON(), true);
        $this->assertTrue($json['status']);
        $this->assertEquals(0, $json['is_active']);
    }

    public function testToggleStatusNotFound(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/users/toggle-status/999');

        $result->assertOK();
        $json = json_decode($result->getJSON(), true);
        $this->assertFalse($json['status']);
    }

    public function testDeleteSuccess(): void
    {
        $db = \Config\Database::connect('tests');
        $db->table('users')->insert([
            'id' => 5,
            'level_id' => 2,
            'nama' => 'ToDelete',
            'username' => 'todelete',
            'email' => 'del@example.com',
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'is_active' => 1,
        ]);

        $result = $this->withSession($this->authenticatedSession())
            ->get('/users/delete/5');

        $result->assertRedirectTo('/users');
        $result->assertSessionHas('success', 'User berhasil dihapus');
    }

    public function testDatatable(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/users/datatable', [
                'draw' => 1,
                'start' => 0,
                'length' => 10,
                'search' => ['value' => ''],
                'order' => [['column' => 1, 'dir' => 'asc']],
            ]);

        $result->assertOK();
        $json = json_decode($result->getJSON(), true);
        $this->assertArrayHasKey('draw', $json);
        $this->assertArrayHasKey('recordsTotal', $json);
        $this->assertArrayHasKey('recordsFiltered', $json);
        $this->assertArrayHasKey('data', $json);
    }

    public function testDatatableWithSearch(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/users/datatable', [
                'draw' => 1,
                'start' => 0,
                'length' => 10,
                'search' => ['value' => 'admin'],
                'order' => [['column' => 1, 'dir' => 'asc']],
            ]);

        $result->assertOK();
        $json = json_decode($result->getJSON(), true);
        $this->assertGreaterThanOrEqual(1, $json['recordsFiltered']);
    }
}
