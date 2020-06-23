<?php
if(isset($iFeedPage) && (int)$iFeedPage == 0){
	if(!isset($aFeedCallback)
		|| !isset($aFeedCallback['module'])
		|| ( $aFeedCallback['module'] != 'event' && $aFeedCallback['module'] != 'fevent' && $aFeedCallback['module'] != 'directory' && $aFeedCallback['module'] != 'pages' && $aFeedCallback['module'] != 'groups' && $aFeedCallback['module'] != 'auction' && $aFeedCallback['module'] != 'ecommerce')
		){
		$aYnSaAds = Phpfox::getService('socialad.ad')->getToDisplayOnFeed();
		if($aYnSaAds) {
			$iYnPosition = Phpfox::getParam('socialad.position_of_feed_ad');
			array_splice($aRows, $iYnPosition, 0, $aYnSaAds);
		}
	}
}
