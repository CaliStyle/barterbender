<?php

defined('PHPFOX') or exit('NO DICE!');

class Fanot_Component_Ajax_Ajax extends Phpfox_Ajax
{

    public function update()
    {
        Phpfox::isUser(true);
        $iUserId = (int) Phpfox::getUserId();
        $iExpired = (int) Phpfox::getCookie('fExpiredTime_' . $iUserId);
        $sHtml = Phpfox::getCookie('fNotifications_' . $iUserId);
        $sHtml = str_replace("'", "\'", $sHtml);
        if (trim($sHtml) != '' && $iExpired >= PHPFOX_TIME)
        {
            $iTotal = (int) Phpfox::getCookie('fTotalNotifications_' . $iUserId);
            if ($iTotal > 0)
            {
                $this->call('$(\'#js_total_new_notifications\').html(\'' . (int) $iTotal . '\').css({display: \'block\'}).show();');
            }

            $iTotal2 = (int) Phpfox::getCookie('fTotalNotifications2_' . $iUserId);
            if ($iTotal2 > 0)
            {
                Phpfox::getLib('ajax')->call('$(\'#js_total_new_friend_requests\').html(\'' . (int) $iTotal2 . '\').css({display: \'block\'}).show();');
            }
            $this->call('$Core.fanot.setTitle(\'' . $sHtml . '\');');
            return true;
        }
        else
        {
            $iTotal = Phpfox::getService('notification')->getUnseenTotal();
            $iTotal2 = Phpfox::getService('friend.request')->getUnseenTotal();

            Phpfox::massCallback('getGlobalNotifications');
            Phpfox::getBlock('fanot.link');
            $sHtml = $this->getContent(false);
            $sHtml = str_replace("'", "\'", $sHtml);
            if (trim($sHtml) != '')
            {
                $iExpired = PHPFOX_TIME + 3;
                Phpfox::setCookie('fExpiredTime_' . $iUserId, $iExpired, $iExpired);
                Phpfox::setCookie('fNotifications_' . $iUserId, $sHtml, $iExpired);
                Phpfox::setCookie('fTotalNotifications_' . $iUserId, $iTotal, $iExpired);
                Phpfox::setCookie('fTotalNotifications2_' . $iUserId, $iTotal2, $iExpired);
                $this->call('$Core.fanot.setTitle(\'' . $sHtml . '\');');
                return true;
            }
            else
            {
                $iExpired = PHPFOX_TIME + 0;
                Phpfox::setCookie('fExpiredTime_' . $iUserId, 0, $iExpired);
                Phpfox::setCookie('fNotifications_' . $iUserId, '', $iExpired);
                Phpfox::setCookie('fTotalNotifications_' . $iUserId, 0, $iExpired);
                Phpfox::setCookie('fTotalNotifications2_' . $iUserId, 0, $iExpired);
            }
        }
        return false;
    }

    public function hide()
    {
        $iId = (int) $this->get('id');
        Phpfox::getService('fanot')->hide($iId, 1);
    }

    public function updateSeen()
    {
        Phpfox::isUser(true);
        $iId = $this->get('id');
        $sLink = $this->get('l');
        $iType = (int) $this->get('t');
        if ((int) $iId)
        {
            Phpfox::getService('fanot')->updateSeen($iId, $iType);
            if (!empty($sLink))
            {
                $this->call('window.location.href = \'' . $sLink . '\';');
            }
            return true;
        }
        return false;
    }

}

?>