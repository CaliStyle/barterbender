<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/20/16
 * Time: 8:17 AM
 */
?>
{if $type == 'photos'}
    {if $iCount > 0}
        <input type="hidden" value="{$page}" id="current_page">
        {foreach from=$aItems item=aPhoto name=photos}
            <div class="ynstore-photo-item">
                <div class="ynstore-content">
                    <a  onclick="$.ajaxCall('ynsocialstore.setCoverPhoto', 'page_id={$sStoreId}&photo_id={$aPhoto.photo_id}');"
                        title="{$aPhoto.title}"
                        style="
                            {if (Phpfox::isModule('photo')) || ( Phpfox::isModule('advancedphoto') && Phpfox::getParam('advancedphoto.delete_original_after_resize')) }
                                background-image: url({img server_id=$aPhoto.server_id path='photo.url_photo' file=$aPhoto.destination suffix='_240' title=$aPhoto.title return_url='true'})
                            {else}
                                background-image: url({img server_id=$aPhoto.server_id path='photo.url_photo' file=$aPhoto.destination suffix='_240' title=$aPhoto.title}return_url='true'})
                            {/if}
                    ">
                    </a>
                </div>
            </div>
        {/foreach}
    {else}
        {literal}
            <script type="text/javascript">
                $('#js_load_more_photo').remove();
            </script>
        {/literal}
        <span class="ynstore-loadmore-txt">
            {_p var='ynsocialstore.there_are_nothing_to_show'}
        </span>
    {/if}
{else}
    {if $iCount > 0}
    <input type="hidden" value="{$page}" id="current_page">
    {foreach from=$aItems item=aAlbum name=albums}
    <div class="ynstore-album-item">
        <div class="ynstore-content">
            <div class="ynstore-bg">
                <a  href="javascript:void(0)" onclick="$.ajaxCall('ynsocialstore.loadphotogallery', 'store_id={$sStoreId}&type=photos&album_id={$aAlbum.album_id}&albumName={$aAlbum.name|clean}'); return false;"
                    title="{_p var='photo.name_by_full_name' name=$aAlbum.name|clean full_name=$aAlbum.full_name|clean}"
                    id="js_change_cover_photo_gallery_photo_{$aAlbum.album_id}"
                    style="
                        {if isset($aAlbum.destination) && $aAlbum.destination != ''}
                            background-image: url({img server_id=$aAlbum.server_id path='photo.url_photo' file=$aAlbum.destination suffix='_240' return_url='true'})
                        {else}
                            background-image: url({param var='core.path'}module/ynsocialstore/static/image/nocover.jpg)
                        {/if}
                        "
                    >
                </a>
            </div>

            <div class="ynstore-info">
                <a href="javascript:void(0)"
                   onclick="$.ajaxCall('ynsocialstore.loadphotogallery', 'store_id={$sStoreId}&type=photos&album_id={$aAlbum.album_id}&albumName={$aAlbum.name|clean}&page=0'); return false;"
                   id="js_album_inner_title_{$aAlbum.album_id}" class="row_sub_link">{$aAlbum.name|clean|shorten:150:'...'|split:40}</a>
            </div>
        </div>
    </div>
    {/foreach}
    {else}
        {literal}
        <script type="text/javascript">
            $("#js_load_more_album").remove();
        </script>
        {/literal}
        <span class="ynstore-loadmore-txt">
            {_p var='ynsocialstore.there_are_nothing_to_show'}
        </span>
    {/if}
{/if}
