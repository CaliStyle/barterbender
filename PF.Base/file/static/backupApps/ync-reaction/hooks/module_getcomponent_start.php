<?php
if ($sClass == 'like.link') {
    $sClass = 'yncreaction.reaction-link';
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