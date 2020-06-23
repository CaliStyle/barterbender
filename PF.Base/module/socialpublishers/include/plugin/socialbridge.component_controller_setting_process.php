<?php

defined('PHPFOX') or exit('NO DICE!');

if (isset($aMenus))
{
    $aFacebook = phpfox::getService('socialbridge.providers')->getProvider('facebook');
    $aTwitter = phpfox::getService('socialbridge.providers')->getProvider('twitter');
    $aLinkedIn = phpfox::getService('socialbridge.providers')->getProvider('linkedin');

    if (($aFacebook && $aFacebook['is_active'] > 0) || ($aTwitter && $aTwitter['is_active'] > 0) || ($aLinkedIn && $aLinkedIn['is_active'] > 0))
    {
        $aMenus['socialpublishers'] = _p('socialpublishers.social_publishers');
    }
}
?>