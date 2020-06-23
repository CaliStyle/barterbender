<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/27/16
 * Time: 5:11 PM
 */
?>

<div class="ynstore-store-featured-product-block">
    <input type="hidden" id="ynsocialstore_corepath" value="{param var='core.path_file'}">
    <ul class="ynstore-featured-items" id="ynstore-store-featured-products-block" style="display:none" class="owl-carousel">
        {foreach from=$aItems name=product item=aItem}
        <li class="item ynstore-featured-item">
            <a href="{permalink module='ynsocialstore.product' id=$aItem.product_id title=$aItem.product_name}" class="ynstore-product-bg"
                style="background-image: url({if $aItem.logo_path}{img server_id=$aItem.server_id path='core.url_pic' file=$aItem.logo_path suffix='_400' return_url='true'}{else}{param var='core.path'}module/ynsocialstore/static/image/product_default.jpg{/if});">
            </a>

            <div class="ynstore-product-info">
                <div class="ynstore-product-title">
                    <a href="{permalink module='ynsocialstore.product' id=$aItem.product_id title=$aItem.product_name}">{$aItem.product_name}</a>
                </div>

                <div class="ynstore-product-price-block">
                    {if isset($aItem.has_attribute) && $aItem.has_attribute}<span>{_p var='from_l'}</span> {/if}
                    {if $aItem.discount_percentage && ($aItem.discount_timeless || ($aItem.discount_start_date <= PHPFOX_TIME && $aItem.discount_end_date >= PHPFOX_TIME))}
                        <div class="ynstore-product-discount-percentage">
                            {$aItem.discount_percentage}%<b>{_p var='ynsocialstore.off'}</b>
                        </div>

                        <div class="ynstore-product-discount-price">
                            {$aItem.discount_display|ynsocialstore_format_price:$aItem.creating_item_currency}{if !empty($aItem.uom_title)}/{$aItem.uom_title|convert}{/if}
                        </div>
                        <div class="ynstore-product-price">
                            {$aItem.product_price|ynsocialstore_format_price:$aItem.creating_item_currency}
                        </div>
                    {else}
                        <div class="ynstore-product-discount-price">
                            {$aItem.discount_display|ynsocialstore_format_price:$aItem.creating_item_currency}{if !empty($aItem.uom_title)}/{$aItem.uom_title|convert}{/if}
                        </div>
                    {/if}
                </div>

                <div class="ynstore-product-count">
                    <span class="ynstore-rating yn-rating yn-rating-normal">
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
                    <span>|</span>
                    <span class="ynstore-count">
                        {$aItem.total_orders} {if $aItem.total_orders == 1}{_p var='ynsocialstore.order'} {else} {_p var='ynsocialstore.orders'}{/if}
                    </span>
                </div>

                <div class="ynstore-product-decs item-desc item_content item_view_content">
                    {$aItem.short_description|striptag|stripbb}
                </div>
            </div>
        </li>
        {/foreach}
    </ul>
</div>

