<?php

namespace JorisVaesen\KeyValuePairs\Test\TestCase\Model\Behavior;

use ArrayObject;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\TestSuite\TestCase;

class KeyValuePairBehaviorTest extends TestCase
{
    private $table;
    private $entity;
    private $behaviorMethods;

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
    }

    public function tearDown()
    {
        parent::tearDown();

        Cache::drop('configs');
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
}
