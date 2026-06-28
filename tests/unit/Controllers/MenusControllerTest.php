<?php

namespace Tests\Unit\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class MenusControllerTest extends CIUnitTestCase
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
            'name' => 'Menus',
            'url' => 'menus',
            'sort_order' => 1,
            'is_active' => 1,
        ]);
        $db->table('menus')->insert([
            'id' => 2,
            'name' => 'Dashboard',
            'url' => 'dashboard',
            'sort_order' => 2,
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
        $result = $this->get('/menus');
        $result->assertRedirectTo('/login');
    }

    public function testIndexRendersWhenAuthenticated(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->get('/menus');

        $result->assertOK();
        $result->assertSee('Menus');
    }

    public function testCreateRendersForm(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->get('/menus/create');

        $result->assertOK();
        $result->assertSee('Tambah Menu');
    }

    public function testStoreValidationFails(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/menus/store', [
                'name' => '',
                'url' => '',
            ]);

        $result->assertRedirect();
        $result->assertSessionHas('error');
    }

    public function testStoreSuccess(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/menus/store', [
                'name' => 'New Menu',
                'url' => 'new-menu',
                'icon' => 'star',
                'sort_order' => 3,
                'is_active' => 1,
                'parent_id' => '',
            ]);

        $result->assertRedirectTo('/menus');
        $result->assertSessionHas('success', 'Menu berhasil ditambahkan');
    }

    public function testStoreWithParent(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/menus/store', [
                'name' => 'Child Menu',
                'url' => 'child-menu',
                'icon' => 'file',
                'sort_order' => 4,
                'is_active' => 1,
                'parent_id' => 1,
            ]);

        $result->assertRedirectTo('/menus');
        $result->assertSessionHas('success', 'Menu berhasil ditambahkan');
    }

    public function testStoreCreatesMenuAccessForAllLevels(): void
    {
        $this->withSession($this->authenticatedSession())
            ->post('/menus/store', [
                'name' => 'Access Test',
                'url' => 'access-test',
                'icon' => 'key',
                'sort_order' => 5,
                'is_active' => 1,
                'parent_id' => '',
            ]);

        $db = \Config\Database::connect('tests');
        $accessRecords = $db->table('menu_access')
            ->where('menu_id >', 2)
            ->get()
            ->getResultArray();

        $this->assertCount(2, $accessRecords);
    }

    public function testEditRendersForm(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->get('/menus/edit/1');

        $result->assertOK();
        $result->assertSee('Edit Menu');
    }

    public function testEditNotFoundThrowsException(): void
    {
        $this->expectException(\CodeIgniter\Exceptions\PageNotFoundException::class);

        $this->withSession($this->authenticatedSession())
            ->get('/menus/edit/999');
    }

    public function testUpdateValidationFails(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/menus/update/1', [
                'name' => '',
                'url' => '',
            ]);

        $result->assertRedirect();
        $result->assertSessionHas('error');
    }

    public function testUpdateSuccess(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/menus/update/1', [
                'name' => 'Updated Menu',
                'url' => 'updated-menu',
                'icon' => 'edit',
                'sort_order' => 1,
                'is_active' => 1,
                'parent_id' => '',
            ]);

        $result->assertRedirectTo('/menus');
        $result->assertSessionHas('success', 'Menu berhasil diupdate');
    }

    public function testDeleteSuccess(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/menus/delete/2');

        $result->assertRedirectTo('/menus');
        $result->assertSessionHas('success', 'Menu berhasil dihapus');
    }

    public function testDeleteAlsoRemovesMenuAccess(): void
    {
        $db = \Config\Database::connect('tests');
        $db->table('menu_access')->insert([
            'level_id' => 1,
            'menu_id' => 2,
            'can_view' => 1,
            'can_create' => 0,
            'can_update' => 0,
            'can_delete' => 0,
        ]);

        $this->withSession($this->authenticatedSession())
            ->post('/menus/delete/2');

        $count = $db->table('menu_access')
            ->where('menu_id', 2)
            ->countAllResults();

        $this->assertEquals(0, $count);
    }

    public function testToggleStatusSuccess(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/menus/toggle-status/1');

        $result->assertOK();
        $json = json_decode($result->getJSON(), true);
        $this->assertTrue($json['status']);
        $this->assertEquals(0, $json['is_active']);
    }

    public function testToggleStatusNotFound(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/menus/toggle-status/999');

        $result->assertOK();
        $json = json_decode($result->getJSON(), true);
        $this->assertFalse($json['status']);
    }

}
