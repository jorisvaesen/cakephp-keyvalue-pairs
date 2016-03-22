<?php

namespace Jorisvaesen\KeyValuePairs\Model\Behavior;

use ArrayObject;
use Cake\Cache\Cache;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\Utility\Hash;

class KeyValuePairsBehavior extends Behavior
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
        if ($this->config('allowedKeys') && !in_array($entity->{$this->config('fields.key')}, $this->config('allowedKeys'))) {
            $event->stopPropagation();
            return false;
        }
    }

    /**
     * Checks if deletion is allowed
     *
     * @param \Cake\Event\Event $event The beforeSave event that was fired
     * @param \Cake\ORM\Entity $entity The entity that is going to be saved
     * @param \ArrayObject $options the options passed to the save method
     * @return void|false
     */
    public function beforeDelete(Event $event, Entity $entity, ArrayObject $options)
    {
        if ((is_array($this->config('preventDeletion')) && in_array($entity->{$this->config('fields.key')}, $this->config('preventDeletion'))) || $this->config('preventDeletion')) {
            $event->stopPropagation();
            return false;
        }
    }

    /**
     * Get value by key
     *
     * @param string $key The key you want the value of
     * @return string|bool
     */
    public function getValueByKey($key)
    {
        if ($this->config('cache')) {
            $pair = $this->_keysFromCache([$key]);
        } else {
            $pair = $this->_queryBuilder()
                ->andWhere([$this->config('fields.key') => $key])
                ->limit(1)
                ->toArray();
        }

        if (!$pair) {
            return false;
        }

        return $pair[$this->config('fields.value')];
    }

    /**
     * Get many values by keys
     *
     * @param array $keys The keys you want the value of
     * @param bool $requireAll Fail if not all keys exist
     * @return string|bool
     */
    public function getValuesByKeys(array $keys, $requireAll = true)
    {
        if ($this->config('cache')) {
            $pairs = $this->_keysFromCache($keys);
        } else {
            $keyField = $this->config('fields.key');
            $pairs = $this->_queryBuilder()
                ->andWhere(function ($exp, $q) use ($keyField, $keys) {
                    return $exp->in($keyField, $keys);
                })
                ->toArray();
        }


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
    private function _queryBuilder()
    {
        $q = $this->_table
            ->find('list', [
                'keyField' => $this->config('fields.key'),
                'valueField' => $this->config('fields.value')
            ])
            ->contain([])
            ->hydrate(false);

        if ($this->config('scope')) {
            $q->andWhere($this->config('scope'));
        }

        return $q;
    }

    /**
     * Filter needed keys from cache
     *
     * @param array $keys The keys you want the values of
     * @return array The key value pairs
     */
    private function _keysFromCache(array $keys)
    {
        $pairs = $this->_cache();
        return array_intersect($pairs, Hash::normalize($keys));
    }

    /**
     * Read cache
     *
     * @return array All saved key value pairs
     */
    private function _cache()
    {
        $queryBuilder = $this->_queryBuilder();
        return Cache::remember('key_value_pairs_' . $this->_table->table(), function () use ($queryBuilder) {
            return $queryBuilder->toArray();
        }, $this->config('cacheKey'));
    }
}
