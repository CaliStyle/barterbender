<?php

$sDir = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
$sYNCCPath = $sDir . '/PF.Site/Apps/ync-comment/views/block/' ;

if($sTemplate == 'feed.block.comment') {
    $sTemplate = 'ynccomment.block.comment';
    $sTemplateFile = $sYNCCPath . 'comment' . PHPFOX_TPL_SUFFIX;
    if (file_exists($sTemplateFile)) {
        $sFile = $sTemplateFile;
    }
}

if($sTemplate == 'feed.block.link') {
    $sTemplate = 'ynccomment.block.link';
    $sTemplateFile = $sYNCCPath . 'link' . PHPFOX_TPL_SUFFIX;
    if (file_exists($sTemplateFile)) {
        $sFile = $sTemplateFile;
    }
}

if($sTemplate == 'like.block.link') {
    $sTemplate = 'ynccomment.block.like-link';
    $sTemplateFile = $sYNCCPath . 'like-link' . PHPFOX_TPL_SUFFIX;
    if (file_exists($sTemplateFile)) {
        $sFile = $sTemplateFile;
    }
}
if($sTemplate == 'like.block.display') {
    $sTemplate = 'ynccomment.block.like-display';
    $sTemplateFile = $sYNCCPath . 'like-display' . PHPFOX_TPL_SUFFIX;
    if (file_exists($sTemplateFile)) {
        $sFile = $sTemplateFile;
    }
}

if($sTemplate == 'comment.block.mini') {
    $sTemplate = 'ynccomment.block.mini';
    $sTemplateFile = $sYNCCPath . 'mini' . PHPFOX_TPL_SUFFIX;
    if (file_exists($sTemplateFile)) {
        $sFile = $sTemplateFile;
    }
}
