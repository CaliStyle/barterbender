<?php

$sDir = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
$sYNCCPath = $sDir . '/PF.Site/Apps/ync-reaction/views/block/';

if (in_array($sTemplate, ['ynccomment.block.like-link', 'like.block.link'])) {
    $sTemplate = 'yncreaction.block.like-link';
    $sTemplateFile = $sYNCCPath . 'reaction-link' . PHPFOX_TPL_SUFFIX;
    if (file_exists($sTemplateFile)) {
        $sFile = $sTemplateFile;
    }
    //When ajax, re-assign global params
    if (PHPFOX_IS_AJAX) {
        //ync-reaction get list reactions
        $aReactions = Phpfox::getService('yncreaction')->getReactions();
        $aDefaultLike = Phpfox::getService('yncreaction')->getDefaultLike();
        if ($aReactions) {
            Phpfox_Template::instance()->assign([
                'aYncReactions' => $aReactions,
                'aYncLike' => $aDefaultLike
            ]);
        }
    }
}
if (in_array($sTemplate, ['ynccomment.block.like-display', 'like.block.display'])) {
    $sTemplate = 'yncreaction.block.like-display';
    $sTemplateFile = $sYNCCPath . 'reaction-display' . PHPFOX_TPL_SUFFIX;
    if (file_exists($sTemplateFile)) {
        $sFile = $sTemplateFile;
    }
}

