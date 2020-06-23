<?php

defined('PHPFOX') or exit('NO DICE!');

?>

<div class="p-item p-advmarketplace-seller-item">
    <div class="item-outer">
        <div class="p-item-media-wrapper">
            {if empty($sideBlock)}
            <a class="item-media-link p-advmarketplace-seller-cover" href="{url link=$aItem.user_name}">
                {if !empty($aItem.cover_photo_path)}
                    <span class="item-media-src" style="background-image: url({img server_id=$aItem.cover_photo_server_id path='photo.url_photo' file=$aItem.cover_photo_path suffix='_500' return_url=true})"></span>
                {elseif !empty($sCoverDefaultUrl)}
                    <span class="item-media-src" style="background-image: url({$sCoverDefaultUrl})"></span>
                {else}
                    <span class="item-media-src"></span>
                {/if}
            </a>
            {/if}
            <div class="p-advmarketplace-seller-avatar">
                {img user=$aItem suffix='_120' max_width='50' max_height='50'}
            </div>
        </div>

        <div class="item-inner">
            <!-- title -->
            <div class="p-advmarketplace-seller-title-wrapper">
                <h4 class="p-item-title p-advmarketplace-seller-title">
                    {$aItem|user}
                </h4>
            </div>
            <div class="p-item-minor-info p-advmarketplace-seller-info p-seperate-dot-wrapper">
                {if !empty($showClearListing)}
                <a class="p-seperate-dot-item" href="{url link=$aItem.user_name.'.advancedmarketplace'}">{$aItem.total_available} <span class="p-text-lowercase">{_p var='available'}</span></a>
                <span class="p-seperate-dot-item">{$aItem.total_sold} <span class="p-text-lowercase">{_p var='sold'}</span></span>
                {else}
                <a href="{url link=$aItem.user_name.'.advancedmarketplace'}">{$aItem.total_listing} <span class="p-text-lowercase">{if $aItem.total_listing == 1}{_p var='listing'}{else}{_p var='advancedmarketplace_listings_lower_case'}{/if}</span></a>
                {/if}
            </div>
            {if !empty($sideBlock) && $aItem.user_id != Phpfox::getUserId()}
            <div class="p-flex-wrapper p-mt-line js_seller_action_{$aItem.user_id}">
                {if Phpfox::getParam('advancedmarketplace.can_follow_listings')}
                <span class="js_follow_action">
                    {if $isFollow}
                        <a onclick="$(this).addClass('disabled').prop('disabled', true); follow('unfollow',{$aItem.user_id},{$iFollower}); return false;" type="button" class="btn btn-default btn-xs mr-1">{phrase var='advancedmarketplace.unfollow'}</a>
                    {else}
                        <a onclick="$(this).addClass('disabled').prop('disabled', true); follow('follow',{$aItem.user_id},{$iFollower}); return false;" type="button" class="btn btn-primary btn-xs mr-1">{phrase var='advancedmarketplace.follow'}</a>
                    {/if}
                </span>
                {/if}
                <a href="javascript:void();" class="btn btn-default btn-xs" onclick="appAdvMarketplace.contactSeller({l}id: {$aItem.user_id}, listing_id: {$aItem.listing_id}, module_id: 'advancedmarketplace'{r}); return false;">{phrase var='advancedmarketplace.contact'}</a>
            </div>
            {/if}
        </div>
    </div>
</div>