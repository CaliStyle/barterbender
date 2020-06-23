<?php

if (isset($aRow['privacy']))
{
    switch ($aRow['privacy']) {
        case 0:
            $aPostParam['privacy'] = "{'value':'EVERYONE'}";
            break;
        case 1:
            $aPostParam['privacy'] = "{'value':'ALL_FRIENDS'}";
            break;
        case 2:
            $aPostParam['privacy'] = "{'value':'FRIENDS_OF_FRIENDS'}";
            break;
        case 3:
            $aPostParam['privacy'] = "{'value':'SELF'}";
            break;
        case 4:
            $sFql = 'SELECT flid FROM friendlist WHERE owner = me()';
            
            $aResult = Phpfox::getService('socialbridge.provider.facebook')->getApi()->api('/fql', 'GET', array('q' => $sFql));
            
            $aFriendListId = array();
            if (isset($aResult['data']) && isset($aResult['data'][0]))
            {
                $aFriendListId = $aResult['data'][0];
            }
            
            $sFriendListId = implode(',', $aFriendListId);
            
            $aPostParam['privacy'] = "{'value': 'CUSTOM', 'allow': '" . $sFriendListId . "'}";
            break;
        default:
            $aPostParam['privacy'] = "{'value':'SELF'}";
            break;
    }
}
else
{
    $aPostParam['privacy'] = "{'value':'EVERYONE'}";
}
?>
