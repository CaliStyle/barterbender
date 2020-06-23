<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/25/16
 * Time: 2:46 PM
 */
?>
<li class="ynstore-item" data-product-id="{$aItem.product_id}" id="js_product_id_{$aItem.product_id}">
    <div class="ynstore-item-content ynstore-product-listing">

        {if (Phpfox::isAdmin() || !empty($bShowModeration)) && empty($bIsNoModerate)}
        <div class="moderation_row">
            <label class="item-checkbox">
                <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aItem.product_id}" id="check{$aItem.product_id}" />
                <i class="ico ico-square-o"></i>
            </label>
        </div>
        {/if}

        <div class="ynstore-bg-block">
            <a class="ynstore-bg" href="{permalink module='ynsocialstore.product' id=$aItem.product_id title=$aItem.product_name}" style="
             background-image: url(
                {if $aItem.logo_path}
                    {img server_id=$aItem.server_id path='core.url_pic' file=$aItem.logo_path suffix='_400' return_url='true'}
                {else}
                    {param var='core.path_actual'}PF.Base/module/ynsocialstore/static/image/product_default.jpg
                {/if})
            ">
            </a>

            <div class="ynstore-featured">
                <div title="{_p var='ynsocialstore.featured'}" class="ynstore-featured-triangle ynstore_entry_feature_icon-{$aItem.product_id}" style="{if (!$aItem.is_featured)}visibility: hidden{/if}">

                    <i class="ico ico-diamond"></i>
                </div>
            </div>

            <div class="ynstore-addtocart-compare-block">


                {if isset($bIsWishList)}
                <div class="ynstore-addtowishlist" id="ynstore_product_wishlist_{$aItem.product_id}">
                    <a href="javascript:void(0)" title="{_p var='ynsocialstore.wishlist'}" class=" " onclick="$('#js_product_id_{$aItem.product_id}').remove(); ynsocialstore.updateWishList({$aItem.product_id},0); return false;">
                        <i class="ico ico-heart"></i>
                    </a>
                </div>
                {/if}

                {if Phpfox::isUser() && $aItem.user_id != Phpfox::getUserId() && isset($aItem.canAddToCart) && $aItem.canAddToCart}
                <div title="{_p var='ynsocialstore.add_to_cart'}" class="ynstore-btn ynstore-addtocart-btn" onclick="ynsocialstore.addToCart(this,{$aItem.product_id},'{$aItem.product_type}',true); return false;" data-addtocartid="{$aItem.product_id}">
                    <i class="ico ico-cart-plus"></i>
                </div>
                {/if}

                <div title="{_p var='ynsocialstore.add_to_compare'}" class="ynstore-btn ynstore-compare-btn" onclick="ynsocialstore.addToCompare({$aItem.product_id},'product');return false;" data-compareproductid="{$aItem.product_id}">
                    <i class="ico ico-copy"></i>
                </div>
            </div>
        </div>

        <div class="ynstore-info">
            <div class="ynstore-info-detail">
                <div class="ynstore-product-limit-dynamic">
                    <a title="{$aItem.product_name}" href="{permalink module='ynsocialstore.product' id=$aItem.product_id title=$aItem.product_name}" class="ynstore-title">
                        {$aItem.product_name|clean}
                    </a>
                    <div class="ynstore-product-from">
                        <span>{_p var='ynsocialstore.store'}:</span>
                        <a title="{$aItem.store_name|clean}" href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.store_name}">{$aItem.store_name|clean}</a>
                    </div>

                    <div class="ynstore-product-description item_view_content" style="display: none;">
                        {if !empty($aItem.short_description)}
                            {$aItem.short_description|striptag|stripbb}
                        {/if}
                    </div>
                </div>

                <div class="ynstore-product-block4list">
                    <div class="ynstore-product-pullleft">
                        <div class="ynstore-price">
                            {if isset($aItem.has_attribute) && $aItem.has_attribute}<span>{_p var='from_l'}</span> {/if}
                            {if $aItem.discount_percentage != 0 && $aItem.discount_price != 0  && ($aItem.discount_timeless || ($aItem.discount_start_date <= PHPFOX_TIME && $aItem.discount_end_date >= PHPFOX_TIME))}
                                {if isset($aItem.creating_item_currency)}{$aItem.discount_display|ynsocialstore_format_price:$aItem.creating_item_currency}{/if}{if isset($aItem.product_type) && $aItem.product_type =='physical' && !empty($aItem.uom_title)}<span> /{$aItem.uom_title|convert}</span>{/if}
                            {else}
                                {if isset($aItem.creating_item_currency)}{$aItem.product_price|ynsocialstore_format_price:$aItem.creating_item_currency}{/if}{if isset($aItem.product_type) && $aItem.product_type =='physical' && !empty($aItem.uom_title)}<span> /{$aItem.uom_title|convert}</span>{/if}
                            {/if}
                        </div>
                        
                        <div class="ynstore-price-discount-block">
                            {if $aItem.discount_percentage != 0 && $aItem.discount_price != 0 && ($aItem.discount_timeless || ($aItem.discount_start_date <= PHPFOX_TIME && $aItem.discount_end_date >= PHPFOX_TIME))}
                            <span class="ynstore-price-old">{if isset($aItem.creating_item_currency)}{$aItem.product_price|ynsocialstore_format_price:$aItem.creating_item_currency}{/if}</span>
                            &nbsp;
                            <span class="ynstore-discount">-{$aItem.discount_percentage}%</span>
                            {/if}
                        </div>
                    </div>

                    <div class="ynstore-rating-count-block">
                        {if isset($aItem.rating)}
                        <span class="ynstore-rating yn-rating yn-rating-small">
                            {for $i = 0; $i < 5; $i++}
                                {if $i < (int)$aItem.rating}
                                    <i class="ico ico-star" aria-hidden="true"></i>
                                {elseif (($aItem.rating - round($aItem.rating)) > 0) && ($aItem.rating - $i) > 0}
                                    <i class="ico ico-star-half-o" aria-hidden="true"></i>
                                {else}
                                    <i class="ico ico-star yn-rating-disable" aria-hidden="true"></i>
                                {/if}
                            {/for}
                        </span>
                        {/if}
                        
                        <span class="ynstore-count-statistic" style="display: none">
                            {if isset($aItem.total_purchased)}
                                {$aItem.total_purchased}&nbsp;{if $aItem.total_purchased == 1}{_p var='ynsocialstore.order'}{else}{_p var='ynsocialstore.orders'}{/if}
                            {elseif isset($aItem.total_orders)}
                                {$aItem.total_orders}&nbsp;{if $aItem.total_orders == 1}{_p var='ynsocialstore.order'}{else}{_p var='ynsocialstore.orders'}{/if}
                            {/if}
                        </span>
                    </div>
                </div>

         

                {if Phpfox::isUser() && $aItem.user_id != Phpfox::getUserId() && isset($aItem.canAddToCart) && $aItem.canAddToCart}
                <div title="{_p('Add to cart')}" class="ynstore-btn ynstore-addtocart-btn" onclick="ynsocialstore.addToCart(this,{$aItem.product_id},'{$aItem.product_type}',true); return false;" data-addtocartid="{$aItem.product_id}" style="display:none">
                    <i class="ico ico-cart-plus"></i>
                    {_p('add_to_cart')}
                </div>
                {/if}
            </div>

            <!--   This is new section. Just so when click on menu What did friend buy     -->
            {if isset($sView) && $sView == 'friendbuy'}
                {if !empty($aItem.friends_list) && !empty($aItem.friends_list.top_friends)}
                <ul class="ynstore-friendbuy-items">
                    {foreach from = $aItem.friends_list.top_friends item = aFriend}
                    <li class="ynstore-friendbuy-item">
                        {img user=$aFriend suffix='_50_square'}
                    </li>
                    {/foreach}

                    {if $aItem.friends_list.iMore > 0} <a href="javascript:void(0);"  onclick="$Core.box('ynsocialstore.getUsers', 500, 'iProductId={$aItem.product_id}&sType=friend-bought-this'); return false;">+{$aItem.friends_list.iMore}</a> {/if}
                </ul>
                {/if}
            {/if}
        </div>

        <div class="ynstore-featured ynstore-featured-4list" style="display: none;">
            <div title="{_p var='ynsocialstore.featured'}" class="ynstore-featured-triangle ynstore_entry_feature_icon-{$aItem.product_id}" style="{if (!$aItem.is_featured)}visibility: hidden{/if}">
                <i class="ico ico-diamond"></i>
            </div>
        </div>

        {if isset($sTypeBlock) && $sTypeBlock == 'most-liked' && Phpfox::isUser()}
        <div class="ynstore-content-bottom-block">
            <span class="ynstore-count" id="ynstore_count_like_{$aItem.product_id}">
                {$aItem.total_like}&nbsp;{if $aItem.total_like == 1}{_p var='ynsocialstore.like'}{else}{_p var='ynsocialstore.likes'}{/if}
            </span>

            {if Phpfox::isUser() && !$aItem.is_liked}
            <div class="ynstore-like-btn" id="ynstore_product_like_{$aItem.product_id}">
                <a href="javascript:void(0)" onclick="$.ajaxCall('ynsocialstore.addLike', 'type_id=ynsocialstore_product&amp;item_id={$aItem.product_id}'); return false;">
                    <i class="ico ico-thumbup-o"></i>
                    {_p var='ynsocialstore.like'}
                </a>
            </div>
            {else Phpfox::isUser()}
            <div class="ynstore-like-btn" id="ynstore_product_like_{$aItem.product_id}">
                <a href="javascript:void(0)" onclick="$.ajaxCall('ynsocialstore.deleteLike', 'type_id=ynsocialstore_product&amp;item_id={$aItem.product_id}'); return false;">
                    <i class="ico ico-thumbup"></i>
                    {_p var='ynsocialstore.liked'}
                </a>
            </div>
            {/if}
        </div>
        {/if}

        {if isset($sTypeBlock) && $sTypeBlock == 'bought-by-friends'}
            <div class="ynstore-content-bottom-block ynstore-bought-by-friends">
                {_p('Bought by')} {$aItem.friends_list.first_friend.0|user}{if $aItem.friends_list.iMore > 0} {_p('and')} <a href="javascript:void(0);"  onclick="$Core.box('ynsocialstore.getUsers', 500, 'iProductId={$aItem.product_id}&sType=friend-bought-this'); return false;">+{$aItem.friends_list.iMore}</a> {/if}
            </div>
        {/if}

        {if isset($aItem.permission)}
        <div class="ynstore-actions-block">
            <div class="ynstore-cms">
                {template file='ynsocialstore.block.product.link' aItem=$aItem}
            </div>
        </div>
        {/if}
    </div>
</li>
