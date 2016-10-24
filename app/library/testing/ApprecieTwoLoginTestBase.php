<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 07/12/14
 * Time: 12:49
 */

namespace Apprecie\Library\Testing;

use Apprecie\Library\Users\UserEx;

class ApprecieTwoLoginTestBase extends ApprecieTestBase
{
    private $_currentTestUserID = null;
    private $_secondTestUserID = null;
    protected $_firstUserLogin = null, $_secondUserLogin = null;

    public function getTestUserEmail()
    {
        if ($this->_currentTestUserID == null) {
            $this->_currentTestUserID = time();
        }
        return 'test_' . $this->_currentTestUserID . '@born2code.co.uk';
    }

    public function getSecondTestUserEmail()
    {
        if ($this->_secondTestUserID == null) {
            $this->_secondTestUserID = time() . 'b';
        }

        return 'test2_' . $this->_secondTestUserID . '@born2code.co.uk';
    }

    /**
     * @return \UserLogin
     */
    public function getTestUserLogin()
    {
        if ($this->_firstUserLogin == null) {
            $userEx = new UserEx();
            $this->_firstUserLogin = $userEx->createUserWithProfileAndLogin(
                $this->getTestUserEmail(),
                'moopyqR1!v3K8d*',
                'Tester',
                'Tester',
                'Mr',
                null,
                'not set',
                null,
                static::TEST_PORTAL
            );
            _epm($userEx);
        }

        return \UserLogin::findFirst("username='{$this->getTestUserEmail()}'");
    }

    /**
     * @return \UserLogin
     */
    public function getSecondTestUserLogin()
    {
        if ($this->_secondUserLogin == null) {
            $userEx = new UserEx();
            $this->_secondUserLogin = $userEx->createUserWithProfileAndLogin(
                $this->getSecondTestUserEmail(),
                'moopyqR1!v3K8d*',
                'Tester2',
                'Tester',
                'Mr',
                null,
                'not set',
                null,
                static::TEST_PORTAL
            );
        }

        return \UserLogin::findFirst("username='{$this->getSecondTestUserEmail()}'"); //intentional to return from db
    }

    public function tearDown()
    {
        UserEx::ForceActivePortalForUserQueries(static::TEST_PORTAL);

        if ($this->_firstUserLogin != null) {
            (new UserEx())->deleteUser($this->_firstUserLogin, null, false);
            $this->_firstUserLogin = null;
        }

        if ($this->_secondUserLogin != null) {
            (new UserEx())->deleteUser($this->_secondUserLogin, null, false);
            $this->_secondUserLogin = null;
        }

        UserEx::ForceActivePortalForUserQueries();
    }
} 