<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 2/27/17
 * Time: 15:14
 */
namespace Apps\YNC_Affiliate\Service\Commission;

use Phpfox;

class Process extends \Phpfox_Service
{
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('yncaffiliate_commissions');
    }

    public function addCommission($aParams)
    {
        $aRuleMapDetail = Phpfox::getService('yncaffiliate.commissionrule.commissionrule')->getRuleMapDetail('','*','getRow',$aParams['user_id'],$aParams['rule_id'],$aParams['level']);
        if($aRuleMapDetail)
        {
            $sConvertRate = Phpfox::getParam('yncaffiliate.ynaf_points_conversion_rate');
            $iConvertRate = 0;
            if(!empty($sConvertRate))
            {
                $aConvertRate = json_decode($sConvertRate,true);
                if(isset($aConvertRate[$aParams['currency_id']]))
                {
                    $iConvertRate = $aConvertRate[$aParams['currency_id']];
                }
            }
            $commissionRate = $aRuleMapDetail['rule_value'];
            if($commissionRate > 0 && $iConvertRate > 0){
                $commissionAmount = round($aParams['total_amount'] * $commissionRate / 100,2);
                $commissionPoint = $commissionAmount / $iConvertRate;
                $aInsert = [
                    'rule_id' => $aParams['rule_id'],
                    'rulemap_id' => $aRuleMapDetail['rulemap_id'],
                    'rulemapdetail_id' => $aRuleMapDetail['rulemapdetail_id'],
                    'module_id' => $aParams['module_id'],
                    'user_id' => $aParams['user_id'],
                    'from_user_id' => $aParams['new_user_id'],
                    'purchase_currency' => $aParams['currency_id'],
                    'purchase_amount' => $aParams['total_amount'],
                    'purchase_type' => $aParams['rule_id'],
                    'commission_amount' => $commissionAmount,
                    'commission_rate' => $commissionRate,
                    'commission_points' => $commissionPoint,
                    'transaction_id' => isset($aParams['transaction_id']) ? $aParams['transaction_id'] : 0,
                    'status' => (int)setting('ynaf_delay_time_refunds_and_disputes') > 0 ? 'delaying' : 'waiting',
                    'time_stamp' => PHPFOX_TIME
                ];
                $iId = db()->insert($this->_sTable,$aInsert);

                if ($sPlugin = \Phpfox_Plugin::get('yncaffiliate.service_commission_process_addcommission__end')){return eval($sPlugin);}

                return $iId;
            }

        }
        return false;
    }
    public function handlePayment($iNewUserId,$aTransaction,$sPaymentType,$sModule)
    {
        $iPayUserId = $iNewUserId;
        $aRule = Phpfox::getService('yncaffiliate.commissionrule.commissionrule')->getRuleByName($sPaymentType);
        if($aRule)
        {
            $aParams['total_amount'] = $aTransaction['amount'];
            $aParams['currency_id'] = $aTransaction['currency_id'];
            $iMaxLevel = setting('ynaf_number_commission_levels');
            if($iMaxLevel == 0)
                return;
            for ($iLevel = 1; $iLevel <= $iMaxLevel; $iLevel++)
            {
                $aAssoc = Phpfox::getService('yncaffiliate.affiliate.affiliate')->getAssoc($iNewUserId,true);
                //check parent affiliate of user process purchase
                if(!empty($aAssoc['user_id']))
                {
                    $iNewUserId = $aAssoc['user_id'];
                    $aParams['new_user_id'] = $iPayUserId;
                    $aParams['user_id'] = $aAssoc['user_id'];
                    $aParams['level'] = $iLevel;
                    $aParams['module_id'] = $sModule;
                    $aParams['rule_name'] = $sPaymentType;
                    $aParams['rule_id'] = $aRule['rule_id'];
                    $this->addCommission($aParams);
                }
            }
        }
    }
    public function updateStatus($iCommissionId, $sStatus,$sReason = NULL)
    {
        $iDelayTime = setting('ynaf_delay_time_refunds_and_disputes');
        $sTime = $iDelayTime*86400;

        $aCommissions = db()->select('commission_id,status,user_id,time_stamp')
            ->from($this->_sTable)
            ->where('commission_id IN('.$iCommissionId.')')
            ->execute('getRows');
        foreach ($aCommissions as $key => $aCommission) {
            if($sStatus == 'approved' && $aCommission['status'] == 'delaying' && ($aCommission['time_stamp'] + $sTime > PHPFOX_TIME))
            {
                $sStatus = 'delaying';
            }
            if(!empty($sReason))
            {
                db()->update($this->_sTable, array('reason' => $sReason ), "commission_id =".$aCommission['commission_id']);
            }

            $bResult = db()->update($this->_sTable, array('status' => $sStatus, 'time_update' => PHPFOX_TIME ), "commission_id =".$aCommission['commission_id']);

            if(Phpfox::isModule('notification') && ($sStatus == 'approved' || $sStatus == 'denied')){
                Phpfox::getService("notification.process")->add("yncaffiliate_".$sStatus."commission",$aCommission['commission_id'], $aCommission['user_id'], Phpfox::getUserId());
            }
        }
        return $bResult;
    }
    public function updateStatusMulti($sIds,$checkStatus,$newStatus,$sReason = NULL)
    {
        $iDelayTime = setting('ynaf_delay_time_refunds_and_disputes');
        $sTime = $iDelayTime*86400;
        $aComs = db()->select('user_id,commission_id,status,time_stamp')
            ->from($this->_sTable)
            ->where('commission_id IN('.$sIds.')')
            ->execute('getRows');
        if($aComs)
        {
            foreach ($aComs as $aCom)
            {
                if(in_array($aCom['status'],$checkStatus))
                {
                    if($newStatus == 'approved' && $aCom['status'] == 'delaying' && ($aCom['time_stamp'] + $sTime > PHPFOX_TIME))
                    {
                        $newStatus = 'delaying';
                    }
                    if(!empty($sReason))
                    {
                        db()->update($this->_sTable, array('reason' => $sReason ), "commission_id =".$aCom['commission_id']);
                    }
                    db()->update($this->_sTable, array('status' => $newStatus, 'time_update' => PHPFOX_TIME ), "commission_id =".$aCom['commission_id']);
                    if(Phpfox::isModule('notification') && ($newStatus == 'approved' || $newStatus == 'denied')){
                        Phpfox::getService("notification.process")->add("yncaffiliate_".$newStatus."commission",$aCom['commission_id'], $aCom['user_id'], Phpfox::getUserId());
                    }
                }
            }
        }
        return true;
    }
    public function cronUpdateCommission()
    {
        $iDelayTime = setting('ynaf_delay_time_refunds_and_disputes');
        $sTime = (int)$iDelayTime*86400;
        $aCommissions = db()->select('commission_id,user_id,time_stamp,time_update')
                            ->from($this->_sTable)
                            ->where('status = \'delaying\' AND (time_stamp +'.$sTime.') <='.PHPFOX_TIME)
                            ->execute('getRows');
        if(count($aCommissions))
        {
            foreach($aCommissions as $aCommission)
            {
                $bAutoApproved = Phpfox::getService('yncaffiliate.helper')->getUserParam('ynaf_auto_approve_commission',$aCommission['user_id']);
                if($bAutoApproved || $aCommission['time_update'] > 0)
                {
                    db()->update($this->_sTable, array('status' => 'approved', 'time_update' => PHPFOX_TIME ), "commission_id IN ({$aCommission['commission_id']})");
                    if(Phpfox::isModule('notification')){
                        Phpfox::getService("notification.process")->add("yncaffiliate_approvedcommission",$aCommission['commission_id'], $aCommission['user_id'], null,true);
                    }
                }
                else{
                    db()->update($this->_sTable, array('status' => 'waiting', 'time_update' => PHPFOX_TIME ), "commission_id IN ({$aCommission['commission_id']})");
                }

            }
        }

    }
    public function convertPointToRealMoney($iPoints,$sCurrency = 'USD')
    {
        $sConvertRate = setting('yncaffiliate.ynaf_points_conversion_rate');
        if(!$sConvertRate)
        {
            $iConvertRate = 0;
        }
        else{
            $aConvertRate = json_decode($sConvertRate,true);
            if(!isset($aConvertRate[$sCurrency]))
            {
                $iConvertRate = 0;
            }
            else{
                $iConvertRate = $aConvertRate[$sCurrency];
            }
        }
        return $iPoints * $iConvertRate;
    }
}