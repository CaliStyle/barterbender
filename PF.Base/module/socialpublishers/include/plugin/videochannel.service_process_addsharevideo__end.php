<?php
if(phpfox::isModule('socialpublishers') && Phpfox::getUserParam('videochannel.approve_video_before_display') == false)
{
    $sUrl = Phpfox::permalink('videochannel', $iId, $aVals['title']);
    $sType = 'videochannel';
    $iUserId = Phpfox::getUserId();
    $sMessage = $sDescription;
    $aVal['text'] = $sDescription;
    $aVal['url'] = $sUrl;
    $aVal['content'] = $sMessage;
    $aVal['title'] = $aSql['title'];
    $bIsFrame = 3;
    $aShareFeedInsert = array(
        'sType' => 'videochannel',
        'iItemId' => $iId,
        'bIsCallback' => 3,
        'aCallback' => null,
        'iPrivacy' => (int) (isset($aVals['privacy']) ? (int) $aVals['privacy'] : 0),
        'iPrivacyComment' => 0,
    );
    $sIdCache = Phpfox::getLib('cache')->set("socialpublishers_feed_" . $iUserId);
    Phpfox::getLib('cache')->save($sIdCache,$aShareFeedInsert);
    Phpfox::getService('socialpublishers')->showPublisher($sType,$iUserId,$aVal,$bIsFrame);
}

?>
