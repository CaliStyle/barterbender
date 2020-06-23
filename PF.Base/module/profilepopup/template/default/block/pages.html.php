<?php
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_ProfilePopup
 * @version        3.01
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<style type="text/css">
    .yn_profilepopup_cover.type-pages img {l}
        position: relative;
        left:0;
        top: {$coverPhotoPosition}px;
    {r}
</style>

<div class="no-popup uiContextualDialogContent">
    <div class="yn_profilepopup_hovercard_stage" {if !isset($aCoverPhoto)} style="padding-top: 10px;" {/if}>
            <!-- pages not found -->
            {if isset($iIsPages) && $iIsPages == 0}
            <div class="yn_profilepopup_hovercard_content p_10">
                    <div>
                            <div class="yn_profilepopup_info yn_profilepopup_info_left">
                                    {phrase var='profilepopup.pages_not_found'}.
                            </div>
                    </div>
            </div>
            {/if}

            <!-- pages is private -->
            {if isset($iIsCanView) && $iIsCanView == 0}
            <div class="yn_profilepopup_hovercard_content">
                    {if Phpfox::getParam('profilepopup.enable_thumbnails')}
                    <div class="yn_profilepopup_image">
                        {if isset($aPage.pages_image_path)}
                                <a href="{$aPage.link}">{img server_id=$aPage.image_server_id title=$aPage.title path='pages.url_image' file=$aPage.pages_image_path suffix='_120' max_width='100' max_height='100'}</a>
                        {else}
                                <a href="{$aPage.link}">{img server_id=$aPage.image_server_id title=$aPage.title path='pages.url_image' file=$aPage.image_path suffix='_120' max_width='100' max_height='100'}</a>
                        {/if}
                    </div>
                    {/if}
                    <div class="yn_profilepopup_main" {if !Phpfox::getParam('profilepopup.enable_thumbnails')}style="margin-left: 10px;"{/if}>
                            <div class="yn_profilepopup_main_title {if !Phpfox::getParam('profilepopup.enable_thumbnails')}yn_profilepopup_title_nophoto{/if} "> <a href="{$aPage.link}" class="link">{$aPage.title|clean|shorten:55:'...'|split:40}</a></div>
                            <div>
                                    <div class="yn_profilepopup_info yn_profilepopup_info_left">
                                            {phrase var='privacy.the_item_or_section_you_are_trying_to_view_has_specific_privacy_settings_enabled_and_cannot_be_viewed_at_this_time'}
                                    </div>
                            </div>
                    </div>
            </div>
            {/if}

            <!-- show profile -->
            {if isset($iIsPages) && $iIsPages == 1 && isset($iIsCanView) && $iIsCanView == 1}
            <div class="yn_profilepopup_hovercard_content">
                   {if isset($aCoverPhoto)}
                      <div class="yn_profilepopup_cover type-pages">
                          {img server_id=$aCoverPhoto.server_id path='photo.url_photo' file=$aCoverPhoto.destination suffix='_500' }
                          <div class="yn_profilepopup_backgroundcover"></div>
                      </div>
                      {else}
                      <div class="yn-profilepopup-nocover"></div>
                     {/if}
                     <div class="yn-profilepopup_basic_info" {if !Phpfox::getParam('profilepopup.enable_thumbnails')}style="margin-left: 10px;"{/if}>
                     {if Phpfox::getParam('profilepopup.enable_thumbnails')}
                        <div class="yn_profilepopup_image">
                            {if isset($aPage.pages_image_path)}
                                <a href="{$aPage.link}">{img server_id=$aPage.image_server_id title=$aPage.title path='pages.url_image' file=$aPage.pages_image_path suffix='_120' max_width='100' max_height='100'}</a>
                            {else}
                                    <a href="{$aPage.link}">{img server_id=$aPage.image_server_id title=$aPage.title path='pages.url_image' file=$aPage.image_path suffix='_120' max_width='100' max_height='100'}</a>
                            {/if}
                        </div>
                     {/if}
                     <div class="yn_profilepopup_main_title {if !Phpfox::getParam('profilepopup.enable_thumbnails')}yn_profilepopup_title_nophoto{/if} "><a href="{$aPage.link}" class="link">{$aPage.title|clean|shorten:55:'...'|split:40}</a></div>
                </div>

                    <div class="yn_profilepopup_main" {if !Phpfox::getParam('profilepopup.enable_thumbnails')}style="margin-left: 10px;"{/if}>

                            <div>
                                    {if count($aAllItems) > 0}
                                    {foreach from=$aAllItems key=iKey item=aItem}
                                    <!-- category -->
                                    {if array_key_exists('category_name', $aPage) === true && $aItem.name == 'category_name' && strlen(trim($aPage.category_name)) > 0 && intval($aItem.is_active) == 1 && intval($aItem.is_display) == 1}
                                    <div class="yn_profilepopup_info">
                                            <div class="yn_profilepopup_info_left">{$aItem.lang_name}:&nbsp;</div>
                                            <div class="yn_profilepopup_info_right">{$aPage.category_name|convert|clean}&nbsp;</div>
                                    </div>
                                    {/if}
                                    <!-- total of like -->
                                    {if array_key_exists('total_like', $aPage) === true && $aItem.name == 'total_like' && intval($aItem.is_active) == 1 && intval($aItem.is_display) == 1}
                                    <div class="yn_profilepopup_info">
                                            <div class="yn_profilepopup_info_left">{$aItem.lang_name}:&nbsp;</div>
                                            <div class="yn_profilepopup_info_right">{$aPage.total_like}&nbsp;</div>
                                    </div>
                                    {/if}
                                    {/foreach}
                                    {/if}
                            </div>
                            {if isset($sShowJoinedFriend) && $sShowJoinedFriend == '1' && $iJoinedFriendTotal > 0}
                            <div class="yn_profilepopup_mutual">
                                    <a href="#" onclick="$Core.box('profilepopup.getJoinedFriends', 300, '&item_type=pages&item_id={$aPage.page_id}');return false;">{phrase var='profilepopup.joined_friends_total' total=$iJoinedFriendTotal}</a>
                                    <div class="yn_profilepopup_block_listing_inline">
                                            <ul>
                                                    {foreach from=$aJoinedFriend key=iKey item=aMutual}
                                                    <li>{img user=$aMutual suffix='_50_square' max_width=32 max_height=32 class='js_hover_title'}</li>
                                                    {/foreach}
                                            </ul>
                                    </div>
                            </div>
                            {/if}
                    </div>
            </div>
            {/if}
    </div>
    <div class="yn_profilepopup_hovercard_footer">
        {if Phpfox::isUser() &&  isset($iIsPages) && $iIsPages == 1 && isset($iIsCanView) && $iIsCanView == 1}
            <ul class="yn_profilepopup_list_horizontal">
                {if Phpfox::isModule('foxfavorite') && Phpfox::isUser() && isset($sFFModule) && isset($iFFItemId) && $sFFModule == 'pages'}
                    {if !$bFFIsAlreadyFavorite}
                        <li class="yn_profilepopup_list_item">
                            <a title="{phrase var='profilepopup.favorite'}" onclick="ynfbpp.closePopup(); $('#js_favorite_link_unlike_{$iFFItemId}').show(); $('#js_favorite_link_like_{$iFFItemId}').hide(); $.ajaxCall('foxfavorite.addFavorite', 'type={$sFFModule}&amp;id={$iFFItemId}', 'GET'); {if $bEnableCachePopup}window.setTimeout('ynfbpp.refreshPage(null)', 500);{/if} return false;" class="yn_profilepopup_icon_favorite" href="#" >
                                <i class="fa fa-heart"></i>&nbsp;{phrase var='profilepopup.favorite'}
                            </a>
                        </li>
                    {else}
                        <li class="yn_profilepopup_list_item">
                            <a title="{phrase var='profilepopup.unfavorite'}" onclick="ynfbpp.closePopup(); $('#js_favorite_link_like_{$iFFItemId}').show(); $('#js_favorite_link_unlike_{$iFFItemId}').hide(); $.ajaxCall('foxfavorite.deleteFavorite', 'type={$sFFModule}&amp;id={$iFFItemId}', 'GET'); {if $bEnableCachePopup}window.setTimeout('ynfbpp.refreshPage(null)', 500);{/if} return false;" class="yn_profilepopup_icon_unfavorite" href="#" >
                                <i class="fa fa-heart-o"></i>{phrase var='profilepopup.unfavorite'}
                            </a>
                        </li>
                    {/if}
                {/if}

                <li class="yn_profilepopup_list_item" >
                    <a href="#" title="{phrase var='profilepopup.pp_share'}" onclick="tb_show('{phrase var='share.share' phpfox_squote=true}', $.ajaxBox('share.popup', 'height=300&amp;width=550&amp;type={$sBookmarkType}&amp;url={$sBookmarkUrl}&amp;title={$sBookmarkTitle}{if isset($sFeedShareId) && $sFeedShareId > 0}&amp;feed_id={$sFeedShareId}{/if}{if isset($sFeedType)}&amp;is_feed_view=1{/if}&amp;sharemodule={$sShareModuleId}')); return false;" class="yn_profilepopup_icon_share" >
                        <i class="fa fa-share-square-o"></i>&nbsp;{phrase var='share.share'}
                    </a>
                </li>

                {if !Phpfox::getUserBy('profile_page_id')  && Phpfox::isUser()}
                    {if !$aPage.is_liked}
                        {if $aPage.reg_method == '2' && !isset($aPage.is_invited) && $aPage.page_type == '1'}
                        {else}
                                {if isset($aPage.is_reg) && $aPage.is_reg}
                                {else}
                                <li class="yn_profilepopup_list_item" >
                                    <a title="{phrase var='profilepopup.like'}" onclick="ynfbpp.closePopup(); {if $aPage.page_type == '1' && $aPage.reg_method == '1'} $.ajaxCall('profilepopup.signup', 'page_id={$aPage.page_id}'); {else}$.ajaxCall('profilepopup.likePages', 'type_id=pages&amp;item_id={$aPage.page_id}');{/if} return false;" class="yn_profilepopup_icon_like" href="#">
                                       <i class="fa fa-thumbs-up"></i>&nbsp;{phrase var='profilepopup.like'}
                                    </a>
                                </li>
                                {/if}
                        {/if}
                    {else}
                        <li class="yn_profilepopup_list_item" >
                            <a title="{phrase var='profilepopup.unlike'}" onclick="ynfbpp.closePopup(); $.ajaxCall('profilepopup.unlikePages', 'type_id=pages&amp;item_id={$aPage.page_id}');return false;" class="yn_profilepopup_icon_unlike" href="#">
                                <i class="fa fa-thumbs-o-up"></i>&nbsp;{phrase var='profilepopup.unlike'}
                            </a>
                        </li>
                    {/if}
                {/if}
            </ul>
        {/if}
        <div class="clearfix"></div>
        <div class="clearfix"></div>
    </div>
</div>

