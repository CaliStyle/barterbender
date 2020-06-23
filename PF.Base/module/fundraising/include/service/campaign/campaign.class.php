<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Service_Campaign_Campaign extends Phpfox_Service {

	private $_status = array(
		'ongoing' => 1,
		'reached' => 2,
		'expired' => 3,
		'closed' => 4,
		'draft' => 5,
		'pending' => 6
	);

	/**
	 * Hold the information about what controller call this function
	 *
	 * @var array 
	 */
	private $_aCallback = null;

	/**
	 * Initilialize the table that class will mainly work on
	 * @TODO: none 
	 * <pre>
	 * </pre>
	 * @by minhta
	 */
	public function __construct() {
		$this->_sTable = Phpfox::getT('fundraising_campaign');
	}


	public function checkIsCampaignInPage($iCampaignId)
	{
		$aCampaign = $this->database()->select('module_id')
			->from(Phpfox::getT('fundraising_campaign'))
			->where('campaign_id = ' . $iCampaignId)
			->execute('getSlaveRow');	

		if(isset($aCampaign['module_id']) && $aCampaign['module_id'] == 'pages')
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function getBasicInfoOfCampaign($iCampaignId)
	{
		$aCampaign = $this->database()->select('*')
			->from(Phpfox::getT('fundraising_campaign'))
			->where('campaign_id = ' . $iCampaignId)
			->execute('getSlaveRow');	

		return $aCampaign;
		
	}

	public function notifyToAllFollowers($iCampaignId, $sItemType = '', $iDonorUserId = 0)
	{
		$aFollowers = Phpfox::getService('fundraising.user')->getAlllFollowersOfCampaign($iCampaignId);
		$aCampaign = $this->database()->select('campaign.title, campaign.campaign_id, ' . Phpfox::getUserField())
				->from(Phpfox::getT('fundraising_campaign'), 'campaign')
				->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = campaign.user_id')
				->where('campaign.campaign_id = ' . $iCampaignId)
				->execute('getSlaveRow');	

		$aDonorUser = array();
		if($iDonorUserId)
		{
			$aDonorUser =  $this->database()->select('fd.campaign_id, fd.user_id, ' . Phpfox::getUserField())	
				->from(Phpfox::getT('fundraising_donor'), 'fd')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = fd.user_id')
				->where('fd.donor_id =  ' . (int) $iDonorUserId)
				->execute('getSlaveRow');
		}
		
		foreach($aFollowers as $aFollower)
		{
			switch($sItemType)
			{
				case 'image':
						(Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->add($sType = 'fundraising_notice_image', $iItemId = $iCampaignId, $aFollower['user_id'], $iSentUserId = $aCampaign['user_id']) : null);
					break;

				case 'video':
						(Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->add($sType = 'fundraising_notice_video', $iItemId = $iCampaignId, $aFollower['user_id'], $iSentUserId = $aCampaign['user_id']) : null);
					break;

				case 'news':
						(Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->add($sType = 'fundraising_notice_news', $iItemId = $iCampaignId, $aFollower['user_id'], $iSentUserId = $aCampaign['user_id']) : null);
					break;
				case 'reached':
						(Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->add($sType = 'fundraising_notice_reached', $iItemId = $iCampaignId, $aFollower['user_id'], $iSentUserId = $aCampaign['user_id']) : null);
					break;
				case 'expired':
						(Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->add($sType = 'fundraising_notice_expired', $iItemId = $iCampaignId, $aFollower['user_id'], $iSentUserId = $aCampaign['user_id']) : null);
					break;
				case 'closed':
						(Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->add($sType = 'fundraising_notice_closed', $iItemId = $iCampaignId, $aFollower['user_id'], $iSentUserId = $aCampaign['user_id']) : null);
					break;
				case 'donated':
						(Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->add($sType = 'fundraising_notice_donated', $iItemId = $iDonorUserId, $aFollower['user_id'], $iSentUserId = $aCampaign['user_id']) : null);

					break;
				default:
						(Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->add($sType = 'fundraising_notice_follower', $iItemId = $iCampaignId, $aFollower['user_id'], $iSentUserId = $aCampaign['user_id']) : null);
					break;
			}
		
		

			$aMessage = Phpfox::getService('fundraising.campaign')->getMessageNotifyingFollowers($sItemType, $aCampaign, $aDonorUser);
			$sSubject = $aMessage['subject'];
			$sMessage = $aMessage['message'];
			//here we use fox mailing function
			Phpfox::getLib('mail')->to($aFollower['user_id'])
				->subject($sSubject)
				->message($sMessage)
				->send();
			
		}
		
	}

	public function getMessageNotifyingFollowers($sType, $aCampaign, $aDonorUser = array())
	{
		$sLink = Phpfox::getLib('url')->permalink('fundraising', $aCampaign['campaign_id'], $aCampaign['title']);
		switch($sType)
		{
			case 'image':
				$sMessage = _p('full_name_updated_item_type_in_campaign_title_link', array('full_name' => $aCampaign['full_name'], 'title' => $aCampaign['title'], 'link' => $sLink, 'item_type' => _p('images_lower')) );
				$sSubject = _p('full_name_updated_item_type_in_campaign_title_link', array('full_name' => $aCampaign['full_name'], 'title' => $aCampaign['title'], 'item_type' => _p('images_lower'), 'link' => ''));
				break;
				
				break;

			case 'video':
				$sMessage = _p('full_name_updated_item_type_in_campaign_title_link', array('full_name' => $aCampaign['full_name'], 'title' => $aCampaign['title'], 'link' => $sLink, 'item_type' => _p('video_lower')) );
				$sSubject = _p('full_name_updated_item_type_in_campaign_title_link', array('full_name' => $aCampaign['full_name'], 'title' => $aCampaign['title'], 'item_type' => _p('video_lower'), 'link' => ''));
				break;

				break;

			case 'news':
				$sMessage = _p('full_name_updated_item_type_in_campaign_title_link', array('full_name' => $aCampaign['full_name'], 'title' => $aCampaign['title'], 'link' => $sLink, 'item_type' => _p('news_lower')) );
				$sSubject = _p('full_name_updated_item_type_in_campaign_title_link', array('full_name' => $aCampaign['full_name'], 'title' => $aCampaign['title'], 'item_type' => _p('news_lower'), 'link' => ''));
				break;

			case 'reached':
					$sMessage = _p('campaign_title_has_reached_its_goal', array('title' => $aCampaign['title'], 'link' => $sLink) );
					$sSubject = _p('campaign_title_has_reached_its_goal', array('title' => $aCampaign['title'], 'link' => '') );
					break;
			case 'expired':
					$sMessage = _p('campaign_title_has_expired', array('title' => $aCampaign['title'], 'link' => $sLink) );
					$sSubject = _p('campaign_title_has_expired', array('title' => $aCampaign['title'], 'link' => '') );
					break;
				break;
			case 'closed':
					$sMessage = _p('campaign_title_has_been_close_link', array('title' => $aCampaign['title'], 'link' => $sLink) );
					$sSubject = _p('campaign_title_has_been_close_link', array('title' => $aCampaign['title'], 'link' => '') );
				break;

			case 'donated':
					$sMessage = _p('full_name_donated_to_campaign_title_link', array('full_name' => $aDonorUser['full_name'], 'title' => $aCampaign['title'], 'link' => $sLink) );
					$sSubject = _p('full_name_donated_to_campaign_title_link', array('full_name' => $aDonorUser['full_name'], 'title' => $aCampaign['title'], 'link' => '') );
				break;
					
			default:

				$sMessage = _p('full_name_updated_item_type_in_campaign_title_link', array('full_name' => $aCampaign['full_name'], 'title' => $aCampaign['title'], 'link' => $sLink, 'item_type' => _p('some_infomation'), 'link' => '') );
				$sSubject = _p('full_name_updated_item_type_in_campaign_title_link', array('full_name' => $aCampaign['full_name'], 'title' => $aCampaign['title'], 'item_type' => _p('some_infomation')));
				break;
		}

		return array(
			'message' => $sMessage,
			'subject' => $sSubject
		);
	}
		

	public function getCampaignForCheckingPermission($iCampaignId)
	{
		if (Phpfox::isModule('friend'))
		{
			$this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = camp.user_id AND f.friend_user_id = " . Phpfox::getUserId());					
		}
		
		$aCampaign = $this->database()->select('camp.*')
			->from($this->_sTable, 'camp')
			->where('camp.campaign_id = ' . $iCampaignId)
			->execute('getSlaveRow');		

		return $aCampaign;
	}
	public function searchCampaigns($aConds, $sSort = 'campaign.title ASC', $iPage = '', $iLimit = '')
	{
		
		$iCnt = $this->database()->select('COUNT(*)')
			->from($this->_sTable, 'campaign')
			->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = campaign.user_id')
			->where($aConds)
			->order($sSort)
			->execute('getSlaveField');
		$aStatus = array(
			Phpfox::getService('fundraising.campaign')->getStatusCode('closed') => _p('closed'),
			Phpfox::getService('fundraising.campaign')->getStatusCode('ongoing') => _p('on_going'),
			Phpfox::getService('fundraising.campaign')->getStatusCode('expired') => _p('expired'),
			Phpfox::getService('fundraising.campaign')->getStatusCode('reached') => _p('reached'),
			Phpfox::getService('fundraising.campaign')->getStatusCode('draft') => _p('draft'),
			Phpfox::getService('fundraising.campaign')->getStatusCode('pending') => _p('pending')
		);
		$aItems = array();
		if ($iCnt)
		{		
			$aItems = $this->database()->select('campaign.*, ' . Phpfox::getUserField())
				->from($this->_sTable, 'campaign')
				->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = campaign.user_id')
				->where($aConds)
				->order($sSort)
				->limit($iPage, $iLimit, $iCnt)
				->execute('getSlaveRows');
			foreach ($aItems as $iKey => $aItem)
			{
				
				$aItems[$iKey]['campaign_status_text'] = $aStatus[$aItem['status']];
				if($aItem['is_approved'] == 0)
				{
					$aItems[$iKey]['campaign_status_text']  = _p('pending');
				}				
				$aItems[$iKey]['link'] = ($aItem['user_id'] ? Phpfox::getLib('url')->permalink($aItem['user_name'] . '.fundraising', $aItem['campaign_id'], $aItem['title']) : Phpfox::getLib('url')->permalink('fundraising', $aItem['campaign_id'], $aItem['title']));
			}
		}
			
		
		$this->processRows($aItems);
		
		return array($iCnt, $aItems);
	}	
	
	public function processRows(&$aRows)
	{
		foreach ($aRows as $iKey => $aRow)
		{
			if($aRow['module_id'] === 'pages')
			{
				$aPage = Phpfox::getService('pages')->getPage($aRow['item_id']);
				if($aPage['vanity_url'])
				{
					$aRows[$iKey]['page_link'] = Phpfox::permalink($aPage['vanity_url'], 'fundraising');
				}
				else
				{
					$aRows[$iKey]['page_link'] = Phpfox::permalink('pages', $aRow['item_id'], 'fundraising');	
				}
				$aRows[$iKey]['page_name'] = $aPage['title'];
			}			
		}
	}	

	public function retrieveMoreInfoFromCampaign($aCampaign, $bRetrievePermission = false)
	{
		if(!$aCampaign['financial_goal'])
		{
			$iPercent = (int) ((round($aCampaign['total_amount'] / ($aCampaign['total_amount'] + 1000),2)) * 100);
			$aCampaign['financial_percent'] = $iPercent . '%'  ;
		}
		else
		{
			$iPercent = (int) (round(($aCampaign['total_amount'] /$aCampaign['financial_goal']),2) * 100) ;
			$aCampaign['financial_percent'] = $iPercent . '%'  ;
		}

		if(isset($aCampaign['end_time']) && $aCampaign['end_time'] > PHPFOX_TIME)
		{

			$aCampaign['remain_time'] = Phpfox::getService('fundraising.helper')->convertTimeToCountdownString($aCampaign['end_time']);
		}
		else if (!isset($aCampaign['end_time']))
		{
			$aCampaign['remain_time'] = _p('unlimited_time_upper');
		}
		else if ($aCampaign['end_time'] < PHPFOX_TIME) 
		{
			$aCampaign['remain_time'] = _p('expired');
		}

		if($bRetrievePermission)
		{

			$aCampaign['can_donate_campaign'] = Phpfox::getService('fundraising.permission')->canDonateCampaign($aCampaign['campaign_id'], Phpfox::getUserId());


			$aCampaign['can_edit_campaign'] = Phpfox::getService('fundraising.permission')->canEditCampaign($aCampaign['campaign_id'], Phpfox::getUserId());
			$aCampaign['can_delete_campaign'] = Phpfox::getService('fundraising.permission')->canDeleteCampaign($aCampaign['campaign_id'], Phpfox::getUserId());
			$aCampaign['can_feature_campaign'] = Phpfox::getService('fundraising.permission')->canFeatureCampaign($aCampaign['campaign_id'], Phpfox::getUserId());
			$aCampaign['can_highlight_campaign'] = Phpfox::getService('fundraising.permission')->canHighlightCampaign($aCampaign['campaign_id'], Phpfox::getUserId());
			$aCampaign['can_close_campaign'] = Phpfox::getService('fundraising.permission')->canCloseCampaign($aCampaign['campaign_id'], Phpfox::getUserId());
			$aCampaign['can_view_statistic'] = Phpfox::getService('fundraising.permission')->canViewStatisticCampaign($aCampaign['campaign_id'], Phpfox::getUserId());
			$aCampaign['can_email_to_all_donors'] = Phpfox::getService('fundraising.permission')->canEmailToAllDonorsCampaign($aCampaign['campaign_id'], Phpfox::getUserId());

			$aCampaign['having_action_button'] = false;
			if($aCampaign['can_edit_campaign'] || $aCampaign['can_delete_campaign'] || $aCampaign['can_feature_campaign'] || $aCampaign['can_highlight_campaign'] || $aCampaign['can_close_campaign'] || $aCampaign['can_view_statistic'] || $aCampaign['can_email_to_all_donors'])
			{
				$aCampaign['having_action_button'] = true;
			}


		}

		$aCampaign['total_amount_text'] = Phpfox::getService('fundraising')->getCurrencyText($aCampaign['total_amount'], $aCampaign['currency']);
		$aCampaign['financial_goal_text'] = Phpfox::getService('fundraising')->getCurrencyText($aCampaign['financial_goal'], $aCampaign['currency']);

		return $aCampaign;
		
	}

	public function getOwnerOfCampaign($iCampaignId) {
		$iUserId = $this->database()->select('user_id')
				->from($this->_sTable, 'camp')
				->where('campaign_id = ' . $iCampaignId)
				->execute('getSlaveField');

		return $iUserId;
	}

	/**
	 * get status number based on the name of status
	 * @by minhta
	 * @param string $sStatus name of status we want to retrieve
	 * @return
	 */
	public function getStatusCode($sStatus) {
		if (isset($this->_status[$sStatus])) {
			return $this->_status[$sStatus];
		} else {
			return false;
		}
	}

	public function getAllStatus() {
		return $this->_status;
	}

	public function getUneditableStatus() {
		return array(
			Phpfox::getService('fundraising.campaign')->getStatusCode('closed')	,
			Phpfox::getService('fundraising.campaign')->getStatusCode('expired'),
			Phpfox::getService('fundraising.campaign')->getStatusCode('reached')
		);	
	}
	public function getAllStatusWithText() {
		$aStatus = array(
			'' . Phpfox::getService('fundraising.campaign')->getStatusCode('closed') => _p('closed'),
			'' . Phpfox::getService('fundraising.campaign')->getStatusCode('ongoing') => _p('on_going'),
			'' . Phpfox::getService('fundraising.campaign')->getStatusCode('expired') => _p('expired'),
			'' . Phpfox::getService('fundraising.campaign')->getStatusCode('reached') => _p('reached'),
			'' . Phpfox::getService('fundraising.campaign')->getStatusCode('draft') => _p('draft'),
		);
		return $aStatus;
	}

	public function getCampaigns($sType = 'latest', $iLimit = 9, $aCustom = array()) {

		$aCampaigns = array();
		$oCustomCache = Phpfox::getService('fundraising.cache');
		$mConditions = 'camp.module_id = \'fundraising\' AND camp.privacy = 0 AND camp.is_active = 1 AND ';

		$iPage = 1;
		$iPageSize = $iLimit;
		switch ($sType) {
			case 'latest':
					$sKey = 'latest' . $iLimit ;
					$sType = 'latest';
					$mConditions .= 'camp.status = ' . Phpfox::getService('fundraising.campaign')->getStatusCode('ongoing');;
					$sOrder = 'start_time DESC';
				break;
			case 'featured':
					$sKey = 'featured' . $iLimit ;
					$sType = 'featured';
					$mConditions .= 'camp.is_featured = 1';
					$mConditions .= ' AND camp.status = ' . Phpfox::getService('fundraising.campaign')->getStatusCode('ongoing');;
					$sOrder = 'start_time DESC';
				break;
			case 'most-donated':
					$sKey = 'most-donated' . $iLimit ;
					$sType = 'most-donated';
					$mConditions .= 'camp.status = ' . Phpfox::getService('fundraising.campaign')->getStatusCode('ongoing');;
					$sOrder = 'total_donor DESC';
				break;
			case 'most-liked':
					$sKey = 'most-liked' . $iLimit ;
					$sType = 'most-liked';
					$mConditions .= 'camp.status = ' . Phpfox::getService('fundraising.campaign')->getStatusCode('ongoing');;
					$sOrder = 'total_like DESC';
				break;
			default :
				$mConditions = '';
				$sOrder = 'campaign_id';
				
				break;
		}

		
		
		
		if (!($aCampaigns = $oCustomCache->get($sKey, $sType)))
		{
			$iCnt = $this->database()->select('COUNT(*)')
					->from($this->_sTable, 'camp')
					->where($mConditions)
					->execute('getSlaveField');

			if ($iCnt) {

				$aCampaigns = $this->database()->select(Phpfox::getUserField() . ', camp.*')
						->from($this->_sTable, 'camp')
						->join(Phpfox::getT('user'), 'u', 'u.user_id = camp.user_id')
						->where($mConditions)
						->order($sOrder)
						->limit($iPage, $iPageSize, $iCnt)
						->execute('getSlaveRows');
				
				foreach($aCampaigns as &$aCampaign)
				{
					$aCampaign = Phpfox::getService('fundraising.campaign')->retrieveMoreInfoFromCampaign($aCampaign);	
				}

				$oCustomCache->set($sKey, $aCampaigns);
			}

			
		}
		
		if(!$aCampaigns)
		{
			$aCampaigns = array();
		}

	
		return $aCampaigns;
	}

	public function getDefaultCurrency() {
		return 'USD';
	}

	/**
	 * we will append some thing into list of campaign before output to template
	 * @TODO: verify later 
	 * <pre>
	 * Phpfox::getService('fundraising.campaign')->addExtra;
	 * </pre>
	 * @by minhta
	 * @param array $aCampaigns 
	 * @return
	 */
	public function addExtra($aCampaigns) {
		
	}

	/**
	 * get fields will be selected for a campaign
	 * @TODO: need to improve by some pattern to remove hard coded alias 
	 * <pre>
	 * Phpfox::getService('fundraising.campaign')->getFieldsForCampaign();
	 * </pre>
	 * @by minhta
	 * @return string 
	 */
	public function getFieldsForCampaign() {
		return 'c.*,c.country_iso as info_country_iso, u.full_name as owner_name, ' . $this->getFieldForText() . $this->getFieldsForContacInfo() . Phpfox::getUserField().', c.server_id as campaign_server_id';
	}

    /**
     * get field for text , because it conflict with user table
     * @TODO: same with getFieldsForCampaign()
     * @by datlv
     * @return string
     */

    public function getFieldForText() {
        return 'ft.description_parsed as description, ft.*, ';
    }

    /**
     * get field for contact info , because it conflict with user table
     * @TODO: same with getFieldsForCampaign()
     * @by datlv
     * @return string
     */

    public function getFieldsForContacInfo() {
        return 'fci.full_name as contact_full_name , fci.phone as contact_phone, fci.email_address as contact_email_address, fci.country as contact_country_iso, fci.city as contact_city, fci.state as contact_state, fci.street as contact_street, fci.about_me as contact_about_me, ';
    }

	/**
	 * check permission for a campaign
	 * @TODO: need to improve more about description of this function 
	 * <pre>
	 * Phpfox::getService('fundraising.campaign')->hasAccess();
	 * </pre>
	 * @by minhta
	 * @param
	 * @return boolean true if having permission or false if else
	 */
	public function hasAccess($iCampaignId, $sUserPerm, $sGlobalPerm) {
		(($sPlugin = Phpfox_Plugin::get('fundraising.service_campaign_hasaccess_start')) ? eval($sPlugin) : false);

		$aRow = $this->database()->select('u.user_id, c.module_id, c.item_id')
				->from($this->_sTable, 'c')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
				->where('c.campaign_id = ' . (int) $iCampaignId)
				->execute('getSlaveRow');


		(($sPlugin = Phpfox_Plugin::get('campaign.service_campaign_hasaccess_end')) ? eval($sPlugin) : false);

		if (!isset($aRow['user_id'])) {
			return false;
		}

		if ($aRow['module_id'] == 'pages' && Phpfox::getService('pages')->isAdmin($aRow['item_id'])) {
			return true;
		}

		if ((Phpfox::getUserId() == $aRow['user_id'] && Phpfox::getUserParam('fundraising.' . $sUserPerm, true)) || Phpfox::getUserParam('fundraising.' . $sGlobalPerm, true)) {
			return $aRow['user_id'];
		}
		(($sPlugin = Phpfox_Plugin::get('campaign.component_service_campaign_getcampaign__end')) ? eval($sPlugin) : false);
		return false;
	}

	public function getMockupCampaign($iCampaignId) {
		return array(
		);
	}

	//minhta



	public function callback($aCallback) {
		$this->_aCallback = $aCallback;

		return $this;
	}

	public function getCampaignThankyouDonorTemplate($iCampaignId)
	{
		$aRow = $this->database()->select('email_subject, email_message')
				->from(Phpfox::getT('fundraising_text'))
				->where('campaign_id = ' . $iCampaignId)
				->execute('getSlaveRow');

		if($aRow)
		{
			$aRow['email_template'] = $aRow['email_message'];
		}
		return $aRow;
	}

	/**
	 * get campaign by Id
	 * @TODO: complete it later 
	 * <pre>
	 * Phpfox::getService('fundraising.campaign')->getCampaignById($iCampaignId);
	 * </pre>
	 * @by minhta
	 * @param
	 * @return array
	 */
	public function getCampaignById($iCampaignId) {
		if(!$iCampaignId)
		{
			return [];
		}

		if (Phpfox::isModule('like'))
		{
			$this->database()->select('lik.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'lik', 'lik.type_id = \'fundraising\' AND lik.item_id = c.campaign_id AND lik.user_id = ' . Phpfox::getUserId());
		}	
		

		if (Phpfox::isModule('friend'))
		{
			$this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = c.user_id AND f.friend_user_id = " . Phpfox::getUserId());					
		}

		$aRow = $this->database()->select($this->getFieldsForCampaign() . ', fr.rating as has_rated, fd.donor_id as has_donated, ff.follow_id as is_followed')
				->from($this->_sTable, 'c')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
				->leftJoin(Phpfox::getT('fundraising_text'), 'ft', 'ft.campaign_id = c.campaign_id')
				->leftJoin(Phpfox::getT('fundraising_rating'), 'fr', 'fr.item_id = c.campaign_id AND fr.user_id = ' . (Phpfox::isUser() ? Phpfox::getUserId() : 0))
				->leftJoin(Phpfox::getT('fundraising_donor'), 'fd', 'fd.campaign_id = c.campaign_id AND fd.user_id = ' . (Phpfox::isUser() ? Phpfox::getUserId() : 0))
				->leftJoin(Phpfox::getT('fundraising_follow'), 'ff', 'ff.campaign_id = c.campaign_id AND ff.user_id = ' . (Phpfox::isUser() ? Phpfox::getUserId() : 0))
				->leftJoin(Phpfox::getT('fundraising_contact_info'), 'fci', 'fci.campaign_id = c.campaign_id')
				->where('c.campaign_id = ' . (int) $iCampaignId)
				->execute('getSlaveRow');
		if ($aRow) {
			$aPredefines = unserialize($aRow['predefined_amount_list']);
			if($aPredefines)
			{
				asort($aPredefines);
			}
			$aRow['predefined_amount_list'] = $aPredefines;
            $aRow['sponsor_level'] = unserialize($aRow['sponsor_level']);
            $aRow['allow_anonymous'] = ($aRow['allow_anonymous'])?'checked':'';
            $aRow['unlimit_time'] = ($aRow['end_time'])? '' : 'checked';
			$aRow['country_iso'] = $aRow['info_country_iso'];
		}
		return $aRow;
	}

	public function getCampaignForEdit($iCampaignId) {
		$aCampaign = $this->getCampaignById($iCampaignId);
		$aCampaign['categories'] = Phpfox::getService('fundraising.category')->getCategoryIds($iCampaignId);
		return $aCampaign;
	}

	public function getMockupCampaign1($iNumber) {
		$aCampaigns = array();

		for ($i = 0; $i < $iNumber; $i++) {
			$aCampaigns[] = array(
				'description' => 'dfasdfadsfadsf',
				//mock up for feature slide
				'category' => array(
					'link' => '#',
					'name' => 'Animals'
				),
				'short_description' => 'haha',
				'category_id' => '1',
				'campaign_goal' => 100,
				'target' => 'no thing',
				'category_name' => 'Animals',
				'is_liked' => null,
				'campaign_id' => '2',
				'user_id' => '3',
				'signature_goal' => 100,
				'can_sign' => true,
				'module_id' => 'fundraising',
				'item_id' => '0',
				'title' => 'extrapolate ' . $i,
				'time_stamp' => '1350980060',
				'start_time' => '1350980060',
				'end_time' => '1351580760',
				'image_path' => 'fundraising/2012/10/8ac8bce0186ef0e0d6a086fef947aaf5%s.jpg',
				'server_id' => '0',
				'is_approved' => '1',
				'is_featured' => '0',
				'is_hightlight' => '0',
				'is_send_thank' => '1',
				'is_send_online' => '0',
				'is_directsign' => '0',
				'privacy' => '0',
				'privacy_comment' => '0',
				'privacy_sign' => '0',
				'status' => '2',
				'total_sign' => '0',
				'total_comment' => '0',
				'total_donor' => '0',
				'total_amount' => 30,
				'financial_goal' => 100,
				'total_attachment' => '0',
				'total_view' => '0',
				'total_like' => '0',
				'profile_page_id' => '0',
				'user_server_id' => '0',
				'user_name' => 'profile-3',
				'full_name' => 'Admin',
				'gender' => '1',
				'user_image' => null,
				'is_invisible' => '0',
				'user_group_id' => '1',
				'language_id' => 'en',
				'info' => 'Posted October 23, 2012 by <a href="http://minhta.younetco.com/minhta/index.php?do=/profile-3/">Admin</a> in <a href="http://minhta.younetco.com/minhta/index.php?do=/fundraising/category/1/animals/">Animals</a>',
				'bookmark_url' => 'http://minhta.younetco.com/minhta/index.php?do=/fundraising/2/extrapolate/',
				'aFeed' =>
				array(
					'feed_display' => 'mini',
					'comment_type_id' => 'fundraising',
					'privacy' => '0',
					'comment_privacy' => '0',
					'like_type_id' => 'fundraising',
					'feed_is_liked' => false,
					'feed_is_friend' => false,
					'item_id' => '2',
					'user_id' => '3',
					'total_comment' => '0',
					'feed_total_like' => '0',
					'total_like' => '0',
					'feed_link' => 'http://minhta.younetco.com/minhta/index.php?do=/fundraising/2/extrapolate/',
					'feed_title' => 'extrapolate',
					'time_stamp' => '1350980060',
					'report_module' => 'fundraising',
				)
			);
		}
		return $aCampaigns;
	}

	/**
	 * get featured campaigns and number of featured campaigns
	 * @TODO: complete later 
	 * <pre>
	 * </pre>
	 * @by minhta
	 * @param
	 * @return
	 */
	public function getFeatured($iLimit) {
		return array(count($this->getMockupCampaign1(2)), $this->getMockupCampaign1(2));
	}

	/**
	 * get number of total pending campaigns
	 * <pre>
	 * Phpfox::getService('fundraising.campaign')->getTotalPendings(); 
	 * </pre>
	 * @by minhta
	 * @return int total pending campaigns
	 */
	public function getTotalPendings() {
		
		$iTotal = (int) $this->database()->select('COUNT(*)')
							->from($this->_sTable)
							->where('is_approved = 0 AND module_id = \'fundraising\' AND is_active = 1')
							->execute('getSlaveField');


		return $iTotal;
	}

	// ------------------ minh ta
	// datlv ------------------------

	/**
	 * get hightlight campaign to show block , 1 hightlight campaign at 1 time , admin set for hightlight
	 * @TODO : create skeleton wait for design to fill .
	 *  <pre>
	 * Phpfox::getService('fundraising.campaign')->getHightlightCampaign($iCampaignId);
	 * </pre>
	 * @by datlv
	 * @return array
	 */
	public function getHightlightCampaign() {

		$campaign = $this->database()->select('c.*, u.*, ft.*, c.server_id as campaign_server_id')
				->from($this->_sTable, 'c')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
				->leftJoin(Phpfox::getT('fundraising_text'), 'ft', 'ft.campaign_id = c.campaign_id')
				->where('c.is_highlighted = 1 AND c.status = ' . Phpfox::getService('fundraising.campaign')->getStatusCode('ongoing'))
				->execute('getSlaveRow');

		return $campaign;
	}

	public function getMockupCampaignForEdit() {
		$aCampaigns = array(
			'description' => 'dfasdfadsfadsf',
			//mock up for feature slide
			'category' => array(
				'link' => '#',
				'name' => 'Animals'
			),
			'short_description' => 'haha',
			'category_id' => '1',
			'campaign_goal' => 100,
			'target' => 'no thing',
			'category_name' => 'Animals',
			'is_liked' => null,
			'campaign_id' => '1',
			'user_id' => '3',
			'module_id' => 'fundraising',
			'item_id' => '0',
			'title' => 'extrapolate 1',
			'time_stamp' => '1350980060',
			'start_time' => '1350980060',
			'end_time' => '1351580760',
			'image_path' => 'fundraising/2012/10/8ac8bce0186ef0e0d6a086fef947aaf5%s.jpg',
			'server_id' => '0',
			'is_approved' => '1',
			'is_featured' => '0',
			'is_hightlight' => '0',
			'is_send_thank' => '1',
			'is_send_online' => '0',
			'is_directsign' => '0',
			'privacy' => '0',
			'privacy_comment' => '0',
			'privacy_sign' => '0',
			'status' => '2',
			'total_sign' => '0',
			'total_comment' => '0',
			'total_donor' => '0',
			'total_amount' => 30,
			'financial_goal' => 100,
			'total_attachment' => '0',
			'total_view' => '0',
			'total_like' => '0',
			'profile_page_id' => '0',
			'user_server_id' => '0',
			'user_name' => 'profile-3',
			'full_name' => 'Admin',
			'gender' => '1',
			'user_image' => null,
			'is_invisible' => '0',
			'user_group_id' => '1',
			'language_id' => 'en',
			'info' => 'Posted October 23, 2012 by <a href="http://minhta.younetco.com/minhta/index.php?do=/profile-3/">Admin</a> in <a href="http://minhta.younetco.com/minhta/index.php?do=/fundraising/category/1/animals/">Animals</a>',
			'bookmark_url' => 'http://minhta.younetco.com/minhta/index.php?do=/fundraising/2/extrapolate/',
			'location_venue' => 'ho chi minh',
			'address' => '',
			'city' => 'ho chi minh',
			'country_iso' => 'vn',
			'country' => 'vietnam',
			'postal_code' => '70000',
			'gmap' => 'a:2:{s:8:"latitude";s:17:"37.35977070000001";s:9:"longitude";s:18:"-86.74498949999997";}',
			'about' => 'zzzz',
			'full_nam' => 'datlv',
			'phone' => '1234567789',
			'email_address' => 'datlv@younetco.com',
			'country' => 'viet name',
			'state' => 'dong bang song cuu long',
			'city' => 'ho chi minh',
			'street' => 'ttk',
			'about_me' => 'developer',
		);

		return $aCampaigns;
	}

	//----------------- datlv
	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
	 */
	public function __call($sMethod, $aArguments) {
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('fundraising.service_category_category__call')) {
			return eval($sPlugin);
		}

		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}

}

?>
