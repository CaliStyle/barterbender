<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		MinhTA
 * @package  		Module_socialad
 */

class Socialad_Service_Mail extends Phpfox_Service
{
	public function sendMailAndNotificaiton($sType, $iAdId) {
		$aAd = Phpfox::getService('socialad.ad')->getAdBasicInfoById($iAdId);
		$iSenderUserId = 0;
    	if((int)Phpfox::getUserId() > 0){
    		$iSenderUserId = Phpfox::getUserId();
    	} else {
    		$iSenderUserId = (int)$aAd['ad_user_id'];
    	}

		switch($sType) {
		case 'approve_ad': 
			$sSubject = array('socialad.ad_approved');
			$sMessage = array('socialad.your_ad_on_site_name_has_been_approved', array(
					'site_name' => Phpfox::getParam('core.site_title'),
					'link' => Phpfox::getLib('url')->makeUrl('socialad.ad.detail', array('id' => $aAd['ad_id']))
				)
			);

			(Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->add($sType = 'socialad_ad_approve', $iItemId = $iAdId, $aAd['ad_user_id'], $iSenderUserId) : null);
			break;

		case 'deny_ad': 
			$sSubject = array('socialad.ad_denied');
			$sMessage = array('socialad.your_ad_on_site_name_has_been_denied', array(
					'site_name' => Phpfox::getParam('core.site_title'),
					'link' => Phpfox::getLib('url')->makeUrl('socialad.ad.detail', array('id' => $aAd['ad_id']))
				)
			);
			(Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->add($sType = 'socialad_ad_deny', $iItemId = $iAdId, $aAd['ad_user_id'], $iSenderUserId) : null);
			break;

		case 'order_confirm': 
			$sSubject = array('socialad.order_confirmed');
			$sMessage = array('socialad.your_pay_later_request_for_ad_title_on_site_name_has_been_confirmed', array(
					'site_name' => Phpfox::getParam('core.site_title'),
					'link' => Phpfox::getLib('url')->makeUrl('socialad.ad.detail', array('id' => $aAd['ad_id'])),
					'ad_title' => $aAd['ad_title']
				)
			);
			(Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->add($sType = 'socialad_ad_orderconfirm', $iItemId = $iAdId, $aAd['ad_user_id'], $iSenderUserId) : null);
			break;

		case 'run_ad': 
			$sSubject = array('socialad.ad_is_running');
			$sMessage = array('socialad.your_ad_is_running', array(
					'site_name' => Phpfox::getParam('core.site_title'),
					'link' => Phpfox::getLib('url')->makeUrl('socialad.ad.detail', array('id' => $aAd['ad_id'])),
					'ad_title' => $aAd['ad_title']
				)
			);
			(Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->add($sType = 'socialad_ad_running', $iItemId = $iAdId, $aAd['ad_user_id'], $iSenderUserId) : null);
			break;
		}

		Phpfox::getLib('mail')->to($aAd['ad_user_id'])						
			->subject($sSubject)
			->message($sMessage)
			->send();				

		return true;
	}


}



