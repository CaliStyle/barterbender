<?php

$mobileReq1 = \Phpfox_Request::instance()->get('req1');
$mobileReq2 = \Phpfox_Request::instance()->get('req2');

if ($mobileReq1 == 'mobile' && $mobileReq2 == 'token') {
    if (
        (Phpfox::isUser() && Phpfox::getUserBy('status_id') == 1 && Phpfox::getParam('user.logout_after_change_email_if_verify') && !isset($bEmailVerification)) ||
        (Phpfox::isUser() && in_array(Phpfox::getUserBy('view_id'), [2, 1]))
    ) {
        \Phpfox_Request::instance()->set('req1', 'user');
    }
}