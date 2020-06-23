<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Service_User_Process extends Phpfox_Service {

	/**
	 * this function will update status of need updating flag corresponding to iStatus
	 * @by minhta
	 * @return boolean 
	 */
	public function updateNeedUpdatingStatusOfOwnerProfile($iCampaignId, $iStatus = 0) {
		if (!($iUserId = Phpfox::getService('fundraising.campaign')->getOwnerOfCampaign($iCampaignId))) {
			return false;
		}

		if(!Phpfox::getService('fundraising.user')->checkOwnerExist($iUserId))
		{
			Phpfox::getService('fundraising.user.process')->addOwnerProfile($iUserId);
		}

		if ($this->database()->update(Phpfox::getT('fundraising_campaign_owner_profile'), array('is_need_updating' => $iStatus), ' user_id = ' . (int) $iUserId)) {
			return true;
		}

		return false;
	}

	public function addOwnerProfile($iOwnerUserId)
	{
		$aInsert = array(
			'user_id' => $iOwnerUserId,
			'time_stamp' => PHPFOX_TIME,
		);

		$iId = $this->database()->insert(Phpfox::getT('fundraising_campaign_owner_profile'), $aInsert);
		return $iId;
	}

	

	public function updateOwnerProfile($iUserId) {
		$aAverage = $this->database()->select('SUM(total_rating) AS total_rating, AVG(total_score) AS avg_rating')
				->from(Phpfox::getT('fundraising_campaign'))
				->where('user_id = ' . (int) $iUserId . ' AND total_rating > 0')
				->execute('getRow');

		$aUpdate = array(
			'time_stamp' => PHPFOX_TIME,
			'total_rating' => $aAverage['total_rating'],
			'avg_rating' => $aAverage['avg_rating'],
			'is_need_updating' => 0,
			'user_id' => $iUserId
		);
		if (!$this->database()->update(Phpfox::getT('fundraising_campaign_owner_profile'), $aUpdate, 'user_id = ' . $iUserId)) {
			return false;
		}


		return $aUpdate;
	}

	public function addDonor($iTransactionId, $iAmount, $aParam = array()) {
		$aTransaction = Phpfox::getService('fundraising.transaction')->getTransactionById($iTransactionId);
		$iDonorId = 0;
		$aInvoice = unserialize($aTransaction['invoice']);
		$aCampaign = Phpfox::getService('fundraising.campaign')->getBasicInfoOfCampaign($aInvoice['campaign_id']);
		// to check shoudl return true or false 
		$iResult = 0;

		// if this user donated before, we will not insert a new row
		if (!$aInvoice['is_guest'] && ($aDonor = Phpfox::getService('fundraising.user')->checkDonorExist($aInvoice['user_id'], $aInvoice['campaign_id']))) {
			$aUpdate = array(
				'amount' => $aDonor['amount'] + $iAmount,
				'time_stamp' => PHPFOX_TIME,
				'is_anonymous' => ($aInvoice['is_anonymous']) ? 1 : 0,
			);

			if($aInvoice['message'])
			{
				$aUpdate['message'] = $aInvoice['message'];
			}

			$iDonorId = $aDonor['donor_id'];
			$iResult = $this->database()->update(Phpfox::getT('fundraising_donor'), $aUpdate, ' donor_id = ' . $aDonor['donor_id']);
			if ($iResult > 0) {
				//make sure we will always return donor id if this function is success
				$iResult = $aDonor['donor_id'];
			}

			// we update campaign info here
			Phpfox::getService('fundraising.campaign.process')->updateTotalAmount($aInvoice['campaign_id']);
			// we didn't add more donor
		} else {
			$aInsert = array(
				'campaign_id' => $aInvoice['campaign_id'],
				'user_id' => !$aInvoice['is_guest'] ? $aInvoice['user_id'] : 0,
				'is_guest' => $aInvoice['is_guest'] ? 1 : 0,
				'is_anonymous' => ($aInvoice['is_anonymous']) ? 1 : 0,
				'message' => $aInvoice['message'],
				//great thing here is we will get amount from Paypal
				'amount' => $iAmount,
				'time_stamp' => PHPFOX_TIME,
				'full_name' => $aInvoice['is_guest'] ? $aInvoice['full_name'] : '',
				'email_address' => $aInvoice['is_guest'] ? $aInvoice['email_address'] : '',
			);

			if(isset($aParam['currency'])){
				$aInsert['currency'] = $aParam['currency'];
			}

			$iResult = $this->database()->insert(Phpfox::getT('fundraising_donor'), $aInsert);
			$iDonorId = $iResult;

			Phpfox::getService('fundraising.campaign.process')->updateTotalAmount($aInvoice['campaign_id']);
			Phpfox::getService('fundraising.campaign.process')->updateTotalDonor($aInvoice['campaign_id']);
		}


		if ($iResult > 0) {
			$aReceivers = array();
			if (!$aInvoice['is_guest']) {
				$aReceivers = array($aInvoice['user_id']);
			} else {
				if ($aInvoice['email_address']) {
					$aReceivers = array($aInvoice['email_address']);
				}
			}

			Phpfox::getService('fundraising.mail.process')->sendEmailTo($sTemplateType = Phpfox::getService('fundraising.mail')->getTypesCode('thankdonor_donor'), $aInvoice['campaign_id'], $aReceivers, $aCustomEmail =array(), $iDonorId);

			//send email to notify owner
			Phpfox::getService('fundraising.mail.process')->sendEmailToOwner($sTemplateType = Phpfox::getService('fundraising.mail')->getTypesCode('updatedonor_owner'), $aInvoice['campaign_id'], $iDonorId);

			if(!$aInvoice['is_guest'] && !$aInvoice['is_anonymous'])
			{
				
				$aCallback = ((!empty($aCampaign['module_id']) && $aCampaign['module_id'] != 'fundraising') ? Phpfox::getService('fundraising')->getFundraisingAddCallback($iDonorId) : null);

				(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback($aCallback)->allowGuest()->add('fundraising_donate', $iDonorId, $aCampaign['privacy'], (isset($aCampaign['privacy_comment']) ? (int) $aCampaign['privacy_comment'] : 0), (($aCampaign['item_id']) ? (int) $aCampaign['item_id'] : 0), $aInvoice['user_id']) : null);

				Phpfox::getService('fundraising.campaign')->notifyToAllFollowers($aCampaign['campaign_id'], 'donated', $iDonorId);
			}

			

			return $iResult;
		} else {
			return false;
		}
	}

}

?>