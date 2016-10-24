<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 09/12/14
 * Time: 11:44
 */

namespace Apprecie\Library\Tracing;


interface CanTrace
{
    public function logActivity($activity, $activityDetails);
} 