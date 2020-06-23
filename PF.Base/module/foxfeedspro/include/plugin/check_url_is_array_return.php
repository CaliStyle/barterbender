<?php

if(Phpfox::isModule('foxfeedspro')){
    $pos = strpos($sUrls, '/news/profileviewrss/go_profilemanagerssprovider/');
    if ($pos !== false) {
      $sUrls = str_replace("/news/profileviewrss/go_profilemanagerssprovider/", "/foxfeedspro/profileviewrss/go_profilemanagerssprovider/", $sUrls);
    }

    $pos = strpos($sUrls, '/' . Phpfox::getParam('admincp.admin_cp') . '/news/');
    if ($pos !== false) {
      $sUrls = str_replace('/' . Phpfox::getParam('admincp.admin_cp') . '/news/', '/' . Phpfox::getParam('admincp.admin_cp') . '/foxfeedspro/', $sUrls);
    }
}

?> 
