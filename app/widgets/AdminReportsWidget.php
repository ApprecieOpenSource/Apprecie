<?php
//@todo GH move all widgits out of global namespace
use Apprecie\Library\Users\UserRole;
use Apprecie\Library\Widgets\WidgetBase;

class AdminReportsWidget extends WidgetBase
{
    public function doIndex() {}

    public function doOrders()
    {
        $this->doCacheForSeconds(600);
        $this->view->setLayout('blank');

        if(! $this->getAuth()->getSessionActiveRole() == UserRole::SYS_ADMIN) {
            return false;
        }

        $options = \Apprecie\Library\Model\FindOptionsHelper::prepareFindOptions('orderId DESC, orderItemId ASC');
        $this->view->orders = AdminOrdersReport::find($options);
        return $this->view->getRender('widgets/adminreports', 'orders');
    }
}