<?php

namespace Tests\Unit\Models;

use App\Models\MenuModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * @internal
 */
final class MenuModelTest extends CIUnitTestCase
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
            'id' => [
                'type' => 'INTEGER',
                'auto_increment' => true,
            ],
            'parent_id' => [
                'type' => 'INTEGER',
                'null' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'icon' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'sort_order' => [
                'type' => 'INTEGER',
                'default' => 0,
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'default' => 1,
            ],
        ]);
        $forge->addKey('id', true);
        $forge->createTable('menus');

        $forge->addField([
            'id' => [
                'type' => 'INTEGER',
                'auto_increment' => true,
            ],
            'level_id' => [
                'type' => 'INTEGER',
            ],
            'menu_id' => [
                'type' => 'INTEGER',
            ],
            'can_view' => [
                'type' => 'TINYINT',
                'default' => 0,
            ],
            'can_create' => [
                'type' => 'TINYINT',
                'default' => 0,
            ],
            'can_update' => [
                'type' => 'TINYINT',
                'default' => 0,
            ],
            'can_delete' => [
                'type' => 'TINYINT',
                'default' => 0,
            ],
        ]);
        $forge->addKey('id', true);
        $forge->createTable('menu_access');
    }

    public function testTableName(): void
    {
        $model = new MenuModel();
        $this->assertSame('menus', $model->getTable());
    }

    public function testAllowedFields(): void
    {
        $model = new MenuModel();
        $allowedFields = $this->getPrivateProperty($model, 'allowedFields');

        $this->assertContains('parent_id', $allowedFields);
        $this->assertContains('name', $allowedFields);
        $this->assertContains('icon', $allowedFields);
        $this->assertContains('url', $allowedFields);
        $this->assertContains('sort_order', $allowedFields);
        $this->assertContains('is_active', $allowedFields);
    }

    public function testReturnType(): void
    {
        $model = new MenuModel();
        $returnType = $this->getPrivateProperty($model, 'returnType');
        $this->assertSame('array', $returnType);
    }

    public function testInsertAndFind(): void
    {
        $model = new MenuModel();
        $model->insert([
            'name' => 'Dashboard',
            'icon' => 'home',
            'url' => 'dashboard',
            'sort_order' => 1,
            'is_active' => 1,
        ]);

        $menu = $model->where('name', 'Dashboard')->first();
        $this->assertNotNull($menu);
        $this->assertSame('Dashboard', $menu['name']);
        $this->assertSame('home', $menu['icon']);
        $this->assertSame('dashboard', $menu['url']);
    }

    public function testGetSidebarMenuReturnsAccessibleMenus(): void
    {
        $model = new MenuModel();

        $model->insert([
            'name' => 'Dashboard',
            'icon' => 'home',
            'url' => 'dashboard',
            'sort_order' => 1,
            'is_active' => 1,
        ]);
        $menuId = $model->getInsertID();

        $db = \Config\Database::connect('tests');
        $db->table('menu_access')->insert([
            'level_id' => 1,
            'menu_id' => $menuId,
            'can_view' => 1,
            'can_create' => 1,
            'can_update' => 1,
            'can_delete' => 1,
        ]);

        $sidebarMenu = $model->getSidebarMenu(1);
        $this->assertNotEmpty($sidebarMenu);
        $this->assertSame('Dashboard', $sidebarMenu[0]['name']);
    }

    public function testGetSidebarMenuExcludesInactiveMenus(): void
    {
        $model = new MenuModel();

        $model->insert([
            'name' => 'Inactive Menu',
            'icon' => 'x',
            'url' => 'inactive',
            'sort_order' => 1,
            'is_active' => 0,
        ]);
        $menuId = $model->getInsertID();

        $db = \Config\Database::connect('tests');
        $db->table('menu_access')->insert([
            'level_id' => 1,
            'menu_id' => $menuId,
            'can_view' => 1,
            'can_create' => 0,
            'can_update' => 0,
            'can_delete' => 0,
        ]);

        $sidebarMenu = $model->getSidebarMenu(1);
        $this->assertEmpty($sidebarMenu);
    }

    public function testGetSidebarMenuExcludesNoViewPermission(): void
    {
        $model = new MenuModel();

        $model->insert([
            'name' => 'No View',
            'icon' => 'x',
            'url' => 'noview',
            'sort_order' => 1,
            'is_active' => 1,
        ]);
        $menuId = $model->getInsertID();

        $db = \Config\Database::connect('tests');
        $db->table('menu_access')->insert([
            'level_id' => 2,
            'menu_id' => $menuId,
            'can_view' => 0,
            'can_create' => 0,
            'can_update' => 0,
            'can_delete' => 0,
        ]);

        $sidebarMenu = $model->getSidebarMenu(2);
        $this->assertEmpty($sidebarMenu);
    }

    public function testGetSidebarMenuIncludesParentMenus(): void
    {
        $model = new MenuModel();

        $model->insert([
            'name' => 'Parent Menu',
            'icon' => 'folder',
            'url' => '',
            'sort_order' => 1,
            'is_active' => 1,
        ]);
        $parentId = $model->getInsertID();

        $model->insert([
            'parent_id' => $parentId,
            'name' => 'Child Menu',
            'icon' => 'file',
            'url' => 'child',
            'sort_order' => 2,
            'is_active' => 1,
        ]);
        $childId = $model->getInsertID();

        $db = \Config\Database::connect('tests');
        $db->table('menu_access')->insert([
            'level_id' => 1,
            'menu_id' => $childId,
            'can_view' => 1,
            'can_create' => 0,
            'can_update' => 0,
            'can_delete' => 0,
        ]);

        $sidebarMenu = $model->getSidebarMenu(1);
        $this->assertCount(2, $sidebarMenu);

        $names = array_column($sidebarMenu, 'name');
        $this->assertContains('Parent Menu', $names);
        $this->assertContains('Child Menu', $names);
    }

    public function testGetSidebarMenuSortsByOrder(): void
    {
        $model = new MenuModel();

        $model->insert([
            'name' => 'Second',
            'icon' => 'b',
            'url' => 'second',
            'sort_order' => 2,
            'is_active' => 1,
        ]);
        $secondId = $model->getInsertID();

        $model->insert([
            'name' => 'First',
            'icon' => 'a',
            'url' => 'first',
            'sort_order' => 1,
            'is_active' => 1,
        ]);
        $firstId = $model->getInsertID();

        $db = \Config\Database::connect('tests');
        $db->table('menu_access')->insert([
            'level_id' => 1,
            'menu_id' => $secondId,
            'can_view' => 1,
            'can_create' => 0,
            'can_update' => 0,
            'can_delete' => 0,
        ]);
        $db->table('menu_access')->insert([
            'level_id' => 1,
            'menu_id' => $firstId,
            'can_view' => 1,
            'can_create' => 0,
            'can_update' => 0,
            'can_delete' => 0,
        ]);

        $sidebarMenu = $model->getSidebarMenu(1);
        $this->assertSame('First', $sidebarMenu[0]['name']);
        $this->assertSame('Second', $sidebarMenu[1]['name']);
    }
}
