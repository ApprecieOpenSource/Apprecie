<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 19/02/15
 * Time: 17:49
 */

namespace Apprecie\Library\Mail;

use Apprecie\Library\Mail\Templates\GenericEmail;
use Apprecie\Library\Mail\Templates\InviteEmail;
use Apprecie\Library\Mail\Templates\PasswordResetEmail;
use Apprecie\Library\Mail\Templates\PostPasswordResetEmail;
use Apprecie\Library\Mail\Templates\SignupEmail;
use Apprecie\Library\Mail\Templates\SuggestionEmail;
use Apprecie\Library\Mail\Templates\UpdatesAndNewslettersEmail;
use Apprecie\Library\Mail\Templates\WelcomeEmail;
use Apprecie\Library\Messaging\PrivateMessageQueue;
use Apprecie\Library\Request\Url;
use Apprecie\Library\Users\ApprecieUser;
use Apprecie\Library\Users\UserEx;
use Phalcon\DI;

class EmailUtility extends PrivateMessageQueue
{
    public function sendGenericEmailMessage($toEmail, $message, $title, $organisation, $url = null)
    {
        $organisation = \Organisation::resolve($organisation);

        $template = new GenericEmail($toEmail, $message, $organisation, $title, $url);

        $template->build();
        $template->sendEmail();

        if ($template->hasMessages()) {
            $this->appendMessageEx($template);
            return false;
        }

        return true;
    }

    public function sendVaultUpdate($toEmail, $baseURL, $items, $organisation)
    {
        $organisation = \Organisation::resolve($organisation);
        $title = _g('Latest events added to your vault');
        $heading = _g('New exciting events that have just become available to you');
        $url = $baseURL . '/vault';
        $linkText = _g('Go to my vault now');

        $config = DI::getDefault()->get('config')->updatesAndNewsletters;
        if (count($items) > $config->numberOfItemsPerEmail) {
            $aboveLinkContent = _p(_g('There are {number} more recently added event(s) that you can view by visiting your vault.', ['number' => (count($items) - $config->numberOfItemsPerEmail)]));
        } else {
            $aboveLinkContent = null;
        }

        $content = '';
        $itemCount = 0;
        $styles = $organisation->getOrganisationStyles();

        foreach ($items as $item) {
            if ($itemCount === $config->numberOfItemsPerEmail) {
                break;
            }

            $itemCount++;

            $item = \Item::resolve($item);
            $event = $item->getEvent();

            $content .= '<tr><td>';
            $content .= '<div style="background-color: #fff;border-bottom: 4px solid #E7EAEC;font-size: 14px;margin-bottom: 20px;">';
            if ($item->getIsByArrangement()) {
                $content .= '<a href="' . $baseURL . '/vault/arranged/' . $event->getEventId() . '" style="text-decoration: none;color: ' . $styles->getFontColor() . ';">';
            } else {
                $content .= '<a href="' . $baseURL . '/vault/event/' . $event->getEventId() . '" style="text-decoration: none;color: ' . $styles->getFontColor() . ';">';
            }
            $content .= '<img src="' . \Assets::getItemPrimaryImage($event->getItemId(), $baseURL) . '" style="border: none;max-width: 580px;height: auto;width: 100%;">';
            $content .= '<p style="font-weight: bold;padding: 20px;margin: 0;">' . $event->getTitle() . '</p>';
            $content .= '</a>';
            $content .= '</div>';
            $content .= '</td></tr>';
        }

        $template = new UpdatesAndNewslettersEmail($toEmail, $content, $organisation, $title, $heading, $linkText, $url, $aboveLinkContent);

        $template->build();
        $template->sendEmail();

        if ($template->hasMessages()) {
            $this->appendMessageEx($template);
            return false;
        }

        return true;
    }

    public function sendInvite($toEmail, $toName, $url, $fromName, $event, $organisation, $ccEmail = null)
    {
        $event = \Event::resolve($event);
        $organisation = \Organisation::resolve($organisation);

        $aboveContent =
            _p(_g('Dear {name},', ['name' => $toName]))
            . _p(
                _g(
                    '{fromName} has invited you to {eventTitle}, on {eventDateStart} at {eventTimeStart}. To accept or decline this invitation, please click the appropriate response below.',
                    ['fromName' => $fromName]
                )
            );

        $belowContent =
            _p(
                _g(
                    'Spaces are extremely limited and on a first come first served basis! To ensure your attendance we recommend you RSVP within 24 hours. Booking Ends: {bookingEndDate}',
                    [
                        'bookingEndDate' => $event->getBookingEndDate(true)
                    ]
                ),
                'font-style: italic;'
            )
            . _p(_g('We hope that you will be able to attend.'))
            . _p(
                _g('Sincerely,') . '<br>' . _g('{fromName}', ['fromName' => $fromName]) . '<br>' . _g(
                    '{companyName}',
                    ['companyName' => $organisation->getOrganisationName()]
                )
            );

        $template = new InviteEmail($toEmail, $aboveContent, $belowContent, $url, $organisation, $event, $fromName, $ccEmail);

        $template->build();
        $template->sendEmail();

        if ($template->hasMessages()) {
            $this->appendMessageEx($template);
            return false;
        }

        return true;
    }

    public function sendPurchaseConfirmationToSupplier($customer, $order)
    {
        $customer = \User::resolve($customer);
        $order = \Order::resolve($order);
        $supplier = $order->getSupplierUser();
        $organisation = $supplier->getOrganisation();

        $supplier->clearStaticCache();
        UserEx::ForceActivePortalForUserQueries($supplier->getPortalId());
        $profile = $supplier->getUserProfile();
        UserEx::ForceActivePortalForUserQueries();

        if ($profile == null || $profile->getEmail() == null) {
            $this->appendMessageEx(
                _g('The intend user has no email setup, it is not possible to send this supplier a receipt')
            );
            return false;
        }

        $description = '';
        $orderItems = $order->getOrderItems();

        foreach ($orderItems as $item) {
            $description .= _p(_eh($item->getDescription()));
        }

        $content =
            _p(_g('Dear {name}', ['name' => $profile->getFirstname()]))
            . _p(
                _g(
                    'The following items have been consumed by   {customer} : {customerEmail}',
                    [
                        'customer' => $customer->getUserProfile()->getFullName(),
                        'customerEmail' => $customer->getUserProfile()->getEmail()
                    ]
                )
            );

        if ($order->getTotalPrice() > 0) {
            $fullAmount = $order->getFormattedFullTotal();

            $content .= _p(_g('The customer was charged {fullAmount}', ['fullAmount' => $fullAmount]));
            $content .= _p(
                _g(
                    'Applicable fees will have been transferred at payment, and you will have a transaction receipt in your stripe account',
                    ['fullAmount' => $fullAmount]
                )
            );
        }

        $content
            .= _p(_g('The following items have been activated / reserved for the customer '));

        foreach ($orderItems as $item) {
            $content .= _p(_eh($item->getDescription()));
        }

        $template = new GenericEmail($profile->getEmail(), $content, $organisation, 'Item consumption confirmation');

        $template->build();
        $template->sendEmail();

        if ($template->hasMessages()) {
            $this->appendMessageEx($template);
            return false;
        }

        return true;
    }

    public function sendPurchaseConfirmation($user, $order, $organisation)
    {
        $user = \User::resolve($user);
        $order = \Order::resolve($order);
        $organisation = \Organisation::resolve($organisation);

        UserEx::ForceActivePortalForUserQueries($user->getPortalId());
        $profile = $user->getUserProfile();
        UserEx::ForceActivePortalForUserQueries();

        if ($profile == null || $profile->getEmail() == null) {
            $this->appendMessageEx(
                _g('The intended user has no email setup, it is not possible to send this user a receipt')
            );
        }

        $description = '';
        $orderItems = $order->getOrderItems();

        foreach ($orderItems as $item) {
            $description .= _p(_eh($item->getDescription()));
        }

        $content =
            _p(_g('Dear {name}', ['name' => $profile->getFirstname()]))
            . _p(_g('Thank you for your order from {supplier}', ['supplier' => $organisation->getOrganisationName()]));

        if ($order->getTotalPrice() > 0) {
            $fullAmount = $order->getFormattedFullTotal();

            $content .= _p(
                _g(
                    'You were successfully charged {fullAmount}',
                    ['supplier' => $organisation->getOrganisationName(), 'fullAmount' => $fullAmount]
                )
            );

            if ($organisation->getVatNumber() != null) {
                $content .= _p(_g('Vat number : {taxNumber}', ['taxNumber' => $organisation->getVatNumber()]));
            }
        }

        $content
            .= _p(
            _g(
                'The following items have been activated in your account '
            )
        );

        foreach ($orderItems as $item) {
            $content .= _p(_eh($item->getDescription()));
        }

        //@todo update when we allow multiple items per order
        $firstOrderItem = $orderItems[0];
        $firstItem = \OrderItems::resolve($firstOrderItem)->getItem();

        $template = new GenericEmail($profile->getEmail(), $content, $organisation, 'Your order confirmation', null, $order->getCalendar()->render(), $firstItem->getTitle() . '.ics');

        $template->build();
        $template->sendEmail();

        if ($template->hasMessages()) {
            $this->appendMessageEx($template);
            return false;
        }

        return true;
    }

    public function sendOffportalEventSuggestion($toEmail, $toName, $organisation, $fromEmail, $fromName, $event, $ccEmail = null)
    {
        $item = \Item::resolve($event);
        $event = $item->getEvent();
        $organisation = \Organisation::resolve($organisation);

        $contactLink = '<a href="mailto:' . $fromEmail . '?subject=' . rawurlencode(
                'RE: ' . _g(
                    'A fantastic new opportunity with {organisation}',
                    ['organisation' => $organisation->getOrganisationName()]
                )
            ) . '" style="font-style: italic;text-decoration: none;">' . _g('contact us') . '</a>';

        $content = _p(_g('Dear {name},', ['name' => $toName]))
            . _p(
                _g(
                    'We saw this fantastic opportunity and thought you might want to take advantage. If this Item interests you and you would like to attend, please {contact us} and we will be happy to prepare an invitation for you.',
                    ['contact us' => $contactLink]
                )
            )
            . _p(
                _g(
                    'Please note that spaces are extremely limited and on a first come, first served basis. To not miss this opportunity please ensure you respond as soon as possible.'
                ),
                'font-style: italic;'
            )
            . _p(
                _g('Sincerely,') . '<br>' . _g('{suggestedBy}', ['suggestedBy' => $fromName]) . '<br>' . _g(
                    '{orgName}',
                    ['orgName' => $organisation->getOrganisationName()]
                ) . '<br>' . _g('{email}', ['email' => $fromEmail])
            );

        $link = false;

        $template = new SuggestionEmail($toEmail, $content, $organisation, $event, $link, $ccEmail);

        $template->build();
        $template->sendEmail();

        if ($template->hasMessages()) {
            $this->appendMessageEx($template);
            return false;
        }

        return true;
    }

    public function sendEventSuggestionEmail($toEmail, $toName, $organisation, $fromName, $event, $ccEmail = null)
    {
        $item = \Item::resolve($event);
        $event = $item->getEvent();
        $organisation = \Organisation::resolve($organisation);

        $content = _p(_g('Dear {name},', ['name' => $toName]))
            . _p(
                _g(
                    'I saw this and thought you might be interested in taking a look. For more details, please click the button below.'
                )
            )
            . _p(
                _g(
                    'Please note that spaces are extremely limited and on a first come, first served basis. To not miss this opportunity please ensure you register your interest on the portal by the booking end date: {bookingEndDate}',
                    [
                        'bookingEndDate' => $event->getBookingEndDate(true)
                    ]
                ),
                'font-style: italic;'
            )
            . _p(_g('Sincerely,') . '<br>' . _g('{suggestedBy}', ['suggestedBy' => $fromName]));

        if ($item->getIsByArrangement()) {
            $link = Url::getConfiguredPortalAddress($organisation->getPortal(), 'vault', 'arranged', [$event->getItemId()]);
        } else {
            $link = Url::getConfiguredPortalAddress($organisation->getPortal(), 'vault', 'event', [$event->getItemId()]);
        }

        $template = new SuggestionEmail($toEmail, $content, $organisation, $event, $link, $ccEmail);

        $template->build();
        $template->sendEmail();

        if ($template->hasMessages()) {
            $this->appendMessageEx($template);
            return false;
        }

        return true;
    }

    //@todo family member emails

    public function welcomeEmailToClientMembers($toEmail, $toName, $organisation, $user)
    {
        $organisation = \Organisation::resolve($organisation);
        $user = \User::resolve($user);

        $url = Url::getConfiguredPortalAddress(null, 'people');
        $people = '<a href="' . $url . '">' . _g('People') . '</a>';
        $url = Url::getConfiguredPortalAddress(null, 'profile');
        $userProfile = '<a href="' . $url . '">' . _g('Profile') . '</a>';
        $url = Url::getConfiguredPortalAddress(null, 'vault');
        $vault = '<a href="' . $url . '">' . _g('Vault') . '</a>';;

        $managerObj = $user->getFirstParent();

        //@todo getFirstParent()  might not return a manager when we have family members

        if ($managerObj != null) {
            $manager = $managerObj->getUserProfile()->getFullName();
            $managerEmail = $managerObj->getUserProfile()->getEmail();
        } else {
            $manager = _g('Appreice, on behalf of {portal}', ['portal' => $organisation->getPortal()->getPortalName()]);
            $managerEmail = $this->config->mail->defaultSupport;
        }

        $content =
            _p(_g('Dear {name},', ['name' => $toName]))
            . _p(
                _g(
                    'Congratulations! You have successfully registered to {portal}!',
                    ['portal' => $organisation->getOrganisationName()]
                )
            )
            . _p(_g('We hope you will enjoy and take advantage of all opportunities available to you.'))
            . _p(_g('To get started, please follow the steps described below:'))
            . _p(
                _g(
                    '1. Personalise your profile by selecting your interests, hobbies, and preferences from the appealing range of categories in {profile}.',
                    ['profile' => $userProfile]
                )
            )
            . _p(
                _g(
                    '2. Invite your family members so that they can share the benefits of your relationship with {organisation}.  Go to the {people} page to add your family members to the portal.',
                    ['organisation' => $organisation->getOrganisationName(), 'people' => $people]
                )
            )
            . _p(
                _g(
                    '3. Explore the {vault} which contains a selection of luxury experiences and opportunities available. The range of offers available will continue to grow and evolve with time.',
                    ['vault' => $vault]
                )
            )
            . _p(
                _g(
                    'If you have any questions, please contact {manager}, at: {managerEmail}.',
                    ['manager' => $manager, 'managerEmail' => $managerEmail]
                )
            )
            . _p(_g('We hope you enjoy your experience with us!'))
            . _p(_g('Yours sincerely,'))
            . _p(_g('{company}', ['company' => $organisation->getOrganisationName()]));

        $template = new WelcomeEmail($toEmail, $content, $organisation);

        $template->build();
        $template->sendEmail();

        if ($template->hasMessages()) {
            $this->appendMessageEx($template);
            return false;
        }

        return true;
    }

    public function welcomeEmailToSupplier($toEmail, $toName, $organisation)
    {
        $organisation = \Organisation::resolve($organisation);

        $url = Url::getConfiguredPortalAddress(null, 'dashboard');
        $dashboard = '<a href="' . $url . '">' . _g('Dashboard') . '</a>';

        $content =
            _p(_g('Dear {name},', ['name' => $toName]))
            . _p(
                _g(
                    'Congratulations! You have successfully registered to {portal}!',
                    ['portal' => $organisation->getOrganisationName()]
                )
            )
            . _p(_g('We hope you will enjoy and take advantage of all opportunities available to you.'))
            . _p(
                _g(
                    'To complete the setup of your Portal please go to your {dashboard} and follow the instructions described there.',
                    ['dashboard' => $dashboard]
                )
            )
            . _p('')
            . _p(_g('If you have any questions, please contact us at support@apprecie.com.'))
            . _p(_g('We hope you enjoy your experience with us!'))
            . _p(_g('Best wishes from the Apprecie Team'));

        $template = new WelcomeEmail($toEmail, $content, $organisation);

        $template->build();
        $template->sendEmail();

        if ($template->hasMessages()) {
            $this->appendMessageEx($template);
            return false;
        }

        return true;
    }

    public function welcomeEmailToOrganisationalOwner($toEmail, $toName, $organisation)
    {
        $organisation = \Organisation::resolve($organisation);

        $url = Url::getConfiguredPortalAddress(null, 'dashboard');
        $dashboard = '<a href="' . $url . '">' . _g('Dashboard') . '</a>';
        $url = Url::getConfiguredPortalAddress(null, 'ui');
        $orgSetup = '<a href="' . $url . '">' . _g('User Interface') . '</a>';
        $url = Url::getConfiguredPortalAddress(null, 'people');
        $people = '<a href="' . $url . '">' . _g('People') . '</a>';
        $url = Url::getConfiguredPortalAddress(null, 'profile');
        $userProfile = '<a href="' . $url . '">' . _g('Profile') . '</a>';

        $content =
            _p(_g('Dear {name},', ['name' => $toName]))
            . _p(
                _g(
                    'Congratulations! You have successfully registered to {portal}!',
                    ['portal' => $organisation->getOrganisationName()]
                )
            )
            . _p(_g('We hope you will enjoy and take advantage of all opportunities available to you.'))
            . _p(_g('To get started, why not try one of the following:'))
            . _p(
                _g(
                    '1. Take a look at the {orgbranding} section to customise the branding of your site and upload a logo and login background image to enforce your brand\'s feel around the site.',
                    ['orgbranding' => $orgSetup]
                )
            )
            . _p(
                _g(
                    '2. Go to the {people} page to begin uploading your colleagues and clients onto the system and share the benefits!',
                    ['people' => $people]
                )
            )
            . _p(
                _g(
                    '3. Personalise your profile by selecting your interests, hobbies, and preferences from the appealing range of categories in {userprofile}.',
                    ['userprofile' => $userProfile]
                )
            )
            . _p(
                _g(
                    '4. Check out your {dashboard} to see the latest information about your portal and any tasks you may have to complete.',
                    ['dashboard' => $dashboard]
                )
            )
            . _p(_g('If you have any questions, please contact us at support@apprecie.com'))
            . _p(_g('We hope you enjoy your experience with us!'))
            . _p(_g('Best wishes from the Apprecie Team'));

        $template = new WelcomeEmail($toEmail, $content, $organisation);

        $template->build();
        $template->sendEmail();

        if ($template->hasMessages()) {
            $this->appendMessageEx($template);
            return false;
        }

        return true;
    }

    public function welcomeEmailToInternalAndManagers($toEmail, $toName, $organisation, $user)
    {
        $organisation = \Organisation::resolve($organisation);

        $url = Url::getConfiguredPortalAddress(null, 'dashboard');
        $dashboard = '<a href="' . $url . '">' . _g('Dashboard') . '</a>';
        $url = Url::getConfiguredPortalAddress(null, 'people');
        $people = '<a href="' . $url . '">' . _g('People') . '</a>';
        $url = Url::getConfiguredPortalAddress(null, 'profile');
        $userProfile = '<a href="' . $url . '">' . _g('Profile') . '</a>';
        $url = Url::getConfiguredPortalAddress(null, 'vault');
        $vault = '<a href="' . $url . '">' . _g('Vault') . '</a>';

        $ownerObj = $user->getFirstParent();

        if ($user->getActiveRole()->getName() == 'Manager') {
            $owner = _g('us');
            $ownerEmail = $this->config->mail->defaultSupport;
        } elseif ($ownerObj != null) {
            $owner = $ownerObj->getUserProfile()->getFullName();
            $ownerEmail = $ownerObj->getUserProfile()->getEmail();
        } else {
            $owner = _g('Appreice, on behalf of {portal}', ['portal' => $organisation->getPortal()->getPortalName()]);
            $ownerEmail = $this->config->mail->defaultSupport;
        }

        $content =
            _p(_g('Dear {name},', ['name' => $toName]))
            . _p(
                _g(
                    'Congratulations! You have successfully registered to {portal}!',
                    ['portal' => $organisation->getOrganisationName()]
                )
            )
            . _p(_g('We hope you will enjoy and take advantage of all opportunities available to you.'))
            . _p(_g('To get started, please follow the steps described below:'))
            . _p(
                _g(
                    '1. Go to the {people} page to begin uploading your colleagues and clients onto the system and share the benefits!',
                    ['people' => $people]
                )
            )
            . _p(
                _g(
                    '2. Explore the {vault} which contains a selection of luxury experiences and opportunities available. The range of offers available will continue to grow and evolve with time.',
                    ['vault' => $vault]
                )
            )
            . _p(
                _g(
                    '3. Personalise your profile by selecting your interests, hobbies, and preferences from the appealing range of categories in your {profile}.',
                    ['profile' => $userProfile]
                )
            )
            . _p(
                _g(
                    '4. Check out your {dashboard} to see the latest information about your portal and any tasks you may have to complete.',
                    ['dashboard' => $dashboard]
                )
            )
            . _p(
                _g(
                    '5. You will receive emails informing you of the new offers that have been added to the portal. To opt out, please indicate this via the Notifications Preferences within your {profile} section.',
                    ['profile' => $userProfile]
                )
            )
            . _p(
                _g(
                    'If you have any questions, please contact {owner} at: {ownerEmail} ',
                    ['owner' => $owner, 'ownerEmail' => $ownerEmail]
                )
            )
            . _p(_g('We hope you enjoy your experience with us!'))
            . _p(_g('Best wishes from the Apprecie Team'));

        $template = new WelcomeEmail($toEmail, $content, $organisation);

        $template->build();
        $template->sendEmail();

        if ($template->hasMessages()) {
            $this->appendMessageEx($template);
            return false;
        }

        return true;
    }

    public function postResetPasswordEmail($toEmail, $toName, $organisation)
    {
        $organisation = \Organisation::resolve($organisation);

        $content =
            _p(_g('Dear {name},', ['name' => $toName]))
            . _p(_g('This email is just to confirm that your password has been successfully changed.'))
            . _p('')
            . _p(
                _g(
                    'If this request was not authorised by yourself, please contact us at: {support}.',
                    ['support' => $this->config->mail->defaultSupport]
                )
            )
            . _p(_g('Yours sincerely,'))
            . _p(_g('The Apprecie Team'));

        $template = new PostPasswordResetEmail($toEmail, $content, $organisation);

        $template->build();
        $template->sendEmail();

        if ($template->hasMessages()) {
            $this->appendMessageEx($template);
            return false;
        }

        return true;
    }

    public function resetPasswordEmail($toEmail, $toName, $resetLink, $organisation)
    {
        $organisation = \Organisation::resolve($organisation);

        $beforeContent =
            _p(_g('Dear {name},', ['name' => $toName]))
            . _p(
                _g(
                    'To reset your password to your account on {portalName}, please click the link below:',
                    ['portalName' => $organisation->getPortal()->getPortalName()]
                )
            );

        $afterContent =
            _p(
                _g(
                    'If clicking the link above does not work, please copy and paste the URL {link} into a new browser window instead.',
                    ['url' => $resetLink]
                )
            )
            . _p(
                _g(
                    'If you didn\'t initiate this request, you don\'t need to take any further action and can safely disregard this email.'
                )
            )
            . _p(_g('Yours sincerely,'))
            . _p(_g('The Apprecie Team'));

        $template = new PasswordResetEmail($toEmail, $beforeContent, $afterContent, $resetLink, $organisation);

        $template->build();
        $template->sendEmail();

        if ($template->hasMessages()) {
            $this->appendMessageEx($template);
            return false;
        }

        return true;
    }

    public function signupToOrganisationOwner($toEmail, $toName, $signUpLink, $organisation, $ccEmail = null)
    {
        $aboveContent =
            _p(_g('Dear {name},', ['name' => $toName]))
            . _p(
                _g('Welcome to your customised, on-demand client engagement portal powered by Apprecie.')
            )
            . _p(
                _g('We have carefully prepared a selection of unique events and experiences to help you deepen relationships with discerning clients and prospects and enjoy higher marketing ROI.')
            );

        $belowContent =
            _p(
                _g(
                    'Click the button above to access your personal and secure online portal, customised for {organisation}. You will first be asked to confirm your personal details and then to create a password for your secure login.',
                    ['organisation' => $organisation->getOrganisationName()]
                )
            )
            . _p(
                _g('Once completed, you can then login to the portal from any current browser to access the Vault of opportunities for tailored engagement with high net worth clients and prospects.')
            )
            . _p(
                _g(
                    'If you have any questions, please contact us at: {email}.',
                    ['email' => $this->config->mail->defaultSupport]
                )
            )
            . _p(_g('Best wishes and welcome to Apprecie,'))
            . _p(_g('The Apprecie Team'));

        $template = new SignupEmail($toEmail, $aboveContent, $belowContent, $signUpLink, $organisation, $ccEmail);
        $template->build();
        $template->sendEmail();

        if ($template->hasMessages()) {
            $this->appendMessageEx($template);
            return false;
        }

        return true;
    }

    public function signupToClientMemberOrFamilyMember($toEmail, $toName, $signUpLink, $organisation, $user, $ccEmail = null)
    {
        $userOwner = $user->getFirstParent();
        $altName = ' ' . _g('support') . ' ';
        if ($userOwner != null) {
            $userManager = $userOwner->getUserProfile()->getFullName();
            $email = $userOwner->getUserProfile()->getEmail();
        } else {
            $userManager = '';
            $email = $this->config->mail->defaultSupport;
        }

        $userParent = $user->getFirstParent()->getUserProfile()->getFullName();

        $aboveContent =
            _p(_g('Dear {name},', ['name' => $toName]))
            . _p(
                _g(
                    'Welcome to the {company} portal, a members invite-only, private service designed exclusively for {company} valued clients.',
                    ['company' => $organisation->getOrganisationName()]
                )
            )
            . _p(
                _g(
                    'We have carefully prepared a selection of luxury events and offers that we hope you and your family will enjoy.'
                )
            );

        $belowContent =
            _p(
                _g(
                    'Click the button above to access your personal and secure online portal. You will first be asked to confirm and complete the personal details entered when you were setup, and then create a password for your personal login.',
                    ['company' => $organisation->getOrganisationName()]
                )
            )
            . _p(
                _g(
                    'Once completed, you can then log into the portal using your registered email address and password, and enjoy the benefits of your portal, and extend them to your family.'
                )
            )
            . _p(
                _g(
                    'If you have any questions, please contact {ownername} at: {owneremail}',
                    ['ownername' => $userManager ? : $altName, 'owneremail' => $email]
                )
            )
            . _p(_g('Best wishes and welcome to a new world of benefits!'))
            . _p(_g('{owner}, on behalf of ', ['owner' => $userParent])) . $organisation->getOrganisationName();

        $template = new SignupEmail($toEmail, $aboveContent, $belowContent, $signUpLink, $organisation, $ccEmail);
        $template->build();
        $template->sendEmail();

        if ($template->hasMessages()) {
            $this->appendMessageEx($template);
            return false;
        }

        return true;
    }

    public function signupToManager($toEmail, $toName, $signUpLink, $organisation, $ccEmail)
    {
        $aboveContent =
            _p(_g('Dear {name},', ['name' => $toName]))
            . _p(
                _g('Welcome to your customised, on-demand client engagement portal powered by Apprecie.')
            )
            . _p(
                _g('We have carefully prepared a selection of unique events and experiences to help you deepen relationships with discerning clients and prospects and enjoy higher marketing ROI.')
            );

        $belowContent =
            _p(
                _g(
                    'Click the button above to access your personal and secure online portal, customised for {organisation}. You will first be asked to confirm your personal details and then to create a password for your secure login.',
                    ['organisation' => $organisation->getOrganisationName()]
                )
            )
            . _p(
                _g('Once completed, you can then login to the portal from any current browser to access the Vault of opportunities for tailored engagement with high net worth clients and prospects.')
            )
            . _p(
                _g(
                    'If you have any questions, please contact us at: {email}.',
                    ['email' => $this->config->mail->defaultSupport]
                )
            )
            . _p(_g('Best wishes and welcome to Apprecie,'))
            . _p(_g('The Apprecie Team'));

        $template = new SignupEmail($toEmail, $aboveContent, $belowContent, $signUpLink, $organisation, $ccEmail);
        $template->build();
        $template->sendEmail();

        if ($template->hasMessages()) {
            $this->appendMessageEx($template);
            return false;
        }

        return true;
    }

    public function signupToInternalMember($toEmail, $toName, $signUpLink, $organisation, $owner, $ccEmail = null)
    {
        $aboveContent =
            _p(_g('Dear {name},', ['name' => $toName]))
            . _p(
                _g('Welcome to your customised, on-demand client engagement portal powered by Apprecie.')
            )
            . _p(
                _g('We have carefully prepared a selection of unique events and experiences to help you deepen relationships with discerning clients and prospects and enjoy higher marketing ROI.')
            );

        $belowContent =
            _p(
                _g(
                    'Click the button above to access your personal and secure online portal, customised for {organisation}. You will first be asked to confirm your personal details and then to create a password for your secure login.',
                    ['organisation' => $organisation->getOrganisationName()]
                )
            )
            . _p(
                _g('Once completed, you can then login to the portal from any current browser to access the Vault of opportunities for tailored engagement with high net worth clients and prospects.')
            )
            . _p(
                _g(
                    'If you have any questions, please contact us at: {email}.',
                    ['email' => $this->config->mail->defaultSupport]
                )
            )
            . _p(_g('Best wishes and welcome to Apprecie,'))
            . _p(_g('The Apprecie Team'));

        $template = new SignupEmail($toEmail, $aboveContent, $belowContent, $signUpLink, $organisation, $ccEmail);
        $template->build();
        $template->sendEmail();

        if ($template->hasMessages()) {
            $this->appendMessageEx($template);
            return false;
        }

        return true;
    }

    public function signupToSupplier($toEmail, $toName, $signUpLink, $organisation, $ccEmail = null)
    {
        $aboveContent =
            _p(_g('Dear {name},', ['name' => $toName]))
            . _p(
                _g(
                    'Welcome to {portal} client engagement portal, designed to aid you in reaching your desired demographic.',
                    ['portal' => $organisation->getPortal()->getPortalName()]
                )
            )
            . _p(
                _g(
                    'We are delighted to bring you our comprehensive yet simple Content Management System that will aid you in creating eye-catching content for a wealth of elite clientele in a secure, walled-garden environment.'
                )
            );

        $belowContent =
            _p(
                _g(
                    'Click the button above to access your personal and secure online portal. You will first be asked to confirm and complete the personal details entered when you were setup, and then create a password for your personal login.'
                )
            )
            . _p(
                _g(
                    'Once completed, you can then log into the portal using your registered email address and password, and begin uploading and managing your items and offers'
                )
            )
            . _p(
                _g(
                    'If you have any questions, please contact us at: {email}',
                    ['email' => $this->config->mail->defaultSupport]
                )
            )
            . _p(_g('Best wishes and welcome to your new platform,'))
            . _p(_g('The Apprecie Team'));

        $template = new SignupEmail($toEmail, $aboveContent, $belowContent, $signUpLink, $organisation, $ccEmail);
        $template->build();
        $template->sendEmail();

        if ($template->hasMessages()) {
            $this->appendMessageEx($template);
            return false;
        }

        return true;
    }

    public function sendUserEmail($type, $sender, $recipient = null, $event = null, $data = null)
    {
        $userEmail = new UserEmail($type);
        $content = $userEmail->getProcessedContent($sender, $recipient, $event);
        $options = $userEmail->getOptions();

        if ($recipient) {
            $recipient = \User::resolve($recipient);
            $oldPortal = (new UserEx())->getActiveQueryPortal();
            UserEx::ForceActivePortalForUserQueries($recipient->getPortal());
            $recipientEmail = $recipient->getUserProfile()->getEmail();
            UserEx::ForceActivePortalForUserQueries($oldPortal);
        } else {
            $recipientEmail = $data['recipientEmail'];
        }

        $sender = \User::resolve($sender);
        $oldPortal = (new UserEx())->getActiveQueryPortal();
        UserEx::ForceActivePortalForUserQueries($sender->getPortal());
        $senderName = $sender->getUserProfile()->getFullName();
        $senderEmail = $sender->getUserProfile()->getEmail();
        UserEx::ForceActivePortalForUserQueries($oldPortal);

        $toEmail = ($options && isset($options['sendToSelf']) && $options['sendToSelf']) ? $senderEmail : $recipientEmail;
        $ccEmail = ($options && isset($options['cc']) && $options['cc']) ? $senderEmail : null;

        switch ($type) {
            case EmailTemplateType::SIGNUP_CLIENT:
            case EmailTemplateType::SIGNUP_INTERNAL:
            case EmailTemplateType::SIGNUP_MANAGER:
            case EmailTemplateType::SIGNUP_APPRECIE_SUPPLIER:
            case EmailTemplateType::SIGNUP_AFFILIATE_SUPPLIER:
            case EmailTemplateType::SIGNUP_PORTAL_ADMIN:

                $oldPortal = (new UserEx())->getActiveQueryPortal();
                UserEx::ForceActivePortalForUserQueries($recipient->getPortal());
                $signUpLink = Url::getConfiguredPortalAddress(
                    $recipient->getPortal(),
                    'signup',
                    'index',
                    [$recipient->getPortalUser()->getRegistrationHash()]
                );
                UserEx::ForceActivePortalForUserQueries($oldPortal);

                $template = new SignupEmail($toEmail, $content['aboveContent']['content'], $content['belowContent']['content'], $signUpLink, $recipient->getOrganisation(), $ccEmail);
                break;
            case EmailTemplateType::SUGGESTION_ON_PORTAL:

                $event = \Event::resolve($event);

                if ($event->getItem()->getIsByArrangement()) {
                    $link = Url::getConfiguredPortalAddress($recipient->getPortal(), 'vault', 'arranged', [$event->getItemId()]);
                } else {
                    $link = Url::getConfiguredPortalAddress($recipient->getPortal(), 'vault', 'event', [$event->getItemId()]);
                }

                $template = new SuggestionEmail($toEmail, $content['content']['content'], $sender->getOrganisation(), $event, $link, $ccEmail);
                break;
            case EmailTemplateType::SUGGESTION_OFF_PORTAL:
                $event = \Event::resolve($event);
                $template = new SuggestionEmail($toEmail, $content['content']['content'], $sender->getOrganisation(), $event, false, $ccEmail);
                break;
            case EmailTemplateType::INVITATION:
                $event = \Event::resolve($event);
                $template = new InviteEmail($toEmail, $content['aboveContent']['content'], $content['belowContent']['content'], $data['rsvpLink'], $sender->getOrganisation(), $event, $senderName, $ccEmail);
                break;
            case EmailTemplateType::POST_EVENT_FOLLOW_UP:
                $template = new GenericEmail($toEmail, $content['content']['content'], $sender->getOrganisation(), _g('Follow-up of an event you attended recently'));
                break;
        }

        if (isset($template)) {

            $template->build();
            $template->sendEmail();

            if ($template->hasMessages()) {
                $this->appendMessageEx($template);
                return false;
            }

            return true;
        }

        return false;
    }

    public function sendSignUpByRole($toEmail, $toName, $role, $signUpLink, $organisation, $user, $ccEmail = null)
    {
        $result = null;

        switch ($role) {
            case 'PortalAdministrator' : //Organisational Admin /  organisation owner
            {
                $result = static::signupToOrganisationOwner($toEmail, $toName, $signUpLink, $organisation, $ccEmail);
                break;
            }
            case 'Manager' :
            {
                $result = static::signupToManager($toEmail, $toName, $signUpLink, $organisation, $ccEmail);
                break;
            }
            case 'ApprecieSupplier':
            case 'AffiliateSupplier':
            {
                $result = static::signupToSupplier($toEmail, $toName, $signUpLink, $organisation, $ccEmail);
                break;
            }
            case 'Internal' :
            {
                $user = \User::resolve($user);
                $result = static::signupToInternalMember(
                    $toEmail,
                    $toName,
                    $signUpLink,
                    $organisation,
                    $user->getFirstParent(),
                    $ccEmail
                );
                break;
            }
            case 'Client' :
            {
                $result = static::signupToClientMemberOrFamilyMember(
                    $toEmail,
                    $toName,
                    $signUpLink,
                    $organisation,
                    $user,
                    $ccEmail
                );
                break;
            }
            default :
                {
                $this->appendMessageEx(_g('Cannot send sign-up email as the user has no role'));
                $result = false;
                }
        }

        return $result;
    }

    public function sendWelcomeByRole($toEmail, $toName, $role, $organisation, $user)
    {
        $result = null;

        switch ($role) {
            case 'PortalAdministrator' : //Organisational Admin /  organisation owner
            {
                $result = static::welcomeEmailToOrganisationalOwner($toEmail, $toName, $organisation);
                break;
            }
            case 'Manager' :
            case 'Internal':
            {
                $result = static::welcomeEmailToInternalAndManagers($toEmail, $toName, $organisation, $user);
                break;
            }
            case 'ApprecieSupplier':
            case 'AffiliateSupplier':
            {
                $result = static::welcomeEmailToSupplier($toEmail, $toName, $organisation);
                break;
            }
            case 'Client' :
            {
                $result = static::welcomeEmailToClientMembers($toEmail, $toName, $organisation, $user);
                break;
            }
            default :
                {
                $this->appendMessageEx(_g('Cannot send welcome email as the user has no role, or role not implemented'));
                $result = false;
                }
        }

        return $result;
    }
} 