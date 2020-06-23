<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/28/16
 * Time: 10:26
 */
?>

<div class="ynstore-product-featured-block">
    <input type="hidden" id="ynsocialstore_corepath" value="{param var='core.path_file'}">
    <ul class="ynstore-featured-items owl-carousel" id="ynstore-products-featured-block" style="display: none">
        {foreach from=$aItems name=store item=aItem}
        <li class="item ynstore-featured-item">
            <div class="ynstore-product-bg-block">
                <a href="{permalink module='ynsocialstore.product' id=$aItem.product_id title=$aItem.product_name}" class="ynstore-product-bg" style="background-image: url({if $aItem.logo_path}{img server_id=$aItem.server_id path='core.url_pic' file=$aItem.logo_path suffix='_1024' return_url='true'}{else}{param var='core.path'}module/ynsocialstore/static/image/product_default.jpg{/if});"></a>
                    <a class="ynstore-title" title="{$aItem.product_name|clean}" href="{permalink module='ynsocialstore.product' id=$aItem.product_id title=$aItem.product_name}">
                        <b>{$aItem.product_name|clean}</b>
                    </a>
            </div>

            <div class="ynstore-info">
                <div class="ynstore-info-top">
                   <div class="ynstore-storename-location">
                      <div class="ynstore-product-from">
                        {_p var='ynsocialstore.store'}:
                        <a title="{$aItem.store_name|clean}" href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.store_name}">
                           {$aItem.store_name|clean}
                        </a>
                     </div>
                     {if isset($aItem.location.address)}
                     <div class="ynstore-location">
                        {_p var='ynsocialstore.location'}:
                        <a href="//maps.google.com/maps?daddr={$aItem.location.latitude},{$aItem.location.longitude}" target="_blank">
                           {$aItem.location.address}
                        </a>
                     </div>
                     {/if}
                   </div>

                    <div class="ynstore-rating-review">
                        <span class="ynstore-rating yn-rating">
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

                        {if $aItem.total_review > 0}
                        <a class="ynstore-review-count" title="{_p var='ynsocialstore.reviews'}" href="{permalink module='ynsocialstore.product' id=$aItem.product_id title=$aItem.product_name}tab_reviews">
                            (<span>{$aItem.total_review} </span>
                            {if $aItem.total_review == 1 }
                                {_p var='ynsocialstore.review_product'}
                            {else}
                                {_p var='ynsocialstore.reviews_product'}
                            {/if})
                        </a>
                        {/if}
                    </div>
                </div>

                <div class="ynstore-info-bottom">
                    {if $aItem.discount_percentage && ($aItem.discount_timeless || ($aItem.discount_start_date <= PHPFOX_TIME && $aItem.discount_end_date >= PHPFOX_TIME))}
                    <div class="ynstore-discount-block">
                        <span class="ynstore-discount">
                            {$aItem.discount_percentage}%
                            <b>{_p var='ynsocialstore.off'}</b>
                        </span>
                    </div>
                    {/if}
                    <div class="ynstore-meta-block">
                        <div class="ynstore-price-block">
                            {if $aItem.discount_percentage && ($aItem.discount_timeless || ($aItem.discount_start_date <= PHPFOX_TIME && $aItem.discount_end_date >= PHPFOX_TIME))}
                            <span class="ynstore-price">
                               {if isset($aItem.has_attribute) && $aItem.has_attribute}<span>{_p var='from_l'}</span> {/if}
                                {$aItem.discount_display|ynsocialstore_format_price:$aItem.creating_item_currency}
                                {if isset($aItem.product_type) && $aItem.product_type =='physical' && !empty($aItem.uom_title)} <span>/ {$aItem.uom_title|convert}</span>{/if}
                            </span>
                            <span class="ynstore-price-old">{$aItem.product_price|ynsocialstore_format_price:$aItem.creating_item_currency}</span>
                            {else}
                            <span class="ynstore-price">
                                {$aItem.product_price|ynsocialstore_format_price:$aItem.creating_item_currency}
                                    {if isset($aItem.product_type) && $aItem.product_type =='physical' && !empty($aItem.uom_title)} <span>/ {$aItem.uom_title|convert}</span>
                                {/if}
                            </span>
                            {/if}
                        </div>

                        <div class="ynstore-count-block">
                            <div class="ynstore-order">
                                <b>{$aItem.total_orders}</b>
                                {if $aItem.total_orders == 1 }
                                    {_p var='ynsocialstore.order'}
                                {else}
                                    {_p var='ynsocialstore.orders'}
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        {/foreach}
    </ul>
</div>
