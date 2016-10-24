<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 28/10/2015
 * Time: 10:36
 */

namespace Apprecie\Library\SearchFilters\Items;


use Apprecie\Library\Search\SearchFilter;

class ItemSearchFilterUtility
{
    public static function vaultSearch($user, $categoryIds, $startDate)
    {

    }

    public static function creatorEventsByStatus($user, $userItemSearchMode = 'both', $endDate = null, $eventStatus = null, $itemState = null, $orderBy = null, $returnFilter = false)
    {
        $user = \User::resolve($user);

        if (!is_array($eventStatus) && $eventStatus != null) {
            $eventStatus = [$eventStatus];
        }

        if (!is_array($itemState) && $itemState != null) {
            $itemState = [$itemState];
        }

        $filter = new SearchFilter('Event');
        $filter->addJoin('Item')
            ->addAndEqualFilter('creatorId', $user->getUserId())
            ->addAndEqualFilter('type', 'event');

        if ($eventStatus != null) {
            $filter->addInFilter('status', $eventStatus);
        }

        if ($itemState != null) {
            $filter->addInFilter('state', $itemState);
        }

        if ($endDate != null) {
            $filter->addAndLessThanFilter('endDateTime', $endDate);
        }

        if ($userItemSearchMode == UserItemSearchMode::BY_ARRANGEMENT) {
            $filter->addAndEqualFilter('isByArrangement', '1');
        } elseif ($userItemSearchMode == UserItemSearchMode::CONFIRMED) {
            $filter->addAndEqualFilter('isByArrangement', '0');
        }

        if($returnFilter) {
            return $filter;
        }

        return \Event::findByFilter($filter, $orderBy);
    }
}