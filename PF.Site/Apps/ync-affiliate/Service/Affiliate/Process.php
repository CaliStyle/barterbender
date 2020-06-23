<?php

/**
 * Created by PhpStorm.
 * User: dai
 * Date: 20/01/2017
 * Time: 10:44
 */

namespace Apps\YNC_Affiliate\Service\Affiliate;

use Phpfox;

Class Process extends \Phpfox_Service
{
    public function __construct()
    {
        $this->_sTable = \Phpfox::getT('yncaffiliate_accounts');
        $this->_sAssocTable = \Phpfox::getT('yncaffiliate_assoc');
    }

    public function updateStatus($iAffiliateId, $sStatus)
    {
        $initStatus = $sStatus;
        db()->update($this->_sTable, array('status' => ($sStatus == 'reactive') ? 'approved' : $sStatus ), "account_id IN ({$iAffiliateId})");
        $aAffs = db()->select('*')
            ->from($this->_sTable)
            ->where('account_id IN('.$iAffiliateId.')')
            ->execute('getRows');
        foreach ($aAffs as $key => $aAff) {
            if(Phpfox::isModule('notification')){
                Phpfox::getService("notification.process")->add("yncaffiliate_".$sStatus."account",$aAff['account_id'], $aAff['user_id'], Phpfox::getUserId());
            }
            if($initStatus == 'approved')
            {
                $sSubject = _p('user_name_welcome_to_affiliate_program',['user_name' => $aAff['contact_name']]);
                $sText = _p('email_approved_affiliate_account_text',['link' => Phpfox::getLib('url')->makeUrl('affiliate')]);
                $sEmail = $aAff['contact_email'];
                Phpfox::getService('yncaffiliate.helper')->sendMail($sEmail,$sText,$sSubject);
            }
            elseif($initStatus == 'denied'){
                $sSubject = _p('user_name_your_registration_was_rejected',['user_name' => $aAff['contact_name']]);
                $sText = _p('email_rejected_affiliate_account_text',['link' => Phpfox::getLib('url')->makeUrl('affiliate')]);
                $sEmail = $aAff['contact_email'];
                Phpfox::getService('yncaffiliate.helper')->sendMail($sEmail,$sText,$sSubject);
            }
        }
        return true;
    }
    public function updateStatusMulti($sIds,$checkStatus,$newStatus)
    {
        $aAffs = db()->select('*')
                        ->from($this->_sTable)
                        ->where('account_id IN('.$sIds.')')
                        ->execute('getRows');
        if($aAffs)
        {
            foreach ($aAffs as $aAff)
            {
                if($aAff['status'] == $checkStatus)
                {
                    db()->update($this->_sTable, array('status' => $newStatus ), "account_id =".$aAff['account_id']);
                    if(Phpfox::isModule('notification')){
                        Phpfox::getService("notification.process")->add("yncaffiliate_".$newStatus."account",$aAff['account_id'], $aAff['user_id'], Phpfox::getUserId());
                    }
                    if($newStatus == 'denied'){
                        $sSubject = _p('user_name_your_registration_was_rejected',['user_name' => $aAff['contact_name']]);
                        $sText = _p('email_rejected_affiliate_account_text',['link' => Phpfox::getLib('url')->makeUrl('affiliate')]);
                        $sEmail = $aAff['contact_email'];
                        Phpfox::getService('yncaffiliate.helper')->sendMail($sEmail,$sText,$sSubject);
                    }
                }
            }
        }
        return true;
    }

    public function deleteAffiliate($iAffiliateId)
    {
        $aAffs = db()->select('*')
                    ->from($this->_sTable)
                    ->where('account_id IN ('.$iAffiliateId.')')
                    ->execute('getRows');
        if(count($aAffs))
        {
            foreach ($aAffs as $aAff){
                if($aAff['status'] == 'pending')
                {
                    db()->delete($this->_sTable, 'account_id IN ('.$aAff['account_id'].')');
                }
            }
        }

        return true;
    }
    public function addAffiliate($aVals)
    {
        if(empty($aVals['user_id']))
        {
            return false;
        }
        if(empty($aVals['email']) || empty($aVals['phone']) || empty($aVals['name']) || empty($aVals['address']))
        {
            return \Phpfox_Error::set(_p('all_fields_are_required'));
        }
        if(!isset($aVals['terms']))
        {
            return \Phpfox_Error::set(_p('you_have_to_agree_with_our_terms_of_service'));
        }
        $oFilter = Phpfox::getLib('parse.input');
        $aInsert = [
            'user_id' => $aVals['user_id'],
            'status' => (setting('ynaf_auto_approve')) ? 'approved' : 'pending',
            'contact_name' => $oFilter->clean($aVals['name'], 255),
            'contact_email' => $aVals['email'],
            'contact_phone' => $aVals['phone'],
            'contact_address' => $aVals['address'],
            'time_stamp' => PHPFOX_TIME
        ];
        $iId = db()->insert($this->_sTable,$aInsert);
        return $iId;
    }
    public function updateAffiliate($aVals)
    {
        if(empty($aVals['user_id']))
        {
            return false;
        }
        if(empty($aVals['email']) || empty($aVals['phone']) || empty($aVals['name']) || empty($aVals['address']))
        {
            return \Phpfox_Error::set(_p('all_fields_are_required'));
        }
        $oFilter = Phpfox::getLib('parse.input');
        $aUpdate = [
            'user_id' => $aVals['user_id'],
            'contact_name' => $oFilter->clean($aVals['name'], 255),
            'contact_email' => $oFilter->clean($aVals['email']),
            'contact_phone' => $oFilter->clean($aVals['phone']),
            'contact_address' => $oFilter->clean($aVals['address']),
        ];
        $iId = db()->update($this->_sTable,$aUpdate,'user_id ='.$aVals['user_id']);
        return $iId;
    }
    public function addAssoc($iUserId,$iNewUserId,$iLinkId = 0,$iInviteTime = 0,$iInviteId = 0,$sInviteCode = null)
    {
        if(!$iUserId || !$iNewUserId)
        {
            return false;
        }

        $aInsert = [
            'user_id' => $iUserId,
            'new_user_id' => $iNewUserId,
            'link_id' => (int)$iLinkId,
            'invite_id' => $iInviteId,
            'invite_code' => $sInviteCode,
            'invited_time' => $iInviteTime,
            'time_stamp' => PHPFOX_TIME
        ];
        if($iLinkId)
        {
            db()->updateCounter('yncaffiliate_links', 'total_success', 'link_id', $iLinkId);
        }
        $iId = db()->insert($this->_sAssocTable,$aInsert);
        return $iId;
    }
    public function addAssocByInvitation($aUser)
    {
        $aInvites = db()->select('i.*')
                        ->from(Phpfox::getT('invite'),'i')
                        ->join(Phpfox::getT('user'), 'u', 'u.user_id = i.user_id')
                        ->where('i.email = \''. $aUser['email'].'\'')
                        ->execute('getSlaveRows');
        if($aInvites){
            foreach ($aInvites as $aInvite)
            {
                $sIsAffiliate = Phpfox::getService('yncaffiliate.affiliate.affiliate')->checkIsAffiliate($aInvite['user_id']);
                if($sIsAffiliate && $sIsAffiliate == 'approved') {
                    $this->addAssoc($aInvite['user_id'], $aUser['user_id'], 0, $aInvite['time_stamp']);
                }
            }
        }
        return true;
    }
}