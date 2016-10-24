<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 09/12/14
 * Time: 11:45
 */

namespace Apprecie\Library\Tracing;

use Phalcon\DI;

trait ActivityTraceTrait
{
    public function logActivity($activity, $activityDetails)
    {
        return DI::getDefault()->get('activitylog')->logActivity($activity, $activityDetails);
    }

    public function logSecurityEvent($event, $details)
    {
        return DI::getDefault()->get('activitylog')->logSecurityEvent($event, $details);
    }
} 