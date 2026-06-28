<?php

namespace Tests\Unit\Models;

use App\Models\UsersModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * @internal
 */
final class UsersModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = false;

    protected function setUp(): void
    {
        parent::setUp();

        $forge = \Config\Database::forge('tests');
        $forge->dropTable('users', true);
        $forge->addField([
            'id' => [
                'type' => 'INTEGER',
                'auto_increment' => true,
            ],
            'level_id' => [
                'type' => 'INTEGER',
                'null' => true,
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'default' => 1,
            ],
            'change_password' => [
                'type' => 'TINYINT',
                'default' => 0,
                'null' => true,
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
        $forge->createTable('users');
    }

    public function testTableName(): void
    {
        $model = new UsersModel();
        $this->assertSame('users', $model->getTable());
    }

    public function testAllowedFields(): void
    {
        $model = new UsersModel();
        $allowedFields = $this->getPrivateProperty($model, 'allowedFields');

        $this->assertContains('level_id', $allowedFields);
        $this->assertContains('nama', $allowedFields);
        $this->assertContains('username', $allowedFields);
        $this->assertContains('email', $allowedFields);
        $this->assertContains('password', $allowedFields);
        $this->assertContains('is_active', $allowedFields);
        $this->assertContains('change_password', $allowedFields);
    }

    public function testReturnType(): void
    {
        $model = new UsersModel();
        $returnType = $this->getPrivateProperty($model, 'returnType');
        $this->assertSame('array', $returnType);
    }

    public function testUseTimestamps(): void
    {
        $model = new UsersModel();
        $useTimestamps = $this->getPrivateProperty($model, 'useTimestamps');
        $this->assertTrue($useTimestamps);
    }

    public function testInsertAndFind(): void
    {
        $model = new UsersModel();
        $model->insert([
            'level_id' => 1,
            'nama' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'is_active' => 1,
        ]);

        $user = $model->where('username', 'testuser')->first();
        $this->assertNotNull($user);
        $this->assertSame('Test User', $user['nama']);
        $this->assertSame('testuser', $user['username']);
        $this->assertSame('test@example.com', $user['email']);
    }

    public function testUpdateUser(): void
    {
        $model = new UsersModel();
        $model->insert([
            'level_id' => 1,
            'nama' => 'Original',
            'username' => 'original',
            'email' => 'orig@example.com',
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'is_active' => 1,
        ]);

        $user = $model->where('username', 'original')->first();
        $model->update($user['id'], ['nama' => 'Updated']);

        $updated = $model->find($user['id']);
        $this->assertSame('Updated', $updated['nama']);
    }

    public function testDeleteUser(): void
    {
        $model = new UsersModel();
        $model->insert([
            'level_id' => 1,
            'nama' => 'ToDelete',
            'username' => 'todelete',
            'email' => 'del@example.com',
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'is_active' => 1,
        ]);

        $user = $model->where('username', 'todelete')->first();
        $model->delete($user['id']);

        $deleted = $model->find($user['id']);
        $this->assertNull($deleted);
    }

    public function testToggleActiveStatus(): void
    {
        $model = new UsersModel();
        $model->insert([
            'level_id' => 1,
            'nama' => 'Active User',
            'username' => 'activeuser',
            'email' => 'active@example.com',
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'is_active' => 1,
        ]);

        $user = $model->where('username', 'activeuser')->first();
        $this->assertEquals(1, $user['is_active']);

        $model->update($user['id'], ['is_active' => 0]);
        $updated = $model->find($user['id']);
        $this->assertEquals(0, $updated['is_active']);
    }
}
