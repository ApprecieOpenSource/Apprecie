<?php
/**
 * Created by PhpStorm.
 * User: hu86
 * Date: 28/09/2015
 * Time: 11:05
 */

class UserTerms extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $userId, $termsId, $acceptedDate;

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getTermsId()
    {
        return $this->termsId;
    }

    /**
     * @param mixed $termsId
     */
    public function setTermsId($termsId)
    {
        $this->termsId = $termsId;
    }

    /**
     * @return mixed
     */
    public function getAcceptedDate()
    {
        return $this->acceptedDate;
    }

    /**
     * @param mixed $acceptedDate
     */
    public function setAcceptedDate($acceptedDate)
    {
        $this->acceptedDate = $acceptedDate;
    }

    public function getSource()
    {
        return 'userterms';
    }

    public function initialize()
    {
        $this->belongsTo('userId', 'users', 'userId');
        $this->belongsTo('termsId', 'terms', 'termsId');
    }

    public function onConstruct()
    {
        $this->setDefaultFields(['acceptedDate']);
    }
}