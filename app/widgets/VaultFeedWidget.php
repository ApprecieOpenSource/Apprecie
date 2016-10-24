<?php


class VaultFeedWidget extends \Apprecie\Library\Widgets\WidgetBase
{
    public function doIndex()
    {
        $this->view->setLayout('blank');
        return $this->view->getRender('widgets/vaultfeed', 'index');
    }

    public function doGenworth()
    {
        $auth=new \Apprecie\Library\Security\Authentication();
        $user=$auth->getAuthenticatedUser();
        $this->view->setLayout('blank');
        $this->view->items=
            \Item::query()
                ->join('ItemVault')
                ->join('Event')
                ->where('ownerId =:0:')
                ->andWhere('organisationId = :1:')
                ->andWhere('bookingStartDate<= :2:')
                ->andWhere('bookingEndDate>= :3:')
                ->andWhere('clientsCanSee=1')
                ->orderBy('startDateTime')
                ->limit(5)
                ->bind([0=>$user->getFirstParent()->getUserId(),1=>Organisation::getActiveUsersOrganisation()->getOrganisationId(),2=>date('Y-m-d'),3=>date('Y-m-d')])
                ->execute();
        return $this->view->getRender('widgets/vaultfeed', 'genworth');
    }
}