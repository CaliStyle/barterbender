<?php
$iPageId = Phpfox::getLib('session')->get('socialintegration_pageId');
if(phpfox::isModule('socialpublishers') && ($iPageId ? 1 : !Phpfox::getUserParam('video.approve_video_before_display')))
{
    if($iPageId)
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
        $aVideo = Phpfox::getService('video')->getVideo($aFeed[0]['item_id'], true);
        $sUrl = $aFeed[0]['feed_link'];
        $sType = 'video';
        $iUserId = phpfox::getUserId();
        $sMessage = (isset($aFeed[0]['feed_status']) && !empty($aFeed[0]['feed_status'])) ? $aFeed[0]['feed_status'] : isset($aFeed[0]['title']) ? $aFeed[0]['title'] : "";        
        $aVals['url'] = $sUrl;
        $aVals['content'] = $sMessage;
        $aVals['title'] = $aVideo['title'];
        $bIsFrame = true;
        phpfox::getService('socialpublishers')->showPublisher($sType,$iUserId,$aVals,$bIsFrame);
    }    
}
?>
