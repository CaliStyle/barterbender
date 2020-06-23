<?php

defined('PHPFOX') or exit('NO DICE!');

if (Phpfox::isModule('socialpublishers'))
{
	
    if ((Phpfox::getUserId() || $iOwnerUserId > 0) && $sType != "event_comment" && strpos(strtolower($sType), 'liked') == false)
    {
        $aShareFeedInsert = array(
            'sType' => $sType,
            'iItemId' => $iItemId,
            'bIsCallback' => $this->_bIsCallback,
            'aCallback' => $this->_aCallback,
            'iPrivacy' => (int) $iPrivacy,
            'iPrivacyComment' => (int) $iPrivacyComment,
        );
		
		$aListModules = array('contest','coupon','jobposting_company','jobposting_job', 'fundraising','directory','ultimatevideo_video','ultimatevideo_playlist','ynsocialstore_store','ynsocialstore_product');
		if (in_array($sType, $aListModules) && $iOwnerUserId>0) {
			PHpfox::getService('user.auth')->setUserId($iOwnerUserId);
		}

        if ($aInsert['user_id'] == Phpfox::getUserId())
        {
            $iActualUserId = Phpfox::getService('socialpublishers')->getRealUser(Phpfox::getUserId());
            if ($sType == 'feed_comment')
            {
                $sIdCache = Phpfox::getLib('cache')->set("socialpublishers_feed_" . $iActualUserId);
                Phpfox::getLib('cache')->save($sIdCache, $aShareFeedInsert);
                $aShareType = $sType;
                $iUserId = $iParentUserId;
                $aSharePublishersFeed = Phpfox::getLib('database')->select("*")->from(Phpfox::getT('feed_comment'))->where('feed_comment_id = ' . (int) $iItemId)->execute('getRow');

                $aSharePublishers['url'] = isset($aSharePublishersFeed['feed_link']) ? $aSharePublishersFeed['feed_link'] : Phpfox::getLib('url')->makeUrl(Phpfox::getUserBy('user_name'));
                if ($iUserId != Phpfox::getUserId())
                {
                    $sProfileName = Phpfox::getLib('database')->select("user_name")->from(Phpfox::getT('user'))->where('user_id = ' . (int) $iUserId)->execute('getField');
                    $aSharePublishers['url'] = Phpfox::getLib('url')->makeUrl($sProfileName);
                }
                $aSharePublishers['text'] = isset($aSharePublishersFeed['content']) ? $aSharePublishersFeed['content'] : "";
                $aSharePublishers['content'] = isset($aSharePublishersFeed['content']) ? $aSharePublishersFeed['content'] : "";
                $aSharePublishers['title'] = isset($aSharePublishersFeed['content']) ? $aSharePublishersFeed['content'] : $sTitle;
                if (isset($aInsert['parent_feed_id']) && $aInsert['parent_feed_id'] > 0)
                {
                    Phpfox::getService('socialpublishers')->showPublisher($aShareType, Phpfox::getUserId(), $aSharePublishers, 2);
                }
                else
                {
                    Phpfox::getService('socialpublishers')->showPublisher($aShareType, Phpfox::getUserId(), $aSharePublishers);
                }
            }
            elseif ($sType != 'pages_comment' && $sType != 'event')
            {
                $aSupportedModule = Phpfox::getService('socialpublishers.modules')->getModule($sType);

                if (count($aSupportedModule) > 0 || $sType == "feed_comment" || $sType == "pages_comment" || $sType == "status" || $sType == "user_status")
                {
                    
                    $sIdCache = Phpfox::getLib('cache')->set("socialpublishers_feed_" . $iActualUserId);
                    Phpfox::getLib('cache')->save($sIdCache, $aShareFeedInsert);
                }
                if (strpos($sType, 'music') !== false && strpos($sType, 'comment') === false)
                {
                    
                    $sIdCache = Phpfox::getLib('cache')->set("socialpublishers_feed_" . $iActualUserId);
                   
                    Phpfox::getLib('cache')->save($sIdCache, $aShareFeedInsert);
                }
            }
        }
    }
}
?>