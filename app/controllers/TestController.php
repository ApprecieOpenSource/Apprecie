<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 24/11/14
 * Time: 11:00
 */
class TestController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function setupController()
    {
        $this->setNoSessionRedirect(''); //because internally getAuthenticatedUser will block cross portal
    }

    public function indexAction()
    {
        $owningUser = $this->getAuthentication()->getSessionOwner();

        if($owningUser == null) {
            _d('You must have a valid admin context to run tests.  Please log in');
        }

        if($owningUser->hasRole(\Apprecie\Library\Users\UserRole::SYS_ADMIN)) {
            if (APPLICATION_ENV == 'dev' || APPLICATION_ENV == 'test') {
                $testStrap = new \Apprecie\Library\Testing\TestStrap(realpath($this->config->application->tests) . '\*');
                $testReporter = new HtmlReporter('UTF-8');
                $group = null;
                if ($this->request->getQuery('group') != null) {
                    $group = 'tests:' . $this->request->getQuery('group');
                }

                foreach ($testStrap as $testGroup) {
                    if ($testGroup->getLabel() == $group || $group == null) {
                        $testGroup->run($testReporter);
                    }
                }
            } else {
                _ep('If you must run tests in this environment pls alter guard in TestController');
            }
        }
    }
} 