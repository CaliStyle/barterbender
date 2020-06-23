<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Service_Permission extends Phpfox_Service {
	

	public function canFeatureCampaign($iCampaignId)
	{
		$aCampaign = Phpfox::getService('fundraising.campaign')->getCampaignForCheckingPermission($iCampaignId);

		if((Phpfox::getUserParam('fundraising.can_feature_campaign') || 
			($aCampaign['module_id'] == 'pages' && Phpfox::getService('pages')->isAdmin($aCampaign['item_id']))) && 
			$aCampaign['is_approved'] == 1 &&
			$aCampaign['status'] == Phpfox::getService('fundraising.campaign')->getStatusCode('ongoing') )
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	public function canHighlightCampaign($iCampaignId, $iUserId)
	{
		$aCampaign = Phpfox::getService('fundraising.campaign')->getCampaignForCheckingPermission($iCampaignId);

		if(Phpfox::getUserParam('fundraising.can_highlight_campaign') && 
			$aCampaign['module_id'] == 'fundraising' &&
			$aCampaign['is_approved'] == 1 &&
			$aCampaign['status'] == Phpfox::getService('fundraising.campaign')->getStatusCode('ongoing') )
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	public function canCloseCampaign($iCampaignId, $iUserId)
	{
		$aCampaign = Phpfox::getService('fundraising.campaign')->getCampaignForCheckingPermission($iCampaignId);

		if((Phpfox::getUserParam('fundraising.can_close_campaign_added_by_other_users') || 
				($aCampaign['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('fundraising.can_close_own_campaign'))) &&
				$aCampaign['is_approved'] == 1 &&
				!$aCampaign['is_closed'] &&
				!$aCampaign['is_draft'])
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	public function canViewStatisticCampaign($iCampaignId, $iUserId)
	{
		$aCampaign = Phpfox::getService('fundraising.campaign')->getCampaignForCheckingPermission($iCampaignId);

		if($aCampaign['user_id'] == Phpfox::getUserId() || Phpfox::isAdmin() )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function canEmailToAllDonorsCampaign($iCampaignId, $iUserId)
	{
		$aCampaign = Phpfox::getService('fundraising.campaign')->getCampaignForCheckingPermission($iCampaignId);

		if($aCampaign['user_id'] == Phpfox::getUserId()  && !$aCampaign['is_draft'] && $aCampaign['is_approved'] )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	/**
	 * this function will check whether in this context, user can perform edit action
	 * @by minhta
	 * @param int $iUserId is 0 if this user is a guest
	 * @return
	 */
	public function canEditCampaign($iCampaignId, $iUserId = 1)
	{
		if(!Phpfox::isUser())
		{
			return false;
		}
		
		if(!$iUserId)
		{
			return false;
		}

		$aCampaign = Phpfox::getService('fundraising.campaign')->getCampaignForCheckingPermission($iCampaignId);

		if(!$aCampaign)
		{
			return false;
		}

		// no way to edit a campaign in uneditable status
		if(in_array($aCampaign['status'], Phpfox::getService('fundraising.campaign')->getUneditableStatus()))
		{
			return false;
		}

		// if he is the owner and he has permission to edit his own campaign
		if(($iUserId == $aCampaign['user_id'] && Phpfox::getUserParam('fundraising.edit_own_campaign')) || Phpfox::getUserParam('fundraising.edit_user_campaign')) 
		{

			return true;
		}

		// page owner is a big BOSS
		if ($aCampaign['module_id'] == 'pages' && Phpfox::getService('pages')->isAdmin($aCampaign['item_id']))
		{		
			return true;
		}	

		return false;

		
	}


	/**
	 * this function will check whether in this context, user can perform edit action
	 * @by minhta
	 * @param int $iUserId is 0 if this user is a guest
	 * @return
	 */
	public function canDeleteCampaign($iCampaignId, $iUserId = 1)
	{
		if(!Phpfox::isUser())
		{
			return false;
		}
		
		if(!$iUserId)
		{
			return false;
		}

		$aCampaign = Phpfox::getService('fundraising.campaign')->getCampaignForCheckingPermission($iCampaignId);

		if(!$aCampaign)
		{
			return false;
		}

		//  only draft and pending campaign can be deleted
		if(!in_array($aCampaign['status'], array(
			Phpfox::getService('fundraising.campaign')->getStatusCode('draft'),
			Phpfox::getService('fundraising.campaign')->getStatusCode('pending')
		)))
		{
			return false;
		}

		// if he is the owner and he has permission to edit his own campaign
		if(($iUserId == $aCampaign['user_id'] && Phpfox::getUserParam('fundraising.delete_own_campaign')) || Phpfox::getUserParam('fundraising.delete_user_campaign')) 
		{

			return true;
		}

		// page owner is a big BOSS
		if ($aCampaign['module_id'] == 'pages' && Phpfox::getService('pages')->isAdmin($aCampaign['item_id']))
		{		
			return true;
		}	

		return false;

		
	}
	


	public function canAddCampaign($sModule = 'fundraising', $iItemId = 0)
	{
		//only site member can add campagin
		Phpfox::isUser(true);

		// if he is the owner and he has permission to edit his own campaign
		if(Phpfox::getUserParam('fundraising.add_new_campaign')) 
		{
			return true;
		}

		// page owner is a big BOSS
		if ($sModule == 'pages' && Phpfox::getService('pages')->isAdmin($iItemId))
		{		
			return true;
		}	

		if ($sModule == 'pages' && Phpfox::getService('pages')->hasPerm($iItemId, 'fundraising.share_campaigns'))
		{
			return true;	
		}
		
		return false;
	}

	public function canViewBrowseFundraisingModule($sModule = 'fundraising', $iItemId = 0)
	{
		//only site member can add campagin
		
		// if he is the owner and he has permission to edit his own campaign
		if(Phpfox::getUserParam('fundraising.view_fundraisings') && $sModule == 'fundraising' ) 
		{
			return true;
		}

		// page owner is a big BOSS
		if ($sModule == 'pages' && Phpfox::getService('pages')->isAdmin($iItemId))
		{		
			return true;
		}	

		if ($sModule == 'pages' && Phpfox::getService('pages')->hasPerm($iItemId, 'fundraising.view_browse_campaigns'))
		{
			return true;	
		}
		
		return false;
	}

	public function canViewBrowseCampaign($iCampaignId, $iUserId = 1)
	{
		
		$aCampaign = Phpfox::getService('fundraising.campaign')->getCampaignForCheckingPermission($iCampaignId);
		
		
		//can not view a campaign if don't have permission to view all campaign 
		if(!Phpfox::getService('fundraising.permission')->canViewBrowseFundraisingModule($aCampaign['module_id'], $aCampaign['item_id']))
		{
			return false;
		}


		if(!$aCampaign)
		{
			return false;
		}

		// only owner and admin can view pending and draft campaign
		if(in_array($aCampaign['status'], array(
			Phpfox::getService('fundraising.campaign')->getStatusCode('draft'),
			Phpfox::getService('fundraising.campaign')->getStatusCode('pending')
		)))
		{
			if($iUserId == $aCampaign['user_id'] || Phpfox::isAdmin())
			{
				return true;
			}

			if ($aCampaign['module_id'] == 'pages' && Phpfox::getService('pages')->isAdmin($aCampaign['item_id']))
			{		
				return true;
			}	

			return false;
		}

		if (Phpfox::isModule('privacy'))
		{
			if(Phpfox::getService('privacy')->check('fundraising', $aCampaign['campaign_id'], $aCampaign['user_id'], $aCampaign['privacy'], $aCampaign['is_friend'], true))		
			{
				return true;
			}
		}


		// page owner is a big BOSS
		

		return false;

		
	}

	public function canDonateCampaign($iCampaignId)
	{
		
		$aCampaign = Phpfox::getService('fundraising.campaign')->getCampaignForCheckingPermission($iCampaignId);
		
		if(!$aCampaign)
		{
			return false;
		}

		// can donate only for ongoing campaign 
		if($aCampaign['status'] != Phpfox::getService('fundraising.campaign')->getStatusCode('ongoing') || $aCampaign['is_approved'] == 0)
		{
			return false;	
		}

		if (Phpfox::isModule('privacy'))
		{
			if(Phpfox::getService('privacy')->check('fundraising', $aCampaign['campaign_id'], $aCampaign['user_id'], $aCampaign['privacy_donate'], $aCampaign['is_friend'], true))		
			{
				return true;
			}
		}


		// page owner is a big BOSS
		

		return false;

		
	}
	
	
}

?>