<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 11/3/16
 * Time: 14:08
 */

?>

<div class="ynstore-product-feed-items">
    {if isset($aParentFeed) && $aParentFeed.type_id == 'ynsocialstore_product'}
    <div class="ynstore-item-content">
        <div class="ynstore-bg-block">

            <a class="ynstore-bg" href="{permalink module='ynsocialstore.product' id=$aParentFeed.item_id title=$aParentFeed.feed_title}" style="
                 background-image: url(
                    {if $aParentFeed.logo_path}
                        {img server_id=$aParentFeed.server_id path='core.url_pic' file=$aParentFeed.logo_path suffix='_400' return_url='true'}
                    {else}
                        {param var='core.path_actual'}PF.Base/module/ynsocialstore/static/image/product_default.jpg
                    {/if})
                 "></a>

        </div>

        <div class="ynstore-product-info">

            <a class="ynstore-title" href="{permalink module='ynsocialstore.product' id=$aParentFeed.item_id title=$aParentFeed.feed_title}"> 
                {$aParentFeed.feed_title|clean}
            </a>
            
            <div class="ynstore-category">
                <span>{_p var='ynsocialstore.categories'}:</span> {$aParentFeed.category_name}
            </div>

            <div>
                <span class="ynstore-price">
                    {if isset($aParentFeed.currency_symbol)}
                    {$aParentFeed.currency_symbol}{/if}{$aParentFeed.discount_display|number_format:2}{if isset($aParentFeed.product_type) && $aParentFeed.product_type =='physical' && !empty($aParentFeed.uom_title)}<span> /{$aParentFeed.uom_title|convert}</span>
                    {/if}
                </span>

                {if $aParentFeed.discount_percentage != 0 && $aParentFeed.discount_price != 0}
                <span class="ynstore-price-discount-block">
                    <span class="ynstore-price-old">{if isset($aParentFeed.currency_symbol)} {$aParentFeed.currency_symbol}{/if}{$aParentFeed.product_price}</span>
                    <span class="ynstore-discount">{$aParentFeed.discount_percentage}%{_p var='ynsocialstore.off'}</span>
                </span>
                {/if}
            </div>

            <div class="ynstore-description item_view_content">
                {$aParentFeed.feed_content|clean}
            </div>
        </div>
    </div>
    {else}

    <div class="ynstore-item-content">
        <div class="ynstore-bg-block">

            <a class="ynstore-bg" href="{permalink module='ynsocialstore.product' id=$aFeed.item_id title=$aFeed.feed_title}" style="
                 background-image: url(
                    {if $aFeed.logo_path}
                        {img server_id=$aFeed.server_id path='core.url_pic' file=$aFeed.logo_path suffix='_400' return_url='true'}
                    {else}
                        {param var='core.path_actual'}PF.Base/module/ynsocialstore/static/image/product_default.jpg
                    {/if})
            "></a>
        </div>
        
        <div class="ynstore-product-info"> 
            <a class="ynstore-title" href="{permalink module='ynsocialstore.product' id=$aFeed.item_id title=$aFeed.feed_title}">{$aFeed.feed_title|clean}</a>

            <div class="ynstore-category">
                <span>{_p var='ynsocialstore.categories'}:</span> {$aFeed.category_name}
            </div>

            <div>
                <span class="ynstore-price">
                    {if isset($aFeed.currency_symbol)}
                    {$aFeed.currency_symbol}{/if}{$aFeed.discount_display|number_format:2}{if isset($aFeed.product_type) && $aFeed.product_type =='physical' && !empty($aFeed.uom_title)}<span> / {$aFeed.uom_title|convert}</span>
                    {/if}
                </span>

                {if $aFeed.discount_percentage != 0 && $aFeed.discount_price != 0}
                <span class="ynstore-price-discount-block">
                    <span class="ynstore-price-old">{if isset($aFeed.currency_symbol)} {$aFeed.currency_symbol}{/if}{$aFeed.product_price}</span>
                    <span class="ynstore-discount">{$aFeed.discount_percentage}%{_p var='ynsocialstore.off'}</span>
                </span>
                {/if}
            </div>

            <div class="ynstore-description item_view_content">
                {$aFeed.feed_content|clean}
            </div>
        </div>
    </div>
    {/if}
    {unset var=$aParentFeed}
</div>
