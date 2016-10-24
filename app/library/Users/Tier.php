<?php
/**
 * Created by PhpStorm.
 * User: hu86
 * Date: 05/01/2016
 * Time: 15:09
 */

namespace Apprecie\Library\Users;

use Apprecie\Library\Collections\Enum;

class Tier extends Enum
{
    const ONE = 1;
    const TWO = 2;
    const THREE = 3;
    const CORPORATE = -1;

    protected $_name = null;
    protected $_strings = array();
    protected $_explanatoryStrings = array();
    protected $_helpStrings = array();

    function __construct($name)
    {
        $this->_name = $name;
        $this->_strings = array(
            static::ONE => _g('Tier 1'),
            static::TWO => _g('Tier 2'),
            static::THREE => _g('Tier 3'),
            static::CORPORATE => _g('Corporate')
        );

        $this->_explanatoryStrings = array(
            static::ONE => _g('Tier 1 ($1M+)'),
            static::TWO => _g('Tier 2 ($5M+)'),
            static::THREE => _g('Tier 3 ($30M+)'),
            static::CORPORATE => _g('Corporate')
        );

        $this->_helpStrings = array(
            static::ONE => _g("With liquid assets in excess of $1M"),
            static::TWO => _g("With liquid assets in excess of $5M"),
            static::THREE => _g("With liquid assets in excess of $30M"),
            static::CORPORATE => _g("Corporate will not have a wealth level assigned")
        );
    }

    public function getExplanatoryText() {
        if(defined('static::' . $this->getKeyByValue($this->_name)) === false || array_key_exists($this->_name, $this->_explanatoryStrings) === false) {
            return '';
        } else {
            return $this->_explanatoryStrings[$this->_name];
        }
    }

    public function getHelpText() {
        if(defined('static::' . $this->getKeyByValue($this->_name)) === false || array_key_exists($this->_name, $this->_helpStrings) === false) {
            return '';
        } else {
            return $this->_helpStrings[$this->_name];
        }
    }
}