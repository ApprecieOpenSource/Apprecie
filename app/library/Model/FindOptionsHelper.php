<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 07/12/2015
 * Time: 14:56
 */

namespace Apprecie\Library\Model;


class FindOptionsHelper
{
    /**
     * Takes a series of prams and builds a Phalcon find compatible options array.
     *
     * Note that conditions can be inline or bound, if conditions are bound then the values should be in $bind and
     * the place holders in the conditions use the format ?n
     *
     * <code>
     * $conditions = 'name = ?1 AND type = ?2';
     * $bind = [1=>'gavin',2=>'robot'];     *
     * </code>
     *
     * $fields is equivalent to the phalcon columns option and allows returning only a subset of the actual records.
     * <code>
     * $fields = 'id, name';
     * </code>
     *
     * hydrationMode is identical to the Phalcon hydration modes, and is default (Models)  when null, and could also be
     * one of the result set hydration modes such as Resultset::HYDRATE_OBJECTS
     * @param null $orderBy
     * @param null $limit
     * @param null $offset
     * @param null $conditions
     * @param null $bind
     * @param null $hydrationMode
     * @param null $fields
     * @return array
     */
    public static function prepareFindOptions($orderBy = null, $limit = null, $offset = null, $conditions = null, $bind = null, $hydrationMode = null, $fields = null)
    {
        $params = [];
        if($orderBy != null) {
            $params['order'] = $orderBy;
        }

        if($limit != null) {
            $params['limit'] = $limit;
        }

        if($offset != null) {
            $params['offset'] = $offset;
        }

        if($hydrationMode != null) {
            $params['hydration'] = $hydrationMode;
        }

        if($conditions != null) {
            $params['conditions'] = $conditions;
        }

        if($bind != null) {
            if(! is_array($bind)) $bind = [$bind];
            $params['bind'] = $bind;
        }

        if($fields != null) {
            $params['columns'] = $fields;
        }

        if($orderBy != null) {
            $params['order'] = $orderBy;
        }

        return $params;
    }
}