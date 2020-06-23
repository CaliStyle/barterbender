<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/26/16
 * Time: 9:55 AM
 */
?>
<div class="ynstore-product-detail-block">
    <div class="ynstore-title">
        <span style="{if empty($aItem.is_featured)} display: none; {/if}" title="{_p('Featured Product')}" class="ynstore-featured" id="ynstore_product_detail_feature">
            <i class="ico ico-diamond"></i>
            {_p('Feature')}
        </span>
        {$aItem.name|clean}
    </div>

    <div class="ynstore-timestamp-from">
        <span class="ynstore-timestamp">
            {$aItem.product_creation_datetime|date:'core.global_update_time'}
        </span>

        <span class="ynstore-from">
            {_p var='ynsocialstore.store'}
            <a href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.store_name}">{$aItem.store_name}</a>
        </span>
    </div>

    <div class="ynstore-location">
        {if !empty($aItem.location.address)}
            {_p var='ynsocialstore.at'} {$aItem.location.address}
        {/if}
    </div>

    <div class="ynstore-actions">
        <a href="{permalink module='ynsocialstore.product.print' id=$aItem.product_id'}" class="no_ajax btn ynstore-product-btn" target="_blank">
            <i class="ico ico-printer"></i>
        </a>
        <div class="ynstore-cms">
            {template file='ynsocialstore.block.product.link' aItem=$aItem}
        </div>
    </div>

    <div class="ynstore-info">
        <div class="ynstore-product-price-block">
            {if $aItem.discount_percentage != 0 && $aItem.discount_price != 0  && ($aItem.discount_timeless || ($aItem.discount_start_date <= PHPFOX_TIME && $aItem.discount_end_date >= PHPFOX_TIME))}
            <div class="ynstore-discount-block" id="js_product_discount_percentage">
                <span class="ynstore-discount">
                    {$aItem.discount_percentage}%
                    <b>{_p var='ynsocialstore.off'}</b>
                </span>
            </div>
            {/if}

            <div class="ynstore-meta-block">
                <div class="ynstore-price-block">
                    <span class="ynstore-price">
                        <b id="js_product_discount_price">
                            {if $aItem.discount_percentage != 0 && $aItem.discount_price != 0  && ($aItem.discount_timeless || ($aItem.discount_start_date <= PHPFOX_TIME && $aItem.discount_end_date >= PHPFOX_TIME))}
                                {$aItem.discount_display|ynsocialstore_format_price:$aItem.creating_item_currency}
                            {else}
                                {$aItem.product_price|ynsocialstore_format_price:$aItem.creating_item_currency}
                            {/if}
                        </b>
                        {if isset($aItem.product_type) && $aItem.product_type =='physical'} 
                        <span>{if isset($aItem.product_type) && $aItem.product_type =='physical' && !empty($aItem.uom_title)} <i>/</i> {$aItem.uom_title|convert}{/if}</span>
                        {/if}
                    </span>
                    {if $aItem.discount_percentage != 0 && $aItem.discount_price != 0  && ($aItem.discount_timeless || ($aItem.discount_start_date <= PHPFOX_TIME && $aItem.discount_end_date >= PHPFOX_TIME))}
                    <span class="ynstore-price-old">{$sDefaultSymbol}{$aItem.product_price}</span>
                    <span class="ynstore-price-old hide" id="js_product_main_price">{$aItem.product_price}</span>
                    {/if}
                </div>
            </div>
        </div>

        <div class="ynstore-product-statistic">
            <div class="ynstore-statistic-item">
                {if isset($aItem.total_orders)}
                    <span>{$aItem.total_orders}</span> {if $aItem.total_orders == 1}{_p var='ynsocialstore.order'}{else}{_p var='ynsocialstore.orders'}{/if}
                {/if}
            </div>

            <div class="ynstore-statistic-item">
                {if isset($aItem.total_like)}
                    <span>{$aItem.total_like}</span> {if $aItem.total_like == 1}{_p var='ynsocialstore.like'}{else}{_p var='ynsocialstore.likes'}{/if}
                {/if}
            </div>

            <div class="ynstore-statistic-item">
                {if isset($aItem.total_comment)}
                    <span>{$aItem.total_comment}</span> {if $aItem.total_comment == 1}{_p var='ynsocialstore.comment'}{else}{_p var='ynsocialstore.comments'}{/if}
                {/if}
            </div>
        </div>
    </div>
    
    <div class="ynstore-ratings-btn-block">
        <div class="ynstore-ratings-reviews-block">
            <div class="ynstore-rating yn-rating yn-rating-normal">
                <span class="rating">{$aItem.rating}</span>
                {for $i = 0; $i < 5; $i++}
                {if $i < (int)$aItem.rating}
                <i class="ico ico-star" aria-hidden="true"></i>
                {elseif ((round($aItem.rating) - $aItem.rating) > 0) && ($aItem.rating - $i) > 0}
                <i class="ico ico-star-half-o" aria-hidden="true"></i>
                {else}
                <i class="ico ico-star yn-rating-disable" aria-hidden="true"></i>
                {/if}
                {/for}
            </div>

            <a href="javascript:void(0);" onclick="gotoReviewSection()" class="ynstore-review-count">
                {$aItem.total_review}&nbsp;{if $aItem.total_review == 1}{_p var='ynsocialstore.review'}{else}{_p var='ynsocialstore.reviews'}{/if}
            </a>
        </div>

        <div class="ynstore-product-btns">
            <a title="{_p var='ynsocialstore.add_to_compare'}" class="ynstore-product-btn ynstore-check-compare" data-compareproductid="{$aItem.product_id}" onclick="ynsocialstore.addToCompare({$aItem.product_id},'product');return false;">
                <span><i class="ico ico-copy"></i></span>
                {_p var='ynsocialstore.compare'}
            </a>
        
            {if Phpfox::isUser() && $aItem.user_id != Phpfox::getUserId()}
            <div class="ynstore-product-btn" id="ynstore-detail-wishlist-product-{$aItem.product_id}">
                {if !$aItem.is_wishlist}
                <a title="{_p var='ynsocialstore.wishlist'}" class="ynstore-compare-wishlist" onclick="ynsocialstore.updateWishList({$aItem.product_id},1);return false;">
                    <span><i class="ico ico-heart"></i></span>
                    {_p var='ynsocialstore.wishlist'}
                </a>
                {else}
                <a title="{_p var='ynsocialstore.wishlist'}" class="ynstore-compare-wishlist active" onclick="ynsocialstore.updateWishList({$aItem.product_id},0);return false;">
                    <span><i class="ico ico-heart"></i></span>
                    {_p var='ynsocialstore.wishlist'}
                </a>
                {/if}
            </div>
            {/if}
        </div>
    </div>

    {if $aItem.enable_inventory}
    <div class="ynstore-detail-inventory" id="js_product_remain_quantity">
        {if $aItem.remaining}
            <b class="ynstore-green">{_p var='ynsocialstore.available_in_stock'}</b> . {_p var='ynsocialstore.remain'} <b>{$aItem.remaining}</b>/{$aItem.product_quantity_main}
        {elseif !$aItem.product_quantity_main}
            <b class="ynstore-green">
                {_p var='ynsocialstore.unlimited'}
            </b>
        {/if}
    </div>
    {/if}
</div>
{literal}
<script type="text/javascript">
    function gotoReviewSection() {
        $('#ynstore_product_reviews').trigger('click');
        $('html, body').animate({
            scrollTop: $('.page_section_menu.page_section_menu_header').offset().top - 100
        }, 2000);
    }
</script>
{/literal}