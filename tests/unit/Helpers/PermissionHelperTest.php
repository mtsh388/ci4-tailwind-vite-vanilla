<?php

namespace Tests\Unit\Helpers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * @internal
 */
final class PermissionHelperTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = false;

    protected function setUp(): void
    {
        parent::setUp();

        $forge = \Config\Database::forge('tests');

        $forge->dropTable('menu_access', true);
        $forge->dropTable('menus', true);

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
            'can_delete' => 0,
        ]);

        helper('permission');

        $session = session();
        $session->set('level_id', 1);
    }

    protected function tearDown(): void
    {
        session()->remove('level_id');
        parent::tearDown();
    }

    public function testHasPermissionReturnsTrueForViewAccess(): void
    {
        $this->assertTrue(hasPermission('users', 'view'));
    }

    public function testHasPermissionReturnsTrueForCreateAccess(): void
    {
        $this->assertTrue(hasPermission('users', 'create'));
    }

    public function testHasPermissionReturnsTrueForUpdateAccess(): void
    {
        $this->assertTrue(hasPermission('users', 'update'));
    }

    public function testHasPermissionReturnsFalseForDeleteAccess(): void
    {
        $this->assertFalse(hasPermission('users', 'delete'));
    }

    public function testHasPermissionReturnsFalseForNonexistentMenu(): void
    {
        $this->assertFalse(hasPermission('nonexistent', 'view'));
    }

    public function testHasPermissionReturnsFalseForNonexistentLevel(): void
    {
        session()->set('level_id', 999);
        $this->assertFalse(hasPermission('users', 'view'));
    }

    public function testHasPermissionDefaultsToViewForUnknownAction(): void
    {
        $this->assertTrue(hasPermission('users', 'unknown_action'));
    }
}
