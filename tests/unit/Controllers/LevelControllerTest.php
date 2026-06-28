<?php

namespace Tests\Unit\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class LevelControllerTest extends CIUnitTestCase
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
            'name' => 'Levels',
            'url' => 'levels',
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
        $result = $this->get('/levels');
        $result->assertRedirectTo('/login');
    }

    public function testIndexRendersWhenAuthenticated(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->get('/levels');

        $result->assertOK();
        $result->assertSee('Levels');
    }

    public function testCreateRendersForm(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->get('/levels/create');

        $result->assertOK();
        $result->assertSee('Tambah Level');
    }

    public function testStoreValidationFails(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/levels/store', [
                'name' => 'ab',
            ]);

        $result->assertRedirect();
        $result->assertSessionHas('error');
    }

    public function testStoreSuccess(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/levels/store', [
                'name' => 'Manager',
            ]);

        $result->assertRedirectTo('/levels');
        $result->assertSessionHas('success', 'Level berhasil ditambahkan');
    }

    public function testEditRendersForm(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->get('/levels/edit/1');

        $result->assertOK();
        $result->assertSee('Edit Level');
    }

    public function testEditNotFoundThrowsException(): void
    {
        $this->expectException(\CodeIgniter\Exceptions\PageNotFoundException::class);

        $this->withSession($this->authenticatedSession())
            ->get('/levels/edit/999');
    }

    public function testUpdateValidationFails(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/levels/update/1', [
                'name' => 'ab',
            ]);

        $result->assertRedirect();
        $result->assertSessionHas('error');
    }

    public function testUpdateSuccess(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/levels/update/1', [
                'name' => 'Super Admin',
            ]);

        $result->assertRedirectTo('/levels');
        $result->assertSessionHas('success', 'Level berhasil diupdate');
    }

    public function testDeleteSuccess(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/levels/delete/2');

        $result->assertRedirectTo('/levels');
        $result->assertSessionHas('success', 'Level berhasil dihapus');
    }

    public function testDatatable(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/levels/datatable', [
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
        $this->assertEquals(2, $json['recordsTotal']);
    }

    public function testDatatableWithSearch(): void
    {
        $result = $this->withSession($this->authenticatedSession())
            ->post('/levels/datatable', [
                'draw' => 1,
                'start' => 0,
                'length' => 10,
                'search' => ['value' => 'Admin'],
                'order' => [['column' => 1, 'dir' => 'asc']],
            ]);

        $result->assertOK();
        $json = json_decode($result->getJSON(), true);
        $this->assertEquals(1, $json['recordsFiltered']);
    }
}
