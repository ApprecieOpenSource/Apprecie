<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 26/02/15
 * Time: 13:45
 */

namespace Apprecie\Library\Search;

use Apprecie\Library\Messaging\PrivateMessageQueue;

class SearchFilter extends PrivateMessageQueue
{
    protected $_filters = null;
    protected $_joins = null;
    protected $_baseModel = null;


    public function __construct($baseModel)
    {
        $this->_collection = array();
        $this->_joins = array();
        $this->_baseModel = $baseModel;
    }

    public function addJoin($model, $condition = null, $alias = null, $type = 'inner')
    {
        $this->_joins[] = array(strtolower($type), $model, $condition, $alias == null ? $model : $alias);
        return $this;
    }

    public function addAndLikeFilter($field, $value, $alias = '')
    {
        return $this->addFilter($field, $value, $alias);
    }

    public function addOrLikeFilter($field, $value, $alias = '')
    {
        return $this->addFilter($field, $value, $alias, 'LIKE', 'OR');
    }

    public function addAndEqualFilter($field, $value, $alias = '')
    {
        return $this->addFilter($field, $value, $alias, '=');
    }

    public function addAndNotEqualFilter($field, $value, $alias = '')
    {
        return $this->addFilter($field, $value, $alias, '!=');
    }

    public function addAndEqualOrGreaterThanFilter($field, $value, $alias = '')
    {
        return $this->addFilter($field, $value, $alias, '>=');
    }

    public function addAndEqualOrLessThanFilter($field, $value, $alias = '')
    {
        return $this->addFilter($field, $value, $alias, '<=');
    }

    public function addAndLessThanFilter($field, $value, $alias = '')
    {
        return $this->addFilter($field, $value, $alias, '<');
    }

    public function addAndGreaterThanFilter($field, $value, $alias = '')
    {
        return $this->addFilter($field, $value, $alias, '>');
    }

    public function addOrEqualsFilter($field, $value, $alias = '')
    {
        return $this->addFilter($field, $value, $alias, '=', 'OR');
    }

    public function addOrIsNullFilter($field, $alias = '')
    {
        return $this->addFilter($field, null, $alias, 'is null', 'OR');
    }

    public function addOrEqualOrGraterThanFilter($field, $value, $alias = '')
    {
        return $this->addFilter($field, $value, $alias, '>=', 'OR');
    }

    public function addOrEqualOrLessThanFilter($field, $value, $alias = '')
    {
        return $this->addFilter($field, $value, $alias, '<=', 'OR');
    }

    public function addAndIsNullFilter($field, $alias = '')
    {
        return $this->addFilter($field, null, $alias, 'is null');
    }

    public function addAndIsNotNullFilter($field, $alias = '')
    {
        return $this->addFilter($field, null, $alias, 'is null', 'AND', true);
    }

    public function addInFilter($field, $values, $alias = '')
    {
        if (!is_array($values)) {
            $values = [$values];
        }

        if(is_array($values) && count($values) == 0) {
            return $this;
        }

        if(is_array($values) && count($values) == 1 && $values[0] == null) {
            return $this;
        }

        if (count($values) == 1) {
            return $this->addAndEqualFilter($field, $values[0], $alias);
        }

        return $this->addFilter($field, $values, $alias, 'IN', 'IN');
    }

    public function addAndNotInFilter($field, array $values, $alias = '')
    {
        if (!is_array($values)) {
            $values = [$values];
        }

        if(is_array($values) && count($values) == 0) {
            return $this;
        }

        if(is_array($values) && count($values) == 1 && $values[0] == null) {
            return $this;
        }

        if (count($values) == 1) {
            return $this->addAndNotEqualFilter($field, $values[0], $alias);
        }
        return $this->addFilter($field, $values, $alias, 'IN', 'IN', true);
    }

    public function addFilter($field, $value, $alias = '', $operator = 'LIKE', $relation = 'AND', $negate = false)
    {
        $operator = strtolower($operator);
        $relation = strtolower($relation);

        if ($operator == 'in' && !is_array($value)) {
            $value = array($value);
        }

        if ($value != null || $operator == 'is null') {
            if ($operator == 'like') {
                $value = '%' . $value . '%';
            }
            $this->_filters[] = array($relation, $field, $operator, $value, $alias, $negate);
        }
        return $this;
    }

    public function getFilters()
    {
        return $this->_filters;
    }

    public function getJoins()
    {
        return $this->_joins;
    }

    public function execute($sortBy = null, $groupBy=null, $limit=null)
    {
        $modelClass = $this->_baseModel;
        return $modelClass::findByFilter($this, $sortBy, $groupBy, $limit);
    }

    public function getQuery($sortBy = null)
    {
        $modelClass = $this->_baseModel;
        return $modelClass::getQueryBuilderFromFilter($this, $sortBy);
    }
}