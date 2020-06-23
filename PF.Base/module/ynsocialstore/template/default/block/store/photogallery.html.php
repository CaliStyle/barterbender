<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/14/16
 * Time: 1:40 PM
 */
?>
<div id="js_store_popup_gallery_content">
{if $type == 'photos'}
    <div class="ynstore-breadcrumbs">
        <a role="button" id="js_change_cover_photo_gallery" onclick="$.ajaxCall('ynsocialstore.loadphotogallery', 'store_id={$sStoreId}&type=albums&page=0}'); return false;">
            <i class="ico ico-file-photo"></i>
            {_p var='ynsocialstore.all_albums'}
        </a>
            <i class="ico ico-angle-right"></i>
            {$sAlbumName}
            ({$iCount} {if $iCount == 1} {_p var='ynsocialstore.photo'} {else} {_p var='ynsocialstore.photos'}{/if})
    </div>

    <div class="ynstore-photo-popup">
        {if count($aItems) < 1}
            <div class="extra_info">
                {_p var='ynsocialstore.no_item_s_found'}.
            </div>
        {else}
            <input type="hidden" id="current_page" value="{$page}">
            <div class="ynstore-photo-popup-block" id="js_store_popup_gallery_content_ajax">
                {foreach from=$aItems item=aPhoto name=photos}
                <div class="ynstore-photo-item">
                    <div class="ynstore-content">
                        <a  onclick="js_box_remove(this); $.ajaxCall('ynsocialstore.setCoverPhoto', 'page_id={$sStoreId}&photo_id={$aPhoto.photo_id}');" 
                            title="{$aPhoto.title}"
                            style="
                                {if (Phpfox::isModule('photo')) || ( Phpfox::isModule('advancedphoto') && Phpfox::getParam('advancedphoto.delete_original_after_resize')) }
                                    background-image: url({img server_id=$aPhoto.server_id path='photo.url_photo' file=$aPhoto.destination suffix='_240' title=$aPhoto.title return_url='true'})
                                {else}
                                    background-image: url({img server_id=$aPhoto.server_id path='photo.url_photo' file=$aPhoto.destination suffix='_240' title=$aPhoto.title}return_url='true'})
                                {/if}
                            "
                        >
                        </a>
                    </div>
                </div>
                {/foreach}
                <a role="button" class="ynstore-loadmore" id="js_load_more_photo" onclick="onLoadMoreItem()">
                    {_p var='ynsocialstore.load_more'}
                </a>
            </div>


            {literal}
            <script type="text/javascript">
                function onLoadMoreItem() {
                    if ($('#current_page').length > 0)
                    {
                        var page = $('#current_page').val();
                        $('#current_page').remove();
                        $.ajaxCall('ynsocialstore.loadmoregallery', 'store_id={/literal}{$sStoreId}{literal}&album_id={/literal}{$iAlbumId}{literal}&type=photos&page=' + page);
                    }
                }
            </script>
            {/literal}
        {/if}
    </div>
{else}
    <div class="ynstore-album-popup">
        <div class="ynstore-album-popup-block" id="js_store_popup_gallery_content_ajax">
            <div class="ynstore-album-item">
                <div class="ynstore-content">
                    <div class="ynstore-bg">
                            <a  href="javascript:void(0)"
                                onclick="$.ajaxCall('ynsocialstore.loadphotogallery', 'store_id={$sStoreId}&type=photos&album_id=all&albumName=all_photos&page=0'); return false;"
                                title="{_p var='ynsocialstore.all_photos'}"
                                id="js_change_cover_photo_gallery_photo_all"
                                class=""
                                style="
                                    {if isset($aRandomPhoto.destination) && $aRandomPhoto.destination != ''}
                                        background-image: url({img server_id=$aRandomPhoto.server_id path='photo.url_photo' file=$aRandomPhoto.destination suffix='_240' return_url='true'})
                                    {else}
                                        background-image: url({param var='core.path'}module/ynsocialstore/static/image/nocover.jpg)
                                    {/if}
                                    "
                            >
                        </a>
                    </div>
                    <div class="ynstore-info">
                        <a href="javascript:void(0)"
                        onclick="$.ajaxCall('ynsocialstore.loadphotogallery', 'store_id={$sStoreId}&type=photos&album_id=all&albumName=all_photos&page=0'); return false;"
                        id="js_album_inner_title_all" class="">{_p var='ynsocialstore.all_photos'}</a>
                    </div>
                </div>
            </div>
            {if count($aItems) > 0}
            <input type="hidden" value="1" id="current_page">
            {foreach from=$aItems item=aAlbum name=albums}
            <div class="ynstore-album-item">
                <div class="ynstore-content">
                    <div class="ynstore-bg">
                        <a  href="javascript:void(0)" onclick="$.ajaxCall('ynsocialstore.loadphotogallery', 'store_id={$sStoreId}&type=photos&album_id={$aAlbum.album_id}&albumName={$aAlbum.name|clean}'); return false;"
                            title="{_p var='photo.name_by_full_name' name=$aAlbum.name|clean full_name=$aAlbum.full_name|clean}"
                            id="js_change_cover_photo_gallery_photo_{$aAlbum.album_id}"
                            class=""
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
        </div>
        <a role="button" class="ynstore-loadmore" id="js_load_more_album" onclick="onLoadMoreItem()">
            {_p var='ynsocialstore.load_more'}
        </a>
        {literal}
        <script type="text/javascript">
            function onLoadMoreItem() {
                if ($('#current_page').length > 0)
                {
                    var page = $('#current_page').val();
                $('#current_page').remove();
                $.ajaxCall('ynsocialstore.loadmoregallery', 'store_id={/literal}{$sStoreId}{literal}&type=albums&page=' + page);
                }
            }
        </script>
        {/literal}
        {/if}
    </div>
{/if}
</div>
