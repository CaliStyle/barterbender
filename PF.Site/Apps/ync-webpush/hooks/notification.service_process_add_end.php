<?php

if ($sType != 'yncwebpush_admin_push') {
    $bNotAllow = Phpfox::getService('yncwebpush')->isDisablePushNotification($sType, $iOwnerUserId);

    if (!$bNotAllow) {
        $aTokens = Phpfox::getService('yncwebpush.token')->getAllUserToken($iOwnerUserId, true, true);
        if ($aTokens) {
            //Get notification detail
            $aRow = $this->database()->select('n.*, n.user_id as item_user_id, ' . Phpfox::getUserField())
                ->from($this->_sTable, 'n')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = n.owner_user_id')
                ->where('n.notification_id = ' . (int)$iId)
                ->execute('getRow');

            if ($aRow) {
                Phpfox::getService('user.auth')->setUserId($aRow['item_user_id']);

                //Call callback to get message / link
                $aCallBack = Phpfox::callback($aRow['type_id'] . '.getNotification', $aRow);
                if ($aCallBack !== false) {
                    if (!empty($aRow['user_image'])) {
                        $sImage = Phpfox::getLib('image.helper')->display([
                            'user' => $aRow,
                            'suffix' => '_50_square',
                            'return_url' => true
                        ]);
                        $sImage = str_replace('http://', 'https://', $sImage);
                    } else {
                        $sImage = '';
                    }

                    if (isset($aCallBack['message'])) {
                        $aCallBack['message'] = preg_replace('/<span\sclass="drop_data_user">(.*?)<\/span>/', '$1',
                            $aCallBack['message']);
                        $aCallBack['message'] = preg_replace('/<b>(.*?)<\/b>/', '$1',
                            $aCallBack['message']);
                    }

                    Phpfox::getService('yncwebpush.notification.process')->pushNotification(Phpfox::getParam('core.site_title'),
                        $aCallBack['message'], $aCallBack['link'], $sImage, $aTokens, '', $iId);
                }
                Phpfox::getService('user.auth')->setUserId(null);
            }
        }
    }
}