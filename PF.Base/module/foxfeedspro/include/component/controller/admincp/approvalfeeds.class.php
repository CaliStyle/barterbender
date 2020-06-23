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
class FoxFeedsPro_Component_Controller_Admincp_ApprovalFeeds extends Phpfox_Component {
	/*
	 * Process method which is used to process this component
	 */
	public function process() 
	{
		// Generate search conditions
		$sCurrentUrl = $this->url()->getFullUrl();
		$iPage = $this -> request() -> getInt('page');
		$iPageSize = 10;
		$aConds = array('nf.is_approved = 0');
		$oFoxFeedsPro = Phpfox::getService('foxfeedspro');
		$oFoxFeedsProProcess = Phpfox::getService('foxfeedspro.process');
		
		$oSearch = Phpfox::getLib('search') -> set(array('type' => 'request', 'search' => 'search'));
		
		$sName 		 = $oSearch -> get('feed_name');
		$sReset  	 = $oSearch -> get('reset');

		// Check Reset Submit
		if($sReset)
		{
			$this-> url()->send('admincp.foxfeedspro.approvalfeeds');
		}

		// RSS Name Condition
		$sName =  trim($sName);
		if ($sName) 
		{
			$sName =  Phpfox::getLib('database') -> escape($sName);
			$aConds[] ="AND nf.feed_name like '%{$sName}%'";
		}

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
			'foxfeedspro.js' 	   	   => 'module_foxfeedspro' 
		));

		// Set BreadCrumb
		$sUrl = $this -> url() -> makeurl('admincp.foxfeedspro.approvalfeeds');
		$this -> template() -> setBreadCrumb(_p('foxfeedspro.pending_rss_provider_management'), $sUrl);

		//Assign Variables
		$aFormVar =  array(
				'feed_name'   => $sName	
		);
		
		$this -> template() -> assign(array(
				'aFeeds' 	  	 => $aFeeds,
				'aForm'		  	 => $aFormVar,
				'sCurrentUrl'	 => $sCurrentUrl,
				'bIsAdminPanel'	 => (int) Phpfox::isAdminPanel()
		));
	}

}
?>
