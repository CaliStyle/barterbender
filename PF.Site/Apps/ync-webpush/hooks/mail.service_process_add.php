<?php
if (!$bIsThreadReply && Phpfox::getParam('mail.threaded_mail_conversation')) {
    $iUserId = $aVals['to'];
} else {
    foreach ($aThreadUsers as $aThreadUser) {
        if ($aThreadUser['user_id'] == Phpfox::getUserId()) {
            continue;
        }
        $iUserId = $aThreadUser['user_id'];
    }
}
if (!Phpfox::getService('yncwebpush.setting')->checkPushNotificationSetting($iUserId, 'mail.new_message')) {
    $aTokens = Phpfox::getService('yncwebpush.token')->getAllUserToken($iUserId, true, true);
    if ($aTokens) {
        $sTitle = _p('full_name_sent_you_a_message', ['full_name' => Phpfox::getUserBy('full_name')]);
        $sContent = Phpfox::getLib('parse.input')->clean(strip_tags(Phpfox::getLib('parse.bbcode')->cleanCode(str_replace(array(
            '&lt;',
            '&gt;'
        ), array('<', '>'), $aVals['message']))));
        $aUser = Phpfox::getUserBy();
        if (!empty($aUser['user_image'])) {
            $sImage = Phpfox::getLib('image.helper')->display([
                'user' => $aUser,
                'suffix' => '_50_square',
                'return_url' => true
            ]);
            $sImage = str_replace('http://', 'https://', $sImage);
        } else {
            $sImage = '';
        }

        Phpfox::getService('yncwebpush.notification.process')->pushNotification($sTitle, $sContent, $sLink, $sImage,
            $aTokens);
    }
}