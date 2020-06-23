<?php
if(phpfox::isModule('socialpublishers') && !Phpfox::getUserParam('photo.photo_must_be_approved'))
{
    $aFeed = Phpfox::getService('feed')->get(Phpfox::getUserId(), $iFeedId);
    $sUrl = Phpfox::permalink('advancedphoto', $aPhoto['photo_id'], $aPhoto['title']);
    $sType = 'advancedphoto';
    $iUserId = phpfox::getUserId();
    $sMessage = $aFeed[0]['feed_status'];
    $aVals['url'] = $sUrl;
    $aVals['content'] = $sMessage;
    $aVals['title'] = $aPhoto['title'];
    $bIsFrame = true;
    phpfox::getService('socialpublishers')->showPublisher($sType,$iUserId,$aVals,$bIsFrame);    

}

?>
