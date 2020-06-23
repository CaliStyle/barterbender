<?php

$sDir = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
$sYNCCPath = $sDir . '/PF.Site/Apps/ync-statusbg/views/block/';

if ($sTemplate == 'feed.block.focus') {
    $sTemplate = 'yncstatusbg.block.feed-focus';
    $sTemplateFile = $sYNCCPath . 'feed-focus' . PHPFOX_TPL_SUFFIX;
    if (file_exists($sTemplateFile)) {
        $sFile = $sTemplateFile;
    }
} elseif ($sTemplate == 'ynfeed.block.focus') {
    $sTemplate = 'yncstatusbg.block.ync-feed-focus';
    $sTemplateFile = $sYNCCPath . 'ync-feed-focus' . PHPFOX_TPL_SUFFIX;
    if (file_exists($sTemplateFile)) {
        $sFile = $sTemplateFile;
    }
} elseif ($sTemplate == 'ynfeed.block.mini') {
    $sTemplate = 'yncstatusbg.block.ync-feed-mini';
    $sTemplateFile = $sYNCCPath . 'ync-feed-mini' . PHPFOX_TPL_SUFFIX;
    if (file_exists($sTemplateFile)) {
        $sFile = $sTemplateFile;
        if(!empty($this->_aVars['aParentFeed']['item_id'])) {
            $this->_aVars['aParentFeed']['status_background'] = Phpfox::getService('yncstatusbg')->getFeedStatusBackground($this->_aVars['aParentFeed']['item_id'], $this->_aVars['aParentFeed']['type_id'], $this->_aVars['aParentFeed']['user_id']);
        }
    }
} elseif ($sTemplate == 'feed.block.mini') {
    $sTemplate = 'yncstatusbg.block.feed-mini';
    $sTemplateFile = $sYNCCPath . 'feed-mini' . PHPFOX_TPL_SUFFIX;
    if (file_exists($sTemplateFile)) {
        $sFile = $sTemplateFile;
        if(!empty($this->_aVars['aParentFeed']['item_id'])) {
            $this->_aVars['aParentFeed']['status_background'] = Phpfox::getService('yncstatusbg')->getFeedStatusBackground($this->_aVars['aParentFeed']['item_id'], $this->_aVars['aParentFeed']['type_id'], $this->_aVars['aParentFeed']['user_id']);
        }
    }
}
