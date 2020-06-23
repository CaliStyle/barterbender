<?php
//De-active user token in this browser
$sCookie = Phpfox::getCookie('ync_web_push_token');
if (!empty($sCookie)) {
    $aCookie = json_decode($sCookie, true);
    if (!empty($aCookie['token']) && !empty($aCookie['browser'])) {
        Phpfox::getService('yncwebpush.token.process')->addUserToken($aCookie['token'], $aCookie['browser'], 0, false);
    }
}