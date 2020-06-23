<?php

$socialpublisher = phpfox::getLib('session')->get('socialpublisher');
if (count($socialpublisher))
{
    $aSharePublishers = $socialpublisher['aSharePublishers'];
    $iUserId = $socialpublisher['iUserId'];
    $aShareType = $socialpublisher['aShareType'];
    phpfox::getService('socialpublishers')->showPublisher($aShareType, $iUserId, $aSharePublishers);
}
?>