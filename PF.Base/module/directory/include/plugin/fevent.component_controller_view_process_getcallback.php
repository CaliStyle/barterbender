<?php 
;

if (Phpfox::isModule('directory') && isset($aCallback) && isset($aCallback['item_id']) && $aCallback['module_id'] == 'directory')
{
    $aCallback['url_home_pages'] = $aCallback['url_home'] . 'events/';
}

;
?>