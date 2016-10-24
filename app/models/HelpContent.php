<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 03/02/15
 * Time: 09:29
 */
class HelpContent extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $helpId, $description, $content;

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

    /**
     * @param mixed $helpId
     */
    public function setHelpId($helpId)
    {
        $this->helpId = $helpId;
    }

    /**
     * @return mixed
     */
    public function getHelpId()
    {
        return $this->helpId;
    }

    public function onConstuct()
    {
        $this->setIndirectContentFields(['content']);
    }

    public function getSource()
    {
        return 'helpcontent';
    }
} 