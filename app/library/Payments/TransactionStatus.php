<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 21/12/14
 * Time: 16:17
 */

namespace Apprecie\Library\Payments;

use Apprecie\Library\Collections\Enum;

class TransactionStatus extends Enum
{
    const PENDING = 'pending';
    const PROCESSING = 'processing';
    const DECLINED = 'declined';
    const APPROVED = 'approved';
    const ERROR = 'error';

    protected $_name = null;
    protected $_strings = array();

    function __construct($name)
    {
        $this->_name = $name;
        $this->_strings = array(
            static::PENDING => _g('Pending'),
            static::PROCESSING => _g('Processing'),
            static::DECLINED => _g('Declined'),
            static::APPROVED => _g('Approved'),
            static::ERROR => _g('Error')
        );
    }
}