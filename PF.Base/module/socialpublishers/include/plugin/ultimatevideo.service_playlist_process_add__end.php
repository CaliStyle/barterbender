<?php
if(phpfox::isModule('socialpublishers') && !user('ynuv_should_be_approve_before_display_playlist'))
{
    if(isset($iPageId) && $iPageId)
    {
        $aCallBack = array(
			'module' => 'pages',
			'item_id' => $iPageId,
			'table_prefix' => 'pages_'
		);	
        $aFeed = Phpfox::getService('feed')->callback($aCallBack)->get(Phpfox::getUserId(), $iFeedId);    
    }
    else
    {
        $aFeed = Phpfox::getService('feed')->get(Phpfox::getUserId(), $iFeedId);    
    }
    if(count($aFeed))
    { 
        $sUrl = $aFeed[0]['feed_link'];
        $sType = 'ultimatevideo_playlist';
        $iUserId = Phpfox::getUserId();
        $sMessage = (isset($aFeed[0]['feed_status']) && !empty($aFeed[0]['feed_status'])) ? $aFeed[0]['feed_status'] : isset($aFeed[0]['feed_title']) ? $aFeed[0]['feed_title'] : "";        
        $aVal['text'] = (isset($aVals['description'])) ? $oFilter->clean($aVals['description']) : "";
        $aVal['url'] = $sUrl;
        $aVal['content'] = $sMessage;
        $aVal['title'] = $aVals['title'];
        $bIsFrame = 3;
        $aShareFeedInsert = array(
	         	'sType' => 'ultimatevideo_playlist',
	            'iItemId' => $iVideoId,
	            'bIsCallback' => 3,
	            'aCallback' => null,
	            'iPrivacy' => (int) (isset($aVals['privacy']) ? (int) $aVals['privacy'] : 0),
	            'iPrivacyComment' => 0,
        	);
        $sIdCache = Phpfox::getLib('cache')->set("socialpublishers_feed_" . $iUserId);
        Phpfox::getLib('cache')->save($sIdCache,$aShareFeedInsert);
        phpfox::getService('socialpublishers')->showPublisher($sType,$iUserId,$aVal,$bIsFrame);
    }    
}

?>
