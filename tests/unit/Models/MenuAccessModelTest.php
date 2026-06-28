<?php

namespace Tests\Unit\Models;

use App\Models\MenuAccessModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * @internal
 */
final class MenuAccessModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = false;

    protected function setUp(): void
    {
        parent::setUp();

        $forge = \Config\Database::forge('tests');
        $forge->dropTable('menu_access', true);
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
        $model = new MenuAccessModel();
        $this->assertSame('menu_access', $model->getTable());
    }

    public function testAllowedFields(): void
    {
        $model = new MenuAccessModel();
        $allowedFields = $this->getPrivateProperty($model, 'allowedFields');

        $this->assertContains('level_id', $allowedFields);
        $this->assertContains('menu_id', $allowedFields);
        $this->assertContains('can_view', $allowedFields);
        $this->assertContains('can_create', $allowedFields);
        $this->assertContains('can_update', $allowedFields);
        $this->assertContains('can_delete', $allowedFields);
    }

    public function testReturnType(): void
    {
        $model = new MenuAccessModel();
        $returnType = $this->getPrivateProperty($model, 'returnType');
        $this->assertSame('array', $returnType);
    }

    public function testInsertPermission(): void
    {
        $model = new MenuAccessModel();
        $model->insert([
            'level_id' => 1,
            'menu_id' => 1,
            'can_view' => 1,
            'can_create' => 1,
            'can_update' => 0,
            'can_delete' => 0,
        ]);

        $access = $model->where('level_id', 1)->where('menu_id', 1)->first();
        $this->assertNotNull($access);
        $this->assertEquals(1, $access['can_view']);
        $this->assertEquals(1, $access['can_create']);
        $this->assertEquals(0, $access['can_update']);
        $this->assertEquals(0, $access['can_delete']);
    }

    public function testUpdatePermission(): void
    {
        $model = new MenuAccessModel();
        $model->insert([
            'level_id' => 1,
            'menu_id' => 2,
            'can_view' => 0,
            'can_create' => 0,
            'can_update' => 0,
            'can_delete' => 0,
        ]);

        $access = $model->where('level_id', 1)->where('menu_id', 2)->first();
        $model->update($access['id'], ['can_view' => 1, 'can_update' => 1]);

        $updated = $model->find($access['id']);
        $this->assertEquals(1, $updated['can_view']);
        $this->assertEquals(0, $updated['can_create']);
        $this->assertEquals(1, $updated['can_update']);
        $this->assertEquals(0, $updated['can_delete']);
    }

    public function testDeletePermission(): void
    {
        $model = new MenuAccessModel();
        $model->insert([
            'level_id' => 2,
            'menu_id' => 3,
            'can_view' => 1,
            'can_create' => 0,
            'can_update' => 0,
            'can_delete' => 0,
        ]);

        $access = $model->where('level_id', 2)->where('menu_id', 3)->first();
        $model->delete($access['id']);

        $deleted = $model->find($access['id']);
        $this->assertNull($deleted);
    }

    public function testFindByLevelId(): void
    {
        $model = new MenuAccessModel();
        $model->insert([
            'level_id' => 3,
            'menu_id' => 1,
            'can_view' => 1,
            'can_create' => 0,
            'can_update' => 0,
            'can_delete' => 0,
        ]);
        $model->insert([
            'level_id' => 3,
            'menu_id' => 2,
            'can_view' => 1,
            'can_create' => 1,
            'can_update' => 0,
            'can_delete' => 0,
        ]);
        $model->insert([
            'level_id' => 4,
            'menu_id' => 1,
            'can_view' => 1,
            'can_create' => 0,
            'can_update' => 0,
            'can_delete' => 0,
        ]);

        $accessForLevel3 = $model->where('level_id', 3)->findAll();
        $this->assertCount(2, $accessForLevel3);
    }
}
