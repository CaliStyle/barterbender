<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<script type="text/javascript" src="{param var='core.path_actual'}PF.Base/static/jscript/jquery/plugin/imgnotes/jquery.tag.js"></script>
<script type="text/javascript" src="{param var='core.path_actual'}PF.Base/static/jscript/jquery/plugin/imgnotes/jquery.imgareaselect.js"></script>
<script type="text/javascript" src="{param var='core.path_actual'}PF.Base/static/jscript/jquery/plugin/imgnotes/jquery.imgnotes.js"></script>
<script type="text/javascript" src="{param var='core.path_actual'}PF.Site/Apps/ync-photovp/assets/jscript/screenfull.js"></script>
<div class="ync-photovp-container"> <!-- right block may have tag_mode/edit_mode/cannot_comment class-->
    <div class="ync-photovp-block-content "> <!-- photo block (left)-->
        <a href="javascript:void(0)" class="yncphotovp_fullscreen js_yncphotovp_fullscreen" title="{_p var='toggle_fullscreen'}"><i class="ico">
            <img class="icon-collapse" src="{param var='core.path_actual'}/PF.Site/Apps/ync-photovp/assets/images/arrow-collapse.svg" alt="">
            <img class="icon-expand" src="{param var='core.path_actual'}/PF.Site/Apps/ync-photovp/assets/images/arrow-expand.svg" alt="">
        </i></a>
        <div class="photovp_view dont-unbind" data-photo-id="{$aForms.photo_id}">
            {if isset($aTitleLabel) && isset($aTitleLabel.type_id) && isset($aTitleLabel.label) && count($aTitleLabel.label)}
            <div class="{$aTitleLabel.type_id}-icon">
                {foreach from=$aTitleLabel.label key=sKey item=aLabel}
                <div class="sticky-label-icon title-label sticky-{$sKey}-icon" title="{_p var=$sKey}">
                    <span class="ico ico-{$aLabel.icon_class}"></span>
                    <span class="{if isset($aLabel.title_class)}{$aLabel.title_class}{/if}">{$aLabel.title}</span>
                </div>
                {/foreach}
            </div>
            {/if}
            <div class="ync-photovp-image-holder">
                <img id="js_photo_view_image" src="{img id='js_photo_view_image' server_id=$aForms.server_id path='photo.url_photo' suffix='' file=$aForms.destination time_stamp=true title=$aForms.title return_url=true}">
                {if !empty($previousPhoto)}
                <a id="ync_photovp_previous_photo" class="button photo_btn" href="{$previousPhoto.link}" title="{_p var='previous_photo'}">
                    <i class="ico ico-angle-left"></i>
                </a>
                {/if}
                {if !empty($nextPhoto)}
                <a id="ync_photovp_next_photo" class="button photo_btn" href="{$nextPhoto.link}" title="{_p var='next_photo'}">
                    <i class="ico ico-angle-right"></i>
                </a>
                {/if}
            </div>
            {if PHPFOX_IS_AJAX_PAGE}
            <span class="_a_back"><i class="ico ico-arrow-left"></i>{_p var='back'}</span>
            {/if}

            {literal}
            <script>
                var preLoadImages = false;
                var preSetActivePhoto = false;
            </script>
            {/literal}
        </div>
        <div class="ync-photovp-content-info">
            <div class="ync-photovp-title-container">
                <div class="item-title">
                    <span>{$aForms.title}</span>
                </div>
                <div class="item-size-photovp">
                    <div class="item-size-wrapper">
                        <div class="item-stat item-user ync-photovp-only-fullscreen">
                            <span>{_p var='by'}</span>
                            {$aForms|user:'':'':50}
                        </div>
                        <div class="item-stat">
                            <span>{_p var='file_size'}:</span>
                            <span>{$aForms.file_size|filesize}</span>
                        </div>
                        <div class="item-stat">
                            <span>{_p var='dimension'}:</span>
                            <span>{$aForms.width} x {$aForms.height}</span>
                        </div>
                        {if !empty($aForms.album_id)}
                        <div class="item-stat item-album">
                            <span>{_p var='album'}:</span>
                            <a href="{$aForms.album_url}">{$aForms.album_title|convert|clean|split:45}</a>
                        </div>
                        {/if}
                        {if !empty($aForms.sCategories)}
                        <div class="item-stat item-category">
                            {_p var="Categories"}: {$aForms.sCategories}
                        </div>
                        {/if}
                    </div>
                </div>
            </div>
            <div class="photovp_actions">
                <ul>
                    <li class="photos_comment ync-photovp-only-fullscreen js_ync_photovp_fullscreen_comment_toggle" title="{_p var='toggle_comment'}">
                        <a href="javascript:void(0)" id="">
                            <i class="ico ico-comment"></i>
                        </a>
                    </li>
                    {if (Phpfox::getUserParam('photo.can_tag_own_photo') && $aForms.user_id == Phpfox::getUserId() &&
                    Phpfox::getUserParam('photo.how_many_tags_on_own_photo') > 0) ||
                    (Phpfox::getUserParam('photo.can_tag_other_photos') &&
                    Phpfox::getUserParam('photo.how_many_tags_on_other_photo'))}
                    <li class="photos_tag">
                        <a href="javascript:void(0)" id="js_ync_photovp_tag_photo_2" title="{_p var='tag_friends'}">
                            <i class="ico ico-price-tag"></i>
                        </a>
                    </li>
                    {/if}
                    {if $aForms.user_id == Phpfox::getUserId() ||
                    (Phpfox::getUserParam('photo.can_download_user_photos') &&
                    $aForms.allow_download)}
                    <li class="photos_download">
                        <a href="{permalink module='photo' id=$aForms.photo_id title=$aForms.title action=download}"
                           id="js_download_photo" class="no_ajax" title="{_p var='download'}">
                            <i class="ico ico-download-alt"></i>
                        </a>
                    </li>
                    {/if}
                    <li class="photos-edit-more js_ync_photovp_edit_more" title="{_p var='more_actions'}">
                        <div class="dropup dropdown-container">
                        <span role="button" data-toggle="dropdown" class="item_bar_action">
                            <i class="ico ico-gear-o"></i>
                        </span>
                            <ul class="dropdown-menu dropdown-menu-right">
                                {template file='yncphotovp.block.menu'}
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div> <!-- end photo block -->
    <div class="ync-photovp-block-info">
        <div class="ync-photovp-block-info-title">
            sa da dad a
        </div>
        <div class="ync-photovp-block-info-inner">
            <div class="ync_photovp_content_form">
                <div class="item_view">
                    <div class="header-info">
                        {img user=$aForms suffix='_50_square'}
                        <div class="item_info_author">
                            <div class="item-user">{$aForms|user:'':'':50}</div>
                            <div class="item-time">{$aForms.time_stamp|convert_time}</div>
                        </div>
                    </div>
                    {if $aForms.view_id == 1}
                        {template file='core.block.pending-item-action'}
                    {/if}
                    <div class="photovp-title-text">
                        {$aForms.title}
                    </div>
                    {if $aForms.total_view > 0}
                    <div class="photovp-total-view">
                        <span>{$aForms.total_view|short_number}</span>{if $aForms.total_view == 1} {_p var='view_lowercase'}{else} {_p var='views_lowercase'}{/if}
                    </div>
                    {/if}
                    {if $aForms.description}
                    <div class="photovp-description-text item_view_content">
                        <span>
                            {$aForms.description|parse|shorten:100:'feed.view_more':true|split:55|max_line}
                        </span>
                    </div>
                    {else}
                    {if $aForms.canEdit}
                    <div class="ync-photovp-desc-add">
                        <a href="javascript:void(0)" class="js_ync_photovp_edit" data-photo_id="{$aForms.photo_id}" title="{_p var='update_photo_info'}">
                            <i class="ico ico-textedit mr-1"></i>{_p var='update_photo_info'}
                        </a>
                    </div>
                    {/if}
                    {/if}
                </div>
                <div class="ync-photovp-tag-container{if !$bHasTag} only-tag{/if}">
                    <div class="photovp_tag_in_photo js_tagged_section">
                        <!-- <p>{_p var='tagged_in_this_photo'}</p> -->
                        <p>{_p var='with'}</p>
                        <span id="js_photo_in_this_photo" class="ync-photovp-listtag"></span>
                    </div>
                    <div class="ync-photovp-tag-btn">
                        {if (Phpfox::getUserParam('photo.can_tag_own_photo') && $aForms.user_id == Phpfox::getUserId() &&
                        Phpfox::getUserParam('photo.how_many_tags_on_own_photo') > 0) ||
                        (Phpfox::getUserParam('photo.can_tag_other_photos') &&
                        Phpfox::getUserParam('photo.how_many_tags_on_other_photo'))}
                        <a href="javascript:void(0);" id="js_tag_photo" title="{_p var='tag_friends'}">
                            <i class="ico ico-price-tag"></i><span class="item-text">{_p var="tag_friends"}</span>
                        </a>
                        {/if}
                    </div>
                    <div class="btn-toggle-tag">
                        <i class="ico  ico-angle-down"></i>
                    </div>
                </div>
                <div class="item-addthis mb-1 pt-1">
                    {addthis url=$aForms.link title=$aForms.title description=$sShareDescription}
                </div>
                <div class="ync-photovp-comment-container">
                    <div class="item-detail-feedcomment">
                        {module name='feed.comment'}
                    </div>
                </div>
            </div>
            <div class="ync_photovp_edit_form"> <!-- edit form container-->
            </div>
        </div>
        <div class="ync-photovp-block-info-bottom">
        </div>
    </div> <!-- end edit form -->
</div>
<input type="hidden" id="js_ync_photovp_slink" value="{$sLink}">
<input type="hidden" id="js_photo_view_image_small" value="">

<script type="text/javascript">
    var bChangePhoto = true;
    var aPhotos = {$sPhotos};
    var oPhotoTagParams =  {l}{$sPhotoJsContent}{r};
    $Behavior.tagPhoto = function()
    {l}
        $Core.photo_tag.init(oPhotoTagParams);
        $("#page_photo_view input.v_middle" ).focus(function() {l}
            $(this).parent('.table_right').addClass('focus');
            $(this).parents('.table').siblings('.cancel_tagging').addClass('focus');
        {r});
        $("#page_photo_view input.v_middle" ).focusout(function() {l}
            $(this).parent('.table_right').removeClass('focus');
            $(this).parents('.table').siblings('.cancel_tagging').removeClass('focus');
        {r});
    {r};
    $Behavior.removeImgareaselectBox = function()
    {l}
    {literal}
        if ($('body#page_photo_view').length == 0 || ($('body#page_photo_view').length > 0 && bChangePhoto == true)) {
            bChangePhoto = false;
            $('.imgareaselect-outer').hide();
            $('.imgareaselect-selection').each(function() {
                $(this).parent().hide();
            });
        }
    {/literal}
    {r};
</script>

{if $bLoadCheckin}
<script type="text/javascript">
    var bCheckinInit = false;
    $Behavior.prepareInit = function()
    {l}
        if (typeof $Core.Feed === "undefined")
        {l}
            return;
        {r}
        $Core.Feed.sIPInfoDbKey = '';
        $Core.Feed.sGoogleKey = '{param var="core.google_api_key"}';

        {if isset($aVisitorLocation)}
            $Core.Feed.setVisitorLocation({$aVisitorLocation.latitude}, {$aVisitorLocation.longitude} );
        {/if}
        $Core.Feed.googleReady('{param var="core.google_api_key"}');
    {r}
</script>
{/if}