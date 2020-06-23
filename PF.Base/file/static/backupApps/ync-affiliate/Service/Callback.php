<?php

/**
 * Created by PhpStorm.
 * User: dai
 * Date: 20/01/2017
 * Time: 10:44
 */

namespace Apps\YNC_Affiliate\Service;

use Phpfox;

Class Callback extends \Phpfox_Service
{
    public function getNotificationApprovedaccount($aNotification)
    {
        $aRow = db()->select('u.full_name, u.user_id, u.gender, u.user_name, ya.contact_name, ya.account_id, ya.status')
            ->from(Phpfox::getT('yncaffiliate_accounts'), 'ya')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ya.user_id')
            ->where('ya.account_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['account_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sPhrase = _p('your_account_has_been_approved_by_user_name',['user_name' => $sUsers]);

        $sLink = Phpfox::getLib('url')->makeUrl('affiliate');

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }
    public function getNotificationInactiveaccount($aNotification)
    {
        $aRow = db()->select('u.full_name, u.user_id, u.gender, u.user_name, ya.contact_name, ya.account_id, ya.status')
            ->from(Phpfox::getT('yncaffiliate_accounts'), 'ya')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ya.user_id')
            ->where('ya.account_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['account_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sPhrase = _p('your_account_has_been_deactivate_by_user_name',['user_name' => $sUsers]);

        $sLink = Phpfox::getLib('url')->makeUrl('affiliate');

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }
    public function getNotificationReactiveaccount($aNotification)
    {
        $aRow = db()->select('u.full_name, u.user_id, u.gender, u.user_name, ya.contact_name, ya.account_id, ya.status')
            ->from(Phpfox::getT('yncaffiliate_accounts'), 'ya')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ya.user_id')
            ->where('ya.account_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['account_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sPhrase = _p('your_account_has_been_reactivate_by_user_name',['user_name' => $sUsers]);

        $sLink = Phpfox::getLib('url')->makeUrl('affiliate');

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }
    public function getNotificationDeniedaccount($aNotification)
    {
        $aRow = db()->select('u.full_name, u.user_id, u.gender, u.user_name, ya.contact_name, ya.account_id, ya.status')
            ->from(Phpfox::getT('yncaffiliate_accounts'), 'ya')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ya.user_id')
            ->where('ya.account_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['account_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sPhrase = _p('your_account_has_been_denied_by_user_name',['user_name' => $sUsers]);

        $sLink = Phpfox::getLib('url')->makeUrl('affiliate');

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }
    public function getNotificationApprovedcommission($aNotification)
    {
        $aRow = db()->select('u.full_name, u.user_id, u.gender, u.user_name, yc.status,yc.from_user_id,u1.full_name as from_full_name,yr.rule_title,yc.commission_id')
            ->from(Phpfox::getT('yncaffiliate_commissions'), 'yc')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = yc.user_id')
            ->join(Phpfox::getT('user'), 'u1', 'u1.user_id = yc.from_user_id')
            ->join(Phpfox::getT('yncaffiliate_rules'),'yr','yr.rule_id = yc.rule_id')
            ->where('yc.commission_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['commission_id']))
        {
            return false;
        }

        $sPhrase = _p('you_have_received_commission_from_user_name',['user_name' => $aRow['from_full_name'],'payment_type' => $aRow['rule_title']]);
        $sLink = Phpfox::getLib('url')->makeUrl('affiliate.commission-tracking',['id' => $aRow['commission_id']]);

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }
    public function getNotificationDeniedcommission($aNotification)
    {
        $aRow = db()->select('u.full_name, u.user_id, u.gender, u.user_name, yc.status,yc.from_user_id,u1.full_name as from_full_name,yr.rule_title,yc.commission_id')
            ->from(Phpfox::getT('yncaffiliate_commissions'), 'yc')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = yc.user_id')
            ->join(Phpfox::getT('user'), 'u1', 'u1.user_id = yc.from_user_id')
            ->join(Phpfox::getT('yncaffiliate_rules'),'yr','yr.rule_id = yc.rule_id')
            ->where('yc.commission_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['commission_id']))
        {
            return false;
        }

        $sPhrase = _p('your_commisison_from_a_payment_has_been_denied',['user_name' => $aRow['from_full_name'],'payment_type' => $aRow['rule_title']]);
        $sLink = Phpfox::getLib('url')->makeUrl('affiliate.commission-tracking',['id' => $aRow['commission_id']]);

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }
    public function getNotificationRequest($aNotification)
    {
        $aRow = db()->select('u.full_name, u.user_id, u.gender, u.user_name, yr.request_status,yr.request_id')
            ->from(Phpfox::getT('yncaffiliate_requests'), 'yr')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = yr.user_id')
            ->where('yr.request_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['request_id']))
        {
            return false;
        }

        if($aRow['request_status'] == 'completed')
        {
            $sPhrase = _p('your_request_money_were_approved');
        }
        elseif($aRow['request_status'] == 'denied')
        {
            $sPhrase = _p('your_request_money_were_declined');
        }
        $sLink = Phpfox::getLib('url')->makeUrl('affiliate.my-request',['id' => $aRow['request_id']]);

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }
    public function pendingApproval()
    {
        return array(
            'phrase' => _p('affiliate'),
            'value' => $this->getPendingAccountTotal(),
            'link' => \Phpfox_Url::instance()->makeUrl('admincp.yncaffiliate.manage-affiliate')
        );
    }
    public function getPendingAccountTotal()
    {
        if(!Phpfox::getUserId())
            return 0;

        return intval($this->database()->select('count(1)')
            ->from(Phpfox::getT('yncaffiliate_accounts'),'')
            ->where('status = \'pending\'')
            ->execute('getSlaveField'));
    }
    public function paymentApiCallback($aParams)
    {

        Phpfox::log('Module callback recieved: ' . var_export($aParams, true));
        Phpfox::log('Attempting to retrieve purchase from the database');

        $iOrderIds = explode("_", $aParams['item_number']);
        if (empty($iOrderIds)) {
            return false;
        }
        //check if money request callback
        if ($iOrderIds[0] == 'request') {
            return $this->moneyRequestApiCallBack($iOrderIds[1],$aParams);
        }
    }
    public function moneyRequestApiCallBack($iRequestId,$aParams)
    {
        //get money request
        $aRequest = Phpfox::getService('yncaffiliate.request')->get($iRequestId);
        if ($aRequest) {
            if($aRequest['request_method'] == 'activitypoints' || (isset($aParams['gateway']) && $aParams['gateway'] == 'activitypoints'))
            {
                $iTotalPoints = (int) $this->database()->select('activity_points')
                    ->from(Phpfox::getT('user_activity'))
                    ->where('user_id = ' . (int) $aRequest['user_id'])
                    ->execute('getSlaveField');
                $aSetting = Phpfox::getParam('user.points_conversion_rate');
                if (isset($aSetting[$aRequest['request_currency']])) {
                    $iConversion = $aRequest['request_amount'] / $aSetting[$aRequest['request_currency']];
                    $iNewPoints = ($iTotalPoints + $iConversion);

                    $this->database()->update(Phpfox::getT('user_activity'), array('activity_points' => (int) $iNewPoints), 'user_id = ' . (int) $aRequest['user_id']);
                }
            }
            Phpfox::getService('yncaffiliate.request.process')->updateStatus($iRequestId,'completed');
            return true;
        }
        return false;

    }
}