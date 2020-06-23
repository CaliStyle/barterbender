<?php


defined('PHPFOX') or exit('NO DICE!');
$sFullControllerName = Phpfox::getLib('module')->getFullControllerName();
if ($sFullControllerName == 'core.index-member' || $sFullControllerName == 'profile.index')
{
    $iPageId = Phpfox::getLib('session')->remove('socialintegration_pageId');
}

if (Phpfox::isModule('socialpublishers'))
{
    $iUserId = Phpfox::getService('socialpublishers')->getRealUser(Phpfox::getUserId());
    $sIdCache = Phpfox::getLib('cache')->set("socialpublishers_feed_" . $iUserId);
    $aRecentAddedItem = Phpfox::getLib('cache')->get($sIdCache);
    

    if ($aRecentAddedItem && count($aRecentAddedItem))
    {
        $aSharePublishersFeed = Phpfox::getService('socialpublishers')->getAddedInfo($aRecentAddedItem);
        

        if(isset($aRecentAddedItem['sType']) && $aRecentAddedItem['sType']=='resume'){
            if (Phpfox::isModule('resume')){
                $aResume = Phpfox::getService('resume.basic')->getQuick($aRecentAddedItem['iItemId']);
                if($aResume){
                    $aSharePublishersFeed['type_id'] = 'resume';
                    $aSharePublishersFeed['feed_content'] = $aResume['headline'];
                    $aSharePublishersFeed['feed_info'] = $aResume['headline'];
                    $aSharePublishersFeed['feed_link'] = Phpfox::getLib('url')->permalink('resume.view', $aResume['resume_id'], $aResume['headline']);
                }
            }   
        }
        
        if ($aSharePublishersFeed && count($aSharePublishersFeed))
        {
            $aShareType = $aSharePublishersFeed['type_id'];
            //fix for photo to get title
            if ($aShareType == "photo")
            {
                $aSharePublishersFeed['feed_title'] = Phpfox::getLib('database')->select("title")
                        ->from(Phpfox::getT('photo'))
                        ->where('photo_id = ' . (int) $aRecentAddedItem['iItemId'])
                        ->execute('getField');
            }

            $sTitle = isset($aSharePublishersFeed['feed_info']) ?  $aSharePublishersFeed['feed_info'] : "";
            $aSharePublishers['url'] = isset($aSharePublishersFeed['feed_link']) ? $aSharePublishersFeed['feed_link'] : Phpfox::getParam('core.path');
            $aSharePublishers['text'] = (isset($aSharePublishersFeed['feed_status']) && !empty($aSharePublishersFeed['feed_status'])) ? $aSharePublishersFeed['feed_status'] : (isset($aSharePublishersFeed['feed_content']) ? $aSharePublishersFeed['feed_content'] : "");
            $aSharePublishers['content'] = (isset($aSharePublishersFeed['feed_status']) && !empty($aSharePublishersFeed['feed_status'])) ? $aSharePublishersFeed['feed_status'] : (isset($aSharePublishersFeed['feed_content']) ? $aSharePublishersFeed['feed_content'] : "");
            $aSharePublishers['title'] = (isset($aSharePublishersFeed['feed_status']) && !empty($aSharePublishersFeed['feed_status'])) ? $aSharePublishersFeed['feed_status'] : (isset($aSharePublishersFeed['feed_title']) ? $aSharePublishersFeed['feed_title'] : $sTitle);
            $aSharePublishers['item_id'] = (isset($aSharePublishersFeed['item_id']) && !empty($aSharePublishersFeed['item_id'])) ? $aSharePublishersFeed['item_id'] : 0 ;
          

            if (1)
            {              
                Phpfox::getService('socialpublishers')->showPublisher($aShareType, $iUserId, $aSharePublishers);
            }
        }
    }
}
?>
