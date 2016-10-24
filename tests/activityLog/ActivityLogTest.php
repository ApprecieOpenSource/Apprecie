<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 09/12/14
 * Time: 11:20
 */

class ActivityLogTwoLoginTest extends \Apprecie\Library\Testing\ApprecieTwoLoginTestBase
{
    public function testActivityLog()
    {
        $log = \Phalcon\DI::getDefault()->get('activitylog');
        $id = $log->logActivity('test activity', 'just some test data');
        $this->assertTrue($id != false);

        $connection = \Phalcon\DI::getDefault()->get('db');
        $sql = "DELETE FROM activitylog WHERE portalId = {$this->getTestPortal()->getPortalId()}";
        $connection->execute($sql);
    }
} 