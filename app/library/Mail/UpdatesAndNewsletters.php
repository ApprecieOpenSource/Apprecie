<?php
/**
 * Created by PhpStorm.
 * User: hu86
 * Date: 22/10/2015
 * Time: 10:25
 */

namespace Apprecie\Library\Mail;

use Apprecie\Library\Items\EventStatus;
use Apprecie\Library\Request\Url;
use Apprecie\Library\Security\Authentication;
use Apprecie\Library\Users\UserEx;
use Apprecie\Library\Users\UserRole;
use Phalcon\DI;

class UpdatesAndNewsletters
{
    public static function sendVaultUpdates()
    {
        $config = DI::getDefault()->get('config')->updatesAndNewsletters;

        $userContactPreferences = \UserContactPreferences::findBySql('updatesAndNewsletters=1 and (lastRun is null or date_add(lastRun, interval intervalInDays day) <= current_timestamp) limit ' . $config->numberOfUsersToProcess);

        if ($userContactPreferences->count() < 1) {
            return;
        }

        foreach ($userContactPreferences as $userContactPreferencesRecord) {

            $firstRun = false;
            if ($userContactPreferencesRecord->getLastRun() === null) {
                $firstRun = true;
            }

            $userContactPreferencesRecord->setLastRun(date('Y-m-d G:i:s'));
            $userContactPreferencesRecord->update();

            $user = \User::resolve($userContactPreferencesRecord->getUserId());
            $activeRoleName = $user->getActiveRole()->getName();
            $applicableRoleNames = array(
                UserRole::INTERNAL,
                UserRole::MANAGER,
                UserRole::CLIENT
            );
            if (!in_array($activeRoleName, $applicableRoleNames)) {
                continue;
            }

            $vaultItems = $user
                ->getVisibleVaultItems(true)
                ->addAndNotEqualFilter('creatorId', $user->getUserId()); //no need to notify the user if the item was created by the user themselves

            $processedItems = \ItemNotification::query()
                ->where('userId=:1:')
                ->bind([1 => $user->getUserId()])
                ->execute();

            $processedItemIds = array();
            if ($processedItems->count() > 0) {
                foreach ($processedItems as $item) {
                    $processedItemIds[] = $item->getItemId();
                }
            }

            $newItems = $vaultItems
                ->addAndNotInFilter('itemId', $processedItemIds, 'Item')
                ->addAndEqualFilter('status', EventStatus::PUBLISHED, 'Event')
                ->execute('dateCreated desc');

            if ($newItems->count() < 1) {
                continue;
            }

            $sendEmail = true;

            if ($firstRun === true) { //do not send out email updates for the first run
                $sendEmail = false;
            }

            $lastPortal = (new UserEx())->getActiveQueryPortal();
            UserEx::ForceActivePortalForUserQueries($user->getPortalId());
            $userProfile = $user->getUserProfile();

            $auth = new Authentication();
            if (!$auth->userIsInteractive($user)) { //do not send out email updates to users who do not have access to the portal
                $sendEmail = false;
            } else {
                if (!$userProfile->getEmail()) {
                    $sendEmail = false;
                }
            }

            if ($sendEmail === true) {
                $emailUtils = new EmailUtility();
                $emailUtils->sendVaultUpdate(
                    $userProfile->getEmail(),
                    Url::getConfiguredPortalAddress(
                        $user->getPortal()
                    ),
                    $newItems,
                    $user->getOrganisation()
                );
            }

            UserEx::ForceActivePortalForUserQueries($lastPortal);

            foreach ($newItems as $item) {
                $itemNotification = new \ItemNotification();
                $itemNotification->setItemId($item->getItemId());
                $itemNotification->setUserId($user->getUserId());
                if ($sendEmail === true) {
                    $itemNotification->setIsSent(1);
                }
                $itemNotification->create();
            }
        }
    }
}