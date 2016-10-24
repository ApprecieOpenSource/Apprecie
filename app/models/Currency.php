<?php

class Currency extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $currencyId, $alphabeticCode, $currency, $enabled, $symbol;

    /**
     * @param mixed $alphabeticCode
     */
    public function setAlphabeticCode($alphabeticCode)
    {
        $this->alphabeticCode = $alphabeticCode;
    }

    /**
     * @return mixed
     */
    public function getAlphabeticCode()
    {
        return $this->alphabeticCode;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currencyId
     */
    public function setCurrencyId($currencyId)
    {
        $this->currencyId = $currencyId;
    }

    /**
     * @return mixed
     */
    public function getCurrencyId()
    {
        return $this->currencyId;
    }

    /**
     * @param mixed $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param mixed $symbol
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;
    }

    /**
     * @return mixed
     */
    public function getSymbol()
    {
        return $this->symbol;
    }


    public function getSource()
    {
        return 'currencies';
    }

    public function initialize()
    {
        $this->hasMany('currencyId', 'Item', 'currencyId', ['reusable' => true]);
    }

    public function onConstruct()
    {
        static::setCachingMode(\Apprecie\library\Cache\CachingMode::Persistent);
    }
}