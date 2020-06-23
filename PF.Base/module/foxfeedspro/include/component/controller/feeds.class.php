<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 *
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_FoxFeedsPro
 * @version        3.02p5
 *
 */
class FoxFeedsPro_Component_Controller_Feeds extends Phpfox_Component {
	/*
	 * Process method which is used to process this component
	 */
	 
	private function _setGlobalModeration($aMenu) {
        $this->setParam('global_moderation', array(
            'name' => 'ffpfeeds',
            'ajax' => 'foxfeedspro.moderation',
            'menu' => $aMenu,
        ));
    }
	
	public function process() 
	{
		Phpfox::isUser(true);
		// check for processing from profile
		$sYnFfFrom = $this->getParam('sYnFfFrom');
		if('profile' == $sYnFfFrom || 'pages' == $sYnFfFrom){
			$this->template() ->assign(array(
				'sYnFfFrom' => $sYnFfFrom, 
			));			

			if('pages' == $sYnFfFrom){
				$aParentModule = $this->getParam('aParentModule');
				$this->template() ->assign(array(
					'aParentModule' => $aParentModule, 
				));			
			}
		}

		// Build filter section menu on left side
		$aFilterMenu = array();
		if (!defined('PHPFOX_IS_USER_PROFILE') && !defined('PHPFOX_IS_PAGES_VIEW')) 
		{
			$aFilterMenu = array(
				_p('foxfeedspro.browse_all') => '',
				TRUE,
				_p('foxfeedspro.my_rss_providers') 	 => 'foxfeedspro.feeds',
				_p('foxfeedspro.my_news') 			 => 'foxfeedspro.news',
				_p('foxfeedspro.my_favorited_news') 	 => 'foxfeedspro.view_favorite',
			);
		}
		$this -> template() -> buildSectionMenu('foxfeedspro', $aFilterMenu);

		// Generate search conditions
		$sCurrentUrl = $this->url()->getFullUrl();
		if('pages' == $sYnFfFrom){
			$sCurrentUrl = $aParentModule['url'] . 'foxfeedspro/profileviewrss/go_profilemanagerssprovider/';
		}
		$iPage = $this -> request() -> getInt('page');
		$iPageSize = 10;
		$aConds = array();
		$oFoxFeedsPro = Phpfox::getService('foxfeedspro');
		$oFoxFeedsProProcess = Phpfox::getService('foxfeedspro.process');

		$aStatusOption = array(
			array('value'=>'all', 'name'=> _p('foxfeedspro.all')),
			array('value'=>'active', 'name'=> _p('foxfeedspro.active')),
			array('value'=>'inactive', 'name'=> _p('foxfeedspro.inactive')),
			array('value'=>'pending', 'name'=> _p('foxfeedspro.pending')),
			array('value'=>'delined', 'name'=> _p('foxfeedspro.declined')),
			array('value'=>'approved', 'name'=> _p('foxfeedspro.approved'))
		);
		$oSearch = Phpfox::getLib('search') -> set(array('type' => 'request', 'search' => 'search'));

		$sName 		 = $oSearch -> get('feed_name');
		$sStatus   	 = $oSearch -> get('status');
		$iCategoryId = $oSearch -> get('category_id');
		$sReset  	 = $oSearch -> get('reset');

		// Check Reset Submit
		if($sReset)
		{
			$this-> url()->send('foxfeedspro.feeds');
		}

		// RSS User Owner
		$bCanEdit = false;
		if('profile' == $sYnFfFrom || 'pages' == $sYnFfFrom){
			if('profile' == $sYnFfFrom){
				$req1 = $this->request()->get('req1');
				$aViewedUser = Phpfox::getService('user')->getByUserName($req1);
				if(isset($aViewedUser['user_id']) == false){
					$this->url()->send('', null, null);
				} else {
					$iUserId = $aViewedUser['user_id']; 					
				}

				if((int)$iUserId == Phpfox::getUserId()){
					$bCanEdit = true;
				}				
				$aConds[] = "AND nf.user_id = {$iUserId}";			
			} else {
				if(Phpfox::getService('pages')->isAdmin($aParentModule['item_id']) || Phpfox::isAdmin())
				{
					$bCanEdit = true;
				}
				$aConds[] = "AND nf.page_id = " . (int)$aParentModule['item_id'];							
			}
		} else {
			$bCanEdit = true;
			$iUserId = Phpfox::getUserId(); 
			$aConds[] = "AND nf.user_id = {$iUserId}";			
		}
		
		// RSS Name Condition
		$sName =  trim($sName);
		if ($sName) 
		{
			$sName =  Phpfox::getLib('database') -> escape($sName);
			$aConds[] ="AND nf.feed_name like '%{$sName}%'";
		}
		
		// RSS Provider Status Filter
		$sStatus = strtolower($sStatus);
		switch($sStatus) {
			case 'active' :
				$aConds[] ="AND nf.is_active = 1 AND nf.is_approved = 1";
				break;
			case 'inactive' :
				$aConds[] ="AND nf.is_active = 0 AND nf.is_approved = 1";
				break;
			case 'pending' :
				$aConds[] ="AND nf.is_approved = 0";
				break;
			case 'delined' :
				$aConds[] ="AND nf.is_approved = 2";
				break;
			case 'approved' :
				$aConds[] ="AND nf.is_approved = 1";
				break;
			default :
				break;
		}

		// Category Id Search Condition
		$iCategoryId = (int) $iCategoryId;
		if ($iCategoryId > 0) {
			$oNewsCategory = Phpfox::getService('foxfeedspro.category');
			$sFeedIdList = $oNewsCategory->getRelatedFeedIdIntoString($iCategoryId);
			$aConds[] = "AND nf.feed_id IN ({$sFeedIdList})";
		}

		// Save display order
		// if($this->request()->get('save_display_order') == _p('foxfeedspro.save_display_order'))
		if($this->request()->get('save_display_order'))
		{
			$aFeedOrder = $this->request()->getArray('feed_order');
			foreach($aFeedOrder as $iKey => $iOrder)
			{
				$oFoxFeedsProProcess->updateFeedOrder($iKey, $iOrder);
			}
			Phpfox::getLib('url')->send($sCurrentUrl, null, _p('foxfeedspro.rss_providers_display_order_were_successfully_updated'));
		}
		
		// Delete selected RSS Provider
	 	// if($this->request()->get('delete_selected')  == _p('foxfeedspro.delete_selected'))
	 	if($this->request()->get('delete_selected'))
		{
			$aFeedIds = $this->request()->getArray('feed_row');
			foreach($aFeedIds as $iId)
			{
				$oFoxFeedsProProcess->deleteFeed($iId);
			}
			Phpfox::getLib('url')->send($sCurrentUrl, null, _p('foxfeedspro.selected_feeds_were_deleted_successfully'));
		}	
		
		// Get Category Data
		$aCategoryOptions = Phpfox::getService('foxfeedspro.category')->display('option')->get(0);

		//Get Feed Items
		$iCount = $oFoxFeedsPro -> getItemCount('ynnews_feeds', 'nf', $aConds);
		$aFeeds = $oFoxFeedsPro -> getFeeds($aConds, $iPage, $iPageSize, $iCount);
		// Setup pager
		Phpfox::getLib('pager')->set(
			array
			(
				'page'  => $iPage, 
				'size'  => $iPageSize, 
				'count' => $iCount
			)
		);
		
		// Set header
		$this -> template() -> setHeader(array(
			'front_end.js'	=> 'module_foxfeedspro',
			'foxfeedspro_backend.css' => 'module_foxfeedspro',
			'foxfeedspro.js' 	   	   => 'module_foxfeedspro',
			'<script type="text/javascript">$Behavior.searchFeedByCategory = function (){var selectedId ='. $iCategoryId.';if(selectedId > 0){$(\'#js_mp_category_item_\' + selectedId).attr(\'selected\',true);}}</script>'
		));

		// Set BreadCrumb
		$this->template()->setBreadcrumb(_p('foxfeedspro.news'), $this->url()->makeUrl('foxfeedspro'));
		$this -> template() -> setBreadCrumb(_p('foxfeedspro.rss_provider_management'), $this -> url() -> makeurl('foxfeedspro.feeds'));

		//Assign Variables
		$aFormVar =  array(
				'feed_name'   => $sName,
				'status'	  => $sStatus,
				'category_id' => $iCategoryId		
		);
		
		$this -> template() -> assign(array(
				'sCategoryOptions' 	=> Phpfox::getService('foxfeedspro.category')->display('option')->get(0), 
				'aFeeds' 	  	 	=> $aFeeds,
				'bCanEdit' 	  	 	=> $bCanEdit,
				'aForm'		  	 	=> $aFormVar,
				'aStatusOptions' 	=> $aStatusOption,	
				'sCurrentUrl'	 	=> $sCurrentUrl,
				'bCanGetFeedData'	=> Phpfox::getUserParam('foxfeedspro.can_get_feed_data'),
				'bIsAdminPanel'	 	=> (int) Phpfox::isAdminPanel(),
				'iPage'				=> $iPage,
                'sFilePath'		   	=> Phpfox::getParam('core.url_pic'),
		));
		
		$aModerateMenu = array();
		if ($bCanEdit && Phpfox::getUserParam('foxfeedspro.can_delete_approved_feed')) {
			$aModerateMenu = array(
	            array(
	                'phrase' => _p('foxfeedspro.delete_selected'),
	                'action' => 'delete'
	            )
	        );
		}
		
		$this->_setGlobalModeration($aModerateMenu);
		//Special breadcrumb for pages
        if (defined('PHPFOX_IS_PAGES_VIEW') && PHPFOX_IS_PAGES_VIEW){
            if ($aParentModule['module_id'] == 'pages'){
                $this->template()
                    ->clearBreadCrumb();
                $this->template()
                    ->setBreadcrumb(Phpfox::getService('pages')->getTitle($aParentModule['item_id']), $aParentModule['url'])
                    ->setBreadcrumb(_p('foxfeedspro.rss_provider_management'), $aParentModule['url'] . 'foxfeedspro/profileviewrss/go_profilemanagerssprovider/');
            }
        }
	}
	
	/*
	 * Clean method used to generate the top menu of the plugin according to the privacy settings in user group setting
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('foxfeedspro.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}
?>