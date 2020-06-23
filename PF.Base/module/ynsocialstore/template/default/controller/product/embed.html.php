<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 11/2/16
 * Time: 4:10 PM
 */
?>
{foreach from=$aFiles item=file}
{if !empty($file)}
    <script type="text/javascript" src="{$file}"></script>
{/if}
{/foreach}

<link rel="stylesheet" type="text/css" href="{$sCorePath}module/ynsocialstore/static/css/default/default/embed.css" />
<link rel="stylesheet" type="text/css" href="{$sCorePath}theme/frontend/default/style/default/css/font-awesome/css/font-awesome.min.css" />
<!--  -->

<div class="ynstore-product-detail-block ynstore-product-embed">
    <div class="ynstore-title">
        <span style="{if empty($aProduct.is_featured)} display: none; {/if}" title="{_p('Featured Product')}" class="ynstore-featured" id="ynstore_product_detail_feature">
            <i class="ico ico-diamond"></i>
            {_p('Feature')}
        </span>
        {$aProduct.name|clean}
    </div>



    <div class="ynstore-timestamp-from">
        <span class="ynstore-timestamp">
            {$aProduct.product_creation_datetime|date:'core.global_update_time'}
        </span>

        <span class="ynstore-from">
            {_p var='ynsocialstore.from'}
            <a href="{permalink module='ynsocialstore.store' id=$aProduct.store_id title=$aProduct.store_name}">{$aProduct.store_name}</a>
        </span>
    </div>

    <div class="ynstore-location">
        {if !empty($aProduct.location.address)}
            {_p var='ynsocialstore.at'} {$aProduct.location.address}
        {/if}
    </div>
    
    <div class="ynstore-img">
        {if $aProduct.logo_path}
            <img src="{img server_id=$aProduct.server_id path='core.url_pic' file=$aProduct.logo_path suffix='_400' return_url='true'}" alt="">
        {else}
            <img src="{param var='core.path_actual'}PF.Base/module/ynsocialstore/static/image/product_default.jpg" alt="">
        {/if}
    </div>

    <div class="ynstore-info">
        <div class="ynstore-product-price-block">
            {if $aProduct.discount_percentage != 0 && $aProduct.discount_price != 0 && ($aProduct.discount_timeless || ($aProduct.discount_start_date <= PHPFOX_TIME && $aProduct.discount_end_date >= PHPFOX_TIME))}
            <div class="ynstore-discount-block" id="js_product_discount_percentage">
                <span class="ynstore-discount">
                    {$aProduct.discount_percentage}%
                    <b>{_p var='ynsocialstore.off'}</b>
                </span>
            </div>
            {/if}

            <div class="ynstore-meta-block">
                <div class="ynstore-price-block">
                    <span class="ynstore-price">
                        <b id="js_product_discount_price">{if isset($aProduct.currency_symbol)}{$aProduct.currency_symbol}{/if}{$aProduct.discount_display|number_format:2}</b>
                        {if isset($aProduct.product_type) && $aProduct.product_type =='physical'} 
                        <span>{if isset($aProduct.product_type) && $aProduct.product_type =='physical' && !empty($aProduct.uom_title)} <i>/</i> {$aProduct.uom_title|convert}{/if}</span>
                        {/if}
                    </span>
                    {if $aProduct.discount_percentage != 0 && $aProduct.discount_price != 0 && ($aProduct.discount_timeless || ($aProduct.discount_start_date <= PHPFOX_TIME && $aProduct.discount_end_date >= PHPFOX_TIME))}
                    <span class="ynstore-price-old">{if isset($aProduct.currency_symbol)}{$aProduct.currency_symbol}{/if}{$aProduct.product_price}</span>
                    {/if}
                </div>
            </div>
        </div>

        <div class="ynstore-product-statistic">
            <div class="ynstore-statistic-item">
                {if isset($aProduct.total_orders)}
                    <span>{$aProduct.total_orders}</span> {if $aProduct.total_orders == 1}{_p var='ynsocialstore.order'}{else}{_p var='ynsocialstore.orders'}{/if}
                {/if}
            </div>

            <div class="ynstore-statistic-item">
                {if isset($aProduct.total_like)}
                    <span>{$aProduct.total_like}</span> {if $aProduct.total_like == 1}{_p var='ynsocialstore.like'}{else}{_p var='ynsocialstore.likes'}{/if}
                {/if}
            </div>

            <div class="ynstore-statistic-item">
                {if isset($aProduct.total_comment)}
                    <span>{$aProduct.total_comment}</span> {if $aProduct.total_comment == 1}{_p var='ynsocialstore.comment'}{else}{_p var='ynsocialstore.comments'}{/if}
                {/if}
            </div>
        </div>
    </div>
    
    <div class="ynstore-ratings-reviews-block">
        <div class="ynstore-rating yn-rating yn-rating-normal">
            <span class="rating">{$aProduct.rating}</span>
            {for $i = 0; $i < 5; $i++}
            {if $i < (int)$aProduct.rating}
            <i class="ico ico-star" aria-hidden="true"></i>
            {elseif ((round($aProduct.rating) - $aProduct.rating) > 0) && ($aProduct.rating - $i) > 0}
            <i class="ico ico-star-half-o" aria-hidden="true"></i>
            {else}
            <i class="ico ico-star yn-rating-disable" aria-hidden="true"></i>
            {/if}
            {/for}
        </div>

        <a href="javascript:void(0);" onclick="gotoReviewSection()" class="ynstore-review-count">
            {$aProduct.total_review}&nbsp;{if $aProduct.total_review == 1}{_p var='ynsocialstore.review'}{else}{_p var='ynsocialstore.reviews'}{/if}
        </a>
    </div>
</div>