<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 24/11/14
 * Time: 09:42
 */

namespace Apprecie\Library\Testing;

use Apprecie\Library\FileIO\FileSearchIterator;

/**
 * Iterator to extract Test files from a given folder.
 */
class TestFileIterator extends FileSearchIterator
{
    /**
     * @param string $path
     */
    public function __construct($path)
    {
        parent::__construct($path, false, 'php', null, 'Test');
    }
}