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
class FoxFeedsPro_Component_Controller_Admincp_ApprovalItems extends Phpfox_Component 
{
	/*
	 * Process method which is used to process this component
	 */
	public function process() 
	{
		// Generate search conditions
		$sCurrentUrl = $this->url()->getFullUrl();
		$iPage = $this -> request() -> getInt('page');
		$iPageSize = 10;
		$aConds = array('ni.is_approved = 0');
		
		$oFoxFeedsPro = Phpfox::getService('foxfeedspro');
		$oFoxFeedsProProcess = Phpfox::getService('foxfeedspro.process');
		
		$oSearch = Phpfox::getLib('search') -> set(array('type' => 'request', 'search' => 'search'));
		
		$sTitle		 = $oSearch -> get('item_title');
		$iFeedId   	 = $oSearch -> get('feed_id');
		$sReset  	 = $oSearch -> get('reset');
		
		// Generate feed list options for select box
		$aFeedList = $oFoxFeedsPro->getFeedSelectOptions();
		
		// Check Reset Submit
		if($sReset)
		{
			$this-> url()->send('admincp.foxfeedspro.approvalitems');
		}
		
		// News Title Condition
		$sTitle =  trim($sTitle);
		if ($sTitle) 
		{
			$sTitle =  Phpfox::getLib('database') -> escape($sTitle);
			print_r($sTitle);
			$aConds[] ="AND ni.item_title like '%{$sTitle}%'";
		}
		
		// Feed Id Search Condition
		$iFeedId = (int) $iFeedId;
		if ($iFeedId > 0) {
			$aConds[] = "AND ni.feed_id = " . $iFeedId;
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
			'foxfeedspro.js'	 	   => 'module_foxfeedspro' 
		));

		// Set BreadCrumb
		$sUrl = $this -> url() -> makeurl('admincp.foxfeedspro.approvalitems');
		$this -> template() -> setBreadCrumb(_p('foxfeedspro.pending_news_management'), $sUrl);

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