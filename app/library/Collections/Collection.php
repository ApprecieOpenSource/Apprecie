<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 23/11/14
 * Time: 12:57
 */
namespace Apprecie\Library\Collections;

use Phalcon\DI\Injectable;
use Phalcon\Exception;

/**
 * Generic collection class based on ArrayAccess
 * A lot of this was borrowed from  Http://codeutopia.net/code/library/CU/Collection.php.
 *
 * @todo break this down into a basiccollection and a value type collection, and a typed collection etc
 */
class Collection extends Injectable implements \Countable, \IteratorAggregate, \ArrayAccess
{
    protected $_valueType;
    protected $_isBasicType = false;
    protected $_validateFunc;
    protected $_bounceNull = true;
    protected $_collection = array();

    /**
     * If set to true (default) null inserts are bounced without an error or type checking.
     * @param boolean $value
     */
    public function setBounceNull($value)
    {
        $this->_bounceNull = $value;
    }

    /**
     * @return mixed returns the actual type of the contained members
     */
    public function getMemberType()
    {
        return $this->_valueType;
    }

    /**
     * Construct a new typed collection
     * @param string valueType collection value type
     */
    public function __construct($valueType)
    {
        $this->_valueType = $valueType;

        if (function_exists("is_$valueType")) {
            $this->_isBasicType = true;
            $this->_validateFunc = "is_$valueType";
        }
    }

    /**
     * Updates every matching field in the collection with $value, if the member of the collection
     * is a basic type, this method will perform nothing
     * @param $field
     * @param $value
     * @return Collection
     */
    public function decorateAll($field, $value)
    {
        if ($this->_valueType) {
            foreach ($this as &$d) {
                $d->$field = $value;
            }
        }

        return $this;
    }

    /**
     * returns a slice of the collection between start index and end index
     *
     * @return Collection the slice of the collection requested
     * @param integer $start The start index of the slice
     * @param integer $end The end index of the slice
     */
    public function slice($start, $end)
    {
        $validRange = array_slice($this->_collection, $start, $end - $start);
        $col = new Collection($this->_valueType);
        $items = array();

        foreach ($validRange as $item) {
            if ($item != null) {
                $items[] = $item;
            }
        }
        $col->Replace($items);

        return $col;
    }

    /**
     * returns the first item found with a $field matching $value
     * Note that field can also be the return value of any method on the encapsulated object that
     * does not require a parameter, for example property, get and set methods.
     * e.g pass getName as $field to execute $object->getName() and compare that return value
     *
     * @return Object the object with id $id
     * @param string $field The name of the field on the object to match to $value.
     * @param mixed $value The value of the id field on the object to be returned.
     */
    public function getBy($field, $value)
    {
        if ($this->_isBasicType) {
            return null;
        }

        foreach ($this->_collection as $item) {
            if (isset($item->$field)) {
                if ($item->$field == $value) {
                    return $item;
                }
            } elseif (method_exists($item, $field)) {
                if (call_user_func(array($item, $field)) == $value) {
                    return $item;
                }
            }
        }

        return null;
    }

    public function getIndexOf($field, $value)
    {
        if ($this->_isBasicType) {
            return null;
        }

        foreach ($this->_collection as $key => $item) {
            if (isset($item->$field)) {
                if ($item->$field == $value) {
                    return $key;
                }
            } elseif (method_exists($item, $field)) {
                if (call_user_func(array($item, $field)) == $value) {
                    return $key;
                }
            }
        }

        return null;
    }

    /**
     * returns the first item found with a $field matching $value and removes it from the collection
     * Note that field can also be the return value of any method on the encapsulated object that
     * does not require a parameter, for example property, get and set methods.
     * e.g pass getName as $field to execute $object->getName() and compare that return value
     *
     * @return Object the object with id $id
     * @param string $field The name of the field on the object to match to $value.
     * @param mixed $value The value of the id field on the object to be returned.
     * @author Gavin Howden
     */
    public function removeBy($field, $value)
    {
        if ($this->_isBasicType) {
            return null;
        }

        $itm = null;
        $count = 0;

        foreach ($this->_collection as $item) {
            if (method_exists($item, $field)) {
                if (call_user_func(array($item, $field)) == $value) {
                    $itm = $item;
                    break;
                }
            } elseif ($item->$field == $value) {
                $itm = $item;
                break;
            }

            $count++;
        }

        if ($itm != null) {
            $this->remove($count);
        }

        return $itm;
    }

    /**
     * Removes all items in this collection with an occurrence of $field at $value.
     *
     * Modifies the internal collection directly;
     *
     * @param mixed $field
     * @param mixed $value
     */
    public function CleanBy($field, $value)
    {
        if (!$this->_isBasicType) {
            do {
                $item = $this->removeBy($field, $value);
            } while ($item != null);
        }
    }

    /**
     * Filters the internal array, based on the field and value provided.
     *
     * Note that field can also be the return value of any method on the encapsulated object that
     * does not require a parameter, for example property, get and set methods.
     * e.g pass getName as $field to execute $object->getName() and compare that value
     *
     *
     * @param string $field The name of the field on the object to match to $value.
     * @param mixed $value This is the compare, direction is controlled by $positive and can be an array of values
     * @param boolean $positive If true filter out non matches, else filters out matches.
     * @param mixed $like comparison is performed by looking in the current field for $value  CASE INSENSITIVE
     *
     * @param bool $returnArray
     * @return Collection A copy of this collection filtered as requested.
     */
    public function filterBy($field, $value, $positive = true, $like = false, $returnArray = false)
    {
        if ($this->_isBasicType) {
            return $this;
        }

        $collection = new Collection($this->_valueType);
        $retArray = array();

        if (!is_array($value)) {
            $value = array($value);
        }

        $fieldMember = true;
        $firstPass = true;

        foreach ($this->_collection as $item) {
            $item_val = null;

            if ($firstPass) {
                if (isset($item->$field)) {
                    $item_val = $item->$field;
                    $fieldMember = true;
                } elseif (method_exists($item, $field)) {
                    $item_val = call_user_func(array($item, $field));
                    $fieldMember = false;
                }

                $firstPass = false;
            } else {

                if ($fieldMember) { //the field is in fact a field - query assuming it exists as it did on first pass
                    $item_val = $item->$field;
                } else { //the filed is a method call -> method exists on first pass so continue
                    $item_val = call_user_func(array($item, $field));
                }
            }

            $drop = $positive; //simply drop the item by default if this is 'match filter' or keep if 'not match'

            if ($item_val !== null) {
                foreach ($value as $targetVal) {
                    $drop = $like == false ? (strcasecmp($targetVal, $item_val) != 0)
                        : (stripos($item_val, $targetVal) === false);

                    $drop = ($positive == true) ? $drop : !$drop;
                    if ((!$drop && $positive) || ($drop && !$positive)) {
                        break;
                    }
                }
            }

            if (!$drop) {
                $retArray[] = $item;
            }
        }

        if ($returnArray === true) {
            return $retArray;
        }

        $collection->replace($retArray);
        return $collection;
    }

    /**
     * Filters out all elements with a $field, with a value of $operator (< >) $value
     * @param $field
     * @param $operator
     * @param $value
     * @param bool $returnArray
     * @return $this|Collection|array
     */
    public function filterNumeric($field, $operator, $value, $returnArray = false)
    {
        if ($this->_isBasicType) {
            return $this;
        }

        $collection = new Collection($this->_valueType);
        $retArray = array();

        if (!is_array($value)) {
            $value = array($value);
        }

        foreach ($this->_collection as $item) {
            $item_val = null;

            if (isset($item->$field)) {
                $item_val = $item->$field;
            } elseif (method_exists($item, $field)) {
                $item_val = call_user_func(array($item, $field));
            }

            if ($item_val != null) {
                foreach ($value as $targetVal) {
                    $drop = false;
                    $php = '$drop = (' . $item_val . ' ' . $operator . ' ' . $targetVal . ');';

                    eval($php);

                    if ($drop) {
                        break;
                    }
                }

                if (!$drop) {
                    $retArray[] = $item;
                }
            }
        }

        if ($returnArray === true) {
            return $retArray;
        }

        $collection->replace($retArray);

        return $collection;
    }

    /**
     * Appends $array to the end of this array, WARNING performs no type checking to remain performance optimal
     * @param $array
     * @return $this
     */
    public function appendArray($array)
    {
        $this->_collection = array_merge($this->_collection, $array);
        return $this;
    }

    /**
     * Returns the inner array
     * @return array
     */
    public function getArray()
    {
        return $this->_collection;
    }

    /**
     * Sorts the internal collection based on a specific field of one of the contained elements.
     *
     * If $field = null than the sort is performed directly on the element at each index
     *
     * IMPORTANT - This method modifies this collection.
     * @param string $field The field to sort on, or null if the collection contains primitive types
     * @param boolean $asc if true the asort is completed in normal order, else in desc (reverse) order
     * @param mixed $type This is one of php's sort flags defaults to SORT_REGULAR
     * @return Collection Returns a reference to this collection
     */
    public function sortBy($field = null, $asc = true, $type = SORT_REGULAR)
    {
        if (count($this->_collection) > 0) {
            if ($field == null || $this->_isBasicType) { //we short out here for a simple value type array to save the extra processing
                $asc == true ? asort($this->_collection, $type) : arsort($this->_collection, $type);
                return $this;
            }

            $newArray = array();
            $sortableArray = array();

            foreach ($this->_collection as $k => $v) {
                if (is_object($v)) {
                    $itemVal = null;
                    if (isset($v->$field)) {
                        $itemVal = $v->$field;
                    } elseif (method_exists($v, $field)) {
                        $itemVal = call_user_func(array($v, $field));
                    }

                    $sortableArray[$k] = $itemVal;
                } elseif (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $field) {
                            $sortableArray[$k] = $v2;
                        }
                    }
                } else {
                    $sortableArray[$k] = $v;
                }
            }

            $asc == true ? asort($sortableArray, $type) : arsort($sortableArray, $type);

            foreach ($sortableArray as $k => $v) {
                $newArray[] = $this->_collection[$k];
            }

            $this->replace($newArray);
        }

        return $this;
    }

    /**
     * Sorts the collection in place (i,e modifies this collection) based on the vector $vector.
     *
     * $field should indicate the field to sort on of the internal objects or associative arrays.
     *
     * This method is meaningless to an array of basic value types as the return would be identical to $vector
     *
     * @param array $vector An array od values to order by, the collection will match this vector
     * @param string $field The field on any internal objects or arrays to use to sort
     * @return Collection a reference to this collection
     */
    public function vectorSort(array $vector, $field)
    {
        if (count($this->_collection) > 0) {
            if ($field == null) { //a value type array sorted by this vector will equal vector
                return $vector;
            }

            $newArray = array();
            $sortableArray = array();

            foreach ($this->_collection as $k => $v) {
                $itemVal = null;

                if (is_object($v)) {
                    if (isset($v->$field)) {
                        $itemVal = $v->$field;
                    } elseif (method_exists($v, $field)) {
                        $itemVal = call_user_func(array($v, $field));
                    }
                } elseif (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $field) {
                            $itemVal = $sortableArray[$k] = $v2;
                        }
                    }
                } else {
                    $itemVal = $v;
                }

                //is the item in the vector
                if (in_array($itemVal, $vector) === true) {
                    if (!isset($sortableArray[$itemVal])) {
                        $sortableArray[$itemVal] = array();
                    }

                    $sortableArray[$itemVal][] = $k;
                }
            }

            foreach ($vector as $key) {
                if (array_key_exists($key, $sortableArray)) {
                    if (!is_array($sortableArray[$key])) {
                        $newArray[] = $this->_collection[$sortableArray[$key]];
                    } else {
                        foreach ($sortableArray[$key] as $val) {
                            $newArray[] = $this->_collection[$val];
                        }
                    }
                }
            }

            $this->replace($newArray);
        }

        return $this;
    }

    /**
     * Removes duplicates from this collection based on $keyfield
     *
     * Note that this method operates directly on its own collection and removes all duplicates!! all versions of.
     *
     * @param string $keyfield
     * @return Collection
     */
    public function removeDuplicates($keyfield)
    {
        if ($this->_isBasicType) {
            return $this;
        }
        //get an array of the keyfield values
        $keys = array();

        foreach ($this->_collection as $item) {
            $item_val = null;

            if (method_exists($item, $keyfield)) {
                $item_val = call_user_func(array($item, $keyfield));
            } elseif (isset($item->$keyfield)) {
                $item_val = $item->$keyfield;
            }

            if ($item_val != null) {
                $keys[] = $item_val;
            }
        }

        //get duplicates
        $dupkeys = $this->arrayNotUnique($keys);

        //remove them from the collection
        foreach ($dupkeys as $val) {
            do {
                $item = $this->RemoveBy($keyfield, $val);
            } while ($item != null);
        }

        return $this;
    }

    /**
     * Searches the collection for duplicates of $keyfield when a duplicate is found it merges any
     * $mergefields by creating a comma seperated list of values on $mergefield
     *
     * The duplicates are then removed from the collection.
     * Note that this method operates directly on its own collection
     *
     * @param string $keyfield The field that is then unique key on the collection (to find duplicates)
     * @param mixed $mergefields an array of fields to consider for merge.
     * @return \Collection
     */
    public function mergeDuplicates($keyfield, $mergefields)
    {
        if ($this->_isBasicType) {
            return $this;
        }
        //get an array of the keyfield values
        $keys = array();
        if (!is_array($mergefields)) {
            $mergefields = array($mergefields);
        }

        foreach ($this->_collection as $item) {
            $item_val = null;

            if (method_exists($item, $keyfield)) {
                $item_val = call_user_func(array($item, $keyfield));
            } elseif (isset($item->$keyfield)) {
                $item_val = $item->$keyfield;
            }

            if ($item_val != null) {
                $keys[] = $item_val;
            }
        }

        //get duplicates
        $dupkeys = $this->arrayNotUnique($keys);

        //build an array of sorted duplicates, and remove them from the collection
        $items = array();

        foreach ($dupkeys as $val) {
            while (($item = $this->removeBy($keyfield, $val)) != null) {
                if (!array_key_exists($val, $items)) {
                    $items[$val] = array();
                }

                if (!in_array($item, $items[$val])) {
                    $items[$val][] = $item;
                }
            }
        }


        //merge duplicates and add modified record back to the collection
        foreach ($items as $dups) {
            $merged_item = $dups[0];

            $count = count($dups);
            for ($i = 1; $i < $count; $i++) {
                foreach ($mergefields as $merge) {
                    if (!method_exists($merged_item, $merge)) { //we cant merge the results of methods
                        $merged_item->$merge .= ",{$dups[$i]->$merge}"; //value,value,value
                    }
                }
            }

            $this->_collection[] = $merged_item;
        }

        return $this;
    }

    /**
     * @param array $a
     * @return array
     */
    private function arrayNotUnique($a = array())
    {
        return array_diff_key($a, array_unique($a));
    }

    /**
     * Returns an array of ($field) values for each item in the collection.
     *
     * @param string $field The field to return values for
     * @return array $array  A single dimensions array of the collected values
     */
    public function getFieldValues($field)
    {
        if ($this->_isBasicType) {
            return $this->getArray();
        }

        $vals = array();

        foreach ($this->_collection as $item) {
            $item_val = null;

            if (method_exists($item, $field)) {
                $item_val = call_user_func(array($item, $field));
            } elseif (isset($item->$field)) {
                $item_val = $item->$field;
            }

            if ($item_val !== null) {
                $vals[] = $item_val;
            }
        }

        return $vals;
    }

    public function clear()
    {
        $this->_collection = array();
    }

    /**
     * Replaces the entire inner collection with the $innerArray performs no type Testing.
     *
     * If you have 100's of elements to add to the collection add them to a php array and use this emthod
     * rather than calling ->add() 500 times!!
     *
     * @param mixed $innerArray
     * @return \Collection
     */
    public function replace(array $innerArray)
    {
        $this->_collection = $innerArray;
        return $this;
    }


    /**
     * Add a value into the collection
     * @param mixed $value
     * @return \Collection
     * @throws \InvalidArgumentException when wrong type
     */
    public function add($value)
    {
        if ($value == null && $this->_bounceNull = true) {
            return $this;
        }

        if (!$this->isValidType($value)) {
            throw new \InvalidArgumentException('Trying to add a value of wrong type');
        }

        $this->_collection[] = $value;

        return $this;
    }

    /**
     * Set index's value
     * @param integer $index
     * @param mixed $value
     * @return Collection
     * @throws \OutOfRangeException
     * @throws \InvalidArgumentException
     */
    public function set($index, $value)
    {
        if ($index >= $this->count()) {
            throw new \OutOfRangeException('Index out of range');
        }

        if (!$this->isValidType($value)) {
            throw new \InvalidArgumentException('Trying to add a value of wrong type');
        }

        $this->_collection[$index] = $value;

        return $this;
    }

    /**
     * Remove a value from the collection
     * @param integer $index index to remove
     * @return Collection
     * @throws \OutOfRangeException if index is out of range
     */
    public function remove($index)
    {
        if ($index >= $this->count()) {
            throw new \OutOfRangeException('Index out of range');
        }

        array_splice($this->_collection, $index, 1);

        return $this;
    }

    /**
     * Removes the item matching $item from this collection.  Removes all occurrences.
     * @param $item
     * @return $this
     */
    public function removeByValue($item)
    {
        $this->_collection = array_values(array_udiff($this->_collection, array($item), array(&$this, 'compareItems')));
        return $this;
    }


    /**
     * Sorts the collection based on the results of the compareItems method
     *
     * @param string $compareMethod
     * @return Collection
     */
    public function naturalOrder($compareMethod = 'compareItems')
    {
        usort($this->_collection, array(&$this, $compareMethod));
        return $this;
    }

    /**
     * Used by the Remove by value method and the NaturalOrder method.
     *
     * You should update this for better performace to use a single key field
     * on any contained objects if they exist.
     *
     * @param $obj_a
     * @param $obj_b
     * @return int
     */
    public function compareItems($obj_a, $obj_b)
    {
        if ($obj_a == $obj_b) {
            return 0;
        }

        return $obj_a > $obj_b ? +1 : -1;
    }

    /**
     * Return value at index
     * @param integer $index
     * @return mixed
     * @throws \OutOfRangeException
     */
    public function get($index)
    {
        //if($index == -1) throw new \Exception('bing');
        if ($index >= $this->count()) {
            throw new \OutOfRangeException('Index out of range');
        }

        return $this->_collection[$index];
    }

    /**
     * Reverses the order if elements in the internal array and returns self
     * @return Collection
     */
    public function reverse()
    {
        $this->_collection = array_reverse($this->_collection);
        return $this;
    }

    /**
     * @param $object
     * @return bool
     */
    public function contains($object)
    {
        return in_array($object, $this->_collection);
    }

    /**
     * Determine if index exists
     * @param integer $index
     * @return boolean
     */
    public function exists($index)
    {
        if ($index >= $this->count()) {
            return false;
        }

        return true;
    }

    /**
     * Return count of items in collection
     * Implements countable
     * @return integer
     */
    public function count()
    {
        return count($this->_collection);
    }

    /**
     * Determine if this value can be added to this collection
     * @param string $value
     * @return boolean
     */
    public function isValidType($value)
    {
        if ($this->_isBasicType) {
            $validateFunc = $this->_validateFunc;
            return $validateFunc($value);
        } else {
            return $value instanceof $this->_valueType;
        }
    }

    /**
     * Return an iterator
     * Implements IteratorAggregate
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->_collection);
    }

    /**
     * Set offset to value
     * Implements ArrayAccess
     * @see set
     * @param integer $offset
     * @param mixed $value
     * @return Collection
     */
    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    /**
     * Unset offset
     * Implements ArrayAccess
     * @see remove
     * @param integer $offset
     * @return Collection
     */
    public function offsetUnset($offset)
    {
        return $this->remove($offset);
    }

    /**
     * get an offset's value
     * Implements ArrayAccess
     * @see get
     * @param integer $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Determine if offset exists
     * Implements ArrayAccess
     * @see exists
     * @param integer $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return $this->exists($offset);
    }

    /**
     * @param string $dateformat
     * @param null $skipFields
     * @param string $delimeter
     * @return string
     * @throws Exception
     */
    public function getCSV($dateformat = 'd-m-Y', $skipFields = null, $delimeter = ',')
    {
        if (!is_array($skipFields)) {
            $skipFields = array($skipFields);
        }
        $data = array();

        foreach ($this->_collection as $item) {
            $d = get_object_vars($item);
            $validItems = array();

            foreach ($d as $key => $value) {
                if (!in_array($key, $skipFields)) {
                    if ($value instanceof \DateTime) {
                        $d[$key] = $value->format($dateformat);
                    }

                    $validItems[$key] = $d[$key];
                }
            }

            $data[] = $validItems;
        }

        throw new Exception('I am not implimented');
    }

    /**
     * @return mixed
     */
    public function deepCopy()
    {
        return unserialize(serialize($this));
    }
}


