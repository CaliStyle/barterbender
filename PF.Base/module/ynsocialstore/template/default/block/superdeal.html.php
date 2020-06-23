<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/17/16
 * Time: 10:52 AM
 */
?>

<div class="ynstore-product-superdeal {if !empty($bInDetail)}ynstore-store-detail-page{else}ynstore-product-most-block{/if}">
    {if !empty($bInDetail)}
        <a class="ynstore-view-all" href="{permalink module='ynsocialstore.store' id=$iStoreId title=$sStoreName}products/sort_super-deal">
            {_p var='ynsocialstore.view_all_deals'} <i class="ico ico-angle-right"></i>
        </a>
    {/if}

    <ul class="ynstore-product-most-items style-{$iCount}-items">
        {foreach from=$aItems item=aItem}
        <li class="ynstore-item" data-product-id="{$aItem.product_id}" id="js_product_id_{$aItem.product_id}">
            <div class="ynstore-item-content">
                {if !empty($bInDetail)}
                <div class="ynstore-actions-block">
                    <div class="ynstore-cms">
                        {template file='ynsocialstore.block.product.link' aItem=$aItem}
                    </div>
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

                    {if ($aItem.is_featured)}
                    <div class="ynstore-featured">
                        <div title="{_p var='ynsocialstore.featured'}" class="ynstore-featured-triangle ynstore_entry_feature_icon-{$aItem.product_id}" style="{if (!$aItem.is_featured)}visibility: hidden{/if}">
                            <i class="ico ico-diamond"></i>
                        </div>
                    </div>
                    {/if}
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
                        </div>

                        <div class="ynstore-discount-price" style="display:none">
                            <span class="ynstore-discount">{$aItem.discount_percentage}% <b>{_p('off')}</b></span>
                            <div class="ynstore-price">
                                {if isset($aItem.has_attribute) && $aItem.has_attribute}<span>{_p var='from_l'}</span> {/if}
                                {if isset($aItem.creating_item_currency)}
                                    {$aItem.discount_display|ynsocialstore_format_price:$aItem.creating_item_currency}
                                {/if}
                                {if isset($aItem.product_type) && $aItem.product_type =='physical' && !empty($aItem.uom_title|convert)}
                                    <span> /{$aItem.uom_title|convert}</span>
                                {/if}
                            </div>
                        </div>

                        <div class="ynstore-product-block4list">
                            <div class="ynstore-product-pullleft">
                                <div class="ynstore-price">
                                    {if isset($aItem.has_attribute) && $aItem.has_attribute}<span>{_p var='from_l'}</span> {/if}
                                    {if isset($aItem.creating_item_currency)}
                                        {$aItem.discount_display|ynsocialstore_format_price:$aItem.creating_item_currency}
                                    {/if}
                                    {if isset($aItem.product_type) && $aItem.product_type =='physical' && !empty($aItem.uom_title)}<span> /{$aItem.uom_title|convert}</span>
                                    {/if}
                                </div>

                                {if $aItem.discount_price != 0 && $aItem.discount_percentage != 0}
                                <div class="ynstore-price-discount-block">
                                    <span class="ynstore-price-old">
                                        {if isset($aItem.creating_item_currency)}
                                            {$aItem.product_price|ynsocialstore_format_price:$aItem.creating_item_currency}
                                        {/if}
                                    </span>
                                    &nbsp;
                                    <span class="ynstore-discount">-{$aItem.discount_percentage}%</span>
                                </div>
                                {/if}
                            </div>
                        </div>

                        <span class="ynstore-count-statistic">
                            {if isset($aItem.total_orders)}
                                <span>{$aItem.total_orders}</span>&nbsp;{if $aItem.total_orders == 1}{_p var='ynsocialstore.order'}{else}{_p var='ynsocialstore.orders'}{/if}
                            {/if}
                        </span>

                        {if (empty($bInDetail))}
                        <div class="ynstore-addtocart-compare-block">
                            {if Phpfox::isUser() && $aItem.user_id != Phpfox::getUserId() && isset($aItem.canAddToCart) && $aItem.canAddToCart}
                            <div title="{_p var='ynsocialstore.add_to_cart'}" class="ynstore-btn ynstore-addtocart-btn" onclick="ynsocialstore.addToCart(this,{$aItem.product_id},'{$aItem.product_type}',true); return false;" data-addtocartid="{$aItem.product_id}">
                                <i class="ico ico-cart-plus"></i>
                            </div>
                            {/if}

                            <div title="{_p var='ynsocialstore.add_to_compare'}" class="ynstore-btn ynstore-compare-btn" onclick="ynsocialstore.addToCompare({$aItem.product_id},'product');return false;" data-compareproductid="{$aItem.product_id}">
                                <i class="ico ico-copy"></i>
                            </div>
                        </div>
                        {/if}
                    </div>
                </div>
            </div>
        </li>
        {/foreach}
    </ul>
</div>
