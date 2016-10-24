<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 26/05/15
 * Time: 16:26
 */

namespace Apprecie\Library\Model;

use Apprecie\Library\Cache\CachableTrait;
use Apprecie\Library\Cache\CacheInvalidationStrategy;
use Apprecie\Library\Search\SearchFilter;
use Phalcon\DI;

class CachedApprecieModel extends ApprecieModelBase
{
    use CachableTrait;
    protected static $_cacheInvalidationStrategy = CacheInvalidationStrategy::OnUpdateInsertOrDelete;

    public static function setInvalidationStrategy($strategy)
    {
        static::$_cacheInvalidationStrategy = $strategy;
    }

    public static function getInvalidationStrategy()
    {
        return static::$_cacheInvalidationStrategy;
    }

    public function afterCreate()
    {
        if(static::getInvalidationStrategy() == CacheInvalidationStrategy::OnUpdateInsertOrDelete
            || static::getInvalidationStrategy() == CacheInvalidationStrategy::OnUpdateOrInsert) {
            static::clearCache(get_called_class());
        }

        parent::afterCreate();
    }

    public function afterDelete()
    {
        if(static::getInvalidationStrategy() == CacheInvalidationStrategy::OnUpdateInsertOrDelete) {
            static::clearCache(get_called_class());
        }

        parent::afterDelete();
    }

    public function afterUpdate()
    {
        if(static::getInvalidationStrategy() == CacheInvalidationStrategy::OnUpdateInsertOrDelete
            || static::getInvalidationStrategy() == CacheInvalidationStrategy::OnUpdateOrInsert
            || static::getInvalidationStrategy() == CacheInvalidationStrategy::OnUpdate) {
            static::clearCache(get_called_class());
        }

        parent::afterUpdate();
    }

    public static function find($parameters = null) {
        $key = static::createCacheKey($parameters, 'f');
        $content = null;

        if(! static::cacheHasKey(get_called_class(), $key)) {
            $content = parent::find($parameters);
            static::writeToCache(get_called_class(), $key, $content);
        } else {
            $content = static::readFromCache(get_called_class(), $key);
        }

        return $content;
    }

    public static function findFirst($parameters = null) {
        $key = static::createCacheKey($parameters, 'f');
        $content = null;

        if(! static::cacheHasKey(get_called_class(), $key)) {
            $content = parent::findFirst($parameters);
            static::writeToCache(get_called_class(), $key, $content);
        } else {
            $content = static::readFromCache(get_called_class(), $key);
        }

        return $content;
    }

    /**
     * returns the first matching result on $field given $values
     * wraps the ORM findFirst() method but is encryption aware, and will or encrypt $values
     * if the query field should be encrypted.
     *
     * @param $field string The name of the single field to search on
     * @param $values string|array A scalar or array of values to find a match for
     * @param ApprecieModelBase $instance
     * @return mixed
     */
    public static function findFirstBy(
        $field,
        $values,
        ApprecieModelBase $instance = null
    ) {
        $key = static::createCacheKey($values, 'ffb_' . $field);
        $content = null;

        if(! static::cacheHasKey(get_called_class(), $key)) {
            $content = parent::findFirstBy($field, $values, $instance);
            static::writeToCache(get_called_class(), $key, $content);
        } else {
            $content = static::readFromCache(get_called_class(), $key);
        }

        return $content;
    }

    /**
     * returns the matching results on $field given $values
     * wraps the ORM findFirst() method but is encryption aware, and will or encrypt $values
     * if the query field should be encrypted.
     *
     * @param $field string The name of the single field to search on
     * @param $values string|array A scalar or array of values to find a match for
     * @param null $orderBy
     * @param ApprecieModelBase $instance
     * @return mixed
     */
    public static function findBy(
        $field,
        $values,
        $orderBy = null,
        ApprecieModelBase $instance = null
    ) {
        $key = static::createCacheKey($values, 'fb_' . $field);
        $content = null;

        if(! static::cacheHasKey(get_called_class(), $key)) {
            $content = parent::findBy($field, $values, $orderBy, $instance);
            static::writeToCache(get_called_class(), $key, $content);
        } else {
            $content = static::readFromCache(get_called_class(), $key);
        }

        return $content;
    }

    public static function findByFilter(
        SearchFilter $filter,
        $orderBy = null,
        $groupBy = null,
        $limit = null,
        $cacheKey = null,
        $lifeSpan = 3600
    ) {
        return parent::findByFilter(
            $filter,
            $orderBy,
            $groupBy,
            $limit,
            $cacheKey,
            $lifeSpan
        ); // TODO: Change the autogenerated stub
    }

    /**
     * @param string $conditions something = ? and somethingelse = ?
     * @param null $params array of params in condition order
     * @return Model\Resultset\Simple
     */
    public static function findBySql($conditions, $params = null, $cacheKey = null, $lifeSpan = 3600)
    {
        return parent::findBySql($conditions, $params, $cacheKey, $lifeSpan); // TODO: Change the autogenerated stub
    }


} 