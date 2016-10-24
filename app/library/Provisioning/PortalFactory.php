<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 16/10/14
 * Time: 18:02
 */

namespace Apprecie\Library\Provisioning;

use Apprecie\Library\Messaging\PrivateMessageQueue;
use Apprecie\Library\Portals\PortalEditions;
use Apprecie\Library\Tracing\ActivityTraceTrait;
use Phalcon\DI;
use Phalcon\Exception;
use Phalcon\Mvc\Model\TransactionInterface;

class PortalFactory extends PrivateMessageQueue
{
    use ActivityTraceTrait;

    /**
     * @param $name
     * @param $subDomain
     * @param string $edition
     * @param null $transaction
     * @return bool|\Portal
     */
    public function provisionPortal($name, $subDomain, $edition = PortalEditions::SYSTEM, $transaction = null)
    {
        //@todo  add validation  - subdomain valid and unique,  name unique
        $newPortal = new \Portal();
        $fullGuid = uniqid(null);
        $maxLen = 40 - strlen($fullGuid);
        $guidName = substr(str_replace(['.', '-'], '_', $subDomain), 0, $maxLen);

        $newPortal->setPortalGUID($guidName . '_' . $fullGuid);
        $newPortal->setPortalName($name);
        $newPortal->setSuspended(false);
        $newPortal->setPortalSubdomain($subDomain);
        $newPortal->setEdition($edition);

        if ($transaction != null) {
            $newPortal->setTransaction($transaction);
        }

        if (!$newPortal->validation()) {
            $this->appendMessageEx($newPortal->getMessages());
            return false;
        }

        if (!$newPortal->create()) {
            $this->appendMessageEx($newPortal->getMessages());
            return false;
        }

        if ($transaction == null) {
            // Create the asset directory for the portal
            $dir = \Assets::createAssetDirectory($newPortal->getPortalGUID());

            if ($dir !== true) {
                $this->logActivity('Portal creation Failed', _ms($this));
                $this->appendMessageEx($dir);
                return false;
            } elseif (!$this->createPortalPrivateTables($newPortal)) {
                $this->logActivity('Portal creation Failed', _ms($this));
                return false;
            }
        }

        return $newPortal;
    }

    public function confirmPortal(\Portal $portal, TransactionInterface $transaction, $throw = true)
    {
        try {
            if ($this->createPortalPrivateTables($portal)) {
                // Create the asset directory for the portal
                $dir = \Assets::createAssetDirectory($portal->getPortalGUID());
                if ($dir !== true) {
                    $this->appendMessageEx('Failed to create assets folder');
                    $transaction->rollback(_g('The portal could not be saved.  A log has been created'));
                }

                $transaction->commit();
            } else {
                $transaction->rollback(_g('The portal could not be saved.  A log has been created'));
            }
        } catch (Exception $ex) {
            $this->appendMessageEx($ex);
            $this->logActivity('Portal creation Failed', _ms($this));

            if ($throw) {
                throw new Exception('Portal creation Failed');
            }

            return false;
        }

        return true;
    }

    protected function createPortalPrivateTables(\Portal $portal)
    {
        try {
            /** @var $connection \Phalcon\Db\Adapter\Pdo\Mysql */
            $connection = DI::getDefault()->get('db');
            $table1Name = "_{$portal->getPortalGUID()}_userProfiles";
            $table2Name = "_{$portal->getPortalGUID()}_portalUsers";
            $table3Name = "_{$portal->getPortalGUID()}_userLogins";

            $sql = "CREATE TABLE {$table1Name} LIKE userprofiles; CREATE TABLE {$table2Name} LIKE portalusers; CREATE TABLE {$table3Name} LIKE userlogins;";
            $connection->execute($sql);
        } catch (Exception $ex) { //@todo  -  do something useful with this error
            $this->appendMessageEx($ex);
            return false;
        }

        return true;
    }
} 