<?php
//ync-reaction get list reactions
$aReactions = Phpfox::getService('yncreaction')->getReactions();
$aDefaultLike = Phpfox::getService('yncreaction')->getDefaultLike();
if ($aReactions) {
    $oTpl->assign([
        'aYncReactions' => $aReactions,
        'aYncLike' => $aDefaultLike
    ]);
}