<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 19/02/15
 * Time: 17:20
 */

namespace Apprecie\Library\Mail\Templates;

use Apprecie\Library\Request\Url;

class PasswordResetEmail extends EmailTemplate
{
    protected $_organisation = null, $_aboveLinkContent = null, $_belowLinkContent = null, $_link = null;

    public function __construct($to, $aboveLinkContent, $belowLinkContent, $link, $sourceOrganisation)
    {
        $sourceOrganisation = \Organisation::resolve($sourceOrganisation);

        $this->_organisation = $sourceOrganisation;
        $this->_aboveLinkContent = $aboveLinkContent;
        $this->_belowLinkContent = $belowLinkContent;
        $this->_link = $link;

        parent::__construct($to, $this->config->mail->defaultFrom, _g('Password recovery'));
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
                'aboveLinkContent' => $this->_aboveLinkContent,
                'belowLinkContent' => $this->_belowLinkContent,
                'link' => $this->_link,
                'linkText' => _g('Click here to reset your password'),
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

        $this->_htmlBody = $this->getTemplateHTML('middleLink', $styleTokens, $contentTokens);
    }
}