<?php

//     for module which support page
if (Phpfox::isUser() 
    && Phpfox::isModule('fevent') 
    && $sConnection === null 
    && 'fevent.index' == Phpfox::getLib('module')->getFullControllerName()
    )
{
   $sConnection = Phpfox::getLib('module')->getFullControllerName();
   $bIsModulePage = true;

   $sConnection = preg_replace('/(.*)\.profile/i', '\\1.index', $sConnection);
  
   if (($sConnection == 'user.photo' && $oReq->get('req3') == 'register') || ($sConnection == 'invite.index' && $oReq->get('req2') == 'register'))
   {
        return [];
   }

   if('fevent.index' == $sConnection){
        $sCachedId = $oCache->set(array('theme', 'menu_' . str_replace(array('/', '\\'), '_', $sConnection) . (Phpfox::isUser() ? Phpfox::getUserBy('user_group_id') : 0)));
        $oCache->remove($sCachedId);
   }
}          


?>
