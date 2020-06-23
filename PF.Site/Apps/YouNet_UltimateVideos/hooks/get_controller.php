<?php

if (setting('ynuv_app_enabled')) {
    $corePath = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/YouNet_UltimateVideos';
    Phpfox::getLib('template')->setHeader(array());
}