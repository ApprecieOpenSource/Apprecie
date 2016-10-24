<?php

namespace Apprecie\Library\Mail;

use Apprecie\Library\Messaging\PrivateMessageQueue;
use Apprecie\Library\Users\UserEx;
use Apprecie\Library\Users\UserRole;
use Phalcon\Http\Response\Exception;

class UserEmail extends PrivateMessageQueue
{
    public $type;

    function __construct($type)
    {
        $this->type = $type;
    }

    public function getDefaultContent()
    {
        $data = array(
            EmailTemplateType::SIGNUP_CLIENT => array(
                'aboveContent' => array(
                    'description' => _g('Text above the link'),
                    'type' => 'rich',
                    'content' =>
                        _p(_g('Dear {recipient_first_name},', array('recipient_first_name' => EmailMacro::RECIPIENT_FIRST_NAME)))
                        . _p(_g('Welcome to the {recipient_organisation} portal, a members invite-only, private service designed exclusively for {recipient_organisation} valued clients.', array('recipient_organisation' => EmailMacro::RECIPIENT_ORGANISATION)))
                        . _p(_g('We have carefully prepared a selection of luxury events and offers that we hope you and your family will enjoy.'))
                ),
                'belowContent' => array(
                    'description' => _g('Text below the link'),
                    'type' => 'rich',
                    'content' =>
                        _p(_g('Click the button above to access your personal and secure online portal. You will first be asked to confirm and complete the personal details entered when you were setup, and then create a password for your personal login.'))
                        . _p(_g('Once completed, you can then log into the portal using your registered email address and password, and enjoy the benefits of your portal, and extend them to your family.'))
                        . _p(_g('If you have any questions, please contact {sender_full_name} at: {sender_email}', array('sender_full_name' => EmailMacro::SENDER_FULL_NAME, 'sender_email' => EmailMacro::SENDER_EMAIL)))
                        . _p(_g('Best wishes and welcome to a new world of benefits!'))
                        . _p(_g('{sender_full_name}, on behalf of {recipient_organisation}', array('sender_full_name' => EmailMacro::SENDER_FULL_NAME, 'recipient_organisation' => EmailMacro::RECIPIENT_ORGANISATION)))
                )
            ),
            EmailTemplateType::SIGNUP_INTERNAL => array(
                'aboveContent' => array(
                    'description' => _g('Text above the link'),
                    'type' => 'rich',
                    'content' =>
                        _p(_g('Dear {recipient_first_name},', ['recipient_first_name' => EmailMacro::RECIPIENT_FIRST_NAME]))
                        . _p(_g('Welcome to your customised, on-demand client engagement portal powered by Apprecie.'))
                        . _p(_g('We have carefully prepared a selection of unique events and experiences to help you deepen relationships with discerning clients and prospects and enjoy higher marketing ROI.'))
                ),
                'belowContent' => array(
                    'description' => _g('Text below the link'),
                    'type' => 'rich',
                    'content' =>
                        _p(_g('Click the button above to access your personal and secure online portal, customised for {recipient_organisation}. You will first be asked to confirm your personal details and then to create a password for your secure login.', ['recipient_organisation' => EmailMacro::RECIPIENT_ORGANISATION]))
                        . _p(_g('Once completed, you can then login to the portal from any current browser to access the Vault of opportunities for tailored engagement with high net worth clients and prospects.'))
                        . _p(_g('If you have any questions, please contact us at: {support_email}.', ['support_email' => EmailMacro::SUPPORT_EMAIL]))
                        . _p(_g('Best wishes and welcome to Apprecie,'))
                        . _p(_g('The Apprecie Team'))
                )
            ),
            EmailTemplateType::SIGNUP_AFFILIATE_SUPPLIER => array(
                'aboveContent' => array(
                    'description' => _g('Text above the link'),
                    'type' => 'rich',
                    'content' =>
                        _p(_g('Dear {recipient_first_name},', ['recipient_first_name' => EmailMacro::RECIPIENT_FIRST_NAME]))
                        . _p(_g('Welcome to {recipient_portal} client engagement portal, designed to aid you in reaching your desired demographic.', ['recipient_portal' => EmailMacro::RECIPIENT_PORTAL]))
                        . _p(_g('We are delighted to bring you our comprehensive yet simple Content Management System that will aid you in creating eye-catching content for a wealth of elite clientele in a secure, walled-garden environment.'))
                ),
                'belowContent' => array(
                    'description' => _g('Text below the link'),
                    'type' => 'rich',
                    'content' =>
                        _p(_g('Click the button above to access your personal and secure online portal. You will first be asked to confirm and complete the personal details entered when you were setup, and then create a password for your personal login.'))
                        . _p(_g('Once completed, you can then log into the portal using your registered email address and password, and begin uploading and managing your items and offers'))
                        . _p(_g('If you have any questions, please contact us at: {support_email}', ['support_email' => EmailMacro::SUPPORT_EMAIL]))
                        . _p(_g('Best wishes and welcome to your new platform,'))
                        . _p(_g('The Apprecie Team'))
                )
            ),
            EmailTemplateType::SIGNUP_APPRECIE_SUPPLIER => array(
                'aboveContent' => array(
                    'description' => _g('Text above the link'),
                    'type' => 'rich',
                    'content' =>
                        _p(_g('Dear {recipient_first_name},', ['recipient_first_name' => EmailMacro::RECIPIENT_FIRST_NAME]))
                        . _p(_g('Welcome to {recipient_portal} client engagement portal, designed to aid you in reaching your desired demographic.', ['recipient_portal' => EmailMacro::RECIPIENT_PORTAL]))
                        . _p(_g('We are delighted to bring you our comprehensive yet simple Content Management System that will aid you in creating eye-catching content for a wealth of elite clientele in a secure, walled-garden environment.'))
                ),
                'belowContent' => array(
                    'description' => _g('Text below the link'),
                    'type' => 'rich',
                    'content' =>
                        _p(_g('Click the button above to access your personal and secure online portal. You will first be asked to confirm and complete the personal details entered when you were setup, and then create a password for your personal login.'))
                        . _p(_g('Once completed, you can then log into the portal using your registered email address and password, and begin uploading and managing your items and offers'))
                        . _p(_g('If you have any questions, please contact us at: {support_email}', ['support_email' => EmailMacro::SUPPORT_EMAIL]))
                        . _p(_g('Best wishes and welcome to your new platform,'))
                        . _p(_g('The Apprecie Team'))
                )
            ),
            EmailTemplateType::SIGNUP_MANAGER => array(
                'aboveContent' => array(
                    'description' => _g('Text above the link'),
                    'type' => 'rich',
                    'content' =>
                        _p(_g('Dear {recipient_first_name},', ['recipient_first_name' => EmailMacro::RECIPIENT_FIRST_NAME]))
                        . _p(_g('Welcome to your customised, on-demand client engagement portal powered by Apprecie.'))
                        . _p(_g('We have carefully prepared a selection of unique events and experiences to help you deepen relationships with discerning clients and prospects and enjoy higher marketing ROI.'))
                ),
                'belowContent' => array(
                    'description' => _g('Text below the link'),
                    'type' => 'rich',
                    'content' =>
                        _p(_g('Click the button above to access your personal and secure online portal, customised for {recipient_organisation}. You will first be asked to confirm your personal details and then to create a password for your secure login.', ['recipient_organisation' => EmailMacro::RECIPIENT_ORGANISATION]))
                        . _p(_g('Once completed, you can then login to the portal from any current browser to access the Vault of opportunities for tailored engagement with high net worth clients and prospects.'))
                        . _p(_g('If you have any questions, please contact us at: {support_email}.', ['support_email' => EmailMacro::SUPPORT_EMAIL]))
                        . _p(_g('Best wishes and welcome to Apprecie,'))
                        . _p(_g('The Apprecie Team'))
                )
            ),
            EmailTemplateType::SIGNUP_PORTAL_ADMIN => array(
                'aboveContent' => array(
                    'description' => _g('Text above the link'),
                    'type' => 'rich',
                    'content' =>
                        _p(_g('Dear {recipient_first_name},', ['recipient_first_name' => EmailMacro::RECIPIENT_FIRST_NAME]))
                        . _p(_g('Welcome to your customised, on-demand client engagement portal powered by Apprecie.'))
                        . _p(_g('We have carefully prepared a selection of unique events and experiences to help you deepen relationships with discerning clients and prospects and enjoy higher marketing ROI.'))
                ),
                'belowContent' => array(
                    'description' => _g('Text below the link'),
                    'type' => 'rich',
                    'content' =>
                        _p(_g('Click the button above to access your personal and secure online portal, customised for {recipient_organisation}. You will first be asked to confirm your personal details and then to create a password for your secure login.', ['recipient_organisation' => EmailMacro::RECIPIENT_ORGANISATION]))
                        . _p(_g('Once completed, you can then login to the portal from any current browser to access the Vault of opportunities for tailored engagement with high net worth clients and prospects.'))
                        . _p(_g('If you have any questions, please contact us at: {support_email}.', ['support_email' => EmailMacro::SUPPORT_EMAIL]))
                        . _p(_g('Best wishes and welcome to Apprecie,'))
                        . _p(_g('The Apprecie Team'))
                )
            ),
            EmailTemplateType::SUGGESTION_ON_PORTAL => array(
                'content' => array(
                    'description' => _g('Message body'),
                    'type' => 'rich',
                    'content' =>
                        _p(_g('Dear {recipient_first_name},', ['recipient_first_name' => EmailMacro::RECIPIENT_FIRST_NAME]))
                        . _p(_g('I saw this and thought you might be interested in taking a look. For more details, please click the button below.'))
                        . _p(_g('Please note that spaces are extremely limited and on a first come, first served basis. To not miss this opportunity please ensure you register your interest on the portal by the booking end date: {event_booking_end}', ['event_booking_end' => EmailMacro::EVENT_BOOKING_END]), 'font-style: italic;')
                        . _p(_g('Sincerely,') . '<br>' . _g('{sender_full_name}', ['sender_full_name' => EmailMacro::SENDER_FULL_NAME]))
                )
            ),
            EmailTemplateType::SUGGESTION_OFF_PORTAL => array(
                'content' => array(
                    'description' => _g('Message body'),
                    'type' => 'rich',
                    'content' =>
                        _p(_g('Dear Client,'))
                        . _p(_g('We saw this fantastic opportunity and thought you might want to take advantage. If this Item interests you and you would like to attend, please {contact_us_suggestion_link} and we will be happy to prepare an invitation for you.', ['contact_us_suggestion_link' => EmailMacro::SUGGESTION_OFF_PORTAL_CONTACT_LINK]))
                        . _p(_g('Please note that spaces are extremely limited and on a first come, first served basis. To not miss this opportunity please ensure you respond as soon as possible.'), 'font-style: italic;')
                        . _p(_g('Sincerely,') . '<br>' . _g('{sender_full_name}', ['sender_full_name' => EmailMacro::SENDER_FULL_NAME]) . '<br>' . _g('{sender_organisation}', ['sender_organisation' => EmailMacro::SENDER_ORGANISATION]) . '<br>' . _g('{sender_email}', ['sender_email' => EmailMacro::SENDER_EMAIL]))
                )
            ),
            EmailTemplateType::INVITATION => array(
                'aboveContent' => array(
                    'description' => _g('Text above the link'),
                    'type' => 'rich',
                    'content' =>
                        _p(_g('Dear {recipient_first_name},', ['recipient_first_name' => EmailMacro::RECIPIENT_FIRST_NAME]))
                        . _p(_g('{sender_full_name} has invited you to {event_title} on {event_start}. To accept or decline this invitation, please click the appropriate response below.', ['sender_full_name' => EmailMacro::SENDER_FULL_NAME, 'event_title' => EmailMacro::EVENT_TITLE, 'event_start' => EmailMacro::EVENT_START]))
                ),
                'belowContent' => array(
                    'description' => _g('Text below the link'),
                    'type' => 'rich',
                    'content' =>
                        _p(_g('Spaces are extremely limited and on a first come first served basis! To ensure your attendance we recommend you RSVP within 24 hours. Booking Ends: {event_booking_end}', ['event_booking_end' => EmailMacro::EVENT_BOOKING_END]), 'font-style: italic;')
                        . _p(_g('We hope that you will be able to attend.'))
                        . _p(_g('Sincerely,') . '<br>' . _g('{sender_full_name}', ['sender_full_name' => EmailMacro::SENDER_FULL_NAME]) . '<br>' . _g('{sender_organisation}', ['sender_organisation' => EmailMacro::SENDER_ORGANISATION]))
                )
            ),
            EmailTemplateType::POST_EVENT_FOLLOW_UP => array(
                'content' => array(
                    'description' => _g('Message body'),
                    'type' => 'rich',
                    'content' =>
                        _p(_g('Dear {recipient_first_name},', ['recipient_first_name' => EmailMacro::RECIPIENT_FIRST_NAME]))
                        . _p(_g('We hope you had a wonderful time at the {event_title}, it was great that you could attend. We would be delighted to hear your feedback on the event, or if you had any outstanding issues or queries please find below the contact name and details of our VIP Team. Please let us know if there is anything else we can do for you.', ['event_title' => EmailMacro::EVENT_TITLE]))
                        . _p(_g('Kindest Regards,') . '<br>' . _g('{sender_full_name}', ['sender_full_name' => EmailMacro::SENDER_FULL_NAME]) . '<br>' . _g('{sender_organisation}', ['sender_organisation' => EmailMacro::SENDER_ORGANISATION]) . '<br>' . _g('{sender_email}', ['sender_email' => EmailMacro::SENDER_EMAIL]) . '<br>' . _g('{sender_phone}', ['sender_phone' => EmailMacro::SENDER_PHONE]))
                )
            )
        );

        return $data[$this->type];
    }

    public function getContent()
    {
        $session = $this->getDI()->get('session');
        if ($session->has('userEmailData') && isset($session->get('userEmailData')[$this->type]) && $session->get('userEmailData')[$this->type]) {
            return $session->get('userEmailData')[$this->type];
        } else {
            return $this->getDefaultContent();
        }
    }

    public function setContent($data)
    {
        $session = $this->getDI()->get('session');
        $session->set('userEmailData', array($this->type => $data));
    }

    public function getOptions()
    {
        $session = $this->getDI()->get('session');
        if ($session->has('userEmailOptions')) {
            return $session->get('userEmailOptions');
        } else {
            return null;
        }
    }

    public function setOptions($options)
    {
        $session = $this->getDI()->get('session');
        $session->set('userEmailOptions', $options);
    }

    public function getAvailableMacros()
    {
        $macros = array();

        switch ($this->type) {
            case EmailTemplateType::SUGGESTION_OFF_PORTAL:
                $macros[EmailMacro::SENDER_TITLE] = (new EmailMacro(EmailMacro::SENDER_TITLE))->getText();
                $macros[EmailMacro::SENDER_FIRST_NAME] = (new EmailMacro(EmailMacro::SENDER_FIRST_NAME))->getText();
                $macros[EmailMacro::SENDER_LAST_NAME] = (new EmailMacro(EmailMacro::SENDER_LAST_NAME))->getText();
                $macros[EmailMacro::SENDER_FULL_NAME] = (new EmailMacro(EmailMacro::SENDER_FULL_NAME))->getText();
                $macros[EmailMacro::SENDER_ORGANISATION] = (new EmailMacro(EmailMacro::SENDER_ORGANISATION))->getText();
                $macros[EmailMacro::SENDER_PORTAL] = (new EmailMacro(EmailMacro::SENDER_PORTAL))->getText();
                $macros[EmailMacro::SENDER_EMAIL] = (new EmailMacro(EmailMacro::SENDER_EMAIL))->getText();
                $macros[EmailMacro::SENDER_PHONE] = (new EmailMacro(EmailMacro::SENDER_PHONE))->getText();

                $macros[EmailMacro::SUPPORT_EMAIL] = (new EmailMacro(EmailMacro::SUPPORT_EMAIL))->getText();

                $macros[EmailMacro::EVENT_TITLE] = (new EmailMacro(EmailMacro::EVENT_TITLE))->getText();
                $macros[EmailMacro::EVENT_START] = (new EmailMacro(EmailMacro::EVENT_START))->getText();
                $macros[EmailMacro::EVENT_END] = (new EmailMacro(EmailMacro::EVENT_END))->getText();
                $macros[EmailMacro::EVENT_BOOKING_START] = (new EmailMacro(EmailMacro::EVENT_BOOKING_START))->getText();
                $macros[EmailMacro::EVENT_BOOKING_END] = (new EmailMacro(EmailMacro::EVENT_BOOKING_END))->getText();

                $macros[EmailMacro::SUGGESTION_OFF_PORTAL_CONTACT_LINK] = (new EmailMacro(EmailMacro::SUGGESTION_OFF_PORTAL_CONTACT_LINK))->getText();

                break;
            case EmailTemplateType::INVITATION:
            case EmailTemplateType::SUGGESTION_ON_PORTAL:
                $macros[EmailMacro::EVENT_TITLE] = (new EmailMacro(EmailMacro::EVENT_TITLE))->getText();
                $macros[EmailMacro::EVENT_START] = (new EmailMacro(EmailMacro::EVENT_START))->getText();
                $macros[EmailMacro::EVENT_END] = (new EmailMacro(EmailMacro::EVENT_END))->getText();
                $macros[EmailMacro::EVENT_BOOKING_START] = (new EmailMacro(EmailMacro::EVENT_BOOKING_START))->getText();
                $macros[EmailMacro::EVENT_BOOKING_END] = (new EmailMacro(EmailMacro::EVENT_BOOKING_END))->getText();
            default:
                $macros[EmailMacro::RECIPIENT_TITLE] = (new EmailMacro(EmailMacro::RECIPIENT_TITLE))->getText();
                $macros[EmailMacro::RECIPIENT_FIRST_NAME] = (new EmailMacro(EmailMacro::RECIPIENT_FIRST_NAME))->getText();
                $macros[EmailMacro::RECIPIENT_LAST_NAME] = (new EmailMacro(EmailMacro::RECIPIENT_LAST_NAME))->getText();
                $macros[EmailMacro::RECIPIENT_FULL_NAME] = (new EmailMacro(EmailMacro::RECIPIENT_FULL_NAME))->getText();
                $macros[EmailMacro::RECIPIENT_ORGANISATION] = (new EmailMacro(EmailMacro::RECIPIENT_ORGANISATION))->getText();
                $macros[EmailMacro::RECIPIENT_PORTAL] = (new EmailMacro(EmailMacro::RECIPIENT_PORTAL))->getText();
                $macros[EmailMacro::RECIPIENT_EMAIL] = (new EmailMacro(EmailMacro::RECIPIENT_EMAIL))->getText();
                $macros[EmailMacro::RECIPIENT_PHONE] = (new EmailMacro(EmailMacro::RECIPIENT_PHONE))->getText();

                $macros[EmailMacro::SENDER_TITLE] = (new EmailMacro(EmailMacro::SENDER_TITLE))->getText();
                $macros[EmailMacro::SENDER_FIRST_NAME] = (new EmailMacro(EmailMacro::SENDER_FIRST_NAME))->getText();
                $macros[EmailMacro::SENDER_LAST_NAME] = (new EmailMacro(EmailMacro::SENDER_LAST_NAME))->getText();
                $macros[EmailMacro::SENDER_FULL_NAME] = (new EmailMacro(EmailMacro::SENDER_FULL_NAME))->getText();
                $macros[EmailMacro::SENDER_ORGANISATION] = (new EmailMacro(EmailMacro::SENDER_ORGANISATION))->getText();
                $macros[EmailMacro::SENDER_PORTAL] = (new EmailMacro(EmailMacro::SENDER_PORTAL))->getText();
                $macros[EmailMacro::SENDER_EMAIL] = (new EmailMacro(EmailMacro::SENDER_EMAIL))->getText();
                $macros[EmailMacro::SENDER_PHONE] = (new EmailMacro(EmailMacro::SENDER_PHONE))->getText();

                $macros[EmailMacro::SUPPORT_EMAIL] = (new EmailMacro(EmailMacro::SUPPORT_EMAIL))->getText();
        }

        return $macros;
    }

    public function getProcessedContent($sender, $recipient, $event)
    {
        $content = $this->getContent();

        if ($sender) {

            $sender = \User::resolve($sender);

            $oldPortal = (new UserEx())->getActiveQueryPortal();
            UserEx::ForceActivePortalForUserQueries($sender->getPortal());

            $senderProfile = $sender->getUserProfile();
            $senderOrganisation = $sender->getOrganisation();
            $senderPortal = $sender->getPortal();

            foreach ($content as &$contentBlock) {
                $contentBlock = str_replace(EmailMacro::SENDER_TITLE, $senderProfile->getTitle(), $contentBlock);
                $contentBlock = str_replace(EmailMacro::SENDER_FIRST_NAME, $senderProfile->getFirstname(), $contentBlock);
                $contentBlock = str_replace(EmailMacro::SENDER_LAST_NAME, $senderProfile->getLastname(), $contentBlock);
                $contentBlock = str_replace(EmailMacro::SENDER_FULL_NAME, $senderProfile->getFullName(), $contentBlock);
                $contentBlock = str_replace(EmailMacro::SENDER_EMAIL, $senderProfile->getEmail(), $contentBlock);
                $contentBlock = str_replace(EmailMacro::SENDER_ORGANISATION, $senderOrganisation->getOrganisationName(), $contentBlock);
                $contentBlock = str_replace(EmailMacro::SENDER_PORTAL, $senderPortal->getPortalName(), $contentBlock);
                $contentBlock = str_replace(EmailMacro::SENDER_PHONE, $senderProfile->getPhone(), $contentBlock);

                if ($this->type === EmailTemplateType::SUGGESTION_OFF_PORTAL) {
                    $contactLink = '<a href="mailto:' . $senderProfile->getEmail() . '?subject=' . rawurlencode('RE: ' . _g('A fantastic new opportunity with {sender_organisation}', ['sender_organisation' => $senderOrganisation->getOrganisationName()])) . '" style="font-style: italic;text-decoration: none;">' . _g('contact us') . '</a>';
                    $contentBlock = str_replace(EmailMacro::SUGGESTION_OFF_PORTAL_CONTACT_LINK, $contactLink, $contentBlock);
                }
            }

            UserEx::ForceActivePortalForUserQueries($oldPortal);
        }

        if ($recipient) {

            $recipient = \User::resolve($recipient);

            $oldPortal = (new UserEx())->getActiveQueryPortal();
            UserEx::ForceActivePortalForUserQueries($recipient->getPortal());

            $recipientProfile = $recipient->getUserProfile();
            $recipientOrganisation = $recipient->getOrganisation();
            $recipientPortal = $recipient->getPortal();

            foreach ($content as &$contentBlock) {
                $contentBlock = str_replace(EmailMacro::RECIPIENT_TITLE, $recipientProfile->getTitle(), $contentBlock);
                $contentBlock = str_replace(EmailMacro::RECIPIENT_FIRST_NAME, $recipientProfile->getFirstname(), $contentBlock);
                $contentBlock = str_replace(EmailMacro::RECIPIENT_LAST_NAME, $recipientProfile->getLastname(), $contentBlock);
                $contentBlock = str_replace(EmailMacro::RECIPIENT_FULL_NAME, $recipientProfile->getFullName(), $contentBlock);
                $contentBlock = str_replace(EmailMacro::RECIPIENT_EMAIL, $recipientProfile->getEmail(), $contentBlock);
                $contentBlock = str_replace(EmailMacro::RECIPIENT_ORGANISATION, $recipientOrganisation->getOrganisationName(), $contentBlock);
                $contentBlock = str_replace(EmailMacro::RECIPIENT_PORTAL, $recipientPortal->getPortalName(), $contentBlock);
                $contentBlock = str_replace(EmailMacro::RECIPIENT_PHONE, $recipientProfile->getPhone(), $contentBlock);
            }

            UserEx::ForceActivePortalForUserQueries($oldPortal);
        }

        if ($event) {

            $event = \Event::resolve($event);

            foreach ($content as &$contentBlock) {
                $contentBlock = str_replace(EmailMacro::EVENT_TITLE, $event->getTitle(), $contentBlock);
                $contentBlock = str_replace(EmailMacro::EVENT_BOOKING_START, $event->getBookingStartDate(true), $contentBlock);
                $contentBlock = str_replace(EmailMacro::EVENT_BOOKING_END, $event->getBookingEndDate(true), $contentBlock);
                $contentBlock = str_replace(EmailMacro::EVENT_START, $event->getStartDateTime(true), $contentBlock);
                $contentBlock = str_replace(EmailMacro::EVENT_END, $event->getEndDateTime(true), $contentBlock);
            }
        }

        foreach ($content as &$contentBlock) {
            $contentBlock = str_replace(EmailMacro::SUPPORT_EMAIL, $this->config->mail->defaultSupport, $contentBlock);
        }

        return $content;
    }
}