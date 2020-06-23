<?php
if ($aOut['item_id']) {
    $aOut['status_background'] = Phpfox::getService('yncstatusbg')->getFeedStatusBackground($aOut['item_id'],
        $aOut['type_id'], $aOut['user_id']);
}