<?php

$invite_only_contactimporter = 0;
if(Phpfox::getParam('user.invite_only_community')){
    $invite_only_contactimporter = 1;
    $email_contactimporter = $aVals['email'];
    $aVals['email'] = Phpfox::getCookie('invited_by_email_form');
    define('PHPFOX_SKIP_EMAIL_INSERT',true);
}

?>
