<?php

namespace Apprecie\Library\Mail;

use Apprecie\Library\Collections\Enum;
use Apprecie\Library\Users\UserRole;

class EmailTemplateType extends Enum
{
    const SIGNUP_CLIENT = 'signupClient';
    const SIGNUP_INTERNAL = 'signupInternal';
    const SIGNUP_MANAGER = 'signupManager';
    const SIGNUP_APPRECIE_SUPPLIER = 'signupApprecieSupplier';
    const SIGNUP_AFFILIATE_SUPPLIER = 'signupAffiliateSupplier';
    const SIGNUP_PORTAL_ADMIN = 'signupPortalAdministrator';

    const SUGGESTION_ON_PORTAL = 'suggestionOnPortal';
    const SUGGESTION_OFF_PORTAL = 'suggestionOffPortal';

    const INVITATION = 'invitation';

    const POST_EVENT_FOLLOW_UP = 'postEventFollowUp';
    
    public static function getSignupTemplateTypeByRoleName($role)
    {
        $emailTemplateType = null;

        switch ($role) {
            case UserRole::CLIENT:
                $emailTemplateType = static::SIGNUP_CLIENT;
                break;
            case UserRole::INTERNAL:
                $emailTemplateType = static::SIGNUP_INTERNAL;
                break;
            case UserRole::MANAGER:
                $emailTemplateType = static::SIGNUP_MANAGER;
                break;
            case UserRole::APPRECIE_SUPPLIER:
                $emailTemplateType = static::SIGNUP_APPRECIE_SUPPLIER;
                break;
            case UserRole::AFFILIATE_SUPPLIER:
                $emailTemplateType = static::SIGNUP_AFFILIATE_SUPPLIER;
                break;
            case UserRole::PORTAL_ADMIN:
                $emailTemplateType = static::SIGNUP_PORTAL_ADMIN;
                break;
        }

        return $emailTemplateType;
    }
}