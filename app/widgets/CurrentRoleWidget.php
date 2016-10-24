<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 01/10/2015
 * Time: 13:33
 */

use Apprecie\Library\Widgets\WidgetBase;

class CurrentRoleWidget extends WidgetBase
{
    public function doIndex()
    {
        $this->view->setLayout('blank');
        return $this->view->getRender('widgets/currentrole', 'index');
    }
}