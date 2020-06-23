<?php

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Controller_Index extends Phpfox_Component {

	/**
	 * it is true if we are in profile of an user
	 *
	 * @var boolean
	 */
	private $_bIsProfile = false;

	/**
	 * this array contains all information about the user we are viewing profile
	 *
	 * @var array
	 */
	private $_aProfileUser = array();

	/**
	 *  an array contains information of parent module which calls this controller
	 * array(
	 *  'module_id' => string 'pages', 
	 *  'item_id' => string '1', 
	 *  'url' => string 'http://minhta.younetco.com/minhta/index.php?do=/pages/1/'
	 * )
	 * 
	 * @var array
	 */
	private $_aParentModule = null;

	/**
	 * array of featured campaign
	 * 
	 * @var array
	 */
	private $_aFeaturedCampaigns = array();

	/**
	 * this variable hold the status request, it will narrow search result base on the status
	 *
	 * @var int
	 */
	private $_iStatus = 2;
	private $_aCampaignStatus = array();

	private function _checkIfRequest2IsMainSetController404() {
		if ($this->request()->get('req2') == 'main') {
			return Phpfox::getLib('module')->setController('error.404');
		}
	}

	private function _checkIfHavingValidRedirectRequestAndRedirect() {
		// check redirect in page
		if (($iRedirectId = $this->request()->getInt('redirect'))
				&& ($aFundraising = Phpfox::getService('fundraising.campaign')->getCampaignForEdit($iRedirectId))
				&& $aFundraising['module_id'] != 'fundraising'
				&& Phpfox::hasCallback($aFundraising['module_id'], 'getFundraisingRedirect')
		) {
			if (($sForward = Phpfox::callback($aFundraising['module_id'] . '.getFundraisingRedirect', $aFundraising['campaign_id']))) {
				$this->url()->forward($sForward);
			}
		}


		//check redirect in module fundraising
		if (($iRedirectId = $this->request()->get('redirect'))
				&& ($aRedirectFundraising = Phpfox::getService('fundraising.campaign')->getCampaignForEdit($iRedirectId))) {
			Phpfox::permalink('fundraising', $aRedirectFundraising['campaign_id'], $aRedirectFundraising['title'], true);
		}
	}

	private function _checkPermissionToViewThisController() {
		if (!Phpfox::getService('fundraising.permission')->canViewBrowseFundraisingModule($this->_aParentModule ? $this->_aParentModule['module_id'] : 'fundraising', $this->_aParentModule ? $this->_aParentModule['item_id'] : 0 )) {
			$this->url()->send('fundraising.error', array('status' => Phpfox::getService('fundraising')->getErrorStatusNumber('invalid_permission')));
		}
	}

	private function _checkIsInAjaxControllerAndInUserProfile() {
		if (defined('PHPFOX_IS_AJAX_CONTROLLER')) {
			$this->_bIsProfile = true;
			$this->_aProfileUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
			$this->setParam('aUser', $this->_aProfileUser);
		} else {
			$this->_bIsProfile = $this->getParam('bIsProfile');
			if ($this->_bIsProfile === true) {
				$this->_aProfileUser = $this->getParam('aUser');
			}
		}
	}

	private function _checkHavingDeleteRequestAndProcessIt() {
		if (($iDeleteId = $this->request()->getInt('delete'))) {
			if (Phpfox::getService('fundraising.campaign.process')->delete($iDeleteId)) {
				$this->url()->send('fundraising', null, _p('fundraising_successfully_deleted'));
			} else {
				return Phpfox_Error::display(_p('unable_to_find_the_fundraising_you_are_trying_to_delete'));
			}
		}
	}

	private function _checkIsThisAViewDetailRequest() {
		/**
		 * Check if we are going to view an actual fundraising instead of the fundraising index page.
		 * The 2nd URL param needs to be numeric.
		 */
		if ($this->_aParentModule === null && $this->request()->getInt('req2') && !Phpfox::isAdminPanel()) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @return array param to browse campaign list
	 */
	private function _initializeSearchParams() {
		$this->search()->set(array(
			'type' => 'fundraising',
			'field' => 'campaign.campaign_id',
			'search_tool' => array(
				'table_alias' => 'campaign',
				'search' => array(
					'action' => $this->_aParentModule != null ? $this->url()->makeUrl($this->_aParentModule['module_id'], array($this->_aParentModule['item_id'], 'fundraising')) : ($this->_bIsProfile === true ? $this->url()->makeUrl($this->_aProfileUser['user_name'], array('fundraising', 'view' => $this->request()->get('view'))) : $this->url()->makeUrl('fundraising', array('view' => $this->request()->get('view')))),
					'default_value' => _p('search_fundraising_dot'),
					'name' => 'search',
					'field' => array('campaign.title', 'campaign.short_description', 'campaign.financial_goal')
				),
				'sort' => array(
					'latest' => array('campaign.start_time', _p('latest')),
					'most-donated' => array('campaign.total_donor', _p('most_donated')),
					'most-liked' => array('campaign.total_like', _p('most_liked')),
				),
				'show' => array(12, 24, 48)
			)
				)
		);

		$aBrowseParams = array(
			'module_id' => 'fundraising',
			'alias' => 'campaign',
			'field' => 'campaign_id',
			'table' => Phpfox::getT('fundraising_campaign'),
			'hide_view' => array('pending', 'my')
		);

		return $aBrowseParams;
	}

	private function _buildSubsectionMenu() {
		if ($this->_aParentModule === null && !defined('PHPFOX_IS_USER_PROFILE') && !defined('PHPFOX_IS_PAGES_VIEW')) {
			Phpfox::getService('fundraising')->buildMenu();
		}
	}

	private function _checkAndSetStatusRequest() {
		$this->_iStatus = 2;
		$sStatus = $this->request()->get('status');
		if ($sStatus != '') {
			$this->_iStatus = (int) $sStatus;
		}
	}

	private function _setConditionAndHandleActiveView() {
		$this->search()->setCondition('AND campaign.is_active = 1');
	}

	private function _setConditionAndHandlePendingView() {
		Phpfox::isUser(true);
		if (Phpfox::getUserParam('fundraising.can_approve_campaigns')) {
			$this->search()->setCondition('AND campaign.module_id = "fundraising" AND campaign.is_approved = 0');
		}
	}	

	private function _setConditionAndHandleMyView() {
		Phpfox::isUser(true);
		$this->search()->setCondition(' AND campaign.user_id = ' . Phpfox::getUserId());

		if ($this->request()->get('status') == '') {
			$this->_iStatus = 0;
		}
	}

	private function _setConditionAndHandleIDonatedView() {
		Phpfox::isUser(true);
		$this->search()->setCondition(' AND  campaign.privacy IN(%PRIVACY%) AND campaign.module_id = "fundraising" AND donor.user_id =  ' . Phpfox::getUserId());
	}

	private function _setConditionAndHandleFeaturedView() {
		$this->search()->setCondition(' AND  campaign.privacy IN(%PRIVACY%) AND campaign.module_id = "fundraising" AND campaign.is_featured = 1 AND campaign.status = ' . Phpfox::getService('fundraising.campaign')->getStatusCode('ongoing'));
	}

	private function _setConditionAndHandleExpiredView() {
		$this->search()->setCondition(' AND  campaign.privacy IN(%PRIVACY%) AND campaign.module_id = "fundraising" AND campaign.status = ' . $this->_aCampaignStatus['expired']);
	}

	private function _setConditionAndHandleReachedView() {
		$this->search()->setCondition(' AND  campaign.privacy IN(%PRIVACY%) AND campaign.module_id = "fundraising" AND campaign.status = ' . $this->_aCampaignStatus['reached']);
	}

	private function _setConditionAndHandleClosedView() {
		$this->search()->setCondition(' AND  campaign.privacy IN(%PRIVACY%) AND campaign.module_id = "fundraising" AND campaign.status = ' . $this->_aCampaignStatus['closed']);
	}

	private function _setConditionAndHandleOngoingView() {
		$this->search()->setCondition(' AND  campaign.privacy IN(%PRIVACY%) AND campaign.module_id = "fundraising" AND campaign.status = ' . $this->_aCampaignStatus['ongoing']);
	}

	/**
	 * 
	 * @param string $sView name of the view we are going to see
	 */
	private function _setConditionAndHandleDefaultView($sView) {
		if ($this->_bIsProfile === true) {

			$this->search()->setCondition("AND campaign.status > 0 AND campaign.module_id = 'fundraising' AND campaign.user_id = " . $this->_aProfileUser['user_id'] . " AND campaign.is_approved IN(" . ($this->_aProfileUser['user_id'] == Phpfox::getUserId() ? '0,1' : '1') . ") AND campaign.privacy IN(" . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('core')->getForBrowse($this->_aProfileUser)) . ")");

			if($this->_aProfileUser['user_id'] != Phpfox::getUserId())
			{
				$this->search()->setCondition("AND campaign.is_draft = 0 ");	
			}
		} else if ($this->_aParentModule != null && defined('PHPFOX_IS_PAGES_VIEW')) {

			$this->search()->setCondition("AND campaign.module_id = '" . $this->_aParentModule['module_id'] . "' AND campaign.item_id  = " . $this->_aParentModule['item_id'] . " AND campaign.privacy IN(%PRIVACY%)");

			if(Phpfox::getService('pages')->isAdmin($this->_aParentModule['item_id']) || Phpfox::isAdmin())
			{

			}
			else
			{
				$this->search()->setCondition("AND ( (campaign.is_approved = 1 && campaign.is_draft = 0) || campaign.user_id = " . Phpfox::getUserId() . ")");
			}
			
		} else {
			$this->search()->setCondition("AND campaign.module_id = 'fundraising' AND campaign.privacy IN(%PRIVACY%) AND campaign.status = " . Phpfox::getService('fundraising.campaign')->getStatusCode('ongoing') . ' ');
		}
		if (!$this->_bIsProfile && $this->_aParentModule == null && !defined('PHPFOX_IS_PAGES_VIEW')) {
			$this->search()->setCondition("AND campaign.is_approved = 1");
		}

		if (!($this->search()->isSearch()) && !$this->_bIsProfile && $sView != 'listing' && $sView != 'friend' && !($this->_aParentModule != null && defined('PHPFOX_IS_PAGES_VIEW'))) {
			/*
			 * @todo modify later
			 */
		}

		if ($sView == 'friend' && $this->request()->get('status') == '') {
			$this->_iStatus = 0;
		}
	}

	private function _checkIsThisACategoryRequestAndHandleIt() {

		// check category request and set corresponding condition
		if ($this->request()->get(($this->_bIsProfile === true ? 'req3' : 'req2')) == 'category') {
			$iCategory = $this->request()->getInt(($this->_bIsProfile === true ? 'req4' : 'req3'));
			if ($aCampaignCategory = Phpfox::getService('fundraising.category')->getForEdit($iCategory)) {
				$this->template()->setBreadCrumb(Phpfox::getLib('locale')->convert($aCampaignCategory['title']));
				$this->search()->setCondition('AND fundraising_campaign_category.category_id = ' . $iCategory);
				$this->template()->setTitle(Phpfox::getLib('locale')->convert($aCampaignCategory['title']));
				$this->search()->setFormUrl($this->url()->permalink(array('fundraising.category', 'view' => $this->request()->get('view')), $aCampaignCategory['category_id'], $aCampaignCategory['title']));
			}
			$this->setParam('sCategory',$iCategory);
		}

		// check 
		if (($this->request()->get(($this->_bIsProfile === true ? 'req3' : 'req2')) !== 'tag') && !$this->_bIsProfile && !$this->search()->isSearch() && $this->_aParentModule === null && !isset($aCampaignCategory)) {
			$this->_aFeaturedCampaigns = array(true);
		}
	}

	private function _checkIsThisATagRequestAndHandleIt() {
		if ($this->request()->get(($this->_bIsProfile === true ? 'req3' : 'req2')) == 'tag') {
			if (($aTag = Phpfox::getService('tag')->getTagInfo('fundraising', $this->request()->get(($this->_bIsProfile === true ? 'req4' : 'req3'))))) {
				$this->template()->setBreadCrumb(_p('tag.topic') . ': ' . $aTag['tag_text'] . '', $this->url()->makeUrl('current'), true);
				$this->search()->setCondition('AND tag.tag_text = \'' . Phpfox::getLib('database')->escape($aTag['tag_text']) . '\'');
			}
		}
	}

	private function _setMetaAndKeywordsOfPage($aItems) {
		$this->template()->setMeta('keywords', Phpfox::getParam('fundraising.fundraising_meta_keywords'));
		$this->template()->setMeta('description', Phpfox::getParam('fundraising.fundraising_meta_description'));
		if ($this->_bIsProfile) {
			$this->template()->setMeta('description', '' . $this->_aProfileUser['full_name'] . ' has ' . $this->search()->browse()->getCount() . ' fundraisings.');
		}

		foreach ($aItems as $iKey => $aItem) {

			$this->template()->setMeta('keywords', $this->template()->getKeywords($aItem['title']));
			if (!empty($aItem['tag_list'])) {
				$this->template()->setMeta('keywords', Phpfox::getService('tag')->getKeywords($aItem['tag_list']));
			}
		}
	}

	private function _setGlobalModeration() {
		$this->setParam('global_moderation', array(
			'name' => 'fundraising',
			'ajax' => 'fundraising.moderation',
			'menu' => array(
				array(
					'phrase' => _p('delete'),
					'action' => 'delete'
				),
				array(
					'phrase' => _p('approve'),
					'action' => 'approve'
				)
			)
				)
		);
	}

	/**
	 * check status of each campaign, then updating them
	 * <pre>
	 * </pre>
	 * @by minhta
	 * @param array $aCampaigns 
	 * @return array of update campaigns
	 */
	private function _updateStatusOfCampaigns($aCampaigns) {
		$aItems = array();
		if (!empty($aCampaigns)) {
			$aCloses = '';
			foreach ($aCampaigns as $iKey => $aRow) {
				if ($aRow['end_time'] < PHPFOX_TIME && $aRow['status'] == 2 && $aRow['is_approved']) {
					$aCloses .= $aRow['campaign_id'] . ',';
					if ($this->_bIsProfile) {
						$aRow['status'] = 1;
						$aItems[] = $aRow;
					}
				} else {
					$aItems[] = $aRow;
				}
			}
		}


		return $aItems;
	}

	private function _checkIsInHomePage() {
		$bIsInHomePage = false;
		$aParentModule = $this->getParam('aParentModule');
		$sTempSearch = $this->request()->get('s', 0);
		$sTempView = $this->request()->get('view', false);
		if ($sTempSearch == 0
			&& $sTempView == "" 
			&& !isset($aParentModule['module_id']) 
			&& !$this->request()->get('search-id')
				&& !$this->request()->get('sort')
				&& !$this->request()->get('show')
				&& $this->request()->get('req2') == '') {
			if (!defined('PHPFOX_IS_USER_PROFILE')) {
				$bIsInHomePage = true;
			}
		}

		return $bIsInHomePage;
	}

	private function _initializeValriables() {
		$this->_aCampaignStatus = Phpfox::getService('fundraising.campaign')->getAllStatus();
	}

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {

		$bInHomepage = $this->_checkIsInHomePage();
		$this->_checkIfRequest2IsMainSetController404();

		Phpfox::getService('fundraising.campaign.process')->checkAndUpdateStatusOfCampaigns();
		$this->_initializeValriables();

		(($sPlugin = Phpfox_Plugin::get('fundraising.component_controller_index_process_start')) ? eval($sPlugin) : false);

		$this->_checkIfHavingValidRedirectRequestAndRedirect();

		$this->_aParentModule = $this->getParam('aParentModule');
		
		$this->_checkPermissionToViewThisController();

		$this->_checkIsInAjaxControllerAndInUserProfile();

		$this->_checkHavingDeleteRequestAndProcessIt();

		if ($this->_checkIsThisAViewDetailRequest()) {
			return Phpfox::getLib('module')->setController('fundraising.view');
		}

		$this->setParam('sTagType', 'fundraising');

		$this->template()->setTitle(($this->_bIsProfile ? _p('full_name_s_fundraisings', array('full_name' => $this->_aProfileUser['full_name'])) : _p('fundraising_title')))->setBreadCrumb(($this->_bIsProfile ? _p('fundraisings') : _p('fundraising_title')), ($this->_bIsProfile ? $this->url()->makeUrl($this->_aProfileUser['user_name'], 'fundraising') : $this->url()->makeUrl('fundraising')));

		$sView = $this->request()->get('view');

		$aBrowseParams = $this->_initializeSearchParams();

		$this->_buildSubsectionMenu();

		(($sPlugin = Phpfox_Plugin::get('fundraising.component_controller_index_process_search')) ? eval($sPlugin) : false);

		$this->_checkAndSetStatusRequest();

		if(isset($sView) && $sView != 'my'){
			$this->_setConditionAndHandleActiveView();
		}

		switch ($sView) {
			case 'pending':
				$this->_setConditionAndHandlePendingView();
				break;
			case 'my':
				$this->_setConditionAndHandleMyView();
				break;
			case 'idonated':
				$this->_setConditionAndHandleIDonatedView();
				break;
			case 'featured':
				$this->_setConditionAndHandleFeaturedView();
				break;
			case 'expired':
				$this->_setConditionAndHandleExpiredView();
				break;
			case 'reached':
				$this->_setConditionAndHandleReachedView();
				break;
			case 'closed':
				$this->_setConditionAndHandleClosedView();
				break;
			case 'ongoing':
				$this->_setConditionAndHandleOngoingView();
				break;
			default:
				$this->_setConditionAndHandleDefaultView($sView);
				break;
		}

		$this->setParam(array('iStatus' => $this->_iStatus));

		$this->_checkIsThisACategoryRequestAndHandleIt();
		$this->_checkIsThisATagRequestAndHandleIt();

        $this->search()->browse()->setPagingMode(Phpfox::getParam('fundraising.fundraising_paging_mode', 'loadmore'));
        $this->search()->browse()->params($aBrowseParams)->execute();

		$aRows = $this->search()->browse()->getRows();
		$aItems = $aRows;
		foreach ($aItems as &$aCampaign) {
			$aCampaign = Phpfox::getService('fundraising.campaign')->retrieveMoreInfoFromCampaign($aCampaign,  $bRetrievePermission = true);
		}

		Phpfox::getLib('pager')->set(array(
			'page' => $this->search()->getPage(),
			'size' => $this->search()->getDisplay(),
			'count' => $this->search()->browse()->getCount(),
            'paging_mode' => $this->search()->browse()->getPagingMode()
        ));
		// to modify item

		(($sPlugin = Phpfox_Plugin::get('fundraising.component_controller_index_process_middle')) ? eval($sPlugin) : false);

		$this->_setMetaAndKeywordsOfPage($aItems);
		/**
		 * Here we assign the needed variables we plan on using in the template. This is used to pass
		 * on any information that needs to be used with the specific template for this component.
		 */
		$this->template()->assign(array(
					'corepath' => Phpfox::getParam('core.path'),
					'aFeatured' => $this->_aFeaturedCampaigns,
					'iCnt' => $this->search()->browse()->getCount(),
					'aCampaigns' => $aItems,
					'sSearchBlock' => _p('search_fundraisings_dot'),
					'bIsProfile' => $this->_bIsProfile,
					'sTagType' => ($this->_bIsProfile === true ? 'fundraising_profile' : 'fundraising'),
					'sFundraisingStatus' => $this->request()->get('status'),
					'sView' => $sView,
					'iPage' => $this->search()->getPage(),
					'bInHomepage' => $bInHomepage,
					'aCampaignStatus' => Phpfox::getService('fundraising.campaign')->getAllStatus(),
					'googleApiKey' => Phpfox::getParam('core.google_api_key')
						)
				)
				->setHeader('cache', array(
					'jquery/plugin/jquery.highlightFade.js' => 'static_script',
					'quick_edit.js' => 'static_script',
					'comment.css' => 'style_css',
					'pager.css' => 'style_css',
					'feed.js' => 'module_feed',
						)
		);
		
		$this->setParam('bInHomepageFr', $bInHomepage);

		$this->template()->setHeader(
				array(
					'owl.carousel.min.js' => 'module_fundraising',
					'map.js' => 'module_fundraising',
					'mobile.css' => 'module_fundraising'
				)
		);

		$this->_setGlobalModeration();

		(($sPlugin = Phpfox_Plugin::get('fundraising.component_controller_index_process_end')) ? eval($sPlugin) : false);
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean() {
		$this->template()->clean(array(
			'iCnt',
			'aItems',
			'sSearchBlock',
			'aFeatured'
				)
		);

		(($sPlugin = Phpfox_Plugin::get('fundraising.component_controller_index_clean')) ? eval($sPlugin) : false);
	}

}

?>
