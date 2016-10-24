<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 19/02/15
 * Time: 17:20
 */

namespace Apprecie\Library\Mail\Templates;

use Apprecie\Library\Request\Url;

class UpdatesAndNewslettersEmail extends EmailTemplate
{
    protected $_organisation = null, $_content = null, $_url = null, $_aboveLinkContent = null, $_linkText = null, $_heading = null;

    public function __construct($to, $content, $sourceOrganisation, $title, $heading, $linkText, $url, $aboveLinkContent = null)
    {
        $sourceOrganisation = \Organisation::resolve($sourceOrganisation);

        $this->_organisation = $sourceOrganisation;
        $this->_content = $content;
        $this->_heading = $heading;
        $this->_linkText = $linkText;
        $this->_url = $url;
        $this->_aboveLinkContent = $aboveLinkContent;

        parent::__construct($to, $this->config->mail->defaultFrom, $title);
    }

    public function build()
    {
        $styles = $this->_organisation->getOrganisationStyles();

        if ($styles == null) {
            $styles = \Portal::findFirst("portalSubdomain='admin'")->getOwningOrganisation()->getOrganisationStyles();
        }

        $font = $styles->getFont();
        if (!$font) {
            $font = 'Arial, Helvetica, sans-serif';
        }

        $logo = \Assets::getOrganisationBrandLogo($styles->getOrganisationId());
        $logoDimentions = getimagesize(__DIR__ . '\..\..\..\..\public' . $logo);
        if ($logoDimentions[0] > $logoDimentions[1]) {
            $logoAttribute = ' width="150"';
        } else {
            $logoAttribute = ' height="150"';
        }

        $styleTokens =
            [
                'buttonPrimaryBackground' => $styles->getButtonPrimary(),
                'buttonPrimaryBorder' => $styles->getButtonPrimary(),
                'buttonPrimaryText' => $styles->getButtonPrimaryColor(),
                'font' => $font,
                'fontColor' => $styles->getFontColor(),
                'linkColor' => $styles->getA(),
                'linkHoverColor' => $styles->getAHover()
            ];


        $contentTokens =
            [
                'content' => $this->_content,
                'logo' => Url::getConfiguredPortalAddress(
                        $this->_organisation->getPortal(),
                        null,
                        '',
                        null
                    ) . $logo,
                'logoAttribute' => $logoAttribute,
                'logoApprecie' => Url::getConfiguredPortalAddress(
                        $this->_organisation->getPortal(),
                        null,
                        null,
                        array('img', 'apprecie.png')
                    ),
                'termsLink' => Url::getConfiguredPortalAddress(null, 'legal', 'terms'),
                'heading' => $this->_heading,
                'link' => $this->_url,
                'linkText' => $this->_linkText,
                'aboveLinkContent' => $this->_aboveLinkContent
            ];

        $this->_htmlBody = $this->getTemplateHTML('newsletter', $styleTokens, $contentTokens);
    }
}