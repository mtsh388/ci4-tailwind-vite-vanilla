<?php

namespace Tests\Unit\Models;

use App\Models\LevelModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * @internal
 */
final class LevelModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = false;

    protected function setUp(): void
    {
        parent::setUp();

        $forge = \Config\Database::forge('tests');
        $forge->dropTable('levels', true);
        $forge->addField([
            'id' => [
                'type' => 'INTEGER',
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $forge->addKey('id', true);
        $forge->createTable('levels');
    }

    public function testTableName(): void
    {
        $model = new LevelModel();
        $this->assertSame('levels', $model->getTable());
    }

    public function testAllowedFields(): void
    {
        $model = new LevelModel();
        $allowedFields = $this->getPrivateProperty($model, 'allowedFields');
        $this->assertContains('name', $allowedFields);
    }

    public function testReturnType(): void
    {
        $model = new LevelModel();
        $returnType = $this->getPrivateProperty($model, 'returnType');
        $this->assertSame('array', $returnType);
    }

    public function testUseTimestamps(): void
    {
        $model = new LevelModel();
        $useTimestamps = $this->getPrivateProperty($model, 'useTimestamps');
        $this->assertTrue($useTimestamps);
    }

    public function testInsertAndFind(): void
    {
        $model = new LevelModel();
        $model->insert(['name' => 'Admin']);

        $level = $model->where('name', 'Admin')->first();
        $this->assertNotNull($level);
        $this->assertSame('Admin', $level['name']);
    }

    public function testUpdate(): void
    {
        $model = new LevelModel();
        $model->insert(['name' => 'Editor']);

        $level = $model->where('name', 'Editor')->first();
        $model->update($level['id'], ['name' => 'SuperEditor']);

        $updated = $model->find($level['id']);
        $this->assertSame('SuperEditor', $updated['name']);
    }

    public function testDelete(): void
    {
        $model = new LevelModel();
        $model->insert(['name' => 'ToDelete']);

        $level = $model->where('name', 'ToDelete')->first();
        $model->delete($level['id']);

        $deleted = $model->find($level['id']);
        $this->assertNull($deleted);
    }
}
