<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 19/02/15
 * Time: 17:20
 */

namespace Apprecie\Library\Mail\Templates;

use Apprecie\Library\Request\Url;

class GenericEmail extends EmailTemplate
{
    protected $_organisation = null, $_content = null, $_url = null;

    public function __construct($to, $content, $sourceOrganisation, $title, $url = null, $attachmentFile = null, $attachmentName = null)
    {
        $sourceOrganisation = \Organisation::resolve($sourceOrganisation);

        $this->_organisation = $sourceOrganisation;
        $this->_content = $content;
        $this->_url = $url;

        parent::__construct($to, $this->config->mail->defaultFrom, $title, null, $attachmentFile, $attachmentName);
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
                'termsLink' => Url::getConfiguredPortalAddress(null, 'legal', 'terms')
            ];

        if ($this->_url == null) {
            $this->_htmlBody = $this->getTemplateHTML('generic', $styleTokens, $contentTokens);
        } else {
            $contentTokens['link'] = $this->_url;
            $contentTokens['linkText'] = _g('More Details');

            $this->_htmlBody = $this->getTemplateHTML('genericLink', $styleTokens, $contentTokens);
        }
    }
}