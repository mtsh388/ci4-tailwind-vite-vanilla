<?php

namespace Tests\Unit\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class MenuAccessControllerTest extends CIUnitTestCase
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
            'name' => 'Dashboard',
            'url' => 'dashboard',
            'sort_order' => 1,
            'is_active' => 1,
        ]);
        $db->table('menus')->insert([
            'id' => 2,
            'name' => 'Users',
            'url' => 'users',
            'sort_order' => 2,
            'is_active' => 1,
        ]);

        $db->table('menu_access')->insert([
            'id' => 1,
            'level_id' => 1,
            'menu_id' => 1,
            'can_view' => 1,
            'can_create' => 1,
            'can_update' => 1,
            'can_delete' => 1,
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
        $result = $this->get('/menu-access/1');
        $result->assertRedirectTo('/login');
    }

    public function testIndexRendersForValidLevel(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->get('/menu-access/1');

        $result->assertOK();
        $result->assertSee('Menu Access');
    }

    public function testIndexThrows404ForInvalidLevel(): void
    {
        $this->expectException(\CodeIgniter\Exceptions\PageNotFoundException::class);

        $this->withSession($this->authenticatedSession())
            ->get('/menu-access/999');
    }

    public function testUpdatePermissions(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/menu-access/update/1', [
                'permissions' => [
                    1 => ['view' => 'on', 'create' => 'on'],
                    2 => ['view' => 'on'],
                ],
            ]);

        $result->assertRedirectTo('/levels');
        $result->assertSessionHas('success', 'Permission berhasil diupdate');

        $db = \Config\Database::connect('tests');
        $access = $db->table('menu_access')
            ->where('level_id', 1)
            ->where('menu_id', 1)
            ->get()
            ->getRowArray();

        $this->assertEquals(1, $access['can_view']);
        $this->assertEquals(1, $access['can_create']);
        $this->assertEquals(0, $access['can_update']);
        $this->assertEquals(0, $access['can_delete']);
    }

    public function testUpdatePermissionsEmptyDeletesAll(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/menu-access/update/1', []);

        $result->assertRedirectTo('/levels');
        $result->assertSessionHas('success');

        $db = \Config\Database::connect('tests');
        $count = $db->table('menu_access')
            ->where('level_id', 1)
            ->countAllResults();

        $this->assertEquals(0, $count);
    }

    public function testUpdatePermissionViaJson(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->withBodyFormat('json')
            ->post('/menu-access/update-permission', [
                'level_id' => 1,
                'menu_id' => 2,
                'permission' => 'view',
                'value' => 1,
            ]);

        $result->assertOK();
        $json = json_decode($result->getJSON(), true);
        $this->assertTrue($json['success']);
    }

    public function testUpdatePermissionCreatesNewRecord(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->withBodyFormat('json')
            ->post('/menu-access/update-permission', [
                'level_id' => 2,
                'menu_id' => 1,
                'permission' => 'create',
                'value' => 1,
            ]);

        $result->assertOK();
        $json = json_decode($result->getJSON(), true);
        $this->assertTrue($json['success']);

        $db = \Config\Database::connect('tests');
        $access = $db->table('menu_access')
            ->where('level_id', 2)
            ->where('menu_id', 1)
            ->get()
            ->getRowArray();

        $this->assertNotNull($access);
        $this->assertEquals(1, $access['can_create']);
    }

    public function testDatatable(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/menu-access/datatable/1', [
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
            ->post('/menu-access/datatable/1', [
                'draw' => 1,
                'start' => 0,
                'length' => 10,
                'search' => ['value' => 'Dashboard'],
                'order' => [['column' => 1, 'dir' => 'asc']],
            ]);

        $result->assertOK();
        $json = json_decode($result->getJSON(), true);
        $this->assertGreaterThanOrEqual(1, $json['recordsFiltered']);
    }
}
