<?php

namespace Apprecie\Library\Mail;

use Apprecie\Library\Collections\Enum;

class EmailMacro extends Enum
{
    const RECIPIENT_FIRST_NAME = '{[recipient_first_name]}';
    const RECIPIENT_LAST_NAME = '{[recipient_last_name]}';
    const RECIPIENT_FULL_NAME = '{[recipient_full_name]}';
    const RECIPIENT_EMAIL = '{[recipient_email]}';
    const RECIPIENT_ORGANISATION = '{[recipient_organisation]}';
    const RECIPIENT_PORTAL = '{[recipient_portal]}';
    const RECIPIENT_PHONE = '{[recipient_phone]}';
    const RECIPIENT_TITLE = '{[recipient_title]}';

    const SENDER_FIRST_NAME = '{[sender_first_name]}';
    const SENDER_LAST_NAME = '{[sender_last_name]}';
    const SENDER_FULL_NAME = '{[sender_full_name]}';
    const SENDER_EMAIL = '{[sender_email]}';
    const SENDER_ORGANISATION = '{[sender_organisation]}';
    const SENDER_PORTAL = '{[sender_portal]}';
    const SENDER_PHONE = '{[sender_phone]}';
    const SENDER_TITLE = '{[sender_title]}';

    const SUPPORT_EMAIL = '{[support_email]}';

    const EVENT_TITLE = '{[event_title]}';
    const EVENT_BOOKING_END = '{[event_booking_end]}';
    const EVENT_BOOKING_START = '{[event_booking_start]}';
    const EVENT_START = '{[event_start]}';
    const EVENT_END = '{[event_end]}';

    const SUGGESTION_OFF_PORTAL_CONTACT_LINK = '{[contact_us_suggestion_link]}';

    function __construct($name)
    {
        $this->_name = $name;
        $this->_strings = array(
            static::SUGGESTION_OFF_PORTAL_CONTACT_LINK=> 'This macro will insert a link that says the phrase "contact us" (so it can be used in a sentence), that will open a new email in the user\'s own email software, already addressed to you, ready to reply.'
        );
    }
}