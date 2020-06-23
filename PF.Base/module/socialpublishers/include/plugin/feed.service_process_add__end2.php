<?php

if (Phpfox::isModule('socialpublishers') && $sType != 'pages_comment' && $sType != 'event' && $aInsert['type_id'] == "user_status" && isset($aInsert['parent_feed_id']) && $aInsert['parent_feed_id'] > 0)
{    
    $iUserId = Phpfox::getUserId();
    $sIdCache = Phpfox::getLib('cache')->set("socialpublishers_feed_" . $iUserId);
    $aRecentAddedItem = Phpfox::getLib('cache')->get($sIdCache);

    if ($aRecentAddedItem && count($aRecentAddedItem))
    {
        $aSharePublishersFeed = Phpfox::getService('socialpublishers')->getAddedInfo($aRecentAddedItem);

        if ($aSharePublishersFeed && count($aSharePublishersFeed))
        {
            $aShareType = $aSharePublishersFeed['type_id'];
            $iUserId = $iUserId;
            $sTitle = "";
            $aSharePublishers['url'] = isset($aSharePublishersFeed['feed_link']) ? $aSharePublishersFeed['feed_link'] : Phpfox::getParam('core.path');
            $aSharePublishers['text'] = (isset($aSharePublishersFeed['feed_status']) && !empty($aSharePublishersFeed['feed_status'])) ? $aSharePublishersFeed['feed_status'] : (isset($aSharePublishersFeed['feed_content']) ? $aSharePublishersFeed['feed_content'] : "");
            $aSharePublishers['content'] = (isset($aSharePublishersFeed['feed_status']) && !empty($aSharePublishersFeed['feed_status'])) ? $aSharePublishersFeed['feed_status'] : (isset($aSharePublishersFeed['feed_content']) ? $aSharePublishersFeed['feed_content'] : "");
            $aSharePublishers['title'] = (isset($aSharePublishersFeed['feed_status']) && !empty($aSharePublishersFeed['feed_status'])) ? $aSharePublishersFeed['feed_status'] : (isset($aSharePublishersFeed['feed_title']) ? $aSharePublishersFeed['feed_title'] : $sTitle);

            Phpfox::getService('socialpublishers')->showPublisher($aShareType, $iUserId, $aSharePublishers, 2);
        }
    }
}
?>
