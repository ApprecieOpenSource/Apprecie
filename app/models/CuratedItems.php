<?php
//@todo  GH - does this do anything?
class CuratedItems extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $itemId, $portalId;

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

    /**
     * @param mixed $portalId
     */
    public function setPortalId($portalId)
    {
        $this->portalId = $portalId;
    }

    /**
     * @return mixed
     */
    public function getPortalId()
    {
        return $this->portalId;
    }

    public function getSource()
    {
        return 'curateditems';
    }
}