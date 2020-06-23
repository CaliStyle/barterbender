<?php

if (!empty($iFriendId)) {
    if (!Phpfox::getService('yncwebpush.setting')->checkPushNotificationSetting($iFriendId,
        'friend.new_friend_request')
    ) {
        $aTokens = Phpfox::getService('yncwebpush.token')->getAllUserToken($iFriendId, true, true);
        if ($aTokens) {
            $sTitle = _p('new_friend_request');
            $sContent = _p('full_name_added_you_as_a_friend', ['full_name' => Phpfox::getUserBy('full_name')]);
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
}