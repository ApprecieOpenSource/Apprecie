<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 27/10/2015
 * Time: 11:21
 */

namespace Apprecie\Library\Validation\Portal;
use Apprecie\Library\Messaging\PrivateMessageQueue;

class PortalValidationUtility extends PrivateMessageQueue
{
    /**
     * Check the uniqueness of a portal name
     *
     * @param $portalName string the name to be checked
     */
    public function uniquePortalName($portalName)
    {
        if (mb_strlen($portalName) <= 3) {
            $this->appendMessageEx('Portal name must be 3 characters or more');
        }

        if (\Portal::findFirstBy("portalName",$portalName) != null) {
            $this->appendMessageEx('A portal with this name already exists');
        }
    }

    /**
     * Validates the uniqueness of a subdomain
     *
     * @param $subDomain string the subdomain to be checked
     */
    public function uniquePortalSubdomain($subDomain)
    {
        if (mb_strlen($subDomain) <= 3) {
            $this->appendMessageEx('Portal subdomain must be 3 characters or more');
        }
        if (\Portal::findFirst("portalSubdomain='{$subDomain}'") != null) {
            $this->appendMessageEx('A portal with this subdomain already exists');
        }
    }
}