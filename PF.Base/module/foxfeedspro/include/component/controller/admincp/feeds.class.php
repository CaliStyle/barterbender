<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_NewsFeed
 * @version        3.02p5
 *
 */
class FoxFeedsPro_Component_Controller_Admincp_Feeds extends Phpfox_Component {
	/*
	 * Process method which is used to process this component
	 */
	public function process() 
	{
		// Generate search conditions
		$sCurrentUrl = $this -> url() -> makeurl('admincp.foxfeedspro.feeds');
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
		$oParseInput = Phpfox::getLib('parse.input');
		
		$sName 		 = $oParseInput->clean($oSearch -> get('feed_name'));
		$sStatus   	 = $oSearch -> get('status');
		$iCategoryId = $oSearch -> get('category_id');
		$sReset  	 = $oSearch -> get('reset');

		// Check Reset Submit
		if($sReset)
		{
			$this-> url()->send('admincp.foxfeedspro.feeds');
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
				$aConds[] ="AND nf.is_active = 1";
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
			Phpfox::getLib('url')->send($sCurrentUrl, null, _p('foxfeedspro.rss_providers_display_order_was_successfully_updated'));
		}

		// Delete selected RSS Provider
	 	// if($this->request()->get('delete_selected')  == _p('foxfeedspro.delete_selected'))
	 	if($aFeedIds = $this->request()->getArray('feed_row'))
		{
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
		$aFeeds = $oFoxFeedsPro -> getFeeds($aConds, $iPage, $iPageSize, $iCount,'time_update DESC');
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
			'foxfeedspro.js' 	   	   => 'module_foxfeedspro',
			'<script type="text/javascript">$Behavior.searchFeedByCategory = function (){var selectedId ='. $iCategoryId.';if(selectedId > 0){$(\'#js_mp_category_item_\' + selectedId).attr(\'selected\',true);}}</script>'
		));

		// Set BreadCrumb
		$sUrl = $this -> url() -> makeurl('admincp.foxfeedspro.feeds');
		$this -> template() -> setBreadCrumb(_p('foxfeedspro.rss_provider_management'), $sUrl);

		//Assign Variables
		$aFormVar =  array(
				'feed_name'   => $sName,
				'status'	  => $sStatus,
				'category_id' => $iCategoryId		
		);
		
		$this -> template() -> assign(array(
				'sCategoryOptions' 	=> Phpfox::getService('foxfeedspro.category')->display('option')->get(0), 
				'aFeeds' 	  	 	=> $aFeeds,
				'aForm'		  	 	=> $aFormVar,
				'aStatusOptions' 	=> $aStatusOption,	
				'sCurrentUrl'	 	=> $sCurrentUrl,
				'bIsAdminPanel'	 	=> (int) Phpfox::isAdminPanel(),
            	'sFilePath'		   	=> Phpfox::getParam('core.url_pic'),
		));
	}

}
?>
