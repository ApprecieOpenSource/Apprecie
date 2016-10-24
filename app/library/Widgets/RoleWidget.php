<?php
namespace Apprecie\Library\Widgets;

use Apprecie\Library\Security\Authentication;

abstract class RoleWidget extends WidgetBase
{
    protected $_loadedRole = '';

    public function __construct($action = 'role', $params = null)
    {
        $this->_loadedRole = (new Authentication())->getSessionActiveRole();

        if ($action == 'role') { //route to role
            if ($this->canDispatch($this->_loadedRole)) {
                $action = 'role';
            } else {
                $action = 'NoAccess';
            }
        }

        parent::__construct($action, $params);
    }

    public abstract function doNoAccess();
}