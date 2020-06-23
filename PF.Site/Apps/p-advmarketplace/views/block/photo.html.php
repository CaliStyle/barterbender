<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: photo.html.php 1298 2009-12-05 16:19:23Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');
?>
<div class="advancedmarketplace-module manage-photo uploader-photo-fix-height">
    <div class="block">
        <div class="manage-photo-title">
            <div class="mr-2">
                <span class="fw-bold" id="js_listing_total_photo">{$iTotalImage} <span class="p-text-lowercase">{if $iTotalImage == 1}{_p var='photo'}{else}{_p var='photos'}{/if}</span></span>
                <div class="p-mt-line p-text-gray p-text-sm"><span class="fw-bold">{_p var='tips'}:</span> {_p var='photo_tips'}</div>
            </div>
            <div id="js_p_advmarketplace_add_more_photo_btn" {if $iRemainUpload <= 0}style="display:none;"{/if}>
            <a href="javascript:void(0)" id="js_listing_upload_photo" class="btn btn-default" style="float:right;"
               onclick="return $Core.advancedmarketplace.toggleUploadSection({$aForms.listing_id}, 1, {if $isCreating}1{else}0{/if});">
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
                           onclick="$Core.advancedmarketplace.deleteImage({$aImage.image_id}, {$aForms.listing_id}); return false;">
                            <i class="ico ico-close"></i>
                        </a>
                        <a href="javascript:void(0)" style="background-image: url('{img server_id=$aImage.server_id path='advancedmarketplace.url_pic' file=$aImage.image_path suffix='_400_square' max_width='120' max_height='120' class='js_mp_fix_width' return_url=true}');"
                            onclick="$('.item-photo-delete').show(); $(this).closest('.js_mp_photo').find('.item-photo-delete').hide(); $('.is-default').hide(); $(this).find('.is-default').show(); $.ajaxCall('advancedmarketplace.setDefault', 'id={$aImage.image_id}'); return false;">
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
        <div class="extra_info">{_p var='no_photos_found'}</div>
        {/if}
    </div>
</div>


