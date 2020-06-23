<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="advmarketplace-app advmarketplace-feed-item core-feed-item js_listing_item_{$aListing.listing_id}">
    <div class="item-outer">
        <div class="item-media">
            <a class="item-media-src" href="{$aListing.bookmark_url}" target="_blank" style="background-image: url(
                {if !empty($aListing.image_path)}
                    {img server_id=$aListing.server_id title=$aListing.title path='advancedmarketplace.url_pic' file=$aListing.image_path suffix='' return_url=true}
                {else}
                    {img path='core.path_actual' file='PF.Site/Apps/p-advmarketplace/assets/image/default/no-image.png' return_url=true}
                {/if}
            )"></a>
        </div>¡

        <div class="item-inner">
            <div class="item-title">
                <a href="{$aListing.bookmark_url}" class="core-feed-title line-2">{$aListing.title|clean|shorten:100:'...'|split:25}</a>
            </div>
            <div class="item-info-minor">
                <div class="core-feed-minor p-seperate-dot-wrapper">
                    <div class="p-seperate-dot-item">
                        {if !empty($aListing.city)} {$aListing.city|clean}, {/if}{if !empty($aListing.country_child_id)} {$aListing.country_child_id|location_child}, {/if}{$aListing.country_iso|location}
                    </div>
                    <div class="p-seperate-dot-item">
                        <span class="category-title">{_p var='category'}:</span> {$aListing.categories|category_links|shorten:64:'...'}
                    </div>
                </div>
            </div>
            <div class="item-price">
                {if $aListing.price == '0.00'}
                    <div class="p-text-success">{_p var='free'}</div>
                {else}
                    <div class="p-text-warning">{$aListing.listing_price}</div>
                {/if}
            </div>
            {if empty($isEmbed)}
            <div class="item-action">
                <button class="btn btn-default btn-icon p-advmarketplace-item-wishlist-action js_wishlist_btn {if !empty($aListing.is_wishlist)}checked{/if}" data-id="{$aListing.listing_id}" data-wishlist="{if $aListing.is_wishlist}0{else}1{/if}" data-feed="1" onclick="appAdvMarketplace.processWishlist(this); return false;"><i class="ico ico-heart-o"></i> <span class="js_wishlist_text">{if !empty($aListing.is_wishlist)}{_p var='added_to_wish_list_replacement'}{else}{_p var='advancedmarketplace_add_to_wishlist'}{/if}</span></span></button>
            </div>
            {/if}
        </div>
    </div>
</div>
