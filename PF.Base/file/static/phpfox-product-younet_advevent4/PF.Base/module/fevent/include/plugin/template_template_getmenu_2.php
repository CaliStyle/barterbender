<?php

//     for module which support page
if (Phpfox::isUser() 
  && defined('PHPFOX_IS_PAGES_VIEW') 
  && Phpfox::isModule('fevent') 
  && 'fevent.index' == $sConnection
  )
{
    if(is_array($aMenus) == true){
         foreach($aMenus as $ynfeKey => $ynfeVal){
              if(is_array($ynfeVal) == true && isset($ynfeVal['url']) && 'fevent.add' == $ynfeVal['url']){                        
                   $ynfeiPage = $this->_aVars['aPage']['page_id'];
                   $aMenus[$ynfeKey]['url'] = 'fevent.add.module_pages.item_' . $ynfeiPage;
              }
         }
    }
}          



?>
