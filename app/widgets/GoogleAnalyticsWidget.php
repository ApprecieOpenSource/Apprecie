<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 08/07/15
 * Time: 14:22
 */

class GoogleAnalyticsWidget extends \Apprecie\Library\Widgets\WidgetBase
{
    public function doIndex()
    {
        return $this->view->getRender('widgets/googleanalytics', 'index');
    }
}