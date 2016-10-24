<?php

class MembersWidget extends \Apprecie\Library\Widgets\WidgetBase
{
    public function doIndex()
    {
        $this->doCacheForSeconds(600);

        $this->view->setLayout('blank');

        $users = User::find(array('limit'=>'10','order'=>'userId DESC'));

        $latestUsers = array();
        foreach($users as $userRecord)
        {
            $portalId = $userRecord->portalId;
            $selectedPortal = Portal::findFirst("portalId ={$portalId}");
            \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($selectedPortal->getPortalName());
            $userRecord->getUserProfile(); //hydrate this
            $userRecord->portalName = $selectedPortal->getPortalName();
            \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();
            $latestUsers[] = $userRecord;
        }
        $this->view->latestUsers = $latestUsers;

        return $this->view->getRender('widgets/members', 'index');
    }

    public function doRegistrations()
    {
        $this->doCacheForSeconds(600);

        $this->view->setLayout('blank');
        $lastWeek=date('Y-m-d', strtotime('-1 week'));
        $data=array();
        for($i=0; $i<8; $i++){
            $dateToSearch=date('Y-m-d',strtotime($lastWeek.' +'.$i.' day'));
            $users = User::query()
                ->where("creationDate >='".$dateToSearch." 00:00:00'")
                ->andWhere("creationDate <='".$dateToSearch." 23:59:59'")
                ->execute();
            $data[]=array('date'=>date('d/m/Y',strtotime($dateToSearch)),'count'=>count($users));
        }
        $this->view->data=$data;

        return $this->view->getRender('widgets/members', 'registrations');
    }
}