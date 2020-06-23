<?php

if(phpfox::isModule('socialpublishers'))
{
    $sUrl = $aVals['link']['url'];
    $sType = 'link';
    $iUserId = phpfox::getUserId();
    $sMessage = $aVals['status_info'];
    $aVals['url'] = $sUrl;
    $aVals['content'] = $sMessage;
    phpfox::getService('socialpublishers')->showPublisher($sType,$iUserId,$aVals);
}
?>
