<?php

defined('PHPFOX') or exit('NO DICE!');

if (($sSentCookie = Phpfox::getCookie('invited_by_email_form'))) {
    if (!(Phpfox::getLib('mail')->checkEmail($sSentCookie))) {
        $this->template()->assign('aForms', array('email' => ''));
        if (Phpfox::getParam('user.invite_only_community')) {
            if (preg_match('/Anonymous User/', $sSentCookie) || preg_match('/Anonymous Users/', $sSentCookie) || preg_match('/twitter/', $sSentCookie) || preg_match('/linkedin/', $sSentCookie)) {
                Phpfox::setCookie('invite_only_pass', $sSentCookie);
                if (!isset($_SESSION['pass_invite']) || $_SESSION['pass_invite'] == false) {
                    $_SESSION['pass_invite'] = true;
                    Phpfox::getLib("url")->send('user.register');
                }
            }
        }
    }
}

/**
 * process with somethign other but not field.
 */


