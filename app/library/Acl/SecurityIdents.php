<?php

namespace Apprecie\Library\Acl;

use Apprecie\Library\Collections\Enum;

class SecurityIdents extends Enum
{
    const CAN_VIEW_USER = 'can view user';
    const CAN_MANAGE_USER = 'can manage user';
    const CAN_VIEW_ITEM = 'can view item';
    const CAN_EDIT_ITEM = 'can edit item';
    const CAN_SEE_MESSAGES = 'can see messages';
    const CAN_VIEW_ORG_QUOTAS = 'can see organisation quotas';
    const CAN_CREATE_USER = 'can create user';
    const CAN_DELETE_USER = 'can delete user';
    const CAN_PUBLISH_ITEM = 'can publish item';
    const CAN_VIEW_ORGANISATIONS = 'can view portal organisations';
    const CAN_APPROVE_ITEM = 'can approve item';
    const CAN_OPERATE_GROUP = 'can operate group';
    const CAN_OPERATE_GUEST_LIST = 'can operate guest list';
    const CAN_MANAGE_USER_ITEMS = 'can manage user items';
    const CAN_SEE_GUEST_LIST = 'can see guest list';
    const CAN_SEE_ORDER = 'can see order';
    const CAN_MANAGE_PORTAL = 'can manage portal';
    const CAN_MANAGE_ORG = 'can manager organisation';

}


