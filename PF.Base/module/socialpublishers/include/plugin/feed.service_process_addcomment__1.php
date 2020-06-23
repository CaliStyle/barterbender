<?php

defined('PHPFOX') or exit('NO DICE!');
/**
 * Post to Facebook, Twitter, Linked-In for page only.
 */
if (Phpfox::isModule('socialpublishers'))
{
    if (Phpfox::getUserId())
    {
        $aShareFeedInsert = array(
            'sType' => $this->_aCallback['feed_id'],
            'iItemId' => $iStatusId,
            'bIsCallback' => $this->_bIsCallback,
            'aCallback' => $this->_aCallback,
            'iPrivacy' => (int) $aVals['privacy'],
            'iPrivacyComment' => (int) $aVals['privacy_comment'],
        );
        $iTempUserId = (defined('FEED_FORCE_USER_ID') ? FEED_FORCE_USER_ID : Phpfox::getUserId());
        if ($iTempUserId == Phpfox::getUserId() && $this->_aCallback['module'] == 'pages' && $this->_aCallback['table_prefix'] == 'pages_' && $this->_aCallback['feed_id'] == 'pages_comment')
        {
            $sIdCache = Phpfox::getLib('cache')->set("socialpublishers_feed_" . Phpfox::getUserId());
            Phpfox::getLib('cache')->save($sIdCache, $aShareFeedInsert);
            $aShareType = 'feed_comment';
            $iUserId = Phpfox::getUserId();
            $aSharePublishersFeed = Phpfox::getLib('database')->select("*")->from(Phpfox::getT('pages_feed_comment'))->where('feed_comment_id = ' . (int) $iStatusId)->execute('getRow');
            $aRow = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name, e.page_id, e.title, pu.vanity_url')
                    ->from(Phpfox::getT('pages_feed_comment'), 'fc')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
                    ->join(Phpfox::getT('pages'), 'e', 'e.page_id = fc.parent_user_id')
                    ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = e.page_id')
                    ->where('fc.feed_comment_id = ' . (int) $iStatusId)
                    ->execute('getSlaveRow');

            $aSharePublishersFeed['feed_link'] = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);

            $aSharePublishers['url'] = isset($aSharePublishersFeed['feed_link']) ? $aSharePublishersFeed['feed_link'] : Phpfox::getLib('url')->makeUrl(Phpfox::getUserBy('user_name'));
            $aSharePublishers['item_id'] = isset($aSharePublishersFeed['feed_comment_id']) ? $aSharePublishersFeed['feed_comment_id'] : 0;
            $aSharePublishers['text'] = isset($aSharePublishersFeed['content']) ? $aSharePublishersFeed['content'] : "";
            $aSharePublishers['content'] = isset($aSharePublishersFeed['content']) ? $aSharePublishersFeed['content'] : "";
            $aSharePublishers['title'] = isset($aSharePublishersFeed['content']) ? $aSharePublishersFeed['content'] : '';
            
            Phpfox::getService('socialpublishers')->showPublisher($aShareType, $iUserId, $aSharePublishers, 3);
        }
    }
}
?>