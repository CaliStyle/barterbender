<?php
if(phpfox::isModule('socialpublishers') && !Phpfox::getUserParam('music.music_song_approval'))
{
    $sUrl = Phpfox::permalink('music', $aSong['song_id'], $aSong['title']);
    $sType = 'music';
    $iUserId = phpfox::getUserId();
    $sMessage = html_entity_decode($aVals['status_info']);
    $aVals['url'] = $sUrl;
    $aVals['content'] = $sMessage;
    $aVals['title'] = $aSong['title'];
	$bIsFrame = true;
    phpfox::getService('socialpublishers')->showPublisher($sType,$iUserId,$aVals,$bIsFrame);
}

?>
