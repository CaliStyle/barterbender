<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Service_User_User extends Phpfox_Service {


	/*
	 * owner user id is mapped 1-1 with owner profile table
	 */
	public function checkOwnerExist($iOwnerUserId)
	{
		$aRow = $this->database()->select('user_id')
				->from(Phpfox::getT('fundraising_campaign_owner_profile'))
				->where('user_id = ' . (int) $iOwnerUserId)
				->execute('getRow');	

		if($aRow)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function getAlllFollowersOfCampaign($iCampaignId)
	{
		$aRows = $this->database()->select('*')
				->from(Phpfox::getT('fundraising_follow'))
				->where('campaign_id = ' . $iCampaignId)
				->execute('getSlaveRows');

		if(isset($aRows))
		{
			return $aRows;
		}
		else
		{
			return false;
		}
	}
	
	public function getDonorbyId($iDonorId)
	{
		$aRow = $this->database()->select('*')
				->from(Phpfox::getT('fundraising_donor'))
				->where('donor_id = ' . $iDonorId )
				->execute('getSlaveRow');

		if(isset($aRow['donor_id']))
		{
			return $aRow;
		}
		else
		{
			return false;
		}
	}
	
	public function checkDonorExist($iUserId, $iCampaignId)
	{
		$aRow = $this->database()->select('*')
				->from(Phpfox::getT('fundraising_donor'))
				->where('user_id = ' . $iUserId . ' AND campaign_id = ' . $iCampaignId)
				->execute('getSlaveRow');

		if(isset($aRow['donor_id']))
		{
			return $aRow;
		}
		else
		{
			return false;
		}
		
	}
	public function getTopDonorsOfCampaign($iLimit, $iCampaignId = null)
	{
		$aRows = $this->database()->select( $this->getDonorFields() . Phpfox::getUserField())
				->from(Phpfox::getT('fundraising_donor'), 'fd')
				->leftjoin(Phpfox::getT('user'), 'u', 'u.user_id = fd.user_id')
				->where('fd.campaign_id = ' . $iCampaignId)
				->limit($iLimit)
				->order('amount DESC, u.user_id DESC')
				->execute('getSlaveRows');
		return $aRows;
	}
	
	public function getTopSupportersOfCampaign($iLimit, $iCampaignId = null)
	{
		$aRows = $this->database()->select('fs.*, ' . Phpfox::getUserField())
				->from(Phpfox::getT('fundraising_supporter'), 'fs')
				->leftjoin(Phpfox::getT('user'), 'u', 'u.user_id = fs.user_id')
				->where('fs.campaign_id = ' . $iCampaignId)
				->limit($iLimit)
				->order('fs.total_share DESC, u.user_id DESC')
				->execute('getSlaveRows');

		return $aRows;
	}

	
	public function getTopSupporters($iLimit) {
		$aRows = $this->database()->select('sum(fs.total_share) as total_share, ' . Phpfox::getUserField())
				->from(Phpfox::getT('fundraising_supporter'), 'fs')
				->leftjoin(Phpfox::getT('user'), 'u', 'u.user_id = fs.user_id')
				->order('total_share DESC, u.user_id DESC')
				->limit($iLimit)
				->group('fs.user_id')
				->execute('getSlaveRows');

		return $aRows;
	}

	public function getDonorFields()
	{
		return 'fd.full_name as donor_name, fd.email_address as donor_email,  fd.*, ';
	}

	public function getTopDonors($iLimit) {
		$aRows = $this->database()->select('count(fd.user_id) as total_donate, ' . $this->getDonorFields() . Phpfox::getUserField())
				->from(Phpfox::getT('fundraising_donor'), 'fd')
				->leftjoin(Phpfox::getT('user'), 'u', 'u.user_id = fd.user_id')
				->where('fd.is_guest = 0')
				->order('total_donate DESC, u.user_id DESC')
				->limit($iLimit)
				->group('fd.user_id')
				->execute('getSlaveRows');

		return $aRows;
	}

	public function getCampaignOwnerProfile($iUserId)
	{
		$aProfile = $this->database()->select('*')
				->from(Phpfox::getT('fundraising_campaign_owner_profile'))
				->where('user_id = ' . $iUserId)
				->execute('getSlaveRow');

		if(!$aProfile)
		{
			return false;
		}

		if($aProfile['is_need_updating'])
		{
			$aNewProfile = Phpfox::getService('fundraising.user.process')->updateOwnerProfile($aProfile['user_id']);
			if(!$aNewProfile)
			{
				return false;
			}

			return $aNewProfile;
		}

		return $aProfile;
	}


	public function getDonorsOfCampaign($iCampaignId, $iPageSize = 5, $iPage = 1, $iTotal = 0)
	{
		if(!$iCampaignId)
		{
			return false;
		}

		$aRows = $this->database()->select($this->getDonorFields() . Phpfox::getUserField())
				->from(Phpfox::getT('fundraising_donor'), 'fd')
				->leftjoin(Phpfox::getT('user'), 'u', 'u.user_id = fd.user_id')
				->where('fd.campaign_id = ' . $iCampaignId)
				->order('fd.time_stamp DESC')
				->limit(0, $iPageSize * $iPage, $iTotal)
				->execute('getSlaveRows');


		return $aRows;
	}

	public function getTotalDonorsOfCampaign($iCampaignId)
	{
		if(!$iCampaignId)
		{
			return false;
		}

		$iTotal = (int) $this->database()->select('COUNT(fd.donor_id)')
				->from(Phpfox::getT('fundraising_donor'), 'fd')
				->where('fd.campaign_id = ' . $iCampaignId)
				->execute('getSlaveField');


		return $iTotal;
	}

	public function getEmailsOfGuestDonors($iCampaignId, $iPage = 0, $iPageSize = 1000)
	{
		if(!$iCampaignId)
		{
			return false;
		}

		$aRows = $this->database()->select('email_address, donor_id')
				->from(Phpfox::getT('fundraising_donor'))
				->where('campaign_id = ' . $iCampaignId . ' AND is_guest = 1 AND email_address IS NOT NULL AND email_address != \'\'')
				->limit($iPage, $iPageSize)
				->execute('getSlaveRows');

		return $aRows;
		
	}

	public function getIdsOfUserDonors($iCampaignId, $iPage = 0, $iPageSize = 1000)
	{
		if(!$iCampaignId)
		{
			return false;
		}

		$aRows = $this->database()->select('user_id, donor_id')
				->from(Phpfox::getT('fundraising_donor'))
				->where('campaign_id = ' . $iCampaignId . ' AND is_guest = 0')
				->limit($iPage, $iPageSize)
				->execute('getRows');


		return $aRows;
		
	}

    /**
     * get donor name to show and not conflict with full_name of user
     * @param $iDonorId
     * @return mixed
     */

    public function getDonorNameById($iDonorId) {
        return $this->database()->select('fd.full_name as guest_full_name, fd.is_guest, u.full_name')
            ->from(Phpfox::getT('fundraising_donor'), 'fd')
            ->leftjoin(Phpfox::getT('user'), 'u', 'u.user_id = fd.user_id')
            ->where('fd.donor_id = ' . $iDonorId)
            ->execute('getSlaveRow');
    }

	public function getFullNameOfUser($iUserId)
	{
		 return $this->database()->select('full_name')
            ->from(Phpfox::getT('user'))
            ->where('user_id = ' . $iUserId)
            ->execute('getSlaveField');	
	}

    /**
     * get user id list for addthis token back , we need user id to make token
     * @return mixed
     */

    public function getUserIdList() {
        return $this->database()->select("user_id")
                    ->from(Phpfox::getT('fundraising_supporter'))
                    ->execute('getSlaveRows');
    }

	public function checkSupporterExist($iUserId, $iCampaignId)
	{
		$aRow = $this->database()->select("user_id")
                    ->from(Phpfox::getT('fundraising_supporter'))
					->where('campaign_id =' . $iCampaignId . ' AND user_id  = ' .$iUserId )
                    ->execute('getSlaveRow');
		
		if(isset($aRow['user_id']) && $aRow['user_id'] > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

    /**
     * update total_comback for supporter
     * @param $sUserToken
     */

    public function updateSupporter($sType, $iUserId = 1) {
        switch($sType) {
            case 'comeback':
                $this->database()->updateCounter('fundraising_supporter', 'total_comeback', 'user_id', $iUserId);
                break;
            case 'share':
                $this->database()->updateCounter('fundraising_supporter', 'total_share', 'user_id', Phpfox::getUserId());
                break;
            default:
                break;
        }
    }

    /**
     * if this is first time share , we create this supporter in db
     * @param $sUserToken
     * @param $iCampaignId
     */

    public function addSupporter($iCampaignId) {
        $aInsert = array(
            'user_id' => Phpfox::getUserId(),
            'campaign_id' => $iCampaignId,
            'support_token' => md5(Phpfox::getUserId()),
            'total_share' => 1,
            'time_stamp' => PHPFOX_TIME,
        );

        $this->database()->insert(Phpfox::getT('fundraising_supporter'), $aInsert);
    }

    public function getShareClick($iCampaign_id) {
        $iShare = 0; $iClick = 0;
        $iShare = $this->database()->select('sum(total_share)')
                            ->from(Phpfox::getT('fundraising_supporter'))
                            ->where('campaign_id = ' . $iCampaign_id)
                            ->execute('getSlaveField');

        $iClick = $this->database()->select('sum(total_comeback)')
                            ->from(Phpfox::getT('fundraising_supporter'))
                            ->where('campaign_id = ' . $iCampaign_id)
                            ->execute('getSlaveField');

        return array($iShare, $iClick);
    }
}

?>
