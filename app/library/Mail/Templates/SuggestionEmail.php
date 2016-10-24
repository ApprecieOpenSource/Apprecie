<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 24/03/15
 * Time: 11:03
 */

namespace Apprecie\Library\Mail\Templates;

use Apprecie\Library\Request\Url;

class SuggestionEmail extends EmailTemplate
{
    protected $_organisation = null, $_content = null, $_event = null, $_link = null;

    public function __construct($to, $content, $sourceOrganisation, $event, $link, $cc = null)
    {
        $sourceOrganisation = \Organisation::resolve($sourceOrganisation);
        $event = \Event::resolve($event);

        $this->_organisation = $sourceOrganisation;
        $this->_content = $content;
        $this->_event = $event->getHTMLEncodeAdapter();
        $this->_link = $link;

        parent::__construct(
            $to,
            $this->config->mail->defaultFrom,
            _g(
                'A fantastic new opportunity with {organisation}',
                ['organisation' => $sourceOrganisation->getOrganisationName()]
            ),
            $cc
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

        $eventDateTimeStart = $this->_event->getStartDateTime(true);
        $eventDateTimeEnd =$this->_event->getEndDateTime(true);

        $whatIsIncluded = '';
        if ($this->_event->getBreakfast() == 1) {
            $whatIsIncluded .= '<li>' . _g('Breakfast') . '</li>';
        }
        if ($this->_event->getLunch() == 1) {
            $whatIsIncluded .= '<li>' . _g('Lunch') . '</li>';
        }
        if ($this->_event->getLightRefreshment() == 1) {
            $whatIsIncluded .= '<li>' . _g('Light Refreshments') . '</li>';
        }
        if ($this->_event->getAfternoonTea() == 1) {
            $whatIsIncluded .= '<li>' . _g('Afternoon Tea') . '</li>';
        }
        if ($this->_event->getDinner() == 1) {
            $whatIsIncluded .= '<li>' . _g('Dinner') . '</li>';
        }
        $whatIsIncluded = '<ul>' . $whatIsIncluded . '</ul>';

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

        $address=_g('TBC');
        if($this->_event->getAddress()!=null){
            $address=nl2br($this->_event->getAddress()->getLabel());
        }
        $contentTokens =
            [
                'content' => $this->_content,
                'eventTitle' => $this->_event->getTitle(),
                'eventDescription' => $this->_event->getSummary(),
                'eventDescriptionFull' => $this->_event->getDescription(),
                'eventAttendanceTerms' => $this->_event->getAttendanceTerms(),
                'eventVenue' => $address,
                'eventDateTimeStart' => $eventDateTimeStart,
                'eventDateTimeEnd' => $eventDateTimeEnd,
                'eventWhatIsIncluded' => $whatIsIncluded,
                'linkText' => _g('Click here to see more details'),
                'link' => $this->_link,
                'eventImg' => $eventImage,
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

        if ($this->_link == false) {
            $this->_htmlBody = $this->getTemplateHTML(
                'suggestionNoLink',
                $styleTokens,
                $contentTokens
            ); //i.e.  see views/suggestion/index.volt
        } else {
            $this->_htmlBody = $this->getTemplateHTML(
                'suggestion',
                $styleTokens,
                $contentTokens
            ); //i.e.  see views/suggestion/index.volt
        }
    }
} 