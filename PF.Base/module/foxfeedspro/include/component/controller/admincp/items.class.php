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
class FoxFeedsPro_Component_Controller_Admincp_Items extends Phpfox_Component 
{
	/*
	 * Process method which is used to process this component
	 */
	public function process() 
	{
		// Generate search conditions
		$sCurrentUrl = $this -> url() -> makeurl('admincp.foxfeedspro.items');
		$iPage = $this -> request() -> getInt('page');
		$iPageSize = 10;
		$aConds = array();
		$oFoxFeedsPro = Phpfox::getService('foxfeedspro');
		$oFoxFeedsProProcess = Phpfox::getService('foxfeedspro.process');
		
		$oSearch = Phpfox::getLib('search') -> set(array('type' => 'request', 'search' => 'search'));
		$oParseInput = Phpfox::getLib('parse.input');
		
		$sTitle		 = $oSearch -> get('item_title');
		$iFeedId   	 = $oSearch -> get('feed_id');
		$sReset  	 = $oSearch -> get('reset');
		
		if(!$iFeedId)
		{
			$iFeedId = $this->request()->getInt('feed');
		}
		// Generate feed list options for select box
		$aFeedList = $oFoxFeedsPro->getFeedSelectOptions();
		
		// Check Reset Submit
		if($sReset)
		{
			$this-> url()->send('admincp.foxfeedspro.items');
		}
		
		// News Title Condition
		$sTitle =  trim($sTitle);
		if ($sTitle) 
		{
			$sTitle = Phpfox::getLib('database') -> escape($sTitle);
			$sTitle = $oParseInput->clean($sTitle);
			$aConds[] ="AND ni.item_title like '%{$sTitle}%'";
		}
		
		// Feed Id Search Condition
		$iFeedId = (int) $iFeedId;
		if ($iFeedId > 0) {
			$aConds[] = "AND ni.feed_id = " . $iFeedId;
		}
		
		// Delete selected RSS Provider
	 	if($aNewsIds = $this->request()->getArray('news_row'))
		{
			foreach($aNewsIds as $iId)
			{
				$oFoxFeedsProProcess->deleteNews($iId);
			}
			Phpfox::getLib('url')->send($sCurrentUrl, null, _p('foxfeedspro.selected_news_items_were_deleted_successfully'));
		}	
		
		//Get News Items
		$iCount      = $oFoxFeedsPro -> getNewsItemsCount($aConds);
		$aNewsItems = $oFoxFeedsPro -> getNewsItems($aConds, $iPage, $iPageSize, $iCount,'ni.item_pubDate DESC, ni.added_time DESC', 'admincp');

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
			'foxfeedspro.js'	 	   => 'module_foxfeedspro' 
		));

		// Set BreadCrumb
		$sUrl = $this -> url() -> makeurl('admincp.foxfeedspro.items');
		$this -> template() -> setBreadCrumb(_p('foxfeedspro.news_management'), $sUrl);

		//Assign Variables
		$aFormVar =  array(
				'item_title' => $sTitle,				
				'feed_id' 	 => $iFeedId		
		);

		$this -> template() -> assign(array(
				'aFeedList' 	 => $aFeedList, 
				'aNewsItems' 	 => $aNewsItems,
				'aForm'		  	 => $aFormVar,
				'sCurrentUrl'	 => $sCurrentUrl
		));
	}
}


?>		