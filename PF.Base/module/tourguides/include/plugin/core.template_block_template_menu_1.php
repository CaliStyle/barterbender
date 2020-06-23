<?php

if (Phpfox::isModule('tourguides') && !Phpfox::isAdminPanel())
{
    $iUserId = Phpfox::getUserId();
    $sCurrentUrl = Phpfox::getService('tourguides')->getCurrentUrl();
    $sControllerName = Phpfox::getLib('module')->getFullControllerName();
    $aParams = array(
        'sCurrentUrl' => $sCurrentUrl,
        'sControllerName' => $sControllerName,
        );

    $oService = Phpfox::getService('tourguides')->showTour($iUserId, $aParams);
}

?>
