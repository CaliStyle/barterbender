<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          AnNT
 * @package         Module_jobposting
 */
?>
<div class="block">
    <div class="collapse" id="js_jobposting_form_upload_images">
        <input type="hidden" name="id" value="{$aCompany.company_id}">
        <input type="hidden" name="type" value="jobposting_company">
        <div id="js_jobposting_form_holder">
            {module name='core.upload-form' type='jobposting_company' params=$aParamsUpload}
            <div class="jobposting-module cancel-upload">
                <a href="{url link='jobposting.company.add.photos'}?id={$aCompany.company_id}" id="js_listing_done_upload" style="display: none;" class="text-uppercase"><i class="ico ico-check"></i>&nbsp;{_p var='finish_upload'}</a>
            </div>
        </div>
    </div>

    <div class="manage-photo-title">
        <span class="fw-bold"><span id="js_listing_total_photo">{$iTotalImages}</span> {_p var='jobposting_photo_s'}</span>
        <a href="javascript:void(0)" id="js_listing_upload_photo" class="fw-bold" data-toggle="collapse" data-target="#js_jobposting_form_upload_images">
            <i class="ico ico-upload-cloud"></i>&nbsp;{_p var='upload_new_photos'}
        </a>
    </div>

    {if count($aImages)}
    <div class="content item-container sortable">
        {foreach from=$aImages name=images item=aImage}
        <article title="{_p var='set_this_photo_as_main_photo_of_this_product'}"  id="js_photo_holder_{$aImage.image_id}" class="px-1 mb-2 js_mp_photo">
            <div class="item-outer">
                <input type="hidden" name="val[photo-order][{$aImage.image_id}]" class="js_mp_order">
                <div class="item-media">
                    <a onclick="$('.is-default').hide(); $(this).siblings('.is-default').show(); $.ajaxCall('jobposting.setDefaultImage', 'id={$aImage.image_id}'); return false;" href="javascript:void(0)" style="background-image: url({img server_id=$aImage.server_id path='core.url_pic' file='jobposting/'.$aImage.image_path suffix='_500_square' max_width='120' max_height='120' class='js_mp_fix_width' return_url=true});"></a>
                    <span class="item-photo-delete" data-photo="{$aImage.image_id}" title="{_p var='delete_this_image_for_the_listing'}"><i class="ico ico-close"></i></span>
                    <div class="is-default" {if $aForms.image_path != $aImage.image_path}style="display:none"{/if}><div class="item-default"><i class="ico ico-photo-star-o"></i>{_p var='default_photo'}</div></div>
                </div>
            </div>
        </article>
        {/foreach}
    </div>
    {else}
    <div class="alert alert-info mt-2">{_p var='no_photos_found'}</div>
    {/if}
</div>

{literal}
<script type="text/javascript">
    $Behavior.updateOrderCoverPhoto = function () {
        $('.item-photo-delete').on('click', function () {
            var photo_id = $(this).data('photo');
            $Core.jsConfirm({}, function () {
                $.ajaxCall('jobposting.deleteImage', $.param({id: photo_id}));
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