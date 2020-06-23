<?php
if(phpfox::isModule('socialpublishers'))
{
    $sUrl = phpfox::getLib('url')->makeUrl(phpfox::getUserBy('user_name'));
    $sType = 'status';
    $iUserId = phpfox::getUserId();
    $sMessage = html_entity_decode($aVals['user_status']);
    $aVals['url'] = $sUrl;
    $aVals['content'] = $sMessage;
    $aVals['title'] = $sMessage;
    phpfox::getService('socialpublishers')->showPublisher($sType,$iUserId,$aVals);
}
?>
