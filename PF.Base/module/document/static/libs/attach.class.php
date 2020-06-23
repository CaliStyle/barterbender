<?php
defined('PHPFOX') or exit('NO DICE!');

require_once PHPFOX_DIR_LIB_CORE . 'mail' . PHPFOX_DS . 'interface.class.php';

class Phpmailer_Attach implements Phpfox_Mail_Interface
{
    /**
     * PHPMailer Object
     *
     */
    private $_oMail = null;

    /**
     * Class constructor that loads PHPMailer class and sets all the needed variables.
     *
     * @return mixed FALSE if we cannot load PHPMailer, or NULL if we were.
     */
    public function __construct()
    {
        if (Phpfox::getParam('core.method') == "mail") {
            $this->_oMail = new PHPMailer;
            $this->_oMail->From = (Phpfox::getParam('core.email_from_email') ? Phpfox::getParam('core.email_from_email') : 'server@localhost.com');
            $this->_oMail->FromName = (Phpfox::getParam('core.mail_from_name') === null ? Phpfox::getParam('core.site_title') : Phpfox::getParam('core.mail_from_name'));
            $this->_oMail->WordWrap = 75;
            $this->_oMail->CharSet = 'utf-8';
        } else {
            if (Phpfox::getParam('core.method') == "smtp") {
                $this->_oMail = new PHPMailer;
                $this->_oMail->From = (Phpfox::getParam('core.email_from_email') ? Phpfox::getParam('core.email_from_email') : 'server@localhost');
                $this->_oMail->FromName = (Phpfox::getParam('core.mail_from_name') ? Phpfox::getParam('core.mail_from_name') : Phpfox::getParam('core.site_title'));
                if (Phpfox::getParam('core.mail_smtp_authentication')) {
                    $this->_oMail->SMTPAuth = true;
                    $this->_oMail->Username = Phpfox::getParam('core.mail_smtp_username');
                    $this->_oMail->Password = Phpfox::getParam('core.mail_smtp_password');
                }

                $this->_oMail->Port = Phpfox::getParam('core.mail_smtp_port');
                $this->_oMail->Host = Phpfox::getParam('core.mailsmtphost');
                $this->_oMail->Mailer = "smtp";
                $this->_oMail->WordWrap = 75;
                $this->_oMail->CharSet = 'utf-8';
            }
        }
    }

    /**
     * Sends out an email.
     *
     * @param mixed $mTo Can either be a persons email (STRING) or an ARRAY of emails.
     * @param string $sSubject Subject message of the email.
     * @param string $sTextPlain Plain text of the message.
     * @param string $sTextHtml HTML version of the message.
     * @param string $sFromName Name the email is from.
     * @param string $sFromEmail Email the email is from.
     * @return bool TRUE on success, FALSE on failure.
     */
    public function send($mTo, $sSubject, $sTextPlain, $sTextHtml, $sFromName = null, $sFromEmail = null)
    {
        if (is_array($mTo)) {
            foreach ($mTo as $email) {
                $this->_oMail->AddAddress($email, $email);
            }
        } else {
            $this->_oMail->AddAddress($mTo, $mTo);
        }

        $this->_oMail->Subject = $sSubject;
        $this->_oMail->Body = $sTextHtml;
        $this->_oMail->AltBody = $sTextPlain;

        if (isset($_SESSION['document_attachment']) && $_SESSION['document_attachment'] != "") {
            $this->_oMail->AddAttachment($_SESSION['document_attachment'], $_SESSION['document_name']);
        }


        if ($sFromName !== null) {
            $this->_oMail->FromName = $sFromName;
        }

        if ($sFromEmail !== null) {
            $this->_oMail->From = $sFromEmail;
        }

        if (!$this->_oMail->Send()) {
            $this->_oMail->ClearAddresses();
            return false;
            return Phpfox_Error::trigger($this->_oMail->ErrorInfo, E_USER_ERROR);
        }

        $this->_oMail->ClearAddresses();

        return true;
    }
}