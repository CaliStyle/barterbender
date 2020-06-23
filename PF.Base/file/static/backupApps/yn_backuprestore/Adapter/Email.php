<?php
/**
 * User: huydnt
 * Date: 05/01/2017
 * Time: 15:50
 */

namespace Apps\yn_backuprestore\Adapter;

include "libs/vendor/autoload.php";
use Phpfox;

class Email extends Abstracts
{
    protected $oMailer;

    public function __construct()
    {
        $this->oMailer = new \PHPMailer();
        $this->oMailer->IsSMTP();
        $this->oMailer->Host = setting('core.mailsmtphost');
        $this->oMailer->Port = setting('core.mail_smtp_port');
        $this->oMailer->SMTPAuth = true;
        $this->oMailer->Username = setting('core.mail_smtp_username');
        $this->oMailer->Password = setting('core.mail_smtp_password');
        $this->oMailer->setFrom(setting('core.email_from_email', 'noname@noname.com'),
            setting('core.mail_from_name', 'YouNet Backup and Restore'));

    }

    /**
     * Send file via Email
     * @param $sReceiverEmail
     * @param $sFilePath
     * @param $iBackupId
     * @return string
     */
    public function upload($sReceiverEmail, $sFilePath, $iBackupId)
    {
        $aBackup = Phpfox::getService('ynbackuprestore.backup')->getBackup($iBackupId);
        if (is_array($sReceiverEmail)) {
            foreach ($sReceiverEmail as $key => $sEmail) {
                $this->oMailer->addAddress($sEmail);
            }
        } else {
            $this->oMailer->addAddress($sReceiverEmail);
        }
        $this->oMailer->isHTML(true);
        $this->oMailer->Subject = 'Backup and Restore - Backup ';
        $this->oMailer->Subject = 'Backup and Restore - Backup ' . $aBackup['title'];
        $this->oMailer->Body = _p('The attachment is backup of your site. Please save it to recover in future.');
        $this->oMailer->AddAttachment($sFilePath);

        if (!$this->oMailer->send()) {
            return "Mailer Error: " . $this->oMailer->ErrorInfo;
        } else {
            return "Message has been sent successfully " . $sReceiverEmail;
        }
    }
}