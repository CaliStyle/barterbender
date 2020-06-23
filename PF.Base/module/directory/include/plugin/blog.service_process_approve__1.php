<?php 
;

if (Phpfox::isModule('directory'))
{
    $module = $aBlog['module_id'];
    $item = $aBlog['item_id'];
    if(Phpfox::isModule('feed') && $aBlog['post_status'] == 1 && $module == 'directory' && (int)$item > 0 && isset($iId) && (int)$iId > 0){
        // delete feed in home page
        if(Phpfox::isModule('feed')){
            $aFeed = Phpfox::getService('directory.helper')->getFeed('blog', $iId);
            if(isset($aFeed['feed_id'])){
                Phpfox::getService('feed.process')->deleteFeed($aFeed['feed_id']);
            }

            // add to directory_feed
            Phpfox::getService('directory.process')->addDirectoryFeed(array(
                'privacy' => $aBlog['privacy'],
                'privacy_comment' => (isset($aBlog['privacy_comment']) ? (int) $aBlog['privacy_comment'] : 0),
                'type_id' => 'blog',
                'user_id' => $aBlog['user_id'],
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