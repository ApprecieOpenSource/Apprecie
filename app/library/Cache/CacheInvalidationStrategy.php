<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 31/10/2015
 * Time: 12:04
 */

namespace Apprecie\library\Cache;

use Apprecie\Library\Collections\Enum;

class CacheInvalidationStrategy extends Enum
{
    const OnUpdate = 'update';
    const Never = 'never';
    const OnUpdateOrInsert = 'upsert';
    const OnUpdateInsertOrDelete = 'delupsert';
}