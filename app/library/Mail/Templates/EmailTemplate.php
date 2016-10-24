<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 19/02/15
 * Time: 14:27
 */

namespace Apprecie\Library\Mail\Templates;

use Apprecie\Library\Mail\Email;
use Apprecie\Library\Messaging\PrivateMessageQueue;
use Phalcon\Mvc\View;

abstract class EmailTemplate extends PrivateMessageQueue
{
    protected $_from, $_to, $_subject, $_cc, $_attachmentFile, $_attachmentName;
    protected $_htmlBody = null;
    protected static $_blockSend = false;
    protected static $_lastEmailBody = '';

    public static function getLastEmailBody()
    {
        return static::$_lastEmailBody;
    }

    public static function setBlockSend($block)
    {
        static::$_blockSend = $block;
    }

    public static function getBlockSend()
    {
        return static::$_blockSend;
    }

    public function __construct($to, $from, $subject, $cc = null, $attachmentFile = null, $attachmentName = null)
    {
        $this->_from = $from;
        $this->_to = $to;
        $this->_subject = $subject;
        $this->_cc = $cc;
        $this->_attachmentFile = $attachmentFile;
        $this->_attachmentName = $attachmentName;
    }

    /**
     * Template is always expected to take the name of index.volt within the actual designated folder.
     *
     * @param string $templateName located in the views/email  folder i.e. singnup/manager
     * @param array $styleTokens
     * @param array $contentTokens
     * @return mixed
     */
    protected function getTemplateHTML($templateName, $styleTokens = array(), array $contentTokens)
    {
        $currentLayout = $this->view->getLayout();
        $disabled = $this->view->isDisabled();

        $this->view->reset();
        $this->view->setLayout('blank');

        foreach ($styleTokens as $token => $style) {
            $this->view->$token = $style;
        }

        $html = $this->view->getRender('email/' . $templateName, 'index');

        if (is_array($contentTokens)) {
            foreach ($contentTokens as $key => $value) {
                $html = str_replace('{' . $key . '}', $value, $html);
            }
        }

        $this->view->reset();
        $this->view->setLayout($currentLayout);
        if ($disabled) {
            $this->view->disable();
        }

        return $html;
    }

    /**
     * responsible for settings the result of getTemplateHTML() to the $_htmlBody property
     * @return mixed
     */
    public abstract function build();

    public function sendEmail()
    {
        if ($this->_htmlBody == null) {
            throw new \LogicException('Please call build and set the htmlbody before calling send');
        }

        static::$_lastEmailBody = $this->_htmlBody;

        if (!static::$_blockSend) {
            $email = new Email();
            $result = $email->sendEmail($this->_from, $this->_to, $this->_subject, $this->_htmlBody, null, $this->_cc, $this->_attachmentFile, $this->_attachmentName);

            if ($result->body->message != 'success') {
                $this->appendMessageEx('Failed to send Email ' . $result->body->message);
                return false;
            }
        }

        return true;
    }
}