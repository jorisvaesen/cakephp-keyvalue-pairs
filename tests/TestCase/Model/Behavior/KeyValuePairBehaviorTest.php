<?php

namespace JorisVaesen\KeyValuePairs\Test\TestCase\Model\Behavior;

use ArrayObject;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use JorisVaesen\KeyValuePairs\Model\Behavior\KeyValuePairsBehavior;

class KeyValuePairBehaviorTest extends TestCase
{
    public $fixtures = ['plugin.joris_vaesen\key_value_pairs.configs'];
    public $autoFixtures = false;

    private $table;
    private $entity;
    private $behaviorMethods;
    private $datasourceConnection = 'default';

    public function setUp()
    {
        parent::setUp();

        $this->table = $this->getMock('Cake\ORM\Table');
        $this->behaviorMethods = get_class_methods('JorisVaesen\KeyValuePairs\Model\Behavior\KeyValuePairsBehavior');

        $this->entity = new Entity([
            'key' => 'key1',
            'value' => 'value1'
        ]);

        Cache::config('configs', [
            'className' => 'File',
            'duration' => '+1 week',
            'path' => CACHE
        ]);

        $db_dsn = Hash::get($_ENV, 'db_dsn', getenv('db_dsn'));

        if ($db_dsn) {
            ConnectionManager::config('test', ['url' => $db_dsn]);
            $this->datasourceConnection = 'test';
        }
    }

    public function tearDown()
    {
        parent::tearDown();

        Cache::drop('configs');
        TableRegistry::clear();
    }

    public function testBeforeSaveAllowedKeysFalse()
    {
        $settings = [
            'allowedKeys' => false
        ];

        $methods = array_diff($this->behaviorMethods, ['config', 'beforeSave']);
        $behavior = $this->getMock('JorisVaesen\KeyValuePairs\Model\Behavior\KeyValuePairsBehavior', $methods, [$this->table, $settings]);

        $this->assertNull($behavior->beforeSave(new Event('fake.event'), $this->entity, new ArrayObject));
    }

    public function testBeforeSaveAllowedKeysArray()
    {
        $settings = [
            'allowedKeys' => ['key5', 'key6', 'key7']
        ];

        $methods = array_diff($this->behaviorMethods, ['config', 'beforeSave']);
        $behavior = $this->getMock('JorisVaesen\KeyValuePairs\Model\Behavior\KeyValuePairsBehavior', $methods, [$this->table, $settings]);

        $this->assertFalse($behavior->beforeSave(new Event('fake.event'), $this->entity, new ArrayObject));
    }

    public function testBeforeSaveAllowedKeysArrayContainsKey()
    {
        $settings = [
            'allowedKeys' => ['key1', 'key2', 'key3']
        ];

        $methods = array_diff($this->behaviorMethods, ['config', 'beforeSave']);
        $behavior = $this->getMock('JorisVaesen\KeyValuePairs\Model\Behavior\KeyValuePairsBehavior', $methods, [$this->table, $settings]);

        $this->assertNull($behavior->beforeSave(new Event('fake.event'), $this->entity, new ArrayObject));
    }

    public function testAfterSave()
    {
        $settings = [
            'cache' => true,
            'cacheKey' => 'configs'
        ];

        $methods = array_diff($this->behaviorMethods, ['config', 'afterSave']);
        $behavior = $this->getMock('JorisVaesen\KeyValuePairs\Model\Behavior\KeyValuePairsBehavior', $methods, [$this->table, $settings]);
        Cache::write('key_value_pairs_' . $this->table->table(), 'sample value', $settings['cacheKey']);
        $behavior->afterSave(new Event('fake.event'), $this->entity, new ArrayObject);

        $this->assertFalse(Cache::read('key_value_pairs_' . $this->table->table(), $settings['cacheKey']));
    }

    public function testAfterSaveCacheKey()
    {
        $settings = [
            'cache' => true,
            'cacheKey' => 'configs'
        ];

        $methods = array_diff($this->behaviorMethods, ['config', 'afterSave']);
        $behavior = $this->getMock('JorisVaesen\KeyValuePairs\Model\Behavior\KeyValuePairsBehavior', $methods, [$this->table, $settings]);
        Cache::write('key_value_pairs_' . $this->table->table(), 'sample value', 'default');
        $behavior->afterSave(new Event('fake.event'), $this->entity, new ArrayObject);

        $this->assertEquals(Cache::read('key_value_pairs_' . $this->table->table(), 'default'), 'sample value');
    }

    public function testBeforeDeleteWithPreventDeletionFalse()
    {
        $settings = [
            'preventDeletion' => false
        ];

        $methods = array_diff($this->behaviorMethods, ['config', 'beforeDelete']);
        $behavior = $this->getMock('JorisVaesen\KeyValuePairs\Model\Behavior\KeyValuePairsBehavior', $methods, [$this->table, $settings]);

        $this->assertNull($behavior->beforeDelete(new Event('fake.event'), $this->entity, new ArrayObject));
    }

    public function testBeforeDeleteWithPreventDeletionTrue()
    {
        $settings = [
            'preventDeletion' => true
        ];

        $methods = array_diff($this->behaviorMethods, ['config', 'beforeDelete']);
        $behavior = $this->getMock('JorisVaesen\KeyValuePairs\Model\Behavior\KeyValuePairsBehavior', $methods, [$this->table, $settings]);

        $this->assertFalse($behavior->beforeDelete(new Event('fake.event'), $this->entity, new ArrayObject));
    }

    public function testBeforeDeleteWithPreventDeletionArray()
    {
        $settings = [
            'preventDeletion' => ['key5', 'key6', 'key7']
        ];

        $methods = array_diff($this->behaviorMethods, ['config', 'beforeDelete']);
        $behavior = $this->getMock('JorisVaesen\KeyValuePairs\Model\Behavior\KeyValuePairsBehavior', $methods, [$this->table, $settings]);

        $this->assertNull($behavior->beforeDelete(new Event('fake.event'), $this->entity, new ArrayObject));
    }

    public function testBeforeDeleteWithPreventDeletionArrayContainsKey()
    {
        $settings = [
            'preventDeletion' => ['key1', 'key2', 'key3']
        ];

        $methods = array_diff($this->behaviorMethods, ['config', 'beforeDelete']);
        $behavior = $this->getMock('JorisVaesen\KeyValuePairs\Model\Behavior\KeyValuePairsBehavior', $methods, [$this->table, $settings]);

        $this->assertFalse($behavior->beforeDelete(new Event('fake.event'), $this->entity, new ArrayObject));
    }

    public function testAfterDelete()
    {
        $settings = [
            'cache' => true,
            'cacheKey' => 'configs'
        ];

        $methods = array_diff($this->behaviorMethods, ['config', 'afterDelete']);
        $behavior = $this->getMock('JorisVaesen\KeyValuePairs\Model\Behavior\KeyValuePairsBehavior', $methods, [$this->table, $settings]);
        Cache::write('key_value_pairs_' . $this->table->table(), 'sample value', $settings['cacheKey']);
        $behavior->afterDelete(new Event('fake.event'), $this->entity, new ArrayObject);

        $this->assertFalse(Cache::read('key_value_pairs_' . $this->table->table(), $settings['cacheKey']));
    }

    public function testAfterDeleteCacheKey()
    {
        $settings = [
            'cache' => true,
            'cacheKey' => 'configs'
        ];

        $methods = array_diff($this->behaviorMethods, ['config', 'afterDelete']);
        $behavior = $this->getMock('JorisVaesen\KeyValuePairs\Model\Behavior\KeyValuePairsBehavior', $methods, [$this->table, $settings]);
        Cache::write('key_value_pairs_' . $this->table->table(), 'sample value', 'default');
        $behavior->afterDelete(new Event('fake.event'), $this->entity, new ArrayObject);

        $this->assertEquals(Cache::read('key_value_pairs_' . $this->table->table(), 'default'), 'sample value');
    }

    public function testFindPair()
    {
        $this->loadFixtures('Configs');

        $table = new Table([
            'table' => 'configs',
            'alias' => 'Configs',
            'schema' => [
                'id' => ['type' => 'integer'],
                'key' => ['type' => 'string'],
                'value' => ['type' => 'string']
            ],
            'connection' => ConnectionManager::get($this->datasourceConnection)
        ]);

        $methods = array_diff($this->behaviorMethods, ['config', 'findPair']);
        $behavior = $this->getMock('JorisVaesen\KeyValuePairs\Model\Behavior\KeyValuePairsBehavior', $methods, [$table, []]);
        $this->assertEquals('INV-2016', $behavior->findPair('invoice_prefix'));
    }

    public function testFindPairNotExistingKey()
    {
        $this->loadFixtures('Configs');

        $table = new Table([
            'table' => 'configs',
            'alias' => 'Configs',
            'schema' => [
                'id' => ['type' => 'integer'],
                'key' => ['type' => 'string'],
                'value' => ['type' => 'string']
            ],
            'connection' => ConnectionManager::get($this->datasourceConnection)
        ]);

        $methods = array_diff($this->behaviorMethods, ['config', 'findPair']);
        $behavior = $this->getMock('JorisVaesen\KeyValuePairs\Model\Behavior\KeyValuePairsBehavior', $methods, [$table, []]);
        $this->assertFalse($behavior->findPair('not_existing_key'));
    }

    public function testQueryBuilder()
    {
        $table = new Table([
            'table' => 'configs',
            'alias' => 'Configs',
            'schema' => [
                'id' => ['type' => 'integer'],
                'key' => ['type' => 'string'],
                'value' => ['type' => 'string']
            ]
        ]);

        $method = new \ReflectionMethod(
            'JorisVaesen\KeyValuePairs\Model\Behavior\KeyValuePairsBehavior',
            '_queryBuilder'
        );
        $method->setAccessible(true);
        $query = $method->invoke(new KeyValuePairsBehavior($table, []));

        $this->assertInstanceOf('Cake\ORM\Query', $query);
        $this->assertNull($query->clause('where'));
    }
}
