<?php
if(substr($aOut['type_id'], 0, 12) == 'foxfavorite_') {
    $aOut['type_id'] = substr($aOut['type_id'], 12);
}
if($aOut['type_id'] == 'videochannel_favourite') {
    $aOut['type_id'] = 'videochannel';
}
if(preg_match('/^foxfavorite_/', $aRow['type_id']) || $aRow['type_id'] == 'pages_favorite') {
    $aOut['no_share'] = true;
    $aOut['can_share'] = false;
}