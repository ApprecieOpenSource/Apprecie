<?php

namespace Apprecie\Library\Translation;

use Phalcon\DI;
use Phalcon\Translate\Adapter;
use Phalcon\Translate\AdapterInterface;
use Phalcon\Translate\Exception;

/**
 * https://github.com/phalcon/incubator/blob/master/Library/Phalcon/Translate/Adapter/Database.php
 *
 * Class TranslationDBAdapter
 * @package Apprecie\Library\Translation
 */
class TranslationDBAdapter extends Adapter implements AdapterInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Class constructor.
     *
     * @param  array $options
     * @throws \Phalcon\Translate\Exception
     */
    public function __construct($options)
    {
        if (!isset($options['db'])) {
            throw new Exception("Parameter 'db' is required");
        }
        if (!isset($options['table'])) {
            throw new Exception("Parameter 'table' is required");
        }
        if (!isset($options['language'])) {
            throw new Exception("Parameter 'language' is required");
        }
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $index
     * @param  array $placeholders
     * @return string
     */
    public function query($index, $placeholders = null)
    {
        $cache = DI::getDefault()->get('cache');
        $options = $this->options;

        $cacheKey = $options['language'] . '_' . $index;
        $content = $cache->get($cacheKey);

        if ($content == null) {
            $translation = $options['db']->fetchOne(
                sprintf(
                    "SELECT translatedText FROM %s WHERE languageId = '%s' AND englishText = ?",
                    $options['table'],
                    _rl($options['language'])
                ),
                null,
                array($index)
            );

            if ($translation) {
                $content = $translation['translatedText'];;
            }
        }

        if ($content == null) {
            $content = $index;
        }

        $cache->save($cacheKey, $content, 86000);

        if (is_array($placeholders)) {
            foreach ($placeholders as $cacheKey => $value) {
                $content = str_replace('{' . $cacheKey . '}', $value, $content);
            }
        }

        return $content;
    }


    /**
     * {@inheritdoc}
     *
     * @param  string $index
     * @return boolean
     */
    public function exists($index)
    {
        $options = $this->options;
        $exists = $options['db']->fetchOne(
            "SELECT COUNT(*) FROM " . $options['table'] . " WHERE englishText = ?",
            null,
            array($index)
        );
        return $exists[0] > 0;
    }
} 