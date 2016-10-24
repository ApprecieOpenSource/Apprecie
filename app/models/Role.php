<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 20/10/14
 * Time: 18:46
 */

/**
 * A role is used to grant or deny permissions and access throughout the system.
 * There a limited number of named roles within the roles table.
 * Although the model supports multiple roles per user, currently this is logically limited to a single role
 * per user.
 *
 * Class Role
 */
class Role extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $roleId, $name, $description, $defaultController, $defaultAction;

    /**
     * @return mixed
     */
    public function getDefaultAction()
    {
        return $this->defaultAction;
    }

    /**
     * @param mixed $defaultAction
     */
    public function setDefaultAction($defaultAction)
    {
        $this->defaultAction = $defaultAction;
    }

    /**
     * @return mixed
     */
    public function getDefaultController()
    {
        return $this->defaultController;
    }

    /**
     * @param mixed $defaultController
     */
    public function setDefaultController($defaultController)
    {
        $this->defaultController = $defaultController;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $roleId
     */
    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;
    }

    /**
     * @return mixed
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    public function getSource()
    {
        return 'roles';
    }

    public function initialize()
    {
        $this->hasMany('roleId', 'UserRole', 'roleId', ['reusable' => true]);
    }

    public function onConstruct()
    {
        static::setCachingMode(\Apprecie\library\Cache\CachingMode::Persistent);
    }

    public static function resolve($param, $throw = true, \Apprecie\Library\Model\ApprecieModelBase $instance = null)
    {
        $role = null;

        if($param instanceof Role) {
            return $role;
        }

        if($param instanceof UserRole) {
            $role = Role::resolve($param->getRoleId());
        }elseif (is_string($param)) {
            $role = Role::findFirstBy('name', $param, $instance);
        }

        if ($role == null) {
            $role = parent::resolve($param, $throw, $instance);
        }

        return $role;
    }

    public function __toString()
    {
        return $this->getName();
    }
} 