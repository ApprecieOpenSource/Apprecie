<?php

/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 14/10/14
 * Time: 09:39
 */
class Portalstyle extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $portalId;
    public $navigationPrimary, $navigationSecondary, $navigationPrimaryA, $navigationSecondaryA, $a, $aHover, $progressBar, $buttonPrimary, $buttonPrimaryBorder, $buttonPrimaryColor, $buttonPrimaryHover, $buttonPrimaryHoverBorder, $disabledControl, $font, $fontColor;

    public function getSource()
    {
        return 'portalstyles';
    }

    /**
     * @param mixed $a
     */
    public function setA($a)
    {
        $this->a = $a;
    }

    /**
     * @return mixed
     */
    public function getA()
    {
        return $this->a;
    }

    /**
     * @param mixed $aHover
     */
    public function setAHover($aHover)
    {
        $this->aHover = $aHover;
    }

    /**
     * @return mixed
     */
    public function getAHover()
    {
        return $this->aHover;
    }

    /**
     * @param mixed $buttonPrimary
     */
    public function setButtonPrimary($buttonPrimary)
    {
        $this->buttonPrimary = $buttonPrimary;
    }

    /**
     * @return mixed
     */
    public function getButtonPrimary()
    {
        return $this->buttonPrimary;
    }

    /**
     * @param mixed $buttonPrimaryBorder
     */
    public function setButtonPrimaryBorder($buttonPrimaryBorder)
    {
        $this->buttonPrimaryBorder = $buttonPrimaryBorder;
    }

    /**
     * @return mixed
     */
    public function getButtonPrimaryBorder()
    {
        return $this->buttonPrimaryBorder;
    }

    /**
     * @param mixed $buttonPrimaryColor
     */
    public function setButtonPrimaryColor($buttonPrimaryColor)
    {
        $this->buttonPrimaryColor = $buttonPrimaryColor;
    }

    /**
     * @return mixed
     */
    public function getButtonPrimaryColor()
    {
        return $this->buttonPrimaryColor;
    }

    /**
     * @param mixed $buttonPrimaryHover
     */
    public function setButtonPrimaryHover($buttonPrimaryHover)
    {
        $this->buttonPrimaryHover = $buttonPrimaryHover;
    }

    /**
     * @return mixed
     */
    public function getButtonPrimaryHover()
    {
        return $this->buttonPrimaryHover;
    }

    /**
     * @param mixed $buttonPrimaryHoverBorder
     */
    public function setButtonPrimaryHoverBorder($buttonPrimaryHoverBorder)
    {
        $this->buttonPrimaryHoverBorder = $buttonPrimaryHoverBorder;
    }

    /**
     * @return mixed
     */
    public function getButtonPrimaryHoverBorder()
    {
        return $this->buttonPrimaryHoverBorder;
    }

    /**
     * @param mixed $disabledControl
     */
    public function setDisabledControl($disabledControl)
    {
        $this->disabledControl = $disabledControl;
    }

    /**
     * @return mixed
     */
    public function getDisabledControl()
    {
        return $this->disabledControl;
    }

    /**
     * @param mixed $font
     */
    public function setFont($font)
    {
        $this->font = $font;
    }

    /**
     * @return mixed
     */
    public function getFont()
    {
        return $this->font;
    }

    /**
     * @param mixed $fontColor
     */
    public function setFontColor($fontColor)
    {
        $this->fontColor = $fontColor;
    }

    /**
     * @return mixed
     */
    public function getFontColor()
    {
        return $this->fontColor;
    }

    /**
     * @param mixed $navigationPrimary
     */
    public function setNavigationPrimary($navigationPrimary)
    {
        $this->navigationPrimary = $navigationPrimary;
    }

    /**
     * @return mixed
     */
    public function getNavigationPrimary()
    {
        return $this->navigationPrimary;
    }

    /**
     * @param mixed $navigationPrimaryA
     */
    public function setNavigationPrimaryA($navigationPrimaryA)
    {
        $this->navigationPrimaryA = $navigationPrimaryA;
    }

    /**
     * @return mixed
     */
    public function getNavigationPrimaryA()
    {
        return $this->navigationPrimaryA;
    }

    /**
     * @param mixed $navigationSecondary
     */
    public function setNavigationSecondary($navigationSecondary)
    {
        $this->navigationSecondary = $navigationSecondary;
    }

    /**
     * @return mixed
     */
    public function getNavigationSecondary()
    {
        return $this->navigationSecondary;
    }

    /**
     * @param mixed $navigationSecondaryA
     */
    public function setNavigationSecondaryA($navigationSecondaryA)
    {
        $this->navigationSecondaryA = $navigationSecondaryA;
    }

    /**
     * @return mixed
     */
    public function getNavigationSecondaryA()
    {
        return $this->navigationSecondaryA;
    }

    /**
     * @param mixed $portalId
     */
    public function setPortalId($portalId)
    {
        $this->portalId = $portalId;
    }

    /**
     * @return mixed
     */
    public function getPortalId()
    {
        return $this->portalId;
    }

    /**
     * @param mixed $progressBar
     */
    public function setProgressBar($progressBar)
    {
        $this->progressBar = $progressBar;
    }

    /**
     * @return mixed
     */
    public function getProgressBar()
    {
        return $this->progressBar;
    }


    public function initialize()
    {
        $this->belongsTo('portalId', 'Portal', 'portalId');
    }

    public function onConstruct()
    {
        static::setCachingMode(\Apprecie\library\Cache\CachingMode::Persistent);
    }

    public function delete()
    {
        throw new LogicException('Do not delete me.  Delete the Portal and the DB will cascade');
    }
}