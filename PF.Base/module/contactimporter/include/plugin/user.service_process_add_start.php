<?php


if(isset($invite_only_contactimporter) && $invite_only_contactimporter==1){
    $aVals['email'] = $aInsert['email'] = $email_contactimporter;
    Phpfox::setCookie('invite_only_pass','');
    Phpfox::setCookie('invited_by_email_form','');
    Phpfox::setCookie('invited_by_email','');
    define('PHPFOX_SKIP_EMAIL_INSERT',false);
    if(isset($_SESSION['pass_invite'])){
        unset($_SESSION['pass_invite']);
    }
}

?>
