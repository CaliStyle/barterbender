<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 09:32
 */

namespace Apps\YNC_Affiliate\Block;

defined('PHPFOX') or exit('NO DICE!');
use Phpfox;
class RequestMoneyFormBlock extends \Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        $iUserId = Phpfox::getUserId();
        $aUserGateways = Phpfox::getService('api.gateway')->getUserGateways($iUserId);
        $aActiveGateways = Phpfox::getService('api.gateway')->getActive();
        $aAllowGateways = [];
        if (empty($aActiveGateways)) {
            $aAllowGateways = [];
        }
        elseif(is_array($aUserGateways) && count($aUserGateways))
        {
            foreach ($aUserGateways as $sGateway => $aData) {
                if (is_array($aData['gateway']) && count($aData['gateway'])) {
                    foreach ($aActiveGateways as $aActiveGateway) {
                        if ($sGateway == $aActiveGateway['gateway_id']) {
                            $aAllowGateways[] = [
                                'gateway_id' => $sGateway,
                                'title' => $aActiveGateway['title']
                            ];
                            continue;
                        }
                    }
                }
            }

        }
        if(Phpfox::getParam('user.can_purchase_with_points') && Phpfox::getUserParam('user.can_purchase_with_points'))
        {
            $aAllowGateways[] = [
                'gateway_id' => 'activitypoints',
                'title' => _p('activity_points')
            ];
        }
        if(empty($aAllowGateways))
        {
            echo _p('you_need_to_configure_your_payment_account_in_account_settings_before_request_money');
            return false;
        }

        $sDefaultCurrency = Phpfox::getService('core.currency')->getDefault();
        $sCurrencySymbol = Phpfox::getService('core.currency')->getSymbol($sDefaultCurrency);

        $iTotalEarningPoints = Phpfox::getService('yncaffiliate.commission')->getTotalCommissionPoints($iUserId,'approved');
        $iTotalRecievedPoints = Phpfox::getService('yncaffiliate.request')->getTotalRequestPoints('\'completed\'',$iUserId);
        $iTotalPendingPoints = Phpfox::getService('yncaffiliate.request')->getTotalRequestPoints('\'waiting\'',$iUserId);

        if($iTotalEarningPoints >= ($iTotalRecievedPoints + $iTotalPendingPoints))
        {
            $iTotalAvailablePoints = $iTotalEarningPoints - $iTotalRecievedPoints - $iTotalPendingPoints;
        }
        else{
            $iTotalAvailablePoints = 0;
        }
        $iMinRequestPoints = setting('ynaf_minimum_request_points','1');
        if((float)$iMinRequestPoints <= 0)
        {
            $iMinRequestPoints = 1;
        }
        $iMaxRequestPoints = setting('ynaf_maximum_request_points','10');
        if($iMinRequestPoints > $iTotalAvailablePoints)
        {
            echo _p('your_available_amount_is_not_enough_to_make_a_request',['minimum' => $sCurrencySymbol.$iMinRequestPoints]);
            return false;
        }
        if($iTotalAvailablePoints <= $iMaxRequestPoints)
        {
            $iMaxRequestPoints = $iTotalAvailablePoints;
        }
        $this->template()->assign([
            'iMinRequestPoints' => $iMinRequestPoints,
            'iMaxRequestPoints' => $iMaxRequestPoints,
            'iTotalAvailableAmount' => $iTotalAvailablePoints,
            'sCurrencySymbol' => $sCurrencySymbol,
            'sDefaultCurrency' => $sDefaultCurrency,
            'aAllowGateways' => $aAllowGateways
        ]);
    }
}