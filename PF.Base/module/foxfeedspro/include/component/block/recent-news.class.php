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
 
 Class FoxFeedsPro_Component_Block_Recent_News extends Phpfox_Component
 {
 	/*
	 * Process method which is used to process this component
	 */
 	public function process()
	{
		// Get Newsfeed service
		$oFoxFeedsPro 		= Phpfox::getService('foxfeedspro');
		
		// Number of news item will be show
		$iLimit 		= Phpfox::getParam('foxfeedspro.number_recent_news');
		
		// Friendly news URL mode
		$bIsFriendlyUrl = Phpfox::getParam('foxfeedspro.is_friendly_url');
		
		// Current viewer 's language id
		$sLanguageId 	= $oFoxFeedsPro -> getUserUsingLanguage();
		
		// Generate query conditions
		$aConds = array();
		if($sLanguageId != "any")
		{
			$aConds[] = "AND (nf.feed_language = 'any' OR nf.feed_language = '{$sLanguageId}')";
		}
		else
		{
			$aConds[] = "AND nf.feed_language = '$sLanguageId'";
		}
		$aConds[] ="AND ni.is_approved = 1";
		$aConds[] ="AND ni.is_active = 1";
		$sOrder = "ni.item_pubDate DESC";
		
		// Get News Items
		$aNewsItems = $oFoxFeedsPro -> getNewsItems($aConds, $iLimit, null, null, $sOrder);
		
		if(count($aNewsItems) == 0)
		{
			return false;
		}
		
		// Set header and assign Variables
		$this->template()->assign(array(
				'sHeader'  		 => _p('foxfeedspro.recent_news'),
				'bIsFriendlyUrl' => $bIsFriendlyUrl,
				'sDefaultImgLink'=> Phpfox::getParam('core.url_module') . "foxfeedspro/static/image/default.png",
				'aNewsList' 		 => $aNewsItems,
                'aFooter' => array(
                    _p('view_all') => $this->url()->makeUrl('foxfeedspro.sort_latest')
                ),
			)
		);
		return 'block';
	}	
 }

 ?>