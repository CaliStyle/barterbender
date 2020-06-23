<?php

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Controller_User extends Phpfox_Component 
{

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
	private $_aBrowseParam = array();
	private $_aParentModule = null;
	private $_iCampaignId = 0;
	private $_sView = '';

	private function _initializeSearchParamsForDonor() {
		$this->search()->set(array(
				'type' => 'fundraising.user',
				'field' => 'donor.donor_id',
				'search_tool' => array(
					'table_alias' => 'donor',
					'search' => array(
						'action' => $this->_aParentModule != null ? $this->url()->makeUrl($this->_aParentModule['module_id'], array($this->_aParentModule['item_id'], 'fundraising')) : ($this->_bIsProfile === true ? $this->url()->makeUrl($this->_aProfileUser['user_name'], array('fundraising.user', 'view' => $this->request()->get('view'), 'id' => $this->_iCampaignId)) : $this->url()->makeUrl('fundraising.user', array('view' => $this->request()->get('view'), 'id' => $this->_iCampaignId))),
						'default_value' => _p('search_donors_dot'),
						'name' => 'search',
						'field' => array('donor.full_name', 'u.full_name')
					),
					'sort' => array(
						'latest' => array('donor.time_stamp', _p('latest')),
					),
					'show' => array(12, 24, 48)
				)
			)
		);

		$this->_aBrowseParams = array(
			'module_id' => 'fundraising.user',
			'alias' => 'donor',
			'field' => 'donor_id',
			'table' => Phpfox::getT('fundraising_donor'),
			'hide_view' => array('pending', 'my')
		);

	}

	private function _initializeSearchParamsForSupporter() {
		$this->search()->set(array(
				'type' => 'fundraising.user',
				'field' => 'supporter.supporter_id',
				'search_tool' => array(
					'table_alias' => 'supporter',
					'search' => array(
						'action' => $this->_aParentModule != null ? $this->url()->makeUrl($this->_aParentModule['module_id'], array($this->_aParentModule['item_id'], 'fundraising')) : ($this->_bIsProfile === true ? $this->url()->makeUrl($this->_aProfileUser['user_name'], array('fundraising.user', 'view' => $this->request()->get('view'), 'id' => $this->_iCampaignId)) : $this->url()->makeUrl('fundraising.user', array('view' => $this->request()->get('view'), 'id' => $this->_iCampaignId))),
						'default_value' => _p('search_supporters_dot'),
						'name' => 'search',
						'field' => array('u.full_name')
					),
					'sort' => array(
						'latest' => array('supporter.time_stamp', _p('latest')),
					),
					'show' =>array(12, 24, 48)
				)
			)
		);

		$this->_aBrowseParams = array(
			'module_id' => 'fundraising.user',
			'alias' => 'supporter',
			'field' => 'supporter_id',
			'table' => Phpfox::getT('fundraising_supporter'),
			'hide_view' => array('pending', 'my')
		);

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

	private function _setConditionAndHandleDonorRequest()
	{
		$this->_initializeSearchParamsForDonor();
		if($this->request()->get('search-id'))
		{
			$this->search()->setCondition(' AND donor.is_anonymous = 0 ');
		}
		$this->search()->setCondition(' AND donor.campaign_id = ' . $this->_iCampaignId);
		
	}

	private function _setConditionAndHandleSupporterRequest()
	{
		$this->_initializeSearchParamsForSupporter();
		$this->search()->setCondition(' AND supporter.campaign_id = ' . $this->_iCampaignId);
		
	}
	
	public function process()
	{
		Phpfox::getService('fundraising')->buildMenu();
		$this->_sView = $this->request()->get('view'); 
		$this->_iCampaignId = $this->request()->get('id');
		if(strpos($this -> _iCampaignId, '/?s'))
		{
			$aCampaignId = explode('/?s', $this -> _iCampaignId);
			if($aCampaignId)
			{
				$this -> _iCampaignId = $aCampaignId[0];
			}
		}
		$this->_aParentModule = $this->getParam('aParentModule');

		if(!Phpfox::getService('fundraising.permission')->canViewBrowseCampaign($this->_iCampaignId, Phpfox::getUserId()))
		{
			$this->url()->send('fundraising.error', array('status' => Phpfox::getService('fundraising')->getErrorStatusNumber('invalid_permission')));
		}

		$this->_checkIsInAjaxControllerAndInUserProfile();

		switch($this->_sView)
		{
			case 'donor': 
				$this->_setConditionAndHandleDonorRequest();
				break;
			case 'supporter': 
				$this->_setConditionAndHandleSupporterRequest();
				break;
			default:
				$this->_setConditionAndHandleDonorRequest();
				break;
				
		}

		Phpfox::getService('fundraising.phpfoxbrowse')->params($this->_aBrowseParams)->execute();
		$aUsers = Phpfox::getService('fundraising.phpfoxbrowse')->getRows();
		Phpfox::getLib('pager')->set(array('page' => $this->search()->getPage(), 'size' => $this->search()->getDisplay(), 'count' => count($aUsers)));
		
		$aCampaign = Phpfox::getService('fundraising.campaign')->getCampaignById($this->_iCampaignId);
		
		if($this->_sView == 'donor')
		{
			foreach($aUsers as &$aUser)
			{
				$aUser['amount_text'] = Phpfox::getService('fundraising')->getCurrencyText($aUser['amount'], $aUser['currency']);
				$aUser['donor_name'] = $aUser['is_guest'] ? $aUser['guest_full_name'] : $aUser['full_name'];
			}
		}
		
		if ($aCampaign['module_id'] != 'fundraising' && ($aCallback = Phpfox::callback('fundraising.getFundraisingDetails', array('item_id' => $aCampaign['item_id'])))) {
			$this->template()->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
			$this->template()->setBreadcrumb($aCallback['title'], $aCallback['url_home']);
		}
		
		$this->template()->setBreadCrumb(_p('fundraisings'), $aCampaign['module_id'] == 'fundraising' ? $this->url()->makeUrl('fundraising') : $this->url()->permalink('pages', $aCampaign['item_id'], 'fundraising') )
				->setBreadCrumb($aCampaign['title'], $this->url()->permalink('fundraising', $aCampaign['campaign_id'], $aCampaign['title']),true )
				->setBreadCrumb(_p('view_donors_and_supporters'), $this->url()->makeUrl('fundraising.user', array('view' => 'donor' , 'id' => $this->_iCampaignId)) )
				->setBreadCrumb($aCampaign['title'], $this->url()->permalink('fundraising', $aCampaign['campaign_id'], $aCampaign['title']), true )
				->assign(array(
					'aUsers' => $aUsers,
					'iCampaignId' => $this->_iCampaignId,
					'iPage' => $this->search()->getPage(),
					'sView' => $this->_sView
				))
		->setHeader( array(
			'pager.css' => 'style_css'
		));
		
	}
}

?>
