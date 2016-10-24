<?php

class Genworthleaderboard extends \Apprecie\Library\Model\CachedApprecieModel
{
    use \Apprecie\Library\DBConnection;

    protected $kpi, $sales, $userId, $organisationId, $emailAddress, $weekNumber, $importDate;

    /**
     * @param mixed $kpi
     */
    public function setImportDate($importDate)
    {
        $this->importDate = $importDate;
    }

    /**
     * @param mixed $kpi
     */
    public function getImportDate()
    {
        return $this->importDate;
    }


    /**
     * @param mixed $kpi
     */
    public function setKpi($kpi)
    {
        $this->kpi = $kpi;
    }

    /**
     * @param mixed $sales
     */
    public function setSales($sales)
    {
        $this->sales = $sales;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @param mixed $organisationId
     */
    public function setOrganisationId($organisationId)
    {
        $this->organisationId = $organisationId;
    }

    /**
     * @param mixed $emailAddress
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @param mixed $weekNumber
     */
    public function setWeekNumber($weekNumber)
    {
        $this->weekNumber = $weekNumber;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return mixed
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * @return mixed
     */
    public function getOrganisationId()
    {
        return $this->organisationId;
    }

    /**
     * @return mixed
     */
    public function getKpi()
    {
        return $this->kpi;
    }

    /**
     * @return mixed
     */
    public function getWeekNumber()
    {
        return $this->weekNumber;
    }

    /**
     * @return mixed
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    public function getSource()
    {
        return 'genworthleaderboardx';
    }
} 