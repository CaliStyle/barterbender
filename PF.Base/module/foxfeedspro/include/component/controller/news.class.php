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
 * @version        3.02
 *
 */
class FoxFeedsPro_Component_Controller_News extends Phpfox_Component 
{
	
	private function _setGlobalModeration($aMenu) {
        $this->setParam('global_moderation', array(
            'name' => 'ffpnews',
            'ajax' => 'foxfeedspro.moderationnew',
            'menu' => $aMenu,
        ));
    }
	
	/*
	 * Process method which is used to process this component
	 */
	public function process() 
	{
		Phpfox::isUser(true);
		// Build filter section menu on left side
		$aFilterMenu = array();
		if (!defined('PHPFOX_IS_USER_PROFILE')) 
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
		$iPage = $this -> request() -> getInt('page');
		$iUserId = Phpfox::getUserId(); 
		$iPageSize = 10;
		$aConds = array();
		$oFoxFeedsPro = Phpfox::getService('foxfeedspro');
		$oFoxFeedsProProcess = Phpfox::getService('foxfeedspro.process');
		
		$oSearch = Phpfox::getLib('search') -> set(array('type' => 'request', 'search' => 'search'));
		
		$sTitle		 = $oSearch -> get('item_title');
		$iFeedId   	 = $oSearch -> get('feed_id');
		$sReset  	 = $oSearch -> get('reset');
		
		// Generate feed list options for select box
		$aFeedList = $oFoxFeedsPro->getFeedsByUserId($iUserId);
		
		// Check Reset Submit
		if($sReset)
		{
			$this-> url()->send('foxfeedspro.news');
		}
		
		// RSS User Owner
		$aConds[] = " AND ni.user_id = {$iUserId} ";
		// exclude news which belong to pages 
		$aConds[] = " AND ni.page_id = 0 ";
		
		// News Title Condition
		$sTitle =  trim($sTitle);
		if ($sTitle) 
		{
			$sTitle =  Phpfox::getLib('database') -> escape($sTitle);
			$aConds[] ="AND ni.item_title like '%{$sTitle}%'";
		}
		
		// Feed Id Search Condition
		$iFeedId = (int) $iFeedId;
		if ($iFeedId > 0) 
		{
			$aConds[] = "AND ni.feed_id = {$iFeedId}";
		}
		
		// Delete selected RSS Provider
	 	if($this->request()->get('delete_selected')  == _p('foxfeedspro.delete_selected'))
		{
			$aNewsIds = $this->request()->getArray('news_row');
			foreach($aNewsIds as $iId)
			{
				$oFoxFeedsProProcess->deleteNews($iId);
			}
			Phpfox::getLib('url')->send($sCurrentUrl, null, _p('foxfeedspro.selected_news_items_were_deleted_successfully'));
		}	
		
		//Get News Items
		$iCount 	= $oFoxFeedsPro -> getItemCount('ynnews_items','ni', $aConds);
		$aNewsItems = $oFoxFeedsPro -> getNewsItems($aConds, $iPage, $iPageSize, $iCount,'ni.item_pubDate DESC, ni.added_time DESC','admincp');

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
			'foxfeedspro_backend.css' => 'module_foxfeedspro',
			'foxfeedspro.js'	 	   => 'module_foxfeedspro',
			'front_end.js'	=> 'module_foxfeedspro', 
		));

		// Set BreadCrumb
		$this->template()->setBreadcrumb(_p('foxfeedspro.news'), $this->url()->makeUrl('foxfeedspro'));
		$this -> template() -> setBreadCrumb(_p('foxfeedspro.my_news'), $this -> url() -> makeurl('foxfeedspro.news'));
		
		//Assign Variables
		$aFormVar =  array(
				'item_title' => $sTitle,				
				'feed_id' 	 => $iFeedId		
		);

		$this -> template() -> assign(array(
				'aFeedList' 	 => $aFeedList, 
				'aNewsItems' 	 => $aNewsItems,
				'aForm'		  	 => $aFormVar,
				'sCurrentUrl'	 => $sCurrentUrl,
				'iPage'			 => $iPage
		));
		
		$aModerateMenu = array(
            array(
                'phrase' => _p('foxfeedspro.delete_selected'),
                'action' => 'delete'
            )
        );
		
		$this->_setGlobalModeration($aModerateMenu);
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