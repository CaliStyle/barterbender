<?php

$aFeed = $this->getVar('aFeed');
if (!isset($aFeed['like_item_id'])) {
    $aFeed['like_item_id'] = $aFeed['item_id'];
}

Phpfox::getBlock('yncreaction.reaction-display', array('aFeed' => $aFeed));