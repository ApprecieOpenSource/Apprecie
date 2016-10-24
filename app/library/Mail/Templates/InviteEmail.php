<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 19/02/15
 * Time: 17:20
 */

namespace Apprecie\Library\Mail\Templates;

use Apprecie\Library\Request\Url;

class InviteEmail extends EmailTemplate
{
    protected $_organisation = null, $_aboveLinkContent = null, $_belowLinkContent = null, $_link = null;

    public function __construct(
        $to,
        $aboveLinkContent,
        $belowLinkContent,
        $link,
        $sourceOrganisation,
        $event,
        $fromName,
        $cc = null
    ) {
        $sourceOrganisation = \Organisation::resolve($sourceOrganisation);
        $event = \Event::resolve($event);

        $this->_organisation = $sourceOrganisation;
        $this->_aboveLinkContent = $aboveLinkContent;
        $this->_belowLinkContent = $belowLinkContent;
        $this->_link = $link;
        $this->_event = $event->getHTMLEncodeAdapter();

        parent::__construct(
            $to,
            $this->config->mail->defaultFrom,
            _g('You have been invited to an event by {fromName}', ['fromName' => $fromName]),
            $cc,
            $event->getCalendar(false)->render(),
            $event->getTitle() . '.ics'
        );
    }

    public function build()
    {
        $eventImage = \Assets::getItemPrimaryImage($this->_event->getItemId(), Url::getConfiguredPortalAddress($this->_organisation->getPortalId()));

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
                'aboveLinkContent' => $this->_aboveLinkContent,
                'belowLinkContent' => $this->_belowLinkContent,
                'eventTitle' => $this->_event->getTitle(),
                'eventDescription' => $this->_event->getSummary(),
                'eventVenue' => nl2br($this->_event->getAddress()->getLabel()),
                'eventDateTimeStart' =>$this->_event->getStartDateTime(true),
                'eventDateStart' => $this->_event->getStartDateTime(true, true),
                'eventTimeStart' => $this->_event->getStartDateTime(true, false, true),
                'eventDateTimeEnd' => $this->_event->getEndDateTime(true),
                'eventImg' => $eventImage,
                'link' => $this->_link,
                'linkText' => _g('RSVP Now'),
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
                        array('img', 'apprecie.png'),
                        'https'
                    ),
                'termsLink' => Url::getConfiguredPortalAddress(null, 'legal', 'terms')
            ];

        $this->_htmlBody = $this->getTemplateHTML('invite', $styleTokens, $contentTokens);
    }
}