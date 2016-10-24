<?php

namespace Apprecie\Library\Mail\Templates;

use Apprecie\Library\Request\Url;

class PostPasswordResetEmail extends EmailTemplate
{
    protected $_organisation = null, $_content = null;

    public function __construct($to, $content, $sourceOrganisation)
    {
        $sourceOrganisation = \Organisation::resolve($sourceOrganisation);

        $this->_organisation = $sourceOrganisation;
        $this->_content = $content;

        parent::__construct($to, $this->config->mail->defaultFrom, _g('Your password was successfully reset'));
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
                'fontColor' => $styles->getFontColor()
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

        $this->_htmlBody = $this->getTemplateHTML('generic', $styleTokens, $contentTokens);
    }
}