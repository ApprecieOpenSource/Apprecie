<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 06/02/15
 * Time: 13:59
 */

namespace Apprecie\Library\ImportExport\Import;

use Apprecie\Library\Messaging\PrivateMessageQueue;

abstract class Import extends PrivateMessageQueue
{
    protected $_importData = null;
    protected $_hasValidated = false;

    public function __construct($data)
    {
        $this->_importData = $data;
        $this->prepareData();
    }

    public abstract function prepareData();

    public abstract function validateImport();

    public abstract function commitImport();
} 