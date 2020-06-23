<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<form method="post" action="{url link='current'}?id={$iCampaignId}" class="fundraising-module manage-photo page_section_menu_holder" id="js_fundraising_block_gallery" style="display:none;">
    <div class="block">
        <h3>{_p var='photos'}</h3>
        <div class="collapse" id="js_fundraising_form_upload_images">
            <input type="hidden" name="id" value="{$iCampaignId}">
            <input type="hidden" name="type" value="fundraising">
            <div id="js_fundraising_form_holder">
                {module name='core.upload-form' type='fundraising' params=$aParamsUpload}
                <div class="fundraising-module cancel-upload">
                    <a href="{url link='fundraising.add'}?tab=gallery&id={$iCampaignId}" id="js_listing_done_upload" style="display: none;" class="text-uppercase no_ajax"><i class="ico ico-check"></i>&nbsp;{_p var='finish_upload'}</a>
                </div>
            </div>
        </div>

        <div class="manage-photo-title">
            <span class="fw-bold"><span id="js_listing_total_photo">{$iTotalImage}</span> {if $iTotalImage == 1}{_p var='photo'}{else}{_p var='photos'}{/if}</span>
            <a href="javascript:void(0)" id="js_listing_upload_photo" class="fw-bold" data-toggle="collapse" data-target="#js_fundraising_form_upload_images">
                <i class="ico ico-upload-cloud"></i>&nbsp;{_p var='upload_new_photos'}
            </a>
        </div>

        {if count($aImages)}
        <div class="content item-container sortable">
            {foreach from=$aImages name=images item=aImage}
            <article id="js_photo_holder_{$aImage.image_id}" class="px-1 mb-2 js_mp_photo">
                <div class="item-outer">
                    <input type="hidden" name="val[photo-order][{$aImage.image_id}]" class="js_mp_order" value="{$aImage.ordering}">
                    <div class="item-media">
                        <a onclick="$('.is-default').hide(); $(this).siblings('.is-default').show(); $.ajaxCall('fundraising.setDefaultImage', 'id={$aImage.image_id}'); return false;" href="javascript:void(0)" title="{_p var='click_to_set_as_default_image'}" style="background-image: url({img server_id=$aImage.server_id path='core.url_pic' file=$aImage.image_path suffix='_300' max_width='120' max_height='120' class='js_mp_fix_width' return_url=true}); cursor: pointer;"></a>
                        <span class="item-photo-delete" data-photo="{$aImage.image_id}" title="{_p var='delete_this_image_for_the_listing'}"><i class="ico ico-close"></i></span>
                        <div class="is-default" {if $sMainImage != $aImage.image_path}style="display:none"{/if}><div class="item-default"><i class="ico ico-photo-star-o"></i>{_p var='default_photo'}</div></div>
                    </div>
                </div>
            </article>
            {/foreach}
        </div>
        {else}
        <div class="alert alert-info">{_p var='no_photos_found'}</div>
        {/if}
    </div>
    <div class="block">
        {module name='fundraising.campaign.form-gallery-video' iCampaignId=$aForms.campaign_id}
    </div>

    <div id="js_submit_upload_image" class="table_clear">
        <button type="submit" name="val[submit_gallery]" value="{phrase var='save_gallery'}" class="btn btn-sm btn-primary">{phrase var='save_gallery'}</button>
        {if $bIsEdit && $aForms.is_draft == 1}
            <button type="submit" name="val[publish_video]" value="{phrase var='publish'}" class="btn btn-sm btn-default">{phrase var='publish'}</button>
        {/if}
        {if $sSetup == 'setup'}
            <button type="submit" name="val[gallery_next]" value="{phrase var='next'}" class="btn btn-sm btn-default">{phrase var='next'}</button>
        {/if}
    </div>
</form>

{literal}
<script type="text/javascript">
    $Behavior.updateOrderCoverPhoto = function () {
        $('.item-photo-delete').on('click', function () {
            var photo_id = $(this).data('photo');
            $Core.jsConfirm({}, function () {
                $.ajaxCall('fundraising.deleteImage', 'id=' + photo_id);
            }, function () {
            });
            return false;
        });
    };

    function onAfterDeletePhotoSuccess(photo_id) {
        var total_photo = $('.js_mp_photo').length;
        if (parseInt(total_photo) === 0) {
            window.location.reload();
        } else {
            $('#js_listing_total_photo').html(total_photo);
        }

        if (parseInt(photo_id) > 0) {
            var parent_ele = $('#js_photo_holder_' + photo_id);
            $('.item-photo-delete').data('main', 0);
            parent_ele.find('.is-default').show();
            parent_ele.find('.item-photo-delete').data('main', 1);
        }
    }
</script>
{/literal}

