<?php
namespace Apprecie\Library\Cache;

/* Wincache adapter  https://github.com/nazwa/PhalconWincache */
use Phalcon\Cache\Backend;
use Phalcon\Cache\BackendInterface;
use Phalcon\Cache\Exception;

/**
 * Phalcon\Cache\Backend\Wincache
 *
 * This backend uses wincache as cache backend
 */
class Wincache extends Backend implements BackendInterface
{

    /**
     * Phalcon\Cache\Backend\Wincache constructor
     *
     * @param \Phalcon\Cache\FrontendInterface $frontend
     * @param array $options
     * @throws \Phalcon\Cache\Exception
     */
    public function __construct($frontend, $options = null)
    {
        parent::__construct($frontend, $options);
    }

    /**
     * Get cached content from the Wincache backend
     *
     * @param string $keyName
     * @param null $lifetime
     * @param int $lifetime
     * @return mixed|null
     */
    public function get($keyName, $lifetime = null)
    {
        $value = wincache_ucache_get($keyName, $success);
        if ($success === false) {
            return null;
        }

        $frontend = $this->getFrontend();

        $this->setLastKey($keyName);

        return $frontend->afterRetrieve($value);
    }

    /**
     * Stores cached content into the Wincache backend and stops the frontend
     *
     * @param string $keyName
     * @param string $content
     * @param int $lifetime
     * @param boolean $stopBuffer
     * @throws \Phalcon\Cache\Exception
     */
    public function save($keyName = null, $content = null, $lifetime = null, $stopBuffer = true)
    {
        if ($keyName === null) {
            $lastKey = $this->_lastKey;
        } else {
            $lastKey = $keyName;
        }

        if (!$lastKey) {
            throw new Exception('The cache must be started first');
        }

        $frontend = $this->getFrontend();

        if ($content === null) {
            $content = $frontend->getContent();
        }

//Get the lifetime from the frontend
        if ($lifetime === null) {
            $lifetime = $frontend->getLifetime();
        }

        $lastKey = substr($lastKey, 0, 149);

        //GH truncate keys larger than 149 chars
        wincache_lock($lastKey); //GH  lock before setting
        wincache_ucache_set($lastKey, $frontend->beforeStore($content), $lifetime);
        wincache_unlock($lastKey);

        $isBuffering = $frontend->isBuffering();

//Stop the buffer, this only applies for Phalcon\Cache\Frontend\Output
        if ($stopBuffer) {
            $frontend->stop();
        }

//Print the buffer, this only applies for Phalcon\Cache\Frontend\Output
        if ($isBuffering) {
            echo $content;
        }

        $this->_started = false;
    }

    /**
     * Deletes a value from the cache by its key
     *
     * @param string $keyName
     * @return boolean
     */
    public function delete($keyName)
    {
        return wincache_ucache_delete($keyName);
    }

    /**
     * Query the existing cached keys
     *
     * @param string $prefix
     * @return array
     */
    public function queryKeys($prefix = null)
    {
        $info = wincache_ucache_info();
        $entries = array();
        foreach ($info['ucache_entries'] as $entry) {
            if ($prefix === null) {
                $entries[] = $entry['key_name'];
            } else {
                if (substr($entry['key_name'], 0, strlen($prefix)) === $prefix) {
                    $entries[] = $entry['key_name'];
                }
            }
        }
        return $entries;
    }

    /**
     * Checks if a value exists in the cache by checking its key.
     *
     * @param string $keyName
     * @param string $lifetime
     * @return boolean
     */
    public function exists($keyName = null, $lifetime = null)
    {
        return wincache_ucache_exists($keyName);
    }

    /**
     * Immediately invalidates all existing items.
     *
     * @return boolean
     */
    public function flush()
    {
        wincache_ucache_clear();
    }
}