<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Service_Campaign_Process extends Phpfox_Service {

	private $_aCategories = array();

	private $_bIsPublished = false;

	/**
	 * Initilialize the table that class will mainly work on
	 * @by minhta
	 */
	public function __construct() {
		$this->_sTable = Phpfox::getT('fundraising_campaign');
	}


	public function updateViewCounter($iCampaignId)
	{
		$this->database()->updateCounter('fundraising_campaign', 'total_view', 'campaign_id', $iCampaignId);
	}

	public function delete($iCampaignId)
	{         
		$aCampaign = Phpfox::getService('fundraising.campaign')->getCampaignForEdit($iCampaignId);
		if (!isset($aCampaign['campaign_id']))
		{
			return false;
		}
            
		$iUserId = Phpfox::getService('fundraising.campaign')->hasAccess($iCampaignId, 'delete_own_campaign', 'delete_user_campaign');
		
		if (!$iUserId)
		{
		   return false;
		}
		
		$aTempImages = $this->database()->select("image_id")->from(Phpfox::getT('fundraising_image'))->where("campaign_id = '$iCampaignId'")->execute("getRows");
		
		foreach($aTempImages as $aImage)
		{
			Phpfox::getService('fundraising.image.process')->delete($aImage['image_id']);	
		}
		
	    $this->database()->delete(Phpfox::getT('fundraising_campaign'), "campaign_id = " . (int) $iCampaignId);		
	    $this->database()->delete(Phpfox::getT('fundraising_text'), "campaign_id = " . (int) $iCampaignId);		
		$this->database()->delete(Phpfox::getT('fundraising_donor'), 'campaign_id  = ' . (int)$iCampaignId);
		$this->database()->delete(Phpfox::getT('fundraising_supporter'), 'campaign_id  = ' . (int)$iCampaignId);
		$this->database()->delete(Phpfox::getT('fundraising_news'), 'campaign_id  = ' . (int)$iCampaignId);
		$this->database()->delete(Phpfox::getT('fundraising_invited'), 'campaign_id  = ' . (int)$iCampaignId);
		$this->database()->delete(Phpfox::getT('fundraising_rating'), 'item_id  = ' . (int)$iCampaignId);
		$this->database()->delete(Phpfox::getT('fundraising_follow'), 'campaign_id  = ' . (int)$iCampaignId);
		$this->database()->delete(Phpfox::getT('fundraising_contact_info'), 'campaign_id  = ' . (int)$iCampaignId);
		$this->database()->delete(Phpfox::getT('fundraising_video'), 'campaign_id  = ' . (int)$iCampaignId);
		
		$this->database()->delete(Phpfox::getT('fundraising_campaign_category'), "campaign_id = " . $iCampaignId);

		(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('fundraising',(int) $iCampaignId) : null);
		(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('comment_fundraising', $iCampaignId) : null);		
		
            //Delete pages feed
		if($aCampaign['module_id'] == 'pages')
		{
		   $sType = 'fundraising';
		   
		   $aFeeds = $this->database()->select('feed_id, user_id')
				->from(Phpfox::getT($aCampaign['module_id'] . '_feed'))
				->where('type_id = \'' . $sType . '\' AND item_id = ' . (int) $iCampaignId )
				->execute('getRows');
		
		   foreach ($aFeeds as $aFeed)
		   {			
			  $this->database()->delete(Phpfox::getT($aCampaign['module_id'] . '_feed'), 'feed_id = ' . $aFeed['feed_id']);
		   }
		}            

		// Update user activity
		Phpfox::getService('user.activity')->update($aCampaign['user_id'], 'fundraising', '-');
		
		$this->cache()->remove('fundraising', 'substr');		
		
		return true;
	}

	public function createFeedForASuccessfullyCreatedCampaign($aVals, $iCampaignId, $iOwnerId = 0)
	{
		if(!$iOwnerId)
		{
			$iOwnerId = Phpfox::getUserId();
		}
		$aCallback = ((!empty($aVals['module_id']) && $aVals['module_id'] != 'fundraising') ? Phpfox::getService('fundraising')->getFundraisingAddCallback($iCampaignId) : null);

		// if the one who published this campaign is not the owner, we are not gonna allow to post feed on page 
		// due to a bug in fox add feed function
		if($iOwnerId != Phpfox::getUserId())
		{
			$aCallback = NULL;
		}

        //if there was publisher , not send feed now , wait for upload picture to send feed
		if ($this->_bIsPublished) {
			(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback($aCallback)->add('fundraising', $iCampaignId, $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int) $aVals['privacy_comment'] : 0), (isset($aVals['item_id']) ? (int) $aVals['item_id'] : 0), $iOwnerId) : null);
			//public function add($sType, $iItemId, $iPrivacy = 0, $iPrivacyComment = 0, $iParentUserId = 0, $iOwnerUserId = null)
			// Update user activity
		Phpfox::getService('user.activity')->update($iOwnerId, 'fundraising');

			
		}
		
	}

	public function addCategoriesForCampaign($iCampaignId)
	{

		if (isset($this->_aCategories) && count($this->_aCategories))
		{				
			$this->database()->delete(Phpfox::getT('fundraising_campaign_category'), 'campaign_id = ' . (int) $iCampaignId);
			
			foreach ($this->_aCategories as $iCategoryId)
			{
				$this->database()->insert(Phpfox::getT('fundraising_campaign_category'), array('campaign_id' => $iCampaignId, 'category_id' => $iCategoryId));
			}		
		}
	}

	//this function will catch all categories submited and store them into $_aCategories for later use
	public function getCategoriesFromForm($aVals)
	{
		if (isset($aVals['category']) && count($aVals['category']))
		{
			
		    if(empty($aVals['category'][0]))
		    {
				return Phpfox_Error::set(_p('provide_a_category_this_campaign_will_belong_to'));
		    }
		    else{
				foreach ($aVals['category'] as $iCategory)
				{		
					if (empty($iCategory))
					{
						continue;
					}
					
					if (!is_numeric($iCategory))
					{
						continue;
					}			
					
					$this->_aCategories[] = $iCategory;
				}
			}
		}
	}

	/**
	 * check whether ongoings campaing is expired or reached and update status
	 * currently we don't distinct page and module's campaigns, we will update them all at once
	 * by default we will update all campaign status
	 * @aParam array to specific some more information like is it in page or not ,...  
	 */
	public function checkAndUpdateStatusOfCampaigns($aParam = array())
	{
		$aUpdate = array(
			'is_closed' => true, // all state updated is closed
			'time_stamp' => PHPFOX_TIME
		);

		$sPreCond = 'status = ' . Phpfox::getService('fundraising.campaign')->getStatusCode('ongoing') ;
		
		if(isset($aParam['custom_condition']))
		{
			$sPreCond .= $aParam['custom_condition'];
		}



		// firsly we find reached campaign
		$aUpdate['status'] = Phpfox::getService('fundraising.campaign')->getStatusCode('reached');
		$sCond = $sPreCond .  ' AND financial_goal IS NOT NULL AND financial_goal <= total_amount';
		$aReachedRows = $this->database()->select('campaign_id')
				->from($this->_sTable)
				->where($sCond)
				->execute('getSlaveRows');
		foreach($aReachedRows as $aRow)
		{
			Phpfox::getService('fundraising.campaign.process')->reachCampaign($aRow['campaign_id']);
		}

		// secondly we find expired campaign
		$aUpdate['status'] = Phpfox::getService('fundraising.campaign')->getStatusCode('expired');
		$sCond = $sPreCond . ' AND end_time IS NOT NULL AND end_time <= ' . PHPFOX_TIME;
		$aExpiredRows = $this->database()->select('campaign_id')
				->from($this->_sTable)
				->where($sCond)
				->execute('getSlaveRows');
		foreach($aExpiredRows as $aRow)
		{
			Phpfox::getService('fundraising.campaign.process')->expireCampaign($aRow['campaign_id']);
		}

		return true;
	}


	public function checkAndUpdateStatusOfACampaign($iCampaignId)
	{
		$sCustomCond = ' AND campaign_id = ' . $iCampaignId . ' AND status = ' . Phpfox::getService('fundraising.campaign')->getStatusCode('ongoing');
		$aParam = array(
			'custom_condition' => $sCustomCond
		);

		Phpfox::getService('fundraising.campaign.process')->checkAndUpdateStatusOfCampaigns($aParam);
	}

	/**
	 * once a campaign is closed, it will never be opened again so we need to check permission really carefully
	 * @todo check permission,  send notification
	 * @by minhta
	 */
	public function closeCampaign($iCampaignId, $sReason = null)
	{
		$aCampaign = $this->database()->select('c.*, ' . Phpfox::getUserField())
				->from(Phpfox::getT('fundraising_campaign'), 'c')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
				->where('c.campaign_id = ' . (int) $iCampaignId)
				->execute('getRow');

		if(!$aCampaign)
		{
			return false;
		}

		if($sReason)
		{

			Phpfox::getService('fundraising.mail')->setCloseReason($sReason);
			//we send mail first 
			if(!Phpfox::getService('fundraising.mail.process')->sendEmailToOwner(Phpfox::getService('fundraising.mail')->getTypesCode('campaignclose_owner'),$iCampaignId))
			{
				return false;
			}
		}

		Phpfox::getService('fundraising.campaign')->notifyToAllFollowers($iCampaignId, 'closed');
		
		if(!$this->database()->update(Phpfox::getT('fundraising_campaign'), array('is_closed' => '1', 'status' => Phpfox::getService('fundraising.campaign')->getStatusCode('closed')), 'campaign_id = ' . $iCampaignId))	
		{
			return false;
		}
		Phpfox::getService('fundraising.cache')->removeAll();

		return true;

	}

	public function approve($iCampaignId) {
		Phpfox::getUserParam('fundraising.can_approve_campaigns', true);

		$aCampaign = $this->database()->select('c.*, ' . Phpfox::getUserField())
				->from(Phpfox::getT('fundraising_campaign'), 'c')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
				->where('c.campaign_id = ' . (int) $iCampaignId)
				->execute('getRow');

		if (!isset($aCampaign['campaign_id'])) {
			return Phpfox_Error::set(_p('the_fundraising_you_are_trying_to_approve_is_not_valid'));
		}
		if (isset($aCampaign['is_active']) && $aCampaign['is_active'] == '0') {
			return Phpfox_Error::set(_p('active_before_approve'));
		}

		if ($aCampaign['is_approved'] == '1') {
			return false;
		}

		$this->database()->update(Phpfox::getT('fundraising_campaign'), array('is_approved' => '1', 'status' => Phpfox::getService('fundraising.campaign')->getStatusCode('ongoing'), 'time_stamp' => PHPFOX_TIME, 'start_time' => PHPFOX_TIME), 'campaign_id = ' . $aCampaign['campaign_id']);


		$this->_bIsPublished = true;
		$this->createFeedForASuccessfullyCreatedCampaign($aCampaign, $aCampaign['campaign_id'], $iOwnerId = $aCampaign['user_id']);
		if (Phpfox::isModule('notification')) {
			Phpfox::getService('notification.process')->add('fundraising_approved', $aCampaign['campaign_id'], $aCampaign['user_id']);
		}

		//if this campaign is created by spamming, it means is_approved field is set 9 so we handle it here
		if ($aCampaign['is_approved'] == '9') {
			$this->database()->updateCounter('user', 'total_spam', 'user_id', $aCampaign['user_id'], true);
		}


		// Send the user an email
		$sLink = Phpfox::getLib('url')->permalink('fundraising', $aCampaign['campaign_id'], $aCampaign['title']);
		Phpfox::getLib('mail')->to($aCampaign['user_id'])
				->subject(array('fundraising.your_fundraising_has_been_approved_on_site_title', array('site_title' => Phpfox::getParam('core.site_title'))))
				->message(array('fundraising.your_fundraising_has_been_approved_on_site_title_message', array('site_title' => Phpfox::getParam('core.site_title'), 'link' => $sLink)))
				->notification('fundraising.fundraising_is_approved')
				->send();
		$this->cache()->remove('fundraising', 'substr');
		return true;
	}

	public function follow($iCampaignId, $iUserId, $sType) {
		if ($sType == 1) {
			$aInsert = array(
				'campaign_id' => $iCampaignId,
				'user_id' => $iUserId,
				'time_stamp' => PHPFOX_TIME
			);

			$iFollowId = $this->database()->insert(Phpfox::getT('fundraising_follow'), $aInsert);
			if ($iFollowId > 0) {
				return $iFollowId;
			} else {
				return false;
			}
		} else if ($sType == 0) {
			if ($this->database()->delete(Phpfox::getT('fundraising_follow'), 'campaign_id = ' . $iCampaignId . ' AND user_id = ' . $iUserId)) {
				return true;
			} else {
				return false;
			}
		}


		return false;
	}

	/**
	 *  highligh or un highlight a campaign
	 * notice that we have only 1 highlighted campaign 
	 * @by minhta
	 * @param string $sType if sType == 1 means highlight, 0 means un-highlight
	 * @return
	 */
	public function highlight($iCampaignId, $sType = 0) {
		Phpfox::getUserParam('fundraising.can_highlight_campaign', true);
		if (!$this->database()->update($this->_sTable, array('is_highlighted' => 0), 'is_highlighted = 1')) {
			return false;
		}
		if ($sType == 1) {
			if (!$this->database()->update($this->_sTable, array('is_highlighted' => 1), 'campaign_id = ' . (int) $iCampaignId)) {
				return false;
			}
		}

		return true;
	}

	public function setActive($iCampaignId, $sType = 0) {
		Phpfox::getUserParam('fundraising.can_active_campaign', true);

		if ($this->database()->update($this->_sTable, array('is_active' => ($sType == '1' ? 1 : 0)), ' campaign_id = ' . (int) $iCampaignId)) {
			$aCampaign = $this->database()->select('c.*, ' . Phpfox::getUserField())
					->from(Phpfox::getT('fundraising_campaign'), 'c')
					->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
					->where('c.campaign_id = ' . (int) $iCampaignId)
					->execute('getRow');

			//	notification
			if (Phpfox::isModule('notification')) {
				if($sType == '1'){
					Phpfox::getService('notification.process')->add('fundraising_setactive', $aCampaign['campaign_id'], $aCampaign['user_id']);
				} else {
					Phpfox::getService('notification.process')->add('fundraising_setinactive', $aCampaign['campaign_id'], $aCampaign['user_id']);
				}				
			}

			// Send the user an email
			$sLink = Phpfox::getLib('url')->permalink('fundraising', $aCampaign['campaign_id'], $aCampaign['title']);
			if($sType == '1'){
				Phpfox::getLib('mail')->to($aCampaign['user_id'])
						->subject(array('fundraising.setactive_title', array('site_title' => Phpfox::getParam('core.site_title'))))
						->message(array('fundraising.setactive_message', array('site_title' => Phpfox::getParam('core.site_title'), 'link' => $sLink)))
						->notification('fundraising.fundraising_is_set_active')
						->send();
			} else {
				Phpfox::getLib('mail')->to($aCampaign['user_id'])
						->subject(array('fundraising.setinactive_title', array('site_title' => Phpfox::getParam('core.site_title'))))
						->message(array('fundraising.setinactive_message', array('site_title' => Phpfox::getParam('core.site_title'), 'link' => $sLink)))
						->notification('fundraising.fundraising_is_set_inactive')
						->send();
			}				

			Phpfox::getService('fundraising.cache')->removeAll();

			return true;
		}

		return false;
	}

	public function feature($iCampaignId, $sType = 0) {

		if ($this->database()->update($this->_sTable, array('is_featured' => ($sType == '1' ? 1 : 0)), 'is_approved = 1 AND campaign_id = ' . (int) $iCampaignId)) {
			Phpfox::getService('fundraising.cache')->removeAll();
			return true;
		}
		return false;
	}

	/**
	 * set default image for a campaign
	 * @by minhta
	 */
	public function setDefaultImage($iImageId) {
		$aImage = Phpfox::getService('fundraising.image')->getImageById($iImageId);
		if (!isset($aImage['campaign_id'])) {
			return Phpfox_Error::set(_p('unable_to_find_the_image'));
		}

		if (!Phpfox::getService('user.auth')->hasAccess('fundraising', 'campaign_id', $aImage['campaign_id'], 'fundraising.delete_own_campaign', 'fundraising.delete_user_campaign', $aImage['user_id'])) {
			return Phpfox_Error::set(_p('you_do_not_have_sufficient_permission_to_modify_this_fundraising'));
		}

		$this->database()->update($this->_sTable, array('image_path' => $aImage['image_path'], 'server_id' => $aImage['server_id']), 'campaign_id = ' . $aImage['campaign_id']);

		return true;
	}

	/**
     * add new campaign
     * @by: datlv
     * @links
     * @param array $aVals
     * @return
     * @throws
     */
	public function add($aVals) {
		$oFilter = Phpfox::getLib('parse.input');

		// check if the user entered a forbidden word
        Phpfox::getService('ban')->checkAutomaticBan($aVals['description'] . ' ' . $aVals['title']. ' ' . $aVals['short_description']. ' ' . $aVals['financial_goal']);


		// Check if links in titles
		if (!Phpfox::getLib('validator')->check($aVals['title'], array('url'))) {
			return Phpfox_Error::set(_p('we_do_not_allow_links_in_titles'));
		}


		$this->getCategoriesFromForm($aVals);
		if (!isset($aVals['privacy'])) {
			$aVals['privacy'] = 0;
		}
		if (!isset($aVals['privacy_comment'])) {
			$aVals['privacy_comment'] = 0;
		}
		if (!isset($aVals['privacy_donate'])) {
			$aVals['privacy_donate'] = 0;
		}

		$sTitle = $oFilter->clean($aVals['title'], 255);
		$short_description = $oFilter->clean($aVals['short_description'], 500);
		$short_description_parse = $oFilter->prepare($aVals['short_description']);
		$bHasAttachments = false;

		$iEndTime = Phpfox::getLib('date')->mktime(23, 59, 59, $aVals['expired_time_month'], $aVals['expired_time_day'], $aVals['expired_time_year']);

        //check if predefined list is empty and dont add to db
		$aVals['serialize_predefined'] = array();
		if(isset($aVals['predefined'])) {
			foreach ($aVals['predefined'] as $iKey => $predefined) {
				if (!empty($predefined) && is_numeric($predefined))
					$aVals['serialize_predefined'][$iKey] = $predefined;
			}
		}

        //check for end time
		if ($iEndTime < PHPFOX_TIME) {
			return Phpfox_Error::set(_p('please_edit_fundraising_end_date_before_update_status'));
		}
		$aInsert = array(
			'title' => $sTitle,
			'user_id' => Phpfox::getUserId(),
			'module_id' => (isset($aVals['module_id']) ? $aVals['module_id'] : 'fundraising'),
			'item_id' => (isset($aVals['item_id']) ? $aVals['item_id'] : '0'),
			'currency' => $aVals['selected_currency'],
			'short_description' => $short_description,
			'short_description_parsed' => $short_description_parse,
			'financial_goal' => ($aVals['financial_goal']) ? $aVals['financial_goal'] : NULL,
			'time_stamp' => PHPFOX_TIME,
			'start_time' => PHPFOX_TIME,
			'end_time' => isset($aVals['unlimit_time']) ? null : $iEndTime,
			'predefined_amount_list' => serialize($aVals['serialize_predefined']),
			'minimum_amount' => $aVals['minimum_amount'],
			'paypal_account' => $aVals['paypal_account'],
			'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
			'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
			'privacy_donate' => (isset($aVals['privacy_donate']) ? $aVals['privacy_donate'] : '0'),
			'location_venue' => (isset($aVals['location_venue']) ? $aVals['location_venue'] : NULL),
			'address' => (isset($aVals['address']) ? $aVals['address'] : NULL),
			'city' => (empty($aVals['city']) ? NULL : $oFilter->clean($aVals['city'], 255)),
			'postal_code' => (empty($aVals['postal_code']) ? NULL : Phpfox::getLib('parse.input')->clean($aVals['postal_code'], 20)),
			'country_iso' => $aVals['country_iso'],
			'gmap' => serialize($aVals['gmap']),
			'is_approved' => 1,
			'status' => Phpfox::getService('fundraising.campaign')->getStatusCode('ongoing'),
			'allow_anonymous' => isset($aVals['allow_anonymous']) ? 1 : 0,
            'is_draft' => 0,
		);

		 if (Phpfox::getUserParam('fundraising.approve_campaigns')) {
            $aUpdate['is_approved'] = '0';
        }
		

        if(isset($aVals['draft']) || isset($aVals['draft_update'])) {
            $aInsert['is_draft'] = 1;
            $aInsert['status'] = Phpfox::getService('fundraising.campaign')->getStatusCode('draft');
        }
		else
		{
			// this campaign is needed to be published, so we need to do more thing
			$this->_bIsPublished = true;
		}

		$iCampaignId = $this->database()->insert(Phpfox::getT('fundraising_campaign'), $aInsert);
		$this->addCategoriesForCampaign($iCampaignId);

		// insert category into db
		if($this->_bIsPublished)
		{
			$this->publish($iCampaignId);
		}

        //insert this campaign to contact info for edit later , easier for dev

        $this->database()->insert(Phpfox::getT('fundraising_contact_info') , array( 'campaign_id' => $iCampaignId, 'time_stamp' => PHPFOX_TIME));

		$sMaindescription = $oFilter->clean($aVals['description']);
		$sMaindescription_parse = $oFilter->prepare($aVals['description']);

		$aInsertText = array(
			'campaign_id' => $iCampaignId,
			'description' => $sMaindescription,
			'description_parsed' => $sMaindescription_parse,
		);

		$this->database()->insert(Phpfox::getT('fundraising_text'), $aInsertText);

		// tricky part here, by default we add thank donor template into new created campaign first
		$aEmailTemplate = Phpfox::getService('fundraising.mail')->getEmailTemplate(Phpfox::getService('fundraising.mail')->getTypesCode('thankdonor_donor'));
		
		Phpfox::getService('fundraising.campaign.process')->updateEmailConditions($aUpdate = array(
			'email_subject' => $aEmailTemplate['email_subject'],
            'email_message' => $aEmailTemplate['email_template'],
            'term_condition' => ''
		), array('campaign_id' => $iCampaignId));		

		// If we uploaded any attachments make sure we update the 'item_id'
		if ($bHasAttachments) {
			Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iCampaignId);
		}

		if ($aVals['privacy'] == '4') {
			Phpfox::getService('privacy.process')->add('fundraising', $iCampaignId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
		}

        if (Phpfox::VERSION >= '3.7.0' && Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support') && !empty($aVals['description']))
        {
            Phpfox::getService('tag.process')->add('fundraising', $iCampaignId, Phpfox::getUserId(), $aVals['description'], true);
        }

		// $this->cache()->remove(array('user/' . Phpfox::getUserId(), 'blog_browse'), 'substr');

		$this->cache()->remove('fundraising', 'substr');
		return $iCampaignId;
	}

	//------------------------------ datlv
	public function update($aVals, $aEditedCampaign) {

		// Multi-upload

        $oFilter = Phpfox::getLib('parse.input');

        // check if the user entered a forbidden word
        Phpfox::getService('ban')->checkAutomaticBan($aVals['description'] . ' ' . $aVals['title']. ' ' . $aVals['short_description']. ' ' . $aVals['financial_goal']);


        // Check if links in titles
        if (!Phpfox::getLib('validator')->check($aVals['title'], array('url'))) {
            return Phpfox_Error::set(_p('we_do_not_allow_links_in_titles'));
        }

		$this->getCategoriesFromForm($aVals);

        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }
        if (!isset($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }
        if (!isset($aVals['privacy_donate'])) {
            $aVals['privacy_donate'] = 0;
        }

        $sTitle = $oFilter->clean($aVals['title'], 255);

        $short_description = $oFilter->clean($aVals['short_description'], 255);
        $short_description_parse = $oFilter->prepare($aVals['short_description']);

        $bHasAttachments = false; // (!empty($aVals['attachment']) && Phpfox::getUserParam('fundraising.can_attach_on_fundraising'));

        //check if predefined list is empty , dont add to db
        $aVals['serialize_predefined'] = array();
        if(isset($aVals['predefined'])) {
            foreach ($aVals['predefined'] as $iKey => $predefined) {
                if (!empty($predefined) && is_numeric($predefined))
                    $aVals['serialize_predefined'][$iKey] = $predefined;
            }
        }

        $iEndTime = Phpfox::getLib('date')->mktime(23, 59, 59, $aVals['expired_time_month'], $aVals['expired_time_day'], $aVals['expired_time_year']);

        if ($iEndTime < PHPFOX_TIME && !isset($aVals['unlimit_time'])) {
            return Phpfox_Error::set(_p('please_edit_fundraising_end_date_before_update_status'));
        }
        $aUpdate = array(
            'title' => $sTitle,
            'short_description' => $short_description,
            'short_description_parsed' => $short_description_parse,
            'financial_goal' => ($aVals['financial_goal']) ? $aVals['financial_goal'] : NULL,
            'time_stamp' => PHPFOX_TIME,
            'end_time' => isset($aVals['unlimit_time']) ? null : $iEndTime,
            'predefined_amount_list' => serialize($aVals['serialize_predefined']),
            'minimum_amount' => $aVals['minimum_amount'],
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'privacy_donate' => (isset($aVals['privacy_donate']) ? $aVals['privacy_donate'] : '0'),
            'location_venue' => (isset($aVals['location_venue']) ? $aVals['location_venue'] : NULL),
            'address' => (isset($aVals['address']) ? $aVals['address'] : NULL),
            'city' => (empty($aVals['city']) ? NULL : $oFilter->clean($aVals['city'], 255)),
            'postal_code' => (empty($aVals['postal_code']) ? NULL : Phpfox::getLib('parse.input')->clean($aVals['postal_code'], 20)),
            'country_iso' => $aVals['country_iso'],
            'gmap' => serialize($aVals['gmap']),
            'allow_anonymous' => (isset($aVals['allow_anonymous'])) ? 1 : 0,
            'is_draft' => 0,
        );

        
        
		if(isset($aVals['paypal_account']))
		{
			$aUpdate['paypal_account'] = $aVals['paypal_account'];
		}
      

        if(isset($aVals['draft']) || isset($aVals['draft_update'])) {
            $aUpdate['is_draft'] = 1;
            $aUpdate['status'] = Phpfox::getService('fundraising.campaign')->getStatusCode('draft');
            $aUpdate['currency'] =  $aVals['selected_currency'];
        }

        if(isset($aVals['draft_publish'])) {
            $aUpdate['is_draft'] = 0;
            $aUpdate['currency'] =  $aVals['selected_currency'];
			if(Phpfox::getUserParam('fundraising.approve_campaigns') && !Phpfox::getService('fundraising.campaign')->checkIsCampaignInPage($aEditedCampaign['campaign_id']))
			{
				$aUpdate['is_approved'] = 0;
			}
			else
			{
				//everything is good, we gonna publish it
				$aUpdate['status'] = Phpfox::getService('fundraising.campaign')->getStatusCode('ongoing');
				$this->_bIsPublished = true;
			}
                       
        }
       
        $this->database()->update(Phpfox::getT('fundraising_campaign'), $aUpdate, 'campaign_id = ' . $aEditedCampaign['campaign_id']);

		$this->addCategoriesForCampaign($aEditedCampaign['campaign_id']);

        $aCallback = (!empty($aVals['module_id']) ? Phpfox::callback('fundraising.addFundraising', $aEditedCampaign['campaign_id']) : null);


        $sMaindescription = $oFilter->clean($aVals['description']);
        $sMaindescription_parse = $oFilter->prepare($aVals['description']);

        $aUpdateText = array(
            'description' => $sMaindescription,
            'description_parsed' => $sMaindescription_parse,
        );

        $this->database()->update(Phpfox::getT('fundraising_text'), $aUpdateText, 'campaign_id = ' . $aEditedCampaign['campaign_id']);

        // If we uploaded any attachments make sure we update the 'item_id'
        if ($bHasAttachments) {
            Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $aEditedCampaign['campaign_id']);
        }

	
		(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('fundraising',  $aEditedCampaign['campaign_id'], $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int) $aVals['privacy_comment'] : 0)) : null);
		
        if (Phpfox::VERSION >= '3.7.0' && Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support') && !empty($aVals['description']))
        {
            $aObject = $this->database()->select('c.campaign_id, c.user_id')
                ->from(Phpfox::getT('fundraising_campaign'), 'c')
                ->where('c.campaign_id = ' . $aEditedCampaign['campaign_id'])
                ->execute('getSlaveRow');       

            if(isset($aObject['campaign_id'])){
                Phpfox::getService('tag.process')->update('fundraising', $aObject['campaign_id'], $aObject['user_id'], $aVals['description'], true);
            }
        }        

		// in case campaign is published this time
		// it only occurs once for each campaign
		if($this->_bIsPublished)
		{
			// don't worry about duplicate cause when it happens, no feed ever created
			$this->publish($aEditedCampaign['campaign_id']);
		}
		else
		{
			if (Phpfox::isModule('privacy'))
			{
				if ($aVals['privacy'] == '4')
				{
					Phpfox::getService('privacy.process')->update('fundraising',  $aEditedCampaign['campaign_id'], (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
				}
				else 
				{
					Phpfox::getService('privacy.process')->delete('fundraising', $aEditedCampaign['campaign_id']);
				}			
			}
		}

        // $this->cache()->remove(array('user/' . Phpfox::getUserId(), 'blog_browse'), 'substr');

        $this->cache()->remove('fundraising', 'substr');

        return $aEditedCampaign['campaign_id'];
	}

    /*
     * use this function to publish campaign , because there are many publish button
	 * it will check pending approve and other thing before make it published
     * @by datlv
     * <pre>
     * Phpfox::getService('fundraising.campaign.process')->publish($iCampaignId)
     * </pre>
     * @param $iCampaignId 
     * @return campaign_id
     */

    public function publish($iCampaignId) {

		if($iCampaignId)
		{
			$aEditedCampaign = Phpfox::getService('fundraising.campaign')->getBasicInfoOfCampaign($iCampaignId);
		}
		else
		{
			return false;
		}

		if (isset($aEditedCampaign['is_active']) && $aEditedCampaign['is_active'] == '0') {
			Phpfox::getLib('url')->send('fundraising.error', array('status' => Phpfox::getService('fundraising')->getErrorStatusNumber('cannot_publish_because_inactive')));
		}

        $aUpdate = array(
            'is_draft' => '0',
        );

		if (Phpfox::getUserParam('fundraising.approve_campaigns') && !Phpfox::getService('fundraising.campaign')->checkIsCampaignInPage($aEditedCampaign['campaign_id'])) {
			$aUpdate['is_approved'] = 0;
			$aUpdate['status'] = Phpfox::getService('fundraising.campaign')->getStatusCode('pending');
        }
		else
		{
			$aUpdate['start_time'] = PHPFOX_TIME;
			$aUpdate['is_approved'] = 1;
			$aUpdate['status'] = Phpfox::getService('fundraising.campaign')->getStatusCode('ongoing');
		}

        $this->database()->update($this->_sTable, $aUpdate, 'campaign_id = ' . $aEditedCampaign['campaign_id']);

		// create feed for new published campaign
		if($aUpdate['is_approved'])
		{
			$this->createFeedForASuccessfullyCreatedCampaign($aEditedCampaign, $aEditedCampaign['campaign_id'], $aEditedCampaign['user_id']);
			Phpfox::getService('fundraising.cache')->removeAll();
		}

		Phpfox::getService('fundraising.mail.process')->sendEmailTo($sTemplateType = Phpfox::getService('fundraising.mail')->getTypesCode('createcampaignsuccessful_owner'), $aEditedCampaign['campaign_id'], $aReceivers = array($aEditedCampaign['user_id']));

        return $aEditedCampaign['campaign_id'];
    }

	public function updateImagesAndVideos($aVals, $aEditedCampaign) {
		if (isset($aVals['video_url']) && $aVals['video_url'] != '') {
			Phpfox::getService('fundraising.video.process')->addVideoUrl($aVals['video_url'], $aEditedCampaign['campaign_id']);
		}

        if(isset($aVals['publish_video'])) {
			$this->_bIsPublished = true;
            $this->publish($aEditedCampaign['campaign_id']);
        }

		return $aEditedCampaign['campaign_id'];
	}

    /**
     * update for sponsor level
     * @by datlv
     * @param $aVals
     * @param $aEditedCampaign
     * @return $iCampaignId
     */

    public function updateSponsorLevels($aVals , $aEditedCampaign)
    {
        if(isset($aVals['sponsor_level']) && !empty($aVals['sponsor_level']))
        {
            $aUpdate['sponsor_level'] = serialize($aVals['sponsor_level']);

            $this->database()->update($this->_sTable, $aUpdate, 'campaign_id = ' . $aEditedCampaign['campaign_id']);
        }

        if(isset($aVals['publish_sponsor_levels'])) {
			$this->_bIsPublished = true;
            $this->publish($aEditedCampaign['campaign_id']);
        }

        return $aEditedCampaign['campaign_id'];
    }

    public function updateContactInformation($aVals, $aEditedCampaign)
    {
		$sAboutMe = $aVals['contact_about_me'];
        $aUpdate = array(
            'full_name' => $aVals['contact_full_name'],
            'phone' => $aVals['contact_phone'],
            'email_address' => $aVals['contact_email_address'],
            'country' => $aVals['country_iso'],
            'state' => $aVals['contact_state'],
            'city' => $aVals['contact_city'],
            'street' => $aVals['contact_street'],
            'about_me' => $sAboutMe,
            'time_stamp' => PHPFOX_TIME,
        );

        $this->database()->update(Phpfox::getT('fundraising_contact_info'), $aUpdate, 'campaign_id = ' . $aEditedCampaign['campaign_id']);

        if(isset($aVals['publish_contact_information'])) {
			$this->_bIsPublished = true;
            $this->publish($aEditedCampaign['campaign_id']);
        }

        return $aEditedCampaign['campaign_id'];
    }

	public function updateFinancialConfiguration($aVals, $aEditedCampaign) {
		return $aEditedCampaign['campaign_id'];
	}

    public function updateEmailConditions($aVals, $aEditedCampaign)
    {
        $sEmailSubject = $aVals['email_message'];
        $aUpdate = array(
            'email_subject' => $aVals['email_subject'],
            'email_message' => $sEmailSubject,
            'term_condition' => $aVals['term_condition'],
        );

        $this->database()->update(Phpfox::getT('fundraising_text'), $aUpdate, 'campaign_id = ' . $aEditedCampaign['campaign_id']);

        if(isset($aVals['publish_email_conditions'])) {
			$this->_bIsPublished = true;
            $this->publish($aEditedCampaign['campaign_id']);
        }

        return $aEditedCampaign['campaign_id'];
    }

    public function inviteFriends($aVals, $aEditedCampaign)
    {
        $this->sentInvite($aVals,$aEditedCampaign);
        return $aEditedCampaign['campaign_id'];
    }

    /*
     * send invite to other
     * @by datlv
     */

    public function sentInvite($aVals, $aEditedCampagin)
    {
        $oParseInput = Phpfox::getLib('parse.input');
        if (isset($aVals['emails']) || isset($aVals['invite']))
        {
            $aInvites = $this->database()->select('invited_user_id, invited_email')
                ->from(Phpfox::getT('fundraising_invited'))
                ->where('campaign_id = ' . (int) $aEditedCampagin['campaign_id'])
                ->execute('getRows');

            $aInvited = array();
            foreach ($aInvites as $aInvite)
            {
                $aInvited[(empty($aInvite['invited_email']) ? 'user' : 'email')][(empty($aInvite['invited_email']) ? $aInvite['invited_user_id'] : $aInvite['invited_email'])] = true;
            }
        }

	   if (!empty($aVals['personal_message']))
		{
			$sMessage = $aVals['personal_message'];
			$sSubject = $aVals['subject'];
		}
		else
		{
			//in case user leave message box empty
		  $sMessage = _p('full_name_invited_you_to_the_title', array(
					'full_name' => Phpfox::getUserBy('full_name'),
					'title' => $oParseInput->clean($aVals['title'], 255),
					'link' => $sLink
				)
			);
		   $sSubject = _p('full_name_invited_you_to_the_fundraising_title', array(
					'full_name' => Phpfox::getUserBy('full_name'),
					'title' => $oParseInput->clean($aVals['title'], 255),
				)
			);
		}
		
		if (!empty($aVals['subject']))
		{
			$sSubject = $aVals['subject'];
		}
		else
		{
			//in case user leave subject box empty
		   $sSubject = _p('full_name_invited_you_to_the_fundraising_title', array(
					'full_name' => Phpfox::getUserBy('full_name'),
					'title' => $oParseInput->clean($aVals['title'], 255),
				)
			);
		}

		$sSubject = Phpfox::getService('fundraising.mail')->parseTemplate($sSubject, $aEditedCampagin, $iDonorId = 0, $iInviteId = Phpfox::getUserId());
		$sMessage = Phpfox::getService('fundraising.mail')->parseTemplate($sMessage, $aEditedCampagin, $iDonorId = 0, $iInviteId = Phpfox::getUserId());

		$aCustomMesssage = array(
			'subject' => $sSubject,
			'message' => $sMessage
		);
					

        if (isset($aVals['emails']))
        {
            $aEmails = explode(',', $aVals['emails']);

            $aCachedEmails = array();
            foreach ($aEmails as $sEmail)
            {
                $sEmail = trim($sEmail);
                if (!Phpfox::getLib('mail')->checkEmail($sEmail))
                {
                    continue;
                }

                if(isset($aCachedEmails[$sEmail]) && $aCachedEmails[$sEmail] == true)
                {
                    continue;
                }

				$bResult = Phpfox::getService('fundraising.mail.process')->sendEmailTo($sType = 0, $aEditedCampagin['campaign_id'], $aReceivers = $sEmail, $aCustomMesssage);
				if ($bResult)
                {
                    $this->database()->insert(Phpfox::getT('fundraising_invited'), array(
                            'campaign_id' => $aEditedCampagin['campaign_id'],
							'inviting_user_id' =>  Phpfox::getUserId(),
							'invited_email' => $sEmail,
							'time_stamp' => PHPFOX_TIME
                        )
                    );
                }
            }
        }

        if (isset($aVals['invite']) && is_array($aVals['invite']))
        {
            $sUserIds = '';
            $aUsers = array();
            foreach ($aVals['invite'] as $iUserId)
            {
                if (!is_numeric($iUserId))
                {
                    continue;
                }
                $sUserIds .= $iUserId . ',';
            }
            $sUserIds = rtrim($sUserIds, ',');
            if($sUserIds) {
                $aUsers = $this->database()->select('user_id, email, language_id, full_name')
                    ->from(Phpfox::getT('user'))
                    ->where('user_id IN(' . $sUserIds . ')')
                    ->execute('getSlaveRows');
            }

            foreach ($aUsers as $aUser)
            {
				$bResult = Phpfox::getService('fundraising.mail.process')->sendEmailTo($sType = 0, $aEditedCampagin['campaign_id'], $aReceivers = $aUser['user_id'], $aCustomMesssage);
				if ($bResult)
                {
                    $iInviteId = $this->database()->insert(Phpfox::getT('fundraising_invited'), array(
                            'campaign_id' => $aEditedCampagin['campaign_id'],
							'inviting_user_id' =>  Phpfox::getUserId(),
                            'invited_user_id' => $aUser['user_id'],
                            'time_stamp' => PHPFOX_TIME
                        )
                    );
                }
                (Phpfox::isModule('request') ? Phpfox::getService('request.process')->add('fundraising_invited', $aEditedCampagin['campaign_id'], $aUser['user_id']) : null);
            }
        }
    }

	public function updateTotalAmount($iCampaignId)
	{
		$iTotalAmount = $this->database()->select('SUM(amount)')
				->from(Phpfox::getT('fundraising_donor'))
				->where('campaign_id = ' . $iCampaignId)
				->execute('getSlaveField');	

		
		$this->database()->update($this->_sTable, array('total_amount' =>  $iTotalAmount), 'campaign_id = ' . (int) $iCampaignId);
	}


	public function updateTotalDonor($iCampaignId)
	{
		$iTotalDonor = $this->database()->select('COUNT(donor_id)')
				->from(Phpfox::getT('fundraising_donor'))
				->where('campaign_id = ' . $iCampaignId)
				->execute('getSlaveField');	

		$this->database()->update($this->_sTable, array('total_donor' =>  $iTotalDonor), 'campaign_id = ' . (int) $iCampaignId);
	}

	public function donate($aVals, $iCampaignId) {
		$oTransaction = Phpfox::getService('fundraising.transaction');
		$aCampaign = Phpfox::getService('fundraising.campaign')->getCampaignById($iCampaignId);
		$sGateway = 'paypal';
		$sUrl = urlencode(Phpfox::getLib('url')->permalink('fundraising', $aCampaign['campaign_id'], $aCampaign['title']));

		$aInvoice = array(
			'is_guest' => Phpfox::isUser() ? false : true,
			'user_id' => Phpfox::isUser() ? Phpfox::getUserId() : 0,
			'message' => Phpfox::getLib('parse.input')->clean($aVals['message'], 255),
			'full_name' => Phpfox::isUser() ? '' : (isset($aVals['fullname']) ? $aVals['fullname'] : ''),
			'email_address' => Phpfox::isUser() ? '' : $aVals['email'],
			'amount' => $aVals['amount'],
			'campaign_id' => $aCampaign['campaign_id'],
			'is_anonymous' => isset($aVals['is_anonymous']) ? $aVals['is_anonymous'] : 0
		);

		if($aInvoice['is_guest'] && !$aInvoice['full_name'])
		{
			$aInvoice['is_anonymous'] = 1;
		}
		$aInsert = array(
			'invoice' => serialize($aInvoice),
			'campaign_id' => $aCampaign['campaign_id'],
			'time_stamp' => PHPFOX_TIME,
			'status' => $oTransaction->getStatusCode('initialized'),
			'currency' => $aCampaign['currency'] ? $aCampaign['currency'] : Phpfox::getService('fundraising.campagin')->getDefaultCurrency(),
			'amount' => $aVals['amount']
		);
		$iTransactionId = Phpfox::getService('fundraising.transaction.process')->add($aInsert);

		$aParam = array(
			'paypal_email' => $aCampaign['paypal_account'],
			'amount' => $aVals['amount'],
			'currency_code' => $aCampaign['currency'] ? $aCampaign['currency'] : Phpfox::getService('fundraising.campagin')->getDefaultCurrency(),
			'custom' => 'fundraising|' . $iTransactionId,
			'return' => Phpfox::getService('fundraising') -> getStaticPath() . 'module/fundraising/static/thankyou.php?sLocation=' . $sUrl,
			'recurring' => 0,
		);

		$oPayment = Phpfox::getService('fundraising.gateways')->load($sGateway, $aParam);
		if (!$oPayment) {
			
		} else {
			Phpfox::getLib('url')->send($oPayment->getCheckoutUrl());
		}
	}

	public function expireCampaign($iCampaignId)
	{
		$aUpdate = array(
			'is_closed' => 1, // all state updated is closed
			'is_highlighted' => 0,
			'is_featured' => 0, 
			'time_stamp' => PHPFOX_TIME,
			'status' =>  Phpfox::getService('fundraising.campaign')->getStatusCode('expired')
		);

		$iResult = $this->database()->update($this->_sTable, $aUpdate, 'campaign_id = ' . $iCampaignId);

		if($iResult > 0 )
		{
			Phpfox::getService('fundraising.mail.process')->sendEmailToAllDonors($iTemplateType = Phpfox::getService('fundraising.mail')->getTypesCode('campaignexpired_donor'), $iCampaignId);
			Phpfox::getService('fundraising.cache')->removeAll();

			Phpfox::getService('fundraising.mail.process')->sendEmailToOwner($iTemplateType = Phpfox::getService('fundraising.mail')->getTypesCode('campaignexpired_owner'), $iCampaignId);

			Phpfox::getService('fundraising.campaign')->notifyToAllFollowers($iCampaignId, 'expired');

			return true;
		}
		else
		{
			return false;
		}

	}

	public function reachCampaign($iCampaignId)
	{
		$aUpdate = array(
			'is_closed' => true, // all state updated is closed
			'time_stamp' => PHPFOX_TIME,
			'is_highlighted' => 0,
			'is_featured' => 0, 
			'status' =>  Phpfox::getService('fundraising.campaign')->getStatusCode('reached')
		);

		$iResult = $this->database()->update($this->_sTable, $aUpdate, 'campaign_id = ' . $iCampaignId);
		if($iResult > 0 )
		{
			Phpfox::getService('fundraising.mail.process')->sendEmailToAllDonors($iTemplateType = Phpfox::getService('fundraising.mail')->getTypesCode('campaigncloseduetoreach_donor'), $iCampaignId);
			Phpfox::getService('fundraising.cache')->removeAll();

			Phpfox::getService('fundraising.mail.process')->sendEmailToOwner($iTemplateType = Phpfox::getService('fundraising.mail')->getTypesCode('campaigncloseduetoreach_owner'), $iCampaignId);

			Phpfox::getService('fundraising.campaign')->notifyToAllFollowers($iCampaignId, 'reached');
			return true;
		}
		else
		{
			return false;
		}

	}

}

?>