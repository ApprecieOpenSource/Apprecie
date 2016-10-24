<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 20/10/14
 * Time: 12:46
 */

/**
 * The PortalUser entity, sits in the private portal tables and contains a reference, and a possible link
 * to a UserLogin and a UserProfile of a given user.  not all users will have a userProfile or UserLogin.
 *
 * The PortalUser is the most useful user element in most cases within the Apprecie system.  The reference will often be
 * used to identify an otherwise anonymous user.
 *
 * Class PortalUser
 */
class PortalUser extends \Apprecie\Library\Users\ApprecieUserBase
{
    protected $portalId, $portalUserId, $profileId, $loginId, $reference, $signupSent, $welcomeSent, $isAnonymous, $activePortalId, $familyQuotaAvailable, $registrationHash, $passwordRecoverySent, $passwordRecoveryHash;

    /**
     * @param mixed $passwordRecoveryHash
     */
    public function setPasswordRecoveryHash($passwordRecoveryHash)
    {
        $this->passwordRecoveryHash = $passwordRecoveryHash;
    }

    /**
     * @param mixed $passwordRecoverySent
     */
    public function setPasswordRecoverySent($passwordRecoverySent)
    {
        $this->passwordRecoverySent = $passwordRecoverySent;
    }

    /**
     * @return mixed
     */
    public function getPasswordRecoverySent()
    {
        return $this->passwordRecoverySent;
    }

    /**
     * @param mixed $familyQuotaAvailable
     */
    public function setFamilyQuotaAvailable($familyQuotaAvailable)
    {
        $this->familyQuotaAvailable = $familyQuotaAvailable;
    }

    public function setRegistrationHash($hash)
    {
        $this->registrationHash = $hash;
    }

    public function sendWelcomeEmail()
    {
        $profile = $this->getUserProfile();
        if ($profile == null) {
            throw new \Phalcon\Exception('This user has no profile, I dont know who to send the email to!!');
        }

        if ($profile->getEmail() == null) {
            throw new \Phalcon\Exception('This users profile does not contain an email address.');
        }

        $this->welcomeSent = date('Y-m-d G:i:s');

        if (!$this->update()) {
            return false;
        }

        $email = new \Apprecie\Library\Mail\EmailUtility();

        $result = $email->sendWelcomeByRole(
            $profile->getEmail(),
            $this->getUserProfile()->getFirstname(),
            $this->getActiveRole()->getName(),
            $this->getUser()->getOrganisation(),
            $this->getUser()
        );

        if (!$result) {
            $this->appendMessageEx($email);
            $this->logActivity("Email send failure", _ms($this));
            return false;
        }

        return true;
    }

    public function sendPasswordRecoveryEmail()
    {
        $profile = $this->getUserProfile();
        if ($profile == null) {
            throw new \Phalcon\Exception('This user has no profile, I dont know who to send the email to!!');
        }

        if ($profile->getEmail() == null) {
            throw new \Phalcon\Exception('This users profile does not contain an email address.');
        }

        $this->passwordRecoveryHash = (new \Apprecie\Library\Security\Authentication())->generateRegistrationToken();
        $this->passwordRecoverySent = date('Y-m-d G:i:s');

        if (!$this->update()) {
            return false;
        }

        $url = \Apprecie\Library\Request\Url::getConfiguredPortalAddress(
            $this->getPortalId(),
            'login',
            'reset',
            [$this->getPasswordRecoveryHash()]
        );

        $email = new \Apprecie\Library\Mail\EmailUtility();
        $result = $email->resetPasswordEmail(
            $this->getUserProfile()->getEmail(),
            $this->getUserProfile()->getFirstName(),
            $url,
            $this->getUser()->getOrganisation()
        );

        if (!$result) {
            $this->appendMessageEx($email);
            $this->logActivity("Email send failure", _ms($this));
            return false;
        }

        return true;
    }

    public function getPortalId()
    {
        return $this->portalId;
    }

    public function setPortalId()
    {
        $portalGUID = static::getSourcePrefix();
        $this->portalId = \Portal::findFirst("portalGUID = '{$portalGUID}'")->getPortalId();
    }

    /**
     * @return mixed
     */
    public function getPasswordRecoveryHash()
    {
        return $this->passwordRecoveryHash;
    }

    /**
     * Note that calling this method will result in the profile being saved!!
     */
    public function sendRegistrationEmail()
    {
        $profile = $this->getUserProfile();

        if ($profile == null) {
            throw new \Phalcon\Exception('This user has no profile, I don\'t know who to send the email to!');
        }

        if ($profile->getEmail() == null) {
            throw new \Phalcon\Exception('This users profile does not contain an email address.');
        }

        if ($this->registrationHash == null) {
            throw new \Phalcon\Exception('This user does not have access to the Portal.');
        }

        $this->signupSent = date('Y-m-d G:i:s');

        if (!$this->update()) {
            return false;
        }

        $email = new \Apprecie\Library\Mail\EmailUtility();

        $result = $email->sendUserEmail(
            \Apprecie\Library\Mail\EmailTemplateType::getSignupTemplateTypeByRoleName($this->getActiveRole()->getName()),
            $this->getDI()->getDefault()->get('auth')->getAuthenticatedUser(),
            $this->getUser()
        );

        if (!$result) {
            $this->appendMessageEx($email);
            $this->logActivity("Email send failure", _ms($this));
            return false;
        }

        return true;
    }

    public function getRegistrationHash()
    {
        return $this->registrationHash;
    }

    public function sendEventSuggestion($event)
    {
        $event = Event::resolve($event);

        if ($event->getTier() > $this->getUser()->getTier()) {
            $this->appendMessageEx(
                _g(
                    'It is not possible for this user to see the suggested item on portal, as the users tier is too low.'
                )
            );
            return false;
        }

        //send the user an email
        $profile = $this->getUserProfile();

        if ($profile == null) {
            $this->appendMessageEx('This user has no profile, I do not know who to send the email to!!');
            return false;
        }

        if ($profile->getEmail() == null) {
            $this->appendMessageEx('This users profile does not contain an email address.');
            return false;
        }

        //@todo gh when we allow cross portal suggestions this will explode
        $sender = $this->getDI()->getDefault()->get('auth')->getAuthenticatedUser();

        $lastPortal = (new \Apprecie\Library\Users\UserEx())->getActiveQueryPortal();
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($sender->getPortalId());
        $senderProfile = $sender->getUserProfile();
        $senderFullName = $senderProfile->getFullName();
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($lastPortal);

        $email = new \Apprecie\Library\Mail\EmailUtility();

        $result = $email->sendUserEmail(
            \Apprecie\Library\Mail\EmailTemplateType::SUGGESTION_ON_PORTAL,
            $this->getDI()->getDefault()->get('auth')->getAuthenticatedUser(),
            $this->getUser(),
            $event
        );

        if (!$result) {
            $this->appendMessageEx($email);
            $this->logActivity("Email send failure", _ms($this));
            return false;
        }

        if (\Apprecie\Library\Mail\Templates\EmailTemplate::getBlockSend() === false) {
            $thread = new MessageThread();
            $thread->setFirstRecipientUser($this->getUserId());
            $thread->setStartedByUser($sender->getUserId());
            $thread->setType(\Apprecie\Library\Messaging\MessageThreadType::SUGGESTION);

            if (!$thread->create()) {
                $this->appendMessageEx($thread);
                return false;
            }

            $message = new Message();
            $message->setBody(
                'You have received a suggestion for the Item referenced above. Please click on the link above to see the Item.'
            );
            $message->setReferenceItem($event->getItemId());
            $message->setTargetUser($this->getUserId());
            $message->setTitle('Event suggestion');
            $message->setSourceDescription($senderFullName);
            $message->setSourceUser($sender->getUserId());
            $message->setSourcePortal($sender->getPortalId());

            if (!$message->create()) {
                $this->appendMessageEx($message);
                return false;
            }

            if (!$thread->addMessage($message)) {
                $this->appendMessageEx($thread);
                return false;
            }

            //add item to the user vault
            $organisation = $this->getUser()->getOrganisation();
            if (!$organisation->addEventToVault($event, $this->getUser(), false, false, null, $sender)) {
                $this->appendMessageEx($organisation);
                return false;
            }
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getFamilyQuotaAvailable()
    {
        return $this->familyQuotaAvailable;
    }

    /**
     * @param mixed $isAnonymous
     */
    public function setIsAnonymous($isAnonymous)
    {
        $this->isAnonymous = $isAnonymous;
    }

    /**
     * @return mixed
     */
    public function getIsAnonymous()
    {
        return $this->isAnonymous;
    }

    /**
     * @param mixed $loginId
     */
    public function setLoginId($loginId)
    {
        $this->loginId = $loginId;
    }

    /**
     * @return mixed
     */
    public function getLoginId()
    {
        return $this->loginId;
    }

    /**
     * @param mixed $portalUserId
     */
    public function setPortalUserId($portalUserId)
    {
        $this->portalUserId = $portalUserId;
    }

    /**
     * @return mixed
     */
    public function getPortalUserId()
    {
        return $this->portalUserId;
    }

    /**
     * @param mixed $profileId
     */
    public function setProfileId($profileId)
    {
        $this->profileId = $profileId;
    }

    /**
     * @return mixed
     */
    public function getProfileId()
    {
        return $this->profileId;
    }

    /**
     * @param mixed $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return mixed
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param mixed $signupSent
     */
    public function setSignupSent($signupSent)
    {
        $this->signupSent = $signupSent;
    }

    /**
     * @return mixed
     */
    public function getSignupSent()
    {
        return $this->signupSent;
    }

    /**
     * @param mixed $welcomeSent
     */
    public function setWelcomeSent($welcomeSent)
    {
        $this->welcomeSent = $welcomeSent;
    }

    /**
     * @return mixed
     */
    public function getWelcomeSent()
    {
        return $this->welcomeSent;
    }

    public function getSource()
    {
        return '_' . static::getSourcePrefix() . '_portalusers';
    }

    function onConstruct()
    {
        parent::onConstruct();
        $this->setDefaultFields('familyQuotaAvailable');
        $this->setPortalId();
    }

    public function initialize()
    {
        $this->hasOne('profileId', 'UserProfile', 'profileId');
        $this->hasOne('loginId', 'UserLogin', 'loginId');
        $this->belongsTo(array('portalId', 'portalUserId'), 'User', array('portalId', 'portalUserId'));
    }
}