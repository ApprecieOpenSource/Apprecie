<?php

class VaultSPAController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{

    public function indexAction()
    {
        $this->view->setLayout('application');
    }

    public function AjaxGetAllEventsAction()
    {
        $user=$this->getAuthenticatedUser();
        $filter = new \Apprecie\Library\Search\SearchFilter('Item');
        $filter->addJoin('ItemVault', 'Item.itemId = ItemVault.itemId')
            ->addJoin('Event', 'Item.itemId = Event.itemId');
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
                $filter->addAndEqualOrLessThanFilter('tier', $user->getTier(), 'Item');
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

        $filter->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::HELD);
        $filter->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::CLOSED);
        $filter->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::ARRANGING);

        $order = 'startDateTime ASC';

        $items = $filter->execute($order);

        $returnItems=[];
        $returnItems['items']=[];
        $returnItems['totalItems']=count($items);
        foreach($items as $key=>$item){
            $itemRecord=$item->toArray();

            $address=$item->getEvent()->getAddress();
            $itemRecord['latitude']=null;
            $itemRecord['longitude']=null;
            if($address!=null){
                $itemRecord['latitude']=$address->getLatitude();
                $itemRecord['longitude']=$address->getLongitude();
            }
            $itemRecord['image']=Assets::getItemPrimaryImage($item->getItemId());
            $itemRecord['event']=$item->getEvent()->toArray();
            if (strlen($item->getTitle()) > 49) {
                $itemRecord['itemTitle'] = mb_strtoupper(mb_substr($item->getTitle(), 0, 49, 'UTF-8') . "...", 'UTF-8');
            } else {
                $itemRecord['itemTitle'] = mb_strtoupper($item->getTitle(), 'UTF-8');
            }
            if (strlen($item->getSummary()) > 150) {
                $itemRecord['shortSummary'] = mb_substr($item->getSummary(), 0, 150, 'UTF-8') . "...";
            } else {
                $itemRecord['shortSummary'] = $item->getSummary();
            }

            if ($item->getUnitPrice() == 0) {
                $itemRecord['itemType'] = _g('Complimentary Event');
            } else {
                $itemRecord['itemType'] = _g('Fixed Price Event');
            }
            $returnItems['items'][]=$itemRecord;
        }
        echo json_encode($returnItems);
    }
}

