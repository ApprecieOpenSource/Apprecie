<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 20/10/14
 * Time: 18:51
 */

/**
 * Represents a link between a user and a role (link table)
 * Class UserRole
 */
class UserRole extends \Apprecie\Library\Model\ApprecieModelBase
{
    protected $roleId, $userId, $disabled;

    /**
     * @param mixed $disabled
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;
    }

    /**
     * @return mixed
     */
    public function getDisabled()
    {
        return $this->disabled;
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

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    public function getSource()
    {
        return "userroles";
    }

    public function initialize()
    {
        $this->hasMany('userId', 'User', 'userId');
        $this->hasMany('roleId', 'Role', 'roleId');
        $this->belongsTo('userId', 'User', 'userId');
        $this->belongsTo('roleId', 'Role', 'roleId');
    }

    /**
     * @return Role
     */
    public function getRole($options = null)
    {
        return $this->getRelated('Role', $options);
    }

    public function getUser($options = null)
    {
        return $this->getRelated('User', $options);
    }

    public static function resolve($param, $throw = true, \Apprecie\Library\Model\ApprecieModelBase $instance = null)
    {
        $role = null;

        if (is_string($param) && !is_numeric($param)) {
            $role = Role::findFirstBy('Role', $param, $instance);
            if ($role == null) {
                throw new \Phalcon\Exception('It was not possible to resolve the string ' . $param . 'to a role');
            }
        } else {
            $role = Parent::resolve($param, $throw, $instance);
        }

        return $role;
    }
} 