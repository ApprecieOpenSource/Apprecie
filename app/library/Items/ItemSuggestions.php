<?php
namespace Apprecie\Library\Items;

use Apprecie\Library\Messaging\PrivateMessageQueue;
use Apprecie\Library\Model\FindOptionsHelper;
use Organisation;
use Phalcon\DI;
use User;

class ItemSuggestions extends PrivateMessageQueue
{
    public static function getSuggestedUsers($itemId)
    {
        $activeUser = Di::getDefault()->get('auth')->getAuthenticatedUser();
        $return = [];
        // get the item we want to match users with
        $event = \Event::findByItem($itemId);

        // get all the items interests and find the parent interests ids
        $itemCategories = $event->getCategories();

        $itemGender = $event->getGender();
        $itemParentCategories = [];
        foreach ($itemCategories as $category) {
            $parents = $category->getParents();
            foreach ($parents as $parentCategory) {
                if (!in_array($parentCategory->getInterestId(), $itemParentCategories)) {
                    $itemParentCategories[] = $parentCategory->getInterestId();
                }
            }
        }

        // get all the users that are already on the guest list
        //Gav - lets allow this to cache

        $options = FindOptionsHelper::prepareFindOptions(null, null, null,'itemId=?1 AND owningUserId=?2', [1=>$itemId, 2=>$activeUser->getUserId()]);
        $guests = \GuestList::find($options);

        /*$guestList = \GuestList::query();
        $guestList->andWhere('owningUserId=:1:');
        $guestList->andWhere('itemId=:2:');
        $guestList->bind([1 => Di::getDefault()->get('auth')->getAuthenticatedUser()->getUserId(), 2 => $itemId]);
        $guests = $guestList->execute();*/

        $guestArray = [];
        foreach ($guests as $guest) {
            if (!in_array($guest->getUserId(), $guestArray)) {
                array_push($guestArray, $guest->getUserId());
            }
        }

        // get all the children for the current user
        $users = Di::getDefault()->get('auth')->getAuthenticatedUser()->resolveChildren();

        $userDetails = [];

        // for each user we need to find out if they have any category matches and then look for age and gender matches
        foreach ($users as $user) {
            if (!$user->getIsDeleted() and !in_array($user->getUserId(), $guestArray)) {
                // if the user is the correct tier for this item
                $userProfile = $user->getUserProfile();
                if ($user->getTier() <= $event->getTier()) {
                    $userDetails[$user->getUserId()] = [];
                    $userParentCategories = [];

                    // Get all the users interests
                    $interestResults = $user->getInterests(); //gav cachable
                    /*$userInterestFilter = new \Apprecie\Library\Search\SearchFilter('UserInterest');
                    $userInterestFilter->addInFilter('userId', $user->getUserId());
                    $userInterestResults = $userInterestFilter->execute();*/

                    // Get additional user data to match
                    $userGender = $userProfile->getGender();
                    $userAge = $userProfile->getAge();
                    $userDetails[$user->getUserId()]['interestMatch'] = 0;

                    // Get user interests parent id's to match
                    foreach ($interestResults as $interest) {
                        $parentInterests = $interest->getParents();
                        foreach ($parentInterests as $parent) {
                            if (!in_array($parent->getInterestId(), $userParentCategories)) {
                                $userParentCategories[] = $parent->getInterestId();
                            }
                        }
                    }
                    // if the users parent interests match the items parent interests increase match by 1
                    foreach ($userParentCategories as $categoryToMatch) {
                        if (in_array($categoryToMatch, $itemParentCategories)) {
                            $userDetails[$user->getUserId()]['interestMatch']++;
                        }
                    }

                    // if the user has interest matches we can see if they have other demographic matches
                    if ($userDetails[$user->getUserId()]['interestMatch'] != 0) {
                        // populate basic details
                        $userDetails[$user->getUserId()]['userId'] = $user->getUserId();
                        $userDetails[$user->getUserId()]['firstName'] = $userProfile->getFirstName();
                        $userDetails[$user->getUserId()]['lastName'] = $userProfile->getLastName();
                        $userDetails[$user->getUserId()]['email'] = $userProfile->getEmail();
                        $userDetails[$user->getUserId()]['reference'] = $user->getPortalUser()->getReference();
                        $userDetails[$user->getUserId()]['organisation'] = $user->getOrganisation()->getOrganisationName();

                        // do age match
                        $userDetails[$user->getUserId()]['ageMatch'] = 'No';
                        $userDetails[$user->getUserId()]['genderMatch'] = 'No';
                        if ($userAge != 0) {
                            if ($event->getTargetAge18to34() == 1) {
                                if ($userAge >= 18 && $userAge < 34) {
                                    $userDetails[$user->getUserId()]['ageMatch'] = 'Yes';
                                }
                            }
                            if ($event->getTargetAge18to34() == 1) {
                                if ($userAge >= 34 && $userAge < 65) {
                                    $userDetails[$user->getUserId()]['ageMatch'] = 'Yes';
                                }
                            }
                            if ($event->getTargetAge65Plus() == 1) {
                                if ($userAge >= 65) {
                                    $userDetails[$user->getUserId()]['ageMatch'] = 'Yes';
                                }
                            }
                        }

                        // do gender match
                        if ($itemGender != 'mixed' && $itemGender == $userGender) {
                            $userDetails[$user->getUserId()]['genderMatch'] = 'Yes';
                        }

                    } else {
                        unset($userDetails[$user->getUserId()]);
                    }
                }
            }
        }

        $interestMatch = array();
        foreach ($userDetails as $key => $row) {
            $interestMatch[$key] = $row['interestMatch'];
        }
        array_multisort($interestMatch, SORT_DESC, $userDetails);

        $return['totalItems'] = count($userDetails);
        $return['items'] = $userDetails;

        return $return;
    }


    public static function getSuggestedItems($userId)
    {
        $userToSearch = \User::findFirstBy('userId', $userId);
        $user = Di::getDefault()->get('auth')->getAuthenticatedUser();

        $guestList = \GuestList::query();
        $guestList->andWhere('userId=:1:');
        $guestList->bind([1 => $userId]);
        $guestLists = $guestList->execute();

        $guestListArray = [];
        foreach ($guestLists as $item) {
            if (!in_array($item->getItemId(), $guestListArray)) {
                array_push($guestListArray, $item->getItemId());
            }
        }

        $interests = [];

        foreach ($userToSearch->getUserInterests() as $interest) {
            foreach ($interest->getInterest()->getParents() as $parentInterest) {
                $interests[] = $parentInterest->getInterestId();
            }
        }

        $filter = new \Apprecie\Library\Search\SearchFilter('Item');
        $filter->addJoin('ItemVault', 'Item.itemId = ItemVault.itemId')
            ->addJoin('Event', 'Item.itemId = Event.itemId');
        $filter->addJoin('ItemInterest', 'Event.itemId = ItemInterest.itemId', null)
            ->addJoin('InterestLink', 'ItemInterest.interestId = InterestLink.interestId', null);

        $filter->addJoin('User', 'Item.creatorId=User.userId')
            ->addJoin('Organisation', 'User.organisationId=Organisation.organisationId');

        switch ($user->getActiveRole()->getName()) {
            case "Manager":
                $filter->addAndIsNullFilter('ownerId');
                $filter->addOrEqualsFilter('ownerId', $user->getUserId());
                break;
            case "Internal":
                $filter->addInFilter('ownerId', [$user->getFirstParent()->getUserId(), $user->getUserId()]);
                $filter->addAndEqualFilter('internalCanSee', true);
                $filter->addOrEqualsFilter('ownerId', $user->getUserId());
                break;
            case "Client" :
                $filter->addAndEqualOrLessThanFilter('tier', $userToSearch->getTier(), 'Item');
                $filter->addAndEqualFilter('ownerId', $user->getFirstParent()->getUserId());
                $filter->addAndEqualFilter('clientsCanSee', true);
                $filter->addOrEqualsFilter('ownerId', $user->getUserId());
                break;
            case "ApprecieSupplier":
            case "AffiliateSupplier":
                $filter->addAndEqualFilter('creatorId', $user->getUserId());
                break;
        }

        switch ($user->getActiveRole()->getName()) {
            case "ApprecieSupplier":
            case "AffiliateSupplier":

                break;
            default:
                $filter->addAndEqualFilter(
                    'organisationId',
                    Organisation::getActiveUsersOrganisation()->getOrganisationId(),
                    'ItemVault'
                );
                break;
        }

        $filter->addAndEqualOrGreaterThanFilter('bookingEndDate', date('Y-m-d'))
            ->addAndNotEqualFilter('isArranged', true, 'Item')
            ->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::ARRANGING, 'Item');


        if (count($interests) != 0) {
            $filter->addInFilter('parentInterestId', $interests, 'InterestLink');
        } else {
            $filter->addInFilter('parentInterestId', -1, 'InterestLink');

        }

        $filter->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::HELD);
        $filter->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::CLOSED);
        $filter->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::ARRANGING);

        $items = $filter->execute('startDateTime');

        return $items;
    }
}