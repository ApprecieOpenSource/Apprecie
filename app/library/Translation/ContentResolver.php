<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 08/01/15
 * Time: 11:43
 */

namespace Apprecie\Library\Translation;

use Apprecie\Library\DBConnection;
use Apprecie\Library\Messaging\PrivateMessageQueue;
use Apprecie\Library\Model\FindOptionsHelper;
use Apprecie\Library\Utility\StringParsing;

class ContentResolver extends PrivateMessageQueue
{
    use StringParsing;
    use DBConnection;

    public function resolve($value, $languageId = null, $clearFailedMacros = false)
    { //takes an indirect content field and resolves the macro to actual content
        if(! $this->isMacro($value)) return $value;

        $languageId = $languageId ? : _l();
        $options = FindOptionsHelper::prepareFindOptions(null, null, null, 'languageId = ?1 AND contentId = ?2', [1=>$languageId, 2=>$value]);

        $resolved = \Content::findFirst($options);
        if ($resolved != null) {
            $value = $resolved->getContent();
        } elseif ($clearFailedMacros) {
            $value = '';
        }

        return $value;
    }

    /**
     * Note will only return an object for the content macro $macro
     * @param $macro
     * @param null $languageId
     * @return \Content|null
     */
    public function resolveObjectFromMacro($macro, $languageId = null)
    {
        $languageId = $languageId ? : _l();

        return \Content::findByIdAndLanguage($macro, $languageId);
    }


    /**
     * Only provide a contentId if you want to create a variation.
     *
     * @param $value
     * @param null $languageId
     * @param null $contentId
     * @param null $portalId
     * @param null $description
     * @return bool|string
     */
    public function createContent($value, $languageId = null, $contentId = null, $portalId = null, $description = null)
    {
        $languageId = $languageId ? : _l();
        $portalId = $portalId ? : _pid();

        $content = new \Content();
        $content->setContent($value);
        $content->setLanguageId($languageId);
        if ($description != null) {
            $content->setDescription($description);
        }
        $content->setSourcePortalId($portalId);

        if ($this->isMacro($contentId)) {
            $contentId = $this->extractIdFromMacro($contentId);

            //confirm we can resolve the passed macro / id to an actual object in any language.
            $contentObj = \Content::findFirstBy('contentId', $contentId);

            if ($contentObj != null) { //force a variation in new language if content exists, else create
                $content->setContentId($contentObj->getContentId());
            } else {
                _ep('warning suggested variation source does not exist ' . $contentId);
                $content->setContentId($contentId);
            }
        }

        if (!$content->create()) {
            $this->appendMessageEx($content->getMessages());
            _epm($content);
        }

        return $content->getContentId();
    }

    public function updateContent($macro, $content, $languageId = null)
    {
        $languageId = $languageId ? : _l();

        $contentObj = $this->resolveObjectFromMacro($macro, $languageId);

        if ($contentObj == null) {
            $this->appendMessageEx('It is not possible to update the content,  it does not exist');
            return false;
        }

        $contentObj->setContent($content);

        if (!$contentObj->update()) {
            $this->appendMessageEx($content->getMessages());
            return false;
        }

        return $content;
    }

    /**
     * Figures out if this should be an insert, update or variation based passed params
     * if contentId is not provided this is a create, if content id is provided an existing record
     * for contentId and LanguageId will be investigated, if not found a variation will be created, else the
     * existing will be updated.
     *
     * @param $value
     * @param null $languageId
     * @param null $contentId
     * @param null $portalId
     * @param null $description
     * @return bool|string
     */
    public function resolveWrite($value, $languageId = null, $contentId = null, $portalId = null, $description = null)
    {
        if ($value == '' && $contentId == null) {
            return '';
        }

        if (!$this->isMacro($contentId)) {
            return $this->createContent($value, $languageId, null, $portalId, $description);
        }

        //variation or update.
        if ($this->resolve($contentId, $languageId) == $contentId) { //variation
            return $this->createContent($value, $languageId, $contentId, $portalId, $description);
        } else { //update
            if ($this->updateContent($contentId, $value, $languageId)) {
                return $contentId;
            }

            return false;
        }
    }

    /**
     * If $languageId = -1 will delete all variations of $macro
     * @param $macro can be an array of multiple macros
     * @param null $languageId
     */
    public function deleteContent($macros, $languageId = null)
    {
        if(! is_array($macros)) {
            $macros = [$macros];
        }

        $languageId = $languageId ? : _l();

        foreach ($macros as $contentId) {
            if ($languageId == -1) {
                $this->getDbAdapter()->query('DELETE FROM content WHERE contentId = ?', [0 => $contentId]);
            } else {
                $this->getDbAdapter()->query(
                    'DELETE FROM content WHERE contentId = ? AND languageId = ?',
                    [0 => $contentId, 1 => $languageId]
                );
            }
        }
    }

    protected function extractIdFromMacro($macro)
    {
        return $this->getStringBetween($macro, '{c:', '}');
    }

    public function isMacro($content)
    {
        /*if($this->startsWith($content, '{c:') && $this->endsWith($content, '}')) {
            return true;
        }*/

        //14an . 8n
        if(strlen($content) == 23 && strpos($content, '.') !== false) {
            $parts = explode('.', $content);
            if(count($parts) == 2) {
                if(strlen($parts[0]) == 14 && strlen($parts[1]) == 8) {
                    if(ctype_alnum($parts[0]) && is_numeric($parts[1])) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
} 