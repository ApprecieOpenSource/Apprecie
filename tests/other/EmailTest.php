<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 01/12/14
 * Time: 18:50
 */

class EmailTwoLoginTest extends \Apprecie\Library\Testing\ApprecieTwoLoginTestBase
{
    public function testEmailSend()
    {
        /*$email = new Apprecie\Library\Mail\Email();
        $result = $email->sendEmail('admin@born2code.co.uk', 'gavin@born2code.co.uk', 'Something cool', 'just a simple test email');
        $this->assertTrue($result->message == 'success');
        _ep($email->getMessagesString());*/
    }

    public function testSignup()
    {
        $user = $this->getTestUserLogin()->getUser();
        $user->setChildOf($this->getSecondTestUserLogin()->getUser());
        $user->addRole('Client');
        $user->update();
        $this->assertTrue($this->getTestUserLogin()->getPortalUser()->sendRegistrationEmail());
    }

    public function testPasswordRecoveryEmail()
    {
        $this->assertTrue($this->getTestUserLogin()->getPortalUser()->sendPasswordRecoveryEmail());
    }

    public function testWelcomeEmail()
    {
        $user = $this->getTestUserLogin()->getUser();
        $user->addRole('Manager');
        $user->setChildOf($this->getSecondTestUserLogin()->getUser());
        $user->getPortalUser()->sendWelcomeEmail();
        //_epm($user->getPortalUser());
        $email = new \Apprecie\Library\Mail\EmailUtility();
        //$email->signupToManager('etheroverlord@gmail.com', 'Gavin', 'something', $this->getTestPortal()->getOwningOrganisation());
        //$email->welcomeEmailToOrganisationalOwner('etheroverlord@gmail.com', 'Gavin', $this->getTestPortal()->getOwningOrganisation());
        $email = new \Apprecie\Library\Mail\EmailUtility();
        $email->sendEventSuggestionEmail('gavin@born2code.co.uk', 'Gavin', $this->getTestPortal()->getOwningOrganisation(), 'somedude@born2code.co.uk', 'Barry', 2023);
    }
} 