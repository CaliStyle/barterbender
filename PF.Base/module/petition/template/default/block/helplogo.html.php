<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if (!empty($aHelp.image_path))}
    <a class="js_petition_click_image no_ajax_link helplogo" href="{img return_url=true server_id=$aHelp.server_id title=$aHelp.title path='core.url_pic' file=$aHelp.image_path suffix='_200'}">
    {img thickbox=true server_id=$aHelp.server_id title=$aHelp.title path='core.url_pic' file=$aHelp.image_path suffix='_200'}</a>

{/if}

