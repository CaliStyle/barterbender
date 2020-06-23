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
    <div class="block">
        <div class="manage-photo-title">
            <div class="mr-2">
                <span class="fw-bold">
                    {$iTotalImage} <span class="p-text-lowercase">
                        {if $iTotalImage == 1}{_p var='photo'}{else}{_p var='photos'}{/if}
                    </span>
                </span>
                <div class="p-mt-line p-text-gray p-text-sm"><span class="fw-bold">{_p var='tips'}:</span> {_p var='fevent.photo_tips'}</div>
            </div>
            <div id="js_p_fevent_add_more_photo_btn" {if $iRemainUpload <= 0}style="display:none;"{else}style="display:inline-block;"{/if}>
                <a href="javascript:void(0)" id="js_fevent_upload_photo" class="btn btn-default btn-sm" style="float:right;"
                   onclick="return ynfeAddPage.toggleUploadSection({$aForms.event_id}, 1, {if $isCreating}1{else}0{/if});">
                    {_p var='upload_new_photos'}
                </a>
            </div>
        </div>

        {if count($aImages)}
            <div class="content item-container">
                {foreach from=$aImages name=images item=aImage}
                    <article title="{_p var='click_to_set_as_default_image'}" class="px-1 mb-2 js_mp_photo" style="display: inline-block" id="js_photo_holder_{$aImage.image_id}">
                        <div class="item-outer">
                            <div class="item-media">
                                <a href="javascript:void(0);" class="item-photo-delete" title="{_p var='delete_this_image_for_the_listing'}"{if $aForms.image_path == $aImage.image_path} style="display: none;"{/if}
                                   onclick="ynfeAddPage.deleteImage(this, {$aForms.event_id}, {$aImage.image_id});">
                                    <i class="ico ico-close"></i>
                                </a>
                                <a href="javascript:void(0)" style="background-image: url('{img server_id=$aImage.server_id path='event.url_image' file=$aImage.image_path max_width='120' max_height='120' class='js_mp_fix_width' return_url=true}');"
                                    onclick="$('.item-photo-delete').show(); $(this).closest('.js_mp_photo').find('.item-photo-delete').hide(); $('.is-default').hide(); $(this).find('.is-default').show(); ynfeAddPage.setDefault(this, {$aImage.image_id}); return false;">
                                    <div class="is-default" {if $aForms.image_path != $aImage.image_path}style="display:none"{/if}>
                                        <div class="item-default">
                                            <i class="ico ico-photo-star-o"></i>{_p var='default_photo'}
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </article>
                {/foreach}
            </div>
        {else}
            <div class="help-block">{_p var='no_photos_found'}</div>
        {/if}
    </div>
</div>
