<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 28/10/2015
 * Time: 10:31
 */

namespace Apprecie\Library\SearchFilters\Users;

use Apprecie\Library\Security\Authentication;
use Apprecie\Library\Users\UserEx;
use Apprecie\Library\Users\UserRole;

class UserSearchFilterUtility
{
    /**
     * @param null $portalIds
     * @param null $emails
     * @param null $references
     * @param null $organisationIds
     * @param null $roleIds
     * @param $sortBy
     * @return mixed
     */
    public static function userSearch($portalIds = null, $emails = null, $references = null, $organisationIds = null, $roleIds = null, $sortBy = null, $sourceUser = null, $ignoreAcl = false, $returnFilter = false)
    {
        $lastPortal = null;

        if(! $ignoreAcl) {
            if ($sourceUser == null) {
                $sourceUser = (new Authentication())->getAuthenticatedUser(true);
            }

            $lastPortal = (new UserEx())->getActiveQueryPortal();
            UserEx::ForceActivePortalForUserQueries($sourceUser->getPortalId());

            if (! $sourceUser->hasRole(UserRole::SYS_ADMIN)) {
                //we force filters to restrict visibility
                //only allow on portal and on organisation searches
                $portalIds = $sourceUser->getPortalId();
                $organisationIds = $sourceUser->getOrganisationId();

                //build a list of valid visible users
                $allowedUsers = $sourceUser->resolveChildren();
                $aclList = [];

                foreach($allowedUsers as $allow) {
                    $aclList[] = $allow->getUserId();
                }
            }
        }

        $filters = new \Apprecie\Library\Search\SearchFilter('User');
        $users = $filters->addJoin('PortalUser', 'User.portalUserId = PortalUser.portalUserId', 'PortalUser')
            ->addJoin('UserProfile', 'PortalUser.profileId = UserProfile.profileId', 'UserProfile')
            ->addJoin('UserRole');

        if($portalIds != null) {
            $users->addInFilter('portalId', $portalIds);
        }

        if($emails != null) {
            $users->addInFilter('email', $emails);
        }

        if($references != null) {
            $users->addInFilter('reference', $references);
        }

        if($organisationIds != null) {
            $users->addInFilter('organisationId', $organisationIds);
        }

        if($roleIds != null) {
            $users->addInFilter('roleId', $roleIds);
        }

        if(isset($aclList)) {
            $users->addInFilter('userId', $aclList, 'User');
        }

        if($lastPortal != null) {
            UserEx::ForceActivePortalForUserQueries($lastPortal);
        }

        if($returnFilter) {
            return $users;
        }

        return $users->execute($sortBy);
    }
}