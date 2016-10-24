<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 27/10/14
 * Time: 10:31
 */

namespace Apprecie\Library\Validation\Portal;

use Apprecie\Library\Validation\RecordValidator;
use Phalcon\Validation;

class PortalValidation extends RecordValidator
{
    public function __construct($subject = null)
    {
        parent::__construct($subject);
    }

    /**
     * Validates a new Portal
     *
     * @param $portalName string The name of the new Portal
     * @param $subDomain string The subdomain of the new Portal
     * @return bool
     */
    public static function newPortal($portalName, $subDomain)
    {
        $util = new PortalValidationUtility();

        $util->uniquePortalName($portalName);
        $util->uniquePortalSubdomain($subDomain);

        if(count($util->getMessages()) == 0) return true;
        return $util->getMessages();
    }

    /**
     * Validates changes to an existing Portal
     *
     * @param $portalName string The new name for the Portal
     * @param $subDomain string The new subdomain for the Portal
     * @param $portal
     * @return bool
     */
    public function updatePortal($portalName, $subDomain, $portal)
    {
        $thisPortal = \Portal::resolve($portal);
        $util = new PortalValidationUtility();

        if ($thisPortal->getPortalName() != $portalName) {
            $util->uniquePortalName($portalName);
        }
        if ($thisPortal->getPortalSubdomain() != $subDomain) {
            $util->uniquePortalSubdomain($subDomain);
        }

        if(count($util->getMessages()) == 0) return true;
        return $util->getMessages();
    }

    protected function doValidation($obj)
    {
        if($this->_validationMode == ValidationModeEnum::CREATE) {
            $result = static::newPortal($obj->getPortalName(), $obj->getPortalSubdomain());
        } elseif($this->_validationMode == ValidationModeEnum::UPDATE) {
            $result = static::updatePortal($obj->getPortalName(), $obj->getPortalSubdomain(), $obj);
        } else {
            throw new \Exception('Unknown validation mode');
        }

        if($result !== true) {
            $this->_isValid = false;
            $this->appendMessageEx($result);
            return false;
        }

        return true;
    }
}