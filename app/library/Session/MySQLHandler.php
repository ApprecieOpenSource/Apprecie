<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 15/09/2015
 * Time: 13:52
 *
 * based mostly on https://github.com/phalcon/incubator/tree/master/Library/Phalcon/Session/Adapter
 */

namespace Apprecie\Library\Session;

use Phalcon\Db;
use Phalcon\Session\Adapter;
use Phalcon\Session\AdapterInterface;
use Phalcon\Session\Exception;
/**
 * Phalcon\Session\Adapter\Database
 * Database adapter for Phalcon\Session
 */
class MySQLHandler extends Adapter implements AdapterInterface
{
    /**
     * Flag to check if session is destroyed.
     *
     * @var boolean
     */
    protected $isDestroyed = false;
    /**
     * {@inheritdoc}
     *
     * @param  array $options
     *
     * @throws \Phalcon\Session\Exception
     */
    public function __construct($options = null)
    {
        if (!isset($options['db'])) {
            throw new Exception("The parameter 'db' is required");
        }
        if (!isset($options['table'])) {
            throw new Exception("The parameter 'table' is required");
        }
        if (!isset($options['column_session_id'])) {
            $options['column_session_id'] = 'sessionId';
        }
        if (!isset($options['column_data'])) {
            $options['column_data'] = 'data';
        }
        if (!isset($options['column_created_at'])) {
            $options['column_created_at'] = 'createdAt';
        }
        if (!isset($options['column_modified_at'])) {
            $options['column_modified_at'] = 'modifiedAt';
        }
        parent::__construct($options);
        session_set_save_handler(
            array($this, 'open'),
            array($this, 'close'),
            array($this, 'read'),
            array($this, 'write'),
            array($this, 'destroy'),
            array($this, 'gc')
        );
    }
    /**
     * {@inheritdoc}
     * @return boolean
     */
    public function open()
    {
        return true;
    }
    /**
     * {@inheritdoc}
     * @return boolean
     */
    public function close()
    {
        return false;
    }
    /**
     * {@inheritdoc}
     * @param  string $sessionId
     *
     * @return string
     */
    public function read($sessionId)
    {
        $maxlifetime = (int) ini_get('session.gc_maxlifetime');
        $options = $this->getOptions();
        $row = $options['db']->fetchOne(
            sprintf(
                'SELECT %s FROM %s WHERE %s = ? AND COALESCE(%s, %s) + %d >= ?',
                $options['db']->escapeIdentifier($options['column_data']),
                $options['db']->escapeIdentifier($options['table']),
                $options['db']->escapeIdentifier($options['column_session_id']),
                $options['db']->escapeIdentifier($options['column_modified_at']),
                $options['db']->escapeIdentifier($options['column_created_at']),
                $maxlifetime
            ),
            Db::FETCH_NUM,
            array($sessionId, time())
        );
        if (empty($row)) {
            return '';
        }
        return $row[0];
    }
    /**
     * {@inheritdoc}
     * @param  string $sessionId
     * @param  string $data
     *
     * @return boolean
     */
    public function write($sessionId, $data)
    {
        if ($this->isDestroyed || empty($data)) {
            return false;
        }
        $options = $this->getOptions();
        $row = $options['db']->fetchOne(
            sprintf(
                'SELECT COUNT(*) FROM %s WHERE %s = ?',
                $options['db']->escapeIdentifier($options['table']),
                $options['db']->escapeIdentifier($options['column_session_id'])
            ),
            Db::FETCH_NUM,
            array($sessionId)
        );
        if (!empty($row) && intval($row[0]) > 0) {
            return $options['db']->execute(
                sprintf(
                    'UPDATE %s SET %s = ?, %s = ? WHERE %s = ?',
                    $options['db']->escapeIdentifier($options['table']),
                    $options['db']->escapeIdentifier($options['column_data']),
                    $options['db']->escapeIdentifier($options['column_modified_at']),
                    $options['db']->escapeIdentifier($options['column_session_id'])
                ),
                array($data, time(), $sessionId)
            );
        } else {
            return $options['db']->execute(
                sprintf(
                    'INSERT INTO %s (%s, %s, %s, %s) VALUES (?, ?, ?, NULL)',
                    $options['db']->escapeIdentifier($options['table']),
                    $options['db']->escapeIdentifier($options['column_session_id']),
                    $options['db']->escapeIdentifier($options['column_data']),
                    $options['db']->escapeIdentifier($options['column_created_at']),
                    $options['db']->escapeIdentifier($options['column_modified_at'])
                ),
                array($sessionId, $data, time())
            );
        }
    }
    /**
     * {@inheritdoc}
     * @return boolean
     */
    public function destroy($session_id = null)
    {
        if (!$this->isStarted() || $this->isDestroyed) {
            return true;
        }
        if (is_null($session_id)) {
            $session_id = $this->getId();
        }
        $this->isDestroyed = true;
        $options = $this->getOptions();
        $result = $options['db']->execute(
            sprintf(
                'DELETE FROM %s WHERE %s = ?',
                $options['db']->escapeIdentifier($options['table']),
                $options['db']->escapeIdentifier($options['column_session_id'])
            ),
            array($session_id)
        );
        session_regenerate_id();
        return $result;
    }
    /**
     * {@inheritdoc}
     * @param  integer $maxlifetime
     *
     * @return boolean
     */
    public function gc($maxlifetime)
    {
        $options = $this->getOptions();
        return $options['db']->execute(
            sprintf(
                'DELETE FROM %s WHERE COALESCE(%s, %s) + %d < ?',
                $options['db']->escapeIdentifier($options['table']),
                $options['db']->escapeIdentifier($options['column_modified_at']),
                $options['db']->escapeIdentifier($options['column_created_at']),
                $maxlifetime
            ),
            array(time())
        );
    }
}