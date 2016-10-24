<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 29/10/2015
 * Time: 17:50
 */

namespace Apprecie\Library\Metadata;


use Apprecie\Library\Cache\Wincache as CacheBackend;
use Phalcon\Cache\Frontend\Data as CacheFrontend;
use Phalcon\Mvc\Model\MetaData;

/**
 * \Phalcon\Mvc\Model\MetaData\Redis
 * Redis adapter for \Phalcon\Mvc\Model\MetaData
 */
class Wincache extends Base
{
    /**
     * Memcache backend instance.
     *
     * @var \Phalcon\Cache\Backend\Wincache
     */
    protected $wincache = null;
    /**
     * {@inheritdoc}
     *
     * @return \Phalcon\Cache\Backend\Wincache
     */
    protected function getCacheBackend()
    {
        if (null === $this->wincache) {
            $this->wincache = new CacheBackend(
                new CacheFrontend(array('lifetime' => $this->options['lifetime'])),
                array()
            );
        }
        return $this->wincache;
    }
}