<?php
namespace Apprecie\Library\Provisioning;

use Apprecie\Library\Request\Url;
use Apprecie\Library\Testing\ApprecieTestBase;
use Apprecie\Library\Users\UserEx;
use Phalcon\CLI\Dispatcher;
use Phalcon\DI\Injectable;
use Phalcon\Http\Response;
use Portal;

class PortalStrap extends Injectable
{
    protected static $_portal = null;

    /**
     * @return Portal|null
     */
    public static function getActivePortal()
    {
        if (static::$_portal == null) {
            $subdomain = static::DecideActivePortalSubdomain();

            $portal = \Portal::findFirstBy('portalSubdomain', $subdomain);

            if(!$portal && $subdomain == ApprecieTestBase::TEST_PORTAL) { //unit test pass through
                $portal = ApprecieTestBase::provisionTestPortal();
            }
            elseif(!$portal && $subdomain == 'admin') { //generate admin portal
                $portal = static::createAdminPortal();
            }

            if (!$portal) {
                $response = new Response();
                $response->redirect('/html/noportal.html', null, 301);
                $response->send();
            }
            static::$_portal = $portal;
        }

        return static::$_portal;
    }

    /**
     * Override the default detected portal
     * @param $portal string|Portal the internalAlias or a Portal object
     * @throws \InvalidArgumentException
     */
    public static function setActivePortal($portal)
    {
        $portal = Portal::resolve($portal);
        static::$_portal = $portal;
    }

    public static function createAdminPortal()
    {
        $portal = null;

        if (Portal::findFirstBy('portalSubdomain', 'admin') == null) {
            $portal = (new PortalFactory())->provisionPortal('admin', 'admin');
            (new UserEx())->createUserWithProfileAndLogin(
                'admin@apprecie.com',
                '@ppr3c13',
                'Admin',
                'Admin',
                'Mr'
            )
                ->addRole('SystemAdministrator');

        }

        return $portal;
    }

    public static function getActivePortalIdentifier()
    {
        $portal = static::getActivePortal();
        return $portal->getPortalGUID();
    }

    protected static function DecideActivePortalSubdomain()
    {
        return Url::getSubdomain();
    }
} 