<?php
if(phpfox::isModule('socialpublishers') && !Phpfox::getUserParam('poll.poll_requires_admin_moderation'))
{
    $sUrl = Phpfox::permalink('poll', $iPollId, $aPoll['question']);
    $sType = 'poll';
    $iUserId = phpfox::getUserId();
    $sMessage = $aPoll['question'];
    $aVals['url'] = $sUrl;
    $aVals['content'] = $sMessage;
    $aVals['title'] = $sMessage;
    phpfox::getService('socialpublishers')->showPublisher($sType,$iUserId,$aVals);
}
?>
