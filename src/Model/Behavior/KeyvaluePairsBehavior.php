<?php

namespace Jorisvaesen\KeyvaluePairs\Model\Behavior;

use ArrayObject;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;

class KeyvaluePairsBehavior extends Behavior
{
    protected $_defaultConfig = [
        'fields' => [
            'key' => 'key',
            'value' => 'value'
        ],
        'scope' => false,
        'preventDeletion' => false,
        'allowedKeys' => false,
        'caching' => false,
        'cacheKey' => 'default'
    ];

    /**
     * Checks if the saved key is allowed
     *
     * @param \Cake\Event\Event $event The beforeSave event that was fired
     * @param \Cake\ORM\Entity $entity The entity that is going to be saved
     * @param \ArrayObject $options the options passed to the save method
     * @return void|false
     */
    public function beforeSave(Event $event, Entity $entity, ArrayObject $options)
    {
        if ($this->config('allowedKeys') && !in_array($entity->{$this->_table->primaryKey()}, $this->config('allowedKeys'))) {
            $event->stopPropagation();
            return false;
        }
    }

    /**
     * Checks if deletion of a record is allowed
     *
     * @param \Cake\Event\Event $event The beforeSave event that was fired
     * @param \Cake\ORM\Entity $entity The entity that is going to be saved
     * @param \ArrayObject $options the options passed to the save method
     * @return void|false
     */
    public function beforeDelete(Event $event, Entity $entity, ArrayObject $options)
    {
        if (!$this->config('allowDeletion')) {
            $event->stopPropagation();
            return false;
        }
    }

    /**
     * Get value from datasource
     *
     * @param string $key The key you want the value of
     * @return string|bool
     */
    public function getValueByKey($key)
    {
        $pair = $this->queryBuilder()
                            ->where([$this->config('fields.key') => $key])
                            ->limit(1)
                            ->toArray();

        if (!$pair) {
            return false;
        }

        return $pair[$this->config('fields.value')];
    }

    /**
     * Get value from datasource
     *
     * @param array $keys The keys you want the value of
     * @param bool $requireAll Fail if not all keys exist
     * @return string|bool
     */
    public function getValuesByKeys(array $keys, $requireAll = true)
    {
        $keyField = $this->config('fields.key');

        $pairs = $this->queryBuilder()
                            ->where(function ($exp, $q) use ($keyField, $keys) {
                                return $exp->in($keyField, $keys);
                            })
                            ->toArray();

        if (!count($pairs) || ($requireAll && count($keys) != count($pairs))) {
            return false;
        }

        return $pairs;
    }

    /**
     * Get common query builder
     *
     * @return \Cake\ORM\Query The query builder
     */
    private function queryBuilder()
    {
        return $this->_table->table()
                                ->find('list', [
                                    'keyField' => $this->config('fields.key'),
                                    'valueField' => $this->config('fields.value')
                                ])
                                ->contain([])
                                ->hydrate(false);
    }
}
