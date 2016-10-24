<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 07/02/2016
 * Time: 16:54
 */

namespace Apprecie\Library\Acl;
<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 28/10/2015
 * Time: 10:49
 */

namespace Apprecie\Library\Acl;

use Apprecie\Library\Items\ItemState;
use Apprecie\Library\Items\UserItemState;
use Apprecie\Library\Users\RoleHierarchy;
use Apprecie\Library\Users\UserRole;
use Phalcon\DI;

/**
 * Provides a single location for various Acl type functions in the system.
 * Currently these are a set of utility methods, but when these are fully operational with the new aims
 * of providing complex custom acl per portral, it is expected that acl should be integrated into filter procedures
 * and run from the database.
 *
 * Class AccessControl
 * @package Apprecie\library\Acl
 */
class AccessControlEx
{
    /**
     * Indicates if the subject grants visibility to $user
     * @param $observingUser
     * @param $user
     * @param string $redirect
     * @return bool
     */
    public static function userCanBeSeenBy($observingUser, $user, $redirect = 'error/fourofour')
    {//     CAN_VIEW_USER
        $result = AccessControl::userCanBeSeenBy($observingUser, $user, null);

        if(! $result) {
            $providers = (new AccessManager())->permissionGrants($observingUser, 'CAN_VIEW_USER');

            foreach($providers as $provider) {
                $result = AccessControl::userCanBeSeenBy($provider->getProviderUserId(), $user, null);

                if($result) {
                    return $result;
                }
            }
        }

        if (!$result && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $result;
    }

    /**
     * checks a single or array of users for visibility by the subject
     * set second param to null to prevent redirect.
     * @param $userOrUsers
     * @param string $redirect
     * @return bool
     */
    public static function userCanManageUser($observingUser, $userOrUsers, $redirect = 'error/fourofour')
    {//     CAN_MANAGE_USER
        $result = AccessControl::userCanBeSeenBy($observingUser, $userOrUsers, null);

        if(! $result) {
            $providers = (new AccessManager())->permissionGrants($observingUser, 'CAN_MANAGE_USER');

            foreach($providers as $provider) {
                /** @var \ProvidedPermissions $provider */
                $result = AccessControl::userCanBeSeenBy($provider->getProviderUserId(), $userOrUsers, null);

                if($result) {
                    return $result;
                }
            }
        }

        if (!$result && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $result;
    }

    public static function userCanViewItem($user, $item, $redirect = 'error/fourofour')
    {//        CAN_VIEW_ITEM
        $result = AccessControl::userCanViewItem($user, $item, null);

        if(! $result) {
            $providers = (new AccessManager())->permissionGrants($user, 'CAN_VIEW_ITEM');

            foreach($providers as $provider) {
                /** @var \ProvidedPermissions $provider */
                $result = AccessControl::userCanViewItem($provider->getProviderUserId(), $item, null);

                if($result) {
                    return $result;
                }
            }
        }

        if (!$result && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $result;
    }

    /**
     * Is the subject the creator of the item
     * @param $item
     * @param string $redirect
     * @return bool
     */
    public static function userCanEditItem($user, $item, $redirect = 'error/fourofour')
    {//   CAN_EDIT_ITEM
        $result = AccessControl::userCanEditItem($user, $item, null);

        if(! $result) {
            $providers = (new AccessManager())->permissionGrants($user, 'CAN_EDIT_ITEM');

            foreach($providers as $provider) {
                /** @var \ProvidedPermissions $provider */
                $result = AccessControl::userCanEditItem($provider->getProviderUserId(), $item, null);

                if($result) {
                    return $result;
                }
            }
        }

        if (!$result && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $result;
    }

    /**
     * @param $messageThread
     * @param string $redirect
     * @return bool
     * @throws \Phalcon\Exception
     */
    public static function userCanSeeMessageThread($user, $messageThread, $redirect = 'error/fourofour')
    {//        CAN_SEE_MESSAGES
        $result = AccessControl::userCanSeeMessageThread($user, $messageThread, null);

        if(! $result) {
            $providers = (new AccessManager())->permissionGrants($user, 'CAN_SEE_MESSAGES');

            foreach($providers as $provider) {
                /** @var \ProvidedPermissions $provider */
                $result = AccessControl::userCanSeeMessageThread($provider->getProviderUserId(), $messageThread, null);

                if($result) {
                    return $result;
                }
            }
        }

        if (!$result && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $result;
    }

    /**
     * @param $user
     * @param $organisation
     * @return bool
     */
    public static function userCanViewOrganisationQuotas($user, $organisation, $redirect = 'error/fourofour')
    {//        CAN_VIEW_ORG_QUOTAS
        $result = AccessControl::userCanViewOrganisationQuotas($user, $organisation, null);

        if(! $result) {
            $providers = (new AccessManager())->permissionGrants($user, 'CAN_VIEW_ORG_QUOTAS');

            foreach($providers as $provider) {
                /** @var \ProvidedPermissions $provider */
                $result = AccessControl::userCanViewOrganisationQuotas($provider->getProviderUserId(), $organisation, null);

                if($result) {
                    return $result;
                }
            }
        }

        if (!$result && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $result;
    }

    public static function userCanCreateUser($user, $organisation, $role, $redirect = 'error/fourofour')
    {//        CAN_CREATE_USER
        $result = AccessControl::userCanCreateUser($user, $organisation, $role, null);

        if(! $result) {
            $providers = (new AccessManager())->permissionGrants($user, 'CAN_CREATE_USER');

            foreach($providers as $provider) {
                /** @var \ProvidedPermissions $provider */
                $result = AccessControl::userCanCreateUser($provider->getProviderUserId(), $organisation, $role, null);

                if($result) {
                    return $result;
                }
            }
        }

        if (!$result && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $result;
    }

    public static function userCanDeleteUser($user, $organisation, $role, $redirect = 'error/fourofour')
    {//        CAN_DELETE_USER
        $result = AccessControl::userCanDeleteUser($user, $organisation, $role, null);

        if(! $result) {
            $providers = (new AccessManager())->permissionGrants($user, 'CAN_DELETE_USER');

            foreach($providers as $provider) {
                /** @var \ProvidedPermissions $provider */
                $result = AccessControl::userCanDeleteUser($provider->getProviderUserId(), $organisation, $role, null);

                if($result) {
                    return $result;
                }
            }
        }

        if (!$result && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $result;
    }

    public static function userCanPublishItem($user, $item, $redirect = 'error/fourofour')
    {//        CAN_PUBLISH_ITEM
        $result = AccessControl::userCanPublishItem($user, $item, null);

        if(! $result) {
            $providers = (new AccessManager())->permissionGrants($user, 'CAN_PUBLISH_ITEM');

            foreach($providers as $provider) {
                /** @var \ProvidedPermissions $provider */
                $result = AccessControl::userCanPublishItem($provider->getProviderUserId(), $item, null);

                if($result) {
                    return $result;
                }
            }
        }

        if (!$result && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $result;
    }

    public static function userCanViewPortalOrganisations($user, $portal, $redirect = 'error/fourofour')
    {//        CAN_VIEW_ORGANISATIONS
        $result = AccessControl::userCanViewPortalOrganisations($user, $portal, null);

        if(! $result) {
            $providers = (new AccessManager())->permissionGrants($user, 'CAN_VIEW_ORGANISATIONS');

            foreach($providers as $provider) {
                /** @var \ProvidedPermissions $provider */
                $result = AccessControl::userCanViewPortalOrganisations($provider->getProviderUserId(), $portal, null);

                if($result) {
                    return $result;
                }
            }
        }

        if (!$result && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $result;
    }

    public static function userCanApproveItem($user, $item, $redirect = 'error/fourofour')
    {//        CAN_APPROVE_ITEM
        $result = AccessControl::userCanApproveItem($user, $item, null);

        if(! $result) {
            $providers = (new AccessManager())->permissionGrants($user, 'CAN_APPROVE_ITEM');

            foreach($providers as $provider) {
                /** @var \ProvidedPermissions $provider */
                $result = AccessControl::userCanApproveItem($provider->getProviderUserId(), $item, null);

                if($result) {
                    return $result;
                }
            }
        }

        if (!$result && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $result;

    }

    public static function userCanOperateGroup($user, $group, $redirect = 'error/fourofour')
    {//        CAN_OPERATE_GROUP
        $result = AccessControl::userCanOperateGroup($user, $group, null);

        if(! $result) {
            $providers = (new AccessManager())->permissionGrants($user, 'CAN_OPERATE_GROUP');

            foreach($providers as $provider) {
                /** @var \ProvidedPermissions $provider */
                $result = AccessControl::userCanOperateGroup($provider->getProviderUserId(), $group, null);

                if($result) {
                    return $result;
                }
            }
        }

        if (!$result && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $result;
    }

    public static function userCanOperateGuestList($user, $item, $redirect = 'error/fourofour')
    {//        CAN_OPERATE_GUEST_LIST
        $result = AccessControl::userCanOperateGuestList($user, $item, null);

        if(! $result) {
            $providers = (new AccessManager())->permissionGrants($user, 'CAN_OPERATE_GUEST_LIST');

            foreach($providers as $provider) {
                /** @var \ProvidedPermissions $provider */
                $result = AccessControl::userCanOperateGuestList($provider->getProviderUserId(), $item, null);

                if($result) {
                    return $result;
                }
            }
        }

        if (!$result && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $result;
    }

    public static function userCanManageUserItem($user, $item, $redirect = 'error/fourofour')
    {//        CAN_MANAGE_USER_ITEMS
        $result = AccessControl::userCanManageUserItem($user, $item, null);

        if(! $result) {
            $providers = (new AccessManager())->permissionGrants($user, 'CAN_MANAGE_USER_ITEMS');

            foreach($providers as $provider) {
                /** @var \ProvidedPermissions $provider */
                $result = AccessControl::userCanManageUserItem($provider->getProviderUserId(), $item, null);

                if($result) {
                    return $result;
                }
            }
        }

        if (!$result && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $result;
    }

    public static function userCanSeeGuestList($user, $item, $redirect = 'error/fourofour')
    {//        CAN_SEE_GUEST_LIST
        $result = AccessControl::userCanSeeGuestList($user, $item, null);

        if(! $result) {
            $providers = (new AccessManager())->permissionGrants($user, 'CAN_SEE_GUEST_LIST');

            foreach($providers as $provider) {
                /** @var \ProvidedPermissions $provider */
                $result = AccessControl::userCanSeeGuestList($provider->getProviderUserId(), $item, null);

                if($result) {
                    return $result;
                }
            }
        }

        if (!$result && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $result;
    }

    public static function userCanSeeOrder($user, $order, $redirect = 'error/fourofour')
    {//        CAN_SEE_ORDER
        $result = AccessControl::userCanSeeOrder($user, $order, null);

        if(! $result) {
            $providers = (new AccessManager())->permissionGrants($user, 'CAN_SEE_ORDER');

            foreach($providers as $provider) {
                /** @var \ProvidedPermissions $provider */
                $result = AccessControl::userCanSeeOrder($provider->getProviderUserId(), $order, null);

                if($result) {
                    return $result;
                }
            }
        }

        if (!$result && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $result;
    }

    public static function userCanManagePortal($user, $portal, $redirect = 'error/fourofour')
    {//        CAN_MANAGE_PORTAL
        $result = AccessControl::userCanManagePortal($user, $portal, null);

        if(! $result) {
            $providers = (new AccessManager())->permissionGrants($user, 'CAN_MANAGE_PORTAL');

            foreach($providers as $provider) {
                /** @var \ProvidedPermissions $provider */
                $result = AccessControl::userCanManagePortal($provider->getProviderUserId(), $portal, null);

                if($result) {
                    return $result;
                }
            }
        }

        if (!$result && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $result;
    }

    public static function userCanManageOrganisation($user, $organisation, $redirect = 'error/fourofour')
    {//         CAN_MANAGE_ORG
        $result = AccessControl::userCanManageOrganisation($user, $organisation, null);

        if(! $result) {
            $providers = (new AccessManager())->permissionGrants($user, 'CAN_MANAGE_ORG');

            foreach($providers as $provider) {
                /** @var \ProvidedPermissions $provider */
                $result = AccessControl::userCanManageOrganisation($provider->getProviderUserId(), $organisation, null);

                if($result) {
                    return $result;
                }
            }
        }

        if (!$result && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $result;
    }
}