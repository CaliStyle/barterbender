<?php

$sWPSetting = setting('yncwebpush_auth_code_snippet');
$aMatch = [];
if (preg_match('/<script[^>]*>[^<]*<\/script>/', $sWPSetting) !== false) {
    $sData .= $sWPSetting;
    $iSenderId = preg_match('/messagingSenderId:\s"(.*?)"/',$sWPSetting,$aMatch);
} else {
    $aMatch[0] = '';
}

$aParams = [
    'iWaitingTime' => Phpfox::getService('yncwebpush')->getWaitingTimeBeforeShowBanner(),
    'iDelaySettingTime' => Phpfox::getService('yncwebpush')->getPeriodToAppearBannerTime(),
    'sWebPushPath' => Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/ync-webpush/',
    'iSenderId' => $aMatch[0]
];
$sData .= '<script>var yncwebpush_params = ' . json_encode($aParams) . ';</script>';
