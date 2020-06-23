<?php

$sDir = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
$sYNCCPath = $sDir . '/PF.Site/Apps/ync-statusbg/views/block/';

if ($sTemplate == 'feed.block.focus') {
    $sTemplate = 'yncstatusbg.block.feed-focus';
    $sTemplateFile = $sYNCCPath . 'feed-focus' . PHPFOX_TPL_SUFFIX;
    if (file_exists($sTemplateFile)) {
        $sFile = $sTemplateFile;
    }
}
if ($sTemplate == 'ynfeed.block.focus') {
    $sTemplate = 'yncstatusbg.block.ync-feed-focus';
    $sTemplateFile = $sYNCCPath . 'ync-feed-focus' . PHPFOX_TPL_SUFFIX;
    if (file_exists($sTemplateFile)) {
        $sFile = $sTemplateFile;
    }
}
