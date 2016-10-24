<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 07/12/14
 * Time: 12:49
 */

namespace Apprecie\Library\Testing;

use Apprecie\Library\Provisioning\PortalFactory;
use Apprecie\Library\Provisioning\PortalStrap;
use Apprecie\Library\Users\UserEx;
use Apprecie\Library\Utility\UtilityTrait;
use Phalcon\DI;

abstract class ApprecieTestBase extends \UnitTestCase
{
    use UtilityTrait;
    const TEST_PORTAL = 'test_devstage';
    protected $_testUsers = [];

    public static function provisionTestPortal()
    {
        $factory = new PortalFactory();
        $portal = $factory->provisionPortal(static::TEST_PORTAL, static::TEST_PORTAL);
        $portal->getOwningOrganisation(); //creates the default org on first call
        return $portal;
    }

    public function getTempTestUser($roles = null)
    {
        $count = count($this->_testUsers) + 3;
        $id = microtime();
        $email = 'test_' . $id . '@born2code.co.uk';

        $userEx = new UserEx();
        $user = $userEx->createUserWithProfileAndLogin(
            $email,
            'moopyqR1!v3K8d*',
            'Tester' . $count,
            'Tester' . $count,
            'Mr',
            null,
            'not set',
            null,
            static::TEST_PORTAL
        );

        _epm($userEx);

        if($roles != null) {
            if(! is_array($roles)) {
                $roles = [$roles];
            }

            foreach($roles as $r) {
                $user->addRole($r);
            }
        }

        $this->_testUsers[] = $user;

        return $user;
    }

    /**
     * @return \Portal
     */
    public function getTestPortal()
    {
        return \Portal::findFirstBy('internalAlias', static::TEST_PORTAL);
    }

    public function setUp()
    {
        register_shutdown_function(array($this, 'shutdownHandler'));

        if ($this->getTestPortal() == null) {
            static::provisionTestPortal();
        }

        PortalStrap::setActivePortal(static::TEST_PORTAL);
        UserEx::ForceActivePortalForUserQueries(static::TEST_PORTAL);
    }

    /**
     * Logs the provided user in.
     * @param \User $user the user to behave as if logged in
     */
    public function impersonateUser($user)
    {
        $this->getAuthentication()->impersonateUser($user, true);
    }

    public function getDI()
    {
        return DI::getDefault();
    }

    public function shutdownHandler()
    {
        _ep(error_get_last());
        $this->tearDown();
    }

    public function tearDown()
    {
        UserEx::ForceActivePortalForUserQueries(static::TEST_PORTAL);
        $this->getAuthentication()->endImpersonation(true); //return to owning user

        foreach($this->_testUsers as $user) {
            (new UserEx())->deleteUser($user, null, false);
        }
        parent::tearDown();
    }
}