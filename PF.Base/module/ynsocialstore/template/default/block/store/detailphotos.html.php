<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/11/16
 * Time: 9:58 AM
 */
?>

{if isset($bSpecialMenu) && $bSpecialMenu == true}
    {template file='ynsocialstore.block.store.specialmenu'}
{/if}

{if !$bShowPhotos}
    {if count($aItems)}
    <div class="ynstore-album-items">
        {foreach from=$aItems item=aAlbum name=albums}
        <div class="ynstore-album-item">
            <div class="ynstore-album-content"  data-photo-id="{$aAlbum.album_id}" id="js_album_id_{$aAlbum.album_id}">
                <div class="ynstore-album-bg"
                        {if $aAlbum.destination}
                            style="background-image: url({img server_id=$aAlbum.server_id path='photo.url_photo' file=$aAlbum.destination suffix='_500' max_width=500 max_height=500 return_url=true})"
                        {/if}
                    >
                    {if !$aAlbum.destination}
                    <span class="no_image_item_cover"></span>
                    {/if}

                    <div class="ynstore-bg-gradient"></div>

                    <span class="ynstore-count">
                        {if $aAlbum.total_photo}
                            {$aAlbum.total_photo|number_format}
                            <i class="ico ico-file-photo"></i>
                        {/if}
                    </span>
                    <a href="{$aAlbum.link}" class="ynstore-link"></a>
                    {if $bCanDeletePhoto == 1}
                    <div class="moderation_row">
                        <label class="item-checkbox">
                            <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aAlbum.album_id}" id="check{$aAlbum.album_id}" />
                            <i class="ico ico-square-o"></i>
                        </label>
                    </div>
                    {/if}
                </div>
                <div class="ynstore-album-info">
                    <a href="{$aAlbum.link}">
                        {$aAlbum.name|clean}
                    </a>

                    <span class="info">
                        {plugin call='photo.template_block_album_entry_extra_info'}
                    </span>
                </div>
            </div>
        </div>
        {/foreach}
        {pager}
        {if $bCanDeletePhoto == 1}
            {moderation}
        {/if}
    </div>
    {else}
        {if $iPage <= 1}
        <div class="extra_info">
            {_p var='ynsocialstore.no_albums_found'}
        </div>
        {/if}
    {/if}
{else}
<div id="js_actual_photo_content">
    {if count($aItems)}
        <div class="ynstore-photo-items">
            {foreach from=$aItems item=aPhoto}
            <div class="ynstore-photo-item" data-photo-id="{$aPhoto.photo_id}" id="js_photo_id_{$aPhoto.photo_id}">
                <div class="ynstore-photo-item-bg">
                    <a
                        href="{$aPhoto.link}{if isset($iForceAlbumId)}albumid_{$iForceAlbumId}/{/if}{if isset($sPhotoCategory)}category_{$sPhotoCategory}/{/if}"
                        title="{$aPhoto.title}"
                        style="background-image:url({img server_id=$aPhoto.server_id path='photo.url_photo' file=$aPhoto.destination suffix='_500' title=$aPhoto.title return_url='true'})"
                        class="ynstore-photo-img">
                    </a>
                    <a href="{$aPhoto.link}{if isset($iForceAlbumId)}albumid_{$iForceAlbumId}/{/if}{if isset($sPhotoCategory)}category_{$sPhotoCategory}/{/if}" class="ynstore-photo-bg-gradient"></a>

                    <div class="ynstore-photo-info">
                        <div>{_p var='photo.by_user_info' user_info=$aPhoto|user|shorten:30:'...'|split:20}</div>
                        {*if !empty($aPhoto.album_name)}
                        <div>
                            {_p var='photo.in'}
                            <a href="{permalink module='photo.album' id=$aPhoto.album_id title=$aPhoto.album_name}" title="{$aPhoto.album_name|clean}">
                                {if $aPhoto.album_profile_id > 0}
                                {_p var='photo.profile_pictures'}{else}{$aPhoto.album_name|clean|shorten:45:'...'|split:20}
                                {/if}
                            </a>
                        </div>
                        {/if*}
                    </div>
                    {if $bCanDeletePhoto == 1}
                    <div class="moderation_row">
                        <label class="item-checkbox">
                            <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aPhoto.photo_id}" id="check{$aPhoto.photo_id}" />
                            <i class="ico ico-square-o"></i>
                        </label>
                    </div>
                    {/if}
                </div>
            </div>
            {/foreach}
        </div>
        {pager}
        {if $bCanDeletePhoto == 1}
            {moderation}
        {/if}
    {else}
        {if $iPage <= 1}
        <div class="extra_info">
            {_p var='photo.no_photos_found'}
        </div>
        {/if}
    {/if}
</div>
{/if}
{literal}
<script>
    $Behavior.initStoreDetailPhotosBlock = function() {
        if($('#page_ynsocialstore_store_detail #content-stage ._block_top').length) {
            if (!$('#content-stage ._block_top').length)
                $('#content-stage ._block_top').clone().appendTo("#content-stage ._block.location_7");
        }
    }
</script>
{/literal}
