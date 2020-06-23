<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$sCookieEmailForm = Phpfox::getCookie('invited_by_email_form');

$sCookieEmail = Phpfox::getCookie('invited_by_email');

if (!empty($sCookieEmailForm) && $iId)
{
	Phpfox::getService('contactimporter') -> addSocialJoined($iId, $sCookieEmail, $sCookieEmailForm);
}

?>