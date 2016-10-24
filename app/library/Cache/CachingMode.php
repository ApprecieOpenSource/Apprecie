<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 31/10/2015
 * Time: 09:00
 */

namespace Apprecie\library\Cache;

use Apprecie\Library\Collections\Enum;

class CachingMode extends Enum
{
    const NeverCache = 'never';
    const InMemory = 'memory';
    const Persistent = 'persistent';
}