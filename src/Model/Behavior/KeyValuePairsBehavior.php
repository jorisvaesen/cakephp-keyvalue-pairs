<?php

namespace JorisVaesen\KeyValuePairs\Model\Behavior;

use ArrayObject;
use Cake\Cache\Cache;
use Cake\Collection\Collection;
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
        'cache' => false,
        'cacheConfig' => 'default'
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
        if (is_array($this->config('allowedKeys')) && !in_array($entity->{$this->config('fields.key')}, $this->config('allowedKeys'))) {
            $event->stopPropagation();
            return false;
        }
    }

    /**
     * Invalidate cache after changes are saved
     *
     * @param \Cake\Event\Event $event The afterSave event that was fired
     * @param \Cake\ORM\Entity $entity The entity that has been saved
     * @param \ArrayObject $options the options passed to the save method
     * @return void
     */
    public function afterSave(Event $event, Entity $entity, ArrayObject $options)
    {
        if ($this->config('cache')) {
            Cache::delete('key_value_pairs_' . $this->_table->table(), $this->config('cacheConfig'));
        }
    }

    /**
     * Checks if deletion is allowed
     *
     * @param \Cake\Event\Event $event The beforeDelete event that was fired
     * @param \Cake\ORM\Entity $entity The entity that is going to be deleted
     * @param \ArrayObject $options the options passed to the delete method
     * @return void|false
     */
    public function beforeDelete(Event $event, Entity $entity, ArrayObject $options)
    {
        if ($this->config('preventDeletion') === true || (is_array($this->config('preventDeletion')) && in_array($entity->{$this->config('fields.key')}, $this->config('preventDeletion')))) {
            $event->stopPropagation();
            return false;
        }
    }

    /**
     * Invalidate cache after deletion
     *
     * @param \Cake\Event\Event $event The afterDelete event that was fired
     * @param \Cake\ORM\Entity $entity The entity that has been saved
     * @param \ArrayObject $options the options passed to the delete method
     * @return void
     */
    public function afterDelete(Event $event, Entity $entity, ArrayObject $options)
    {
        if ($this->config('cache')) {
            Cache::delete('key_value_pairs_' . $this->_table->table(), $this->config('cacheConfig'));
        }
    }

    /**
     * Get value by key
     *
     * @param string $key The key you want the value of
     * @param bool $asEntity Whether the pair should be returned as entity
     * @return string|bool
     */
    public function getPair($key, $asEntity = false)
    {
        if ($this->config('cache')) {
            $pair = $this->_keysFromCache([$key]);
            $pair = $pair[$key];
        } else {
            $pair = $this->_queryBuilder()
                ->andWhere([$this->config('fields.key') => $key])
                ->first();
        }

        if (!$pair) {
            return false;
        }

        if ($asEntity) {
            return $pair;
        }

        return $pair->{$this->config('fields.value')};
    }

    /**
     * Get many values by keys
     *
     * @param array $keys The keys you want the value of
     * @param bool $requireAll Fail if not all keys exist
     * @param bool $asEntity Whether the pairs should be returned as entities
     * @return string|bool
     */
    public function getPairs(array $keys, $requireAll = true, $asEntity = false)
    {
        if ($this->config('cache')) {
            $pairs = $this->_keysFromCache($keys);
            if (!$asEntity) {
                $pairs = new Collection($pairs);
            }
        } else {
            $keyField = $this->config('fields.key');
            $pairs = $this->_queryBuilder()
                ->andWhere(function ($exp, $q) use ($keyField, $keys) {
                    return $exp->in($keyField, $keys);
                });

            if ($asEntity) {
                $pairs = $pairs->combine($this->config('fields.key'), function ($entity) {
                    return $entity;
                })->toArray();
            }
        }

        if (!$asEntity) {
            $pairs = $pairs->combine(
                $this->config('fields.key'),
                $this->config('fields.value')
            )->toArray();
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
    protected function _queryBuilder()
    {
        $q = $this->_table
                ->find()
                ->contain([]);

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
    protected function _keysFromCache(array $keys)
    {
        return array_intersect_key($this->_cache(), Hash::normalize($keys));
    }

    /**
     * Read cache
     *
     * @return array All saved key value pairs
     */
    protected function _cache()
    {
        $keyField = $this->config('fields.key');
        $queryBuilder = $this->_queryBuilder();
        return Cache::remember('key_value_pairs_' . $this->_table->table(), function () use ($queryBuilder, $keyField) {
            return $queryBuilder->combine($keyField, function ($entity) {
                return $entity;
            })->toArray();
        }, $this->config('cacheConfig'));
    }
}
