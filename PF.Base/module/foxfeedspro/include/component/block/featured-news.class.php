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
 
 Class FoxFeedsPro_Component_Block_Featured_News extends Phpfox_Component
 {
 	/*
	 * Process method which is used to process this component
	 */
	private function _isMobile() {
        if (isset($_SERVER['HTTP_USER_AGENT']))
	    {
	    	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	      	if (preg_match('/(android|iphone|ipad|mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo)/i', $user_agent))
	      	{
	      		return true;
	      	}
			return false;
	     }
	     else
	     {
	        return false;
	     }
    }

 	public function process()
	{
		// Generate conditions and variables
		$oRequest = $this->request();
		
		$sSort 		= $oRequest->get('sort');
		$sWhen 		= $oRequest->get('when');
		$sShow 		= $oRequest->get('show');
		$sSearchId  = $oRequest->get('search-id');
		$sReq2 		= $oRequest->get('req2');
		$sViews		= $oRequest->get('view');
		
		if($sSort || $sWhen || $sShow || $sSearchId || $sReq2 || $sViews)
		{
			return FALSE;
		}
		
		// Get Newsfeed service
		$oFoxFeedsPro 	= Phpfox::getService('foxfeedspro');
		
		// Number of news item will be show
		$iLimit 		= Phpfox::getParam('foxfeedspro.number_featured_news');
		
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
		$aConds[] ="AND ni.is_approved = 1 AND ni.is_active = 1 AND ni.is_featured = 1";
		$aConds[] ="";
		$sOrder = "ni.item_pubDate DESC, ni.item_id DESC";
		
		// Get News Items
		$aNewsItems = $oFoxFeedsPro -> getNewsItems($aConds, $iLimit, null, null, $sOrder);

		if(count($aNewsItems) == 0)
		{
			return FALSE;
		}
		
		$aNewsItems = Phpfox::getService('foxfeedspro')->updateNewsItems($aNewsItems);
		
		$aRenderItems = array();
		$i = 0;
		$arr = array();
		if ($this->_isMobile()) {
			foreach ($aNewsItems as $item) {
				$aRenderItems[] = array(
					'count' => 1,
					'items' => array($item)
				);
			}
		}
		else {
			foreach ($aNewsItems as $item) {
				if ($i % 4 == 0) {
					$arr = array();
					$arr['count'] = ((count($aNewsItems) - $i) >= 4) ? 4 : count($aNewsItems) - $i;
				}
				$arr['items'][] = $item;
				if ($i == (count($aNewsItems) - 1) || $i % 4 == 3) {
					$aRenderItems[] = $arr;
				}
				$i++;
			}
		}
		//die(d($aRenderItems));
		// Set header and assign Variables
		$this->template()->assign(array(
				'sHeader'  		 => _p('foxfeedspro.featured_news'),
				'bIsFriendlyUrl' => $bIsFriendlyUrl,
				'sDefaultImgLink'=> Phpfox::getParam('core.url_module') . "foxfeedspro/static/image/default.png",
				'aNewsItems' 	 => Phpfox::getService('foxfeedspro')->updateNewsItems($aNewsItems),
				'aRenderItems'	 => $aRenderItems,
				'sCorePath'		 => Phpfox::getParam('core.path')
			)
		);
		return 'block';
	}
}


?>