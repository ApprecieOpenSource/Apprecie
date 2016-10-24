<?php
/**
 * Created by PhpStorm.
 * User: hu86
 * Date: 18/01/2016
 * Time: 13:56
 */

use Apprecie\Library\Widgets\WidgetBase;

class EmailWidget extends WidgetBase
{
    public function doIndex()
    {
        $this->view->setLayout('blank');

        $params = $this->getParams();
        $this->view->previewData = $params['previewData'];
        $this->view->callback = $params['callback'];
        $this->view->templateType = $params['templateType'];

        return $this->view->getRender('widgets/email', 'index');
    }
}