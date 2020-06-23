<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/10/16
 * Time: 10:40 AM
 */
?>
<style type="text/css">
    .profiles_banner_bg .cover img.cover_photo
    {l}
        position: relative;
        left: 0;
        top: {$aItem.position_top}px;
    {r}
</style>

<div class="ynstore-store-detail-block ynstore-store-detail-block-tp2">
    <div class="ynstore-info-block">
        {img user=$aItem suffix='_50_square'}

        <div class="ynstore-info">
            <div class="ynstore-time-user">
                {_p var='ynsocialstore.created_on'} {$aItem.time_stamp} {_p var='ynsocialstore.by'} {$aItem|user}
            </div>
            <div class="ynstore-categories">
                <div class="ynstore-categories-content">
                    {if $aItem.hiddencate > 0}
                        {if Phpfox::isPhrase($this->_aVars['aItem']['categories'][0]['title'])}
                            <?php $this->_aVars['value_name'] = _p($this->_aVars['aItem']['categories'][0]['title']) ?>
                        {else}
                            {assign var='value_name' value=$aItem.categories.0.title|convert}
                        {/if}
                        <a href="{permalink module='ynsocialstore.store.category' id=$aItem.categories.0.category_id title=$value_name}">{$value_name}</a>
                        <div class="dropdown">
                            {_p('and')}
                            <a href="javascript:void(0)" data-toggle="dropdown">+{$aItem.hiddencate}</a>
                            <ul class="dropdown-menu">
                                {foreach from=$aItem.categories key=iKey item=aCategory}
                                    {if $iKey > 0}
                                        {if Phpfox::isPhrase($this->_aVars['aCategory']['title'])}
                                            <?php $this->_aVars['value_name'] = _p($this->_aVars['aCategory']['title']) ?>
                                        {else}
                                            {assign var='value_name' value=$aCategory.title|convert}
                                        {/if}
                                    <li><a href="{permalink module='ynsocialstore.store.category' id=$aCategory.category_id title=$value_name}">{$value_name}</a></li>
                                    {/if}
                                {/foreach}
                            </ul>
                        </div>
                    {else}
                        {foreach from=$aItem.categories key=iKey item=aCategory}
                            {if Phpfox::isPhrase($this->_aVars['aCategory']['title'])}
                                <?php $this->_aVars['value_name'] = _p($this->_aVars['aCategory']['title']) ?>
                            {else}
                                {assign var='value_name' value=$aCategory.title|convert}
                            {/if}
                            <a href="{permalink module='ynsocialstore.store.category' id=$aCategory.category_id title=$value_name}">{$value_name}</a>{if $iKey == 0 && count($aItem.categories) > 1}<i>,</i>{/if}
                        {/foreach}
                    {/if}
                </div>
            </div>
        </div>
    </div>

    <div class="ynstore-actions-social">
        <div class="ynstore-embedcode dropdown">
            <a data-caption="HTML Code" onclick="$(this).parent('div').toggleClass('open'); if($('.ynstore_store_html_code_block textarea').length){l} $('.ynstore_store_html_code_block textarea').get(0).select();{r}" title="HTML Code" class="btn btn-default">
                <i class="fa fa-code"></i>
                {_p('Embed')}
                <i class="fa fa-angle-down"></i>
            </a>

            <div class="dropdown-menu ynstore_store_html_code_block">
                <textarea id="ynstore_html_code_value" readony class="form-control disabled"><iframe width="500" height="550" src="{$sUrl}"></iframe></textarea>

                <div class="text-right">
                   <button type="button" onclick="$(this).parents('.ynstore-embedcode').toggleClass('open');" class="btn btn-sm btn-default">
                       {_p var='close'}
                   </button>
                    <button type="button" class="yns-copy-btn btn btn-sm btn-primary" onclick="ynsocialstore.copy_embed_code(this)" data-clipboard-target="#ynstore_html_code_value">
                        {_p('Copy code')}
                    </button>
                </div>
            </div>
        </div>

        <!-- AddThis Button BEGIN -->
        {addthis url=$aItem.bookmark_url title=$aItem.name}
        <!-- AddThis Button END -->

        <div class="ynstore-btn-detail ynstore-cms">
            {template file='ynsocialstore.block.store.link'}
        </div>
    </div>
</div>

<div class="ynstore-store-cover">
    <div class="profiles_banner">
        <div class="profiles_banner_bg">
            <div id="ynstore_status_{$aItem.store_id}" class=ynstore-status-block>
                <div class="ynstore-status ynstatus_{$aItem.status}">
                    {_p var='ynsocialstore.'.$aItem.status}
                </div>
            </div>
            <div class="cover"
                {if !$aItem.cover_path}
                    style="background-image:url({param var='core.path'}module/ynsocialstore/static/image/store_cover_default.jpg)"
                    data-bg="no"
                {else}
                    style="background-image:url({img server_id=$aItem.cover_server_id path='core.url_pic' file='ynsocialstore/'.$aItem.cover_path suffix='_1024' return_url='true'})"
                {/if}>
                {if $aItem.cover_path}
                    {img server_id=$aItem.cover_server_id path='core.url_pic' file='ynsocialstore/'.$aItem.cover_path suffix='_1024' class="hidden-xs cover_photo"}
                {else}
                    <img src="{param var='core.path'}module/ynsocialstore/static/image/store_cover_default.jpg">
                {/if}

                <div id="change_cover_function" class="ynstore-cms" title="{_p var='profile.change_cover_photo'}">
                    {if Phpfox::getUserId() == $aItem.user_id}
                        <div class="dropdown">
                            <a role="button" data-toggle="dropdown">
                                <i class="ico ico-file-photo"></i>
                                <span>{_p('Update cover')}</span>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-right">
                                {if Phpfox::getUserParam('profile.can_change_cover_photo')}
                                <li role="presentation">
                                    <a id="js_change_cover_photo" onclick="$Core.box('ynsocialstore.coverupload', 500, 'store_id={$aItem.store_id}'); return false;">
                                        <i class="ico ico-upload"></i>
                                        {if empty($aItem.cover_path)}{_p var='user.add_a_cover'}{else}{_p var='profile.upload_photo'}{/if}
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a id="js_change_cover_photo_gallery" onclick="$Core.box('ynsocialstore.photogallery', 500, 'store_id={$aItem.store_id}&type=albums'); return false;">
                                        <i class="ico ico-file-photo-o"></i>
                                        {_p var='ynsocialstore.choose_from_gallery'}
                                    </a>
                                </li>
                                {if !empty($aItem.cover_path)}
                                <li role="presentation" class="visible-lg">
                                    <a onclick="ynsocialstore.repositionCoverPhoto({$aItem.store_id}, {$aItem.user_id}); return false;">
                                        <i class="ico ico-arrows-move"></i>
                                        {_p var='user.reposition'}
                                    </a>
                                </li>
                                {/if}
                                {/if}
                            </ul>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="ynstore-store-infomation-block-tp2">
    <div class="ynstore-store-logo-block">
        <div class="profile_image">
            {if !$aStore.logo_path}
                <div class="ynstore-store-nologo">
                    <img src="{param var='core.path'}module/ynsocialstore/static/image/store_default.png">
                </div>
            {else}
                <a href="{permalink module='ynsocialstore.store' id=$aStore.store_id title=$aStore.name}">
                    {img server_id=$aStore.server_id path='core.url_pic' file='ynsocialstore/'.$aStore.logo_path suffix='_480_square'}
                </a>
            {/if}
            {if Phpfox::getUserId() == $aStore.user_id}
                <form class="" method="post" enctype="multipart/form-data" action="#">
                    <input title="{_p var='ynsocialstore.change_logo'}" id="ynstore-store-logo-upload" type="file" accept="image/*" class="ajax_upload" value="Upload" name="image" data-url="{url link='ynsocialstore.store.photo' store_id=$aStore.store_id}">
                    <label for="ynstore-store-logo-upload" href="{url link='user.photo'}"><i class="ico ico-file-photo"></i> {_p var='ynsocialstore.change_logo'}</label>
                </form>
            {/if}
        </div>
    </div>

    <div class="ynstore-store-infomation">
        <div class="ynstore-store-info-break">
            <div class="ynstore-store-info-break-left">
                <div class="ynstore-title">
                    {$aStore.name|clean}
                </div>

                {if !empty($aStore.address) && !empty($aStore.address.0.latitude) && !empty($aStore.address.0.longitude)}
                <div class="ynstore-location">
                    <a href="https://maps.google.com/maps?daddr={$aStore.address.0.latitude},{$aStore.address.0.longitude}" target="_blank">{$aStore.address.0.address}</a>
                </div>
                {/if}

                <a class="ynstore-viewmore-radius ynstore-vm-aboutus-btn" href="{$sUrlAboutUs}">{_p var='ynsocialstore.view_more_abouts_us'}</a>

                <div class="ynstore-ratings-reviews-block">
                    <div class="ynstore-rating yn-rating yn-rating-large">
                        <span class="rating">{$aStore.rating}</span>
                        {for $i = 0; $i < 5; $i++}
                        {if $i < (int)$aStore.rating}
                        <i class="ico ico-star" aria-hidden="true"></i>
                        {elseif ((round($aStore.rating) - $aStore.rating) > 0) && ($aStore.rating - $i) > 0}
                        <i class="ico ico-star-half-o" aria-hidden="true"></i>
                        {else}
                        <i class="ico ico-star yn-rating-disable" aria-hidden="true"></i>
                        {/if}
                        {/for}
                    </div>

                    <a href="{permalink module='ynsocialstore.store' id=$aStore.store_id title=$aStore.name}reviews" class="ynstore-viewmore-radius">
                        {$aStore.total_review}&nbsp;{if $aStore.total_review == 1}{_p var='ynsocialstore.review'}{else}{_p var='ynsocialstore.reviews'}{/if}
                    </a>
                </div>
            </div>

            <div class="ynstore-store-info-break-right">
                <div class="ynstore-info-link">
                    {if Phpfox::getUserId() != $aStore.user_id}
                    <a class="" href="javascript:void(0)" onclick="$Core.composeMessage({l}user_id:{$aStore.user_id}{r}); return false;">
                        <i class="ico ico-envelope-o mr-1"></i>
                        {_p var='ynsocialstore.message_seller'}
                    </a>
                    {/if}

                    {if !empty($aStore.address) && !empty($aStore.address.0.latitude) && !empty($aStore.address.0.longitude)}
                    <a class="btn ynstore-btn-fw" href="https://maps.google.com/maps?daddr={$aStore.address.0.latitude},{$aStore.address.0.longitude}" target="_blank">
                        <i class="ico ico-compass mr-1"></i>
                        {_p var='ynsocialstore.get_directions'}
                    </a>
                    {/if}
                </div>

                <div class="ynstore-store-statistic-actions">
                  <div class="ynstore-store-actions">
                        <div class="ynstore-detail-btn ynstore-compare-btn-detail">
                            <a title="{_p var='ynsocialstore.add_to_compare'}" class="btn btn-default ynstore-check-compare" data-comparestoreid="{$aItem.store_id}" onclick="ynsocialstore.addToCompare({$aItem.store_id},'store');return false;">
                                <i class="ico ico-copy"></i> {_p var='compare'}
                            </a>
                        </div>

                        {if Phpfox::isUser() && $aItem.user_id != Phpfox::getUserId()}
                        <div class="ynstore-detail-btn" id="ynstore-detail-favorite-store-{$aItem.store_id}">
                            {if !$aItem.is_favorite}
                            <a title="{_p('Favorite')}" class="btn btn-default" onclick="ynsocialstore.updateFavorite({$aItem.store_id},1);return false;">
                                <i class="ico ico-star-o"></i> {_p var='ynsocialstore.favorite'}
                            </a>
                            {else}
                            <a title="{_p('Favorited')}" class="btn btn-default" onclick="ynsocialstore.updateFavorite({$aItem.store_id},0);return false;">
                                <i class="ico ico-star"></i> {_p var='ynsocialstore.favorited'}
                            </a>
                            {/if}
                        </div>

                        <div class="ynstore-detail-btn" id="ynstore-detail-follow-store-{$aItem.store_id}">
                            {if !$aItem.is_following}
                            <a title="{_p var='follow'}" class="btn btn-primary" onclick="ynsocialstore.updateFollow({$aItem.store_id},1);return false">
                                <i class="ico ico-plus"></i> {_p var='ynsocialstore.follow'}
                            </a>
                            {else}
                            <a title="{_p('Following')}" class="btn btn-default" onclick="ynsocialstore.updateFollow({$aItem.store_id},0);return false">
                                <i class="ico ico-check"></i> {_p var='ynsocialstore.following'}
                            </a>
                            {/if}
                        </div>
                        {/if}
                    </div>

                    <div class="ynstore-store-statistic">
                        <div class="ynstore-statistic-item {if $aItem.total_follow && $aItem.user_id == Phpfox::getUserId()}ynstore-hover{/if}" {if $aItem.total_follow && $aItem.user_id == Phpfox::getUserId()}onclick="$Core.box('ynsocialstore.getUsers', 500, 'iStoreId={$aItem.store_id}&sType=following'); return false;"{/if}>
                            <span>
                                {$aItem.total_follow}
                            </span>
                            {if $aItem.total_follow == 1}
                                {_p('follower')}
                            {else}
                                {_p('followers')}
                            {/if}
                            {if $aItem.total_follow && $aItem.user_id == Phpfox::getUserId()}<i class="ico ico-angle-right"></i>{/if}
                        </div>

                        <div class="ynstore-statistic-item {if $aItem.total_favorite && $aItem.user_id == Phpfox::getUserId()}ynstore-hover{/if}" {if $aItem.total_favorite && $aItem.user_id == Phpfox::getUserId()}onclick="$Core.box('ynsocialstore.getUsers', 500, 'iStoreId={$aItem.store_id}&sType=favorite'); return false;"{/if}>
                            <span >{$aItem.total_favorite}</span> {if $aItem.total_favorite == 1}{_p('favorite')}{else}{_p('favorites')}{/if}
                            {if $aItem.total_favorite && $aItem.user_id == Phpfox::getUserId()}<i class="ico ico-angle-right"></i>{/if}
                        </div>

                        <div class="ynstore-statistic-item">
                            <span>{$aItem.total_orders}</span> {if $aItem.total_orders == 1}{_p('order')}{else}{_p('orders')}{/if}
                            {if $aItem.total_orders}<i class="ico ico-angle-right"></i>{/if}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ynstore-description">
            {$aStore.short_description|clean}
        </div>
    </div>
</div>

<div class="profiles_menu set_to_fixed dont-unbind built">
    <ul class="ynstore-menu-fs" style="padding-left: 0px;">
        {foreach from=$aProfileMenu key=iKey item=aItemMenu}
        <li class="{if $aItemMenu.sMenu == $sDetailPage}active{/if} {$aItemMenu.sClass}"  >
            <a href="{url link=$aItemMenu.sLink}">
                {$aItemMenu.sPhrase|convert}
            </a>
        </li>
        {/foreach}
        <li class="dropdown">
            <span class="" data-toggle="dropdown"><i class="ico ico-dottedmore"></i></span>
            <ul class="dropdown-menu dropdown-menu-right">
                {foreach from=$aProfileMenu key=iKey item=aItemMenu}
                <li class="{if $aItemMenu.sMenu == $sDetailPage}active{/if} {$aItemMenu.sClass}">
                    <a href="{url link=$aItemMenu.sLink}">
                        {$aItemMenu.sPhrase|convert}
                    </a>
                </li>
                {/foreach}
            </ul>
        </li>
    </ul>
</div>
