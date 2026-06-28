<?php

namespace Tests\Unit\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class DashboardControllerTest extends CIUnitTestCase
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
    }

    public function testDashboardRequiresAuth(): void
    {
        $result = $this->get('/dashboard');
        $result->assertRedirectTo('/login');
    }

    public function testDashboardRendersWhenAuthenticated(): void
    {
        $result = $this->withSession([
            'logged_in' => true,
            'user_id' => 1,
            'level_id' => 1,
            'username' => 'admin',
            'nama' => 'Admin',
        ])->get('/dashboard');

        $result->assertOK();
        $result->assertSee('Dashboard');
    }
}
