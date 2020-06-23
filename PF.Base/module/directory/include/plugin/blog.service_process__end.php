<?php 
;

if (Phpfox::isModule('directory'))
{
    $module = isset($aVals['module_id']) ? $aVals['module_id'] : "";
    $item = isset($aVals['item_id']) ? $aVals['item_id'] : 0;
    if(Phpfox::isModule('feed') && $aVals['post_status'] == 1 && $module == 'directory' && (int)$item > 0 && isset($iId) && (int)$iId > 0){
        // delete feed in home page
        if(Phpfox::isModule('feed')){
            $aFeed = Phpfox::getService('directory.helper')->getFeed('blog', $iId);
            if(isset($aFeed['feed_id'])){
                Phpfox::getService('feed.process')->deleteFeed($aFeed['feed_id']);
            }
            // add to directory_feed
            Phpfox::getService('directory.process')->addDirectoryFeed(array(
                'privacy' => $aVals['privacy'],
                'privacy_comment' => (isset($aVals['privacy_comment']) ? (int) $aVals['privacy_comment'] : 0),
                'type_id' => 'blog',
                'user_id' => Phpfox::getUserId(),
                'parent_user_id' => $item,
                'item_id' => $iId,
                'parent_feed_id' => 0,
                'parent_module_id' => null,
            ));
        }
    }
}

;
?>