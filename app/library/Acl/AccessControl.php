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
class AccessControl
{
    /**
     * Indicates if the subject grants visibility to $user
     * @param $observingUser
     * @param $user
     * @param string $redirect
     * @return bool
     */
    public static function userCanBeSeenBy($observingUser, $user, $redirect = 'error/fourofour')
    {
        $user = \User::resolve($user);
        $observingUser = \User::resolve($observingUser);

        $allowed = $user->userIsDescendant($observingUser, true);

        if (!$allowed && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $allowed;
    }

    /**
     * checks a single or array of users for visibility by the subject
     * set second param to null to prevent redirect.
     * @param $userOrUsers
     * @param string $redirect
     * @return bool
     */
    public static function userCanManageUser($observingUser, $userOrUsers, $redirect = 'error/fourofour')
    {
        $sourceuser = \User::resolve($observingUser);

        if(! is_array($userOrUsers)) {
            $userOrUsers = [$userOrUsers];
        }

        foreach($userOrUsers as $user) {
            $user = \User::resolve($user);

            if(! $user->canBeSeenBy($sourceuser, $redirect)) {
                return false;
            }
        }

        return true;
    }

    public static function userCanViewItem($user, $item, $redirect = 'error/fourofour')
    {
        $item = \Item::resolve($item);
        $user = \User::resolve($user);

        if($user->getActiveRole()->getName() == 'SystemAdministrator') {
            return true;
        }

        if($item->getCreatorId() == $user->getUserId()) {
            return true;
        }

        if($item->getIsArrangedFor() == $user->getUserId()) {
            return true;
        }

        if(static::userCanManageUserItem($user, $item, null)) {
            return true;
        }

        $filter = new \Apprecie\Library\Search\SearchFilter('Item');
        $filter->addJoin('ItemVault', 'Item.itemId = ItemVault.itemId')
            ->addAndEqualFilter('itemId', $item->getItemId(), 'Item');

        switch ($user->getActiveRole()->getName()) {
            case "Manager":
            {
                $filter->addAndEqualFilter('organisationId', $user->getOrganisationId(), 'ItemVault');
                break;
            }
            case "Internal":
            {
                $filter->addInFilter('ownerId', [$user->getFirstParent()->getUserId(), $user->getUserId()])
                    ->addAndEqualFilter('internalCanSee', true)
                    ->addOrEqualsFilter('ownerId', $user->getUserId())
                    ->addAndEqualFilter('itemId', $item->getItemId(), 'Item');
                break;
            }
            case "Client" :
            {
                $filter->addAndEqualOrLessThanFilter('tier', $user->getTier(), 'Item');
                $filter->addAndEqualFilter('ownerId', $user->getFirstParent()->getUserId());
                $filter->addAndEqualFilter('clientsCanSee', true);
                $filter->addOrEqualsFilter('ownerId', $user->getUserId())
                    ->addAndEqualFilter('itemId', $item->getItemId(), 'Item');
                break;
            }
            default :
            {
                $filter->addAndEqualFilter('creatorId', $user->getUserId());
            }
        }

        $result = $filter->execute();

        $allowed =  count($result) > 0;

        //clients can view original BA items if they have requested an arrangement regardless tier
        if(!$allowed && $item->getIsByArrangement() && $user->getActiveRole()->getName() === UserRole::CLIENT) {
            $arrangedItems = \Item::query()
                ->addWhere('sourceByArrangement=:1:')
                ->andWhere('isArrangedFor=:2:')
                ->andWhere('(state=:3: or state=:4:)')
                ->bind([
                    1 => $item->getItemId(),
                    2 => $user->getUserId(),
                    3 => ItemState::APPROVED,
                    4 => ItemState::ARRANGING
                ])
                ->execute();
            if ($arrangedItems->count() > 0) {
                return true;
            }
        }

        if (!$allowed && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $allowed;
    }

    /**
     * Is the subject the creator of the item
     * @param $item
     * @param string $redirect
     * @return bool
     */
    public static function userCanEditItem($user, $item, $redirect = 'error/fourofour')
    {
        $item = \Item::resolve($item);
        $user = \User::resolve($user);

        $allowed = false;

        if($user->getUserId() == $item->getCreatorId()) {
            $allowed = true;
        } elseif($redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $allowed;
    }

    /**
     * @param $messageThread
     * @param string $redirect
     * @return bool
     * @throws \Phalcon\Exception
     */
    public static function userCanSeeMessageThread($user, $messageThread, $redirect = 'error/fourofour') {
        $thread = \MessageThread::resolve($messageThread);
        $user = \User::resolve($user);

        $allowed = false;

        if($user->getUserId() == $thread->getFirstRecipientUser() || $user->getUserId() == $thread->getStartedByUser()) {
            $allowed = true;
        }

        if (!$allowed && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $allowed;
    }

    /**
     * @param $user
     * @param $organisation
     * @return bool
     */
    public static function userCanViewOrganisationQuotas($user, $organisation, $redirect = 'error/fourofour')
    {
        $user = \User::resolve($user);

        if($user->hasRole(UserRole::SYS_ADMIN)) {
            return true;
        }

        if($user->hasRole([UserRole::INTERNAL, UserRole::MANAGER, UserRole::PORTAL_ADMIN])) {
            $organisation = \Organisation::resolve($organisation);
            if($organisation->getOrganisationId() == $user->getOrganisationId()) {
                return true;
            }
        }

        if ($redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return false;
    }

    public static function userCanCreateUser($user, $organisation, $role, $redirect = 'error/fourofour')
    {
        $user = \User::resolve($user);

        if($user->hasRole(UserRole::SYS_ADMIN)) {
            return true;
        }

        if(! is_array($role) && ! $role instanceof \ArrayAccess) {
            $role = [$role];
        }

        if($user->hasRole([UserRole::INTERNAL, UserRole::MANAGER, UserRole::PORTAL_ADMIN])) {
            $organisation = \Organisation::resolve($organisation);
            if($organisation->getOrganisationId() == $user->getOrganisationId()) {
                $roleEnum = new RoleHierarchy($user->getActiveRole()->getName());
                $rolePass = true;

                foreach($role as $r) {
                    $r = \Role::resolve($r);
                    if(! array_key_exists($r->getName(),  $roleEnum->getVisibleRoles())) {
                        $rolePass = false;
                        break;
                    }
                }

                if($rolePass) return true;
            }
        }

        if ($redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return false;
    }

    public static function userCanDeleteUser($user, $organisation, $role, $redirect = 'error/fourofour')
    {
        $user = \User::resolve($user);

        if($user->hasRole(UserRole::SYS_ADMIN)) {
            return true;
        }

        if(! is_array($role) && ! $role instanceof \ArrayAccess) {
            $role = [$role];
        }

        if($user->hasRole([UserRole::INTERNAL, UserRole::MANAGER, UserRole::PORTAL_ADMIN])) {
            $organisation = \Organisation::resolve($organisation);
            if($organisation->getOrganisationId() == $user->getOrganisationId()) {
                $roleEnum = new RoleHierarchy($user->getActiveRole()->getName());
                $rolePass = true;

                foreach($role as $r) {
                    $r = \Role::resolve($r);
                    if(! array_key_exists($r->getName(),  $roleEnum->getVisibleRoles())) {
                        $rolePass = false;
                        break;
                    }
                }

                if($rolePass) return true;
            }
        }

        if ($redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return false;
    }

    public static function userCanPublishItem($user, $item, $redirect = 'error/fourofour')
    {
        $item = \Item::resolve($item);
        $user = \User::resolve($user);

        $allowed = $item->getCreatorId() == $user->getUserId();

        if (!$allowed && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $allowed;
    }

    public static function userCanViewPortalOrganisations($user, $portal, $redirect = 'error/fourofour')
    {
        $user = \User::resolve($user);
        $portal = \Portal::resolve($portal);

        if($user->hasRole(UserRole::SYS_ADMIN)) {
            return true;
        }

        if($user->hasRole([UserRole::INTERNAL, UserRole::MANAGER, UserRole::PORTAL_ADMIN])) {
            if($portal->getPortalId() == $user->getPortalId()) {
                return true;
            }
        }

        if ($redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return false;
    }

    public static function userCanApproveItem($user, $item, $redirect = 'error/fourofour')
    {
        $user = \User::resolve($user);

        if($user->hasRole(UserRole::SYS_ADMIN)) {
            return true;
        } elseif($user->hasRole(UserRole::MANAGER)) {

            $item = \Item::resolve($item);

            //we are looking for a valid item approval record
            $approvalRecord = \ItemApproval::query()
                ->where('verifyingOrganisationId=:org:')
                ->andWhere('itemId=:item:')
                ->bind(['org' => $user->getOrganisationId(), 'item' => $item->getItemId()])
                ->execute();

            if($approvalRecord->count() > 0) {
                return true;
            }
        }

        if ($redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return false;

    }

    public static function userCanOperateGroup($user, $group, $redirect = 'error/fourofour')
    {
        $user = \User::resolve($user);
        $group = \PortalMemberGroup::resolve($group);

        $allowed = $group->getOwnerId() == $user->getUserId();

        if (!$allowed && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $allowed;
    }

    public static function userCanOperateGuestList($user, $item, $redirect = 'error/fourofour')
    {
        $user = \User::resolve($user);
        $item = \Item::resolve($item);

        $allowed = \UserItems::getTotalOwnedUnits($user->getUserId(), $item->getItemId()) > 0;

        if (!$allowed && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $allowed;
    }

    public static function userCanManageUserItem($user, $item, $redirect = 'error/fourofour')
    {
        $user = \User::resolve($user);
        $item = \Item::resolve($item);

        $allowed = count(\UserItems::getBy($user->getUserId(), $item->getItemId(), [UserItemState::OWNED, UserItemState::RESERVED])) > 0;

        if (!$allowed && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $allowed;
    }

    public static function userCanSeeGuestList($user, $item, $redirect = 'error/fourofour')
    {
        $user = \User::resolve($user);
        $item = \Item::resolve($item);

        $allowed = AccessControl::userCanEditItem($user, $item, $redirect);

        if (!$allowed && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $allowed;
    }

    public static function userCanSeeOrder($user, $order, $redirect = 'error/fourofour')
    {
        $user = \User::resolve($user);
        $order = \order::resolve($order);

        $allowed = $user->getUserId() == $order->getCustomerId();

        if (!$allowed && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $allowed;
    }

    public static function userCanManagePortal($user, $portal, $redirect = 'error/fourofour')
    {
        $user = \User::resolve($user);
        $portal = \Portal::resolve($portal);

        $allowed = ($user->getPortalId() == $portal->getPortalId()) && $user->hasRole(UserRole::PORTAL_ADMIN);

        if (!$allowed && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $allowed;
    }

    public static function userCanManageOrganisation($user, $organisation, $redirect = 'error/fourofour')
    {
        $user = \User::resolve($user);
        $organisation = \Organisation::resolve($organisation);

        $allowed = ($user->getOrganisationId() == $organisation->getOrganisationId()) && $user->hasRole(UserRole::PORTAL_ADMIN);

        if (!$allowed && $redirect != null) {
            $response = DI::getDefault()->get('response');
            $response->redirect($redirect);
            $response->send();
        }

        return $allowed;
    }
}