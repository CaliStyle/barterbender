<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
?>
<div class="fevent-manage-photo">
    <h3>{_p var='photos'}</h3>
    <div class="block">
        {if count($aImages)}
            <div class="content item-container">
                {foreach from=$aImages name=images item=aImage}
                <div id="js_photo_holder_{$aImage.image_id}" class="js_mp_photo go_left{if isset($aForms.image_path) && $aForms.image_path == $aImage.image_path} row_focus{/if}" style="text-align:center; margin-bottom:10px; border: 1px #CCC solid; margin-right: 10px;">
                    <div class="js_mp_fix_holder" style="width:110px; margin:auto; position:relative;">
                        <div style="position:absolute; right:0;">
                            <a href="#" title="{_p var='fevent.delete_this_image'}" data-id="{$aImage.image_id}" data-event-id="{$aForms.event_id}" onclick="ynfeAddPage.deleteImage($(this)); return false;">{img theme='misc/delete_hover.gif' alt=''}</a>
                        </div>
                        <a href="#" title="{_p var='fevent.click_to_set_as_default_image'}" onclick="$('.js_mp_photo').removeClass('row_focus'); $(this).parents('.js_mp_photo:first').addClass('row_focus'); $.ajaxCall('fevent.setDefault', 'id={$aImage.image_id}'); return false;">
                        {img server_id=$aImage.server_id path='event.url_image' file=$aImage.image_path suffix='_200' max_width='120' max_height='120' class='js_mp_fix_width'}
                        </a>
                    </div>
                </div>
                {/foreach}
            </div>
        {else}
            <div class="help-block">{_p var='no_photos_found'}</div>
        {/if}
    </div>
</div>