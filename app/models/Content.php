<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 21/12/14
 * Time: 23:33
 */
class Content extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $localId, $contentId, $languageId, $sourcePortalId, $content, $description, $sourceObject;

    public function getSourceObject()
    {
        return $this->sourceObject;
    }

    public function setSourceObject($source)
    {
        $this->sourceObject = $source;
    }

    /**
     * @return mixed
     */
    public function getLocalId()
    {
        return $this->localId;
    }

    /**
     * @param mixed $localId
     */
    public function setLocalId($localId)
    {
        $this->localId = $localId;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $languageId
     */
    public function setLanguageId($languageId)
    {
        $this->languageId = $languageId;
    }

    /**
     * @return mixed
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }

    /**
     * @param mixed $sourcePortalId
     */
    public function setSourcePortalId($sourcePortalId)
    {
        $this->sourcePortalId = $sourcePortalId;
    }

    /**
     * @return mixed
     */
    public function getSourcePortalId()
    {
        return $this->sourcePortalId;
    }

    /**
     * @param mixed $contentId
     */
    public function setContentId($contentId)
    {
        $this->contentId = $contentId;
    }

    /**
     * @return mixed
     */
    public function getContentId()
    {
        return $this->contentId;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function getSource()
    {
        return 'content';
    }

    public function onConstruct()
    {
        static::setCachingMode(\Apprecie\library\Cache\CachingMode::Persistent);
    }

    public function beforeValidationOnCreate()
    {
        if (!isset($this->contentId)) {
            $this->contentId = uniqid('', true);
        }

        return parent::beforeValidationOnCreate();
    }

    public static function findByIdAndLanguage($contentId, $languageId)
    {
        $result = Content::query()->where("contentId=:1:")
            ->andWhere('languageId=:2:')
            ->bind(array(1 => $contentId, 2 => $languageId))
            ->execute();

        return $result->getFirst();
    }

    public static function resolve($param, $throw = true, \Apprecie\Library\Model\ApprecieModelBase $instance = null)
    {
        $resolved = (new \Apprecie\Library\Translation\ContentResolver())->resolveObjectFromMacro($param);

        if (!$resolved && $throw) {
            throw new \Phalcon\Exception('Could not resolve the content macro ' . $param . ' to an actual content record for active UI language ' . _l(
            ));
        }

        return $resolved;
    }
} 