<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 07/12/14
 * Time: 09:35
 */
class ItemInterest extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $itemId, $interestId;

    /**
     * @param mixed $interestId

    public function setInterestId($interestId)
     * {
     * $this->interestId = $interestId;
     * }
     *
     * /**
     * @return mixed
     */
    public function getInterestId()
    {
        return $this->interestId;
    }

    /**
     * @param mixed $itemId
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;
    }

    /**
     * @return mixed
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    public function getSource()
    {
        return 'iteminterests';
    }

    public function initialize()
    {
        $this->hasOne('itemId', 'Item', 'itemId');
        $this->hasOne('interestId', 'Interest', 'interestId');
    }

    /**
     * @return Interest
     */
    public function getInterest($options = null)
    {
        return $this->getRelated('Interest', $options);
    }

    /**
     * @return Item
     */
    public function getItem($options = null)
    {
        return $this->getRelated('Item', $options);
    }
} 