<?php

namespace JorisVaesen\KeyValuePairs\Test\TestCase\Model\Behavior;

use ArrayObject;
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
    }

    public function tearDown()
    {
        parent::tearDown();
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
}
