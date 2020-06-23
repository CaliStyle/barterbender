<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/19/16
 * Time: 15:41
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="ynstore-compare-holder ynstore-mycart-affix-holder" id="ynstore-my-cart-dashboard">
    <div id="ynstore-my-cart-item-list" class="ynstore-compare-block ynstore-mycart-block-snap {if $boxSize == 'min'}ynstore-hide{/if} {if count($iCount)}yes{/if}">
        <div class="ynstore-title">
            {_p('Your Cart')}{if $iCount > 0}&nbsp;({$iCount}){/if}
        </div>
        {if $iCount > 0}
        <div class="ynstore-compare-store-items">
            <div id="js-ynstore-my-cart-confirm" class="ynstore-confirm-btn-block hide"></div>
            <ul class="ynstore-mycart-items">
                {foreach from=$aCartData key=iKey item=aItem}
                    <li class="js_ynstore_my_cart-item ynstore-mycart-item" id="js_ynstore_my_cart-{$aItem.cartproduct_id}">
                        <div class="ynstore-compare-store-content" style="background-image: url(
                            {if !empty($aItem.logo_path)}
                                {img server_id=$aItem.server_id path='core.url_pic' file=$aItem.logo_path suffix='_400' max_width=60 max_height=60 return_url=true}
                            {else}
                                {param var='core.path_file'}module/ynsocialstore/static/image/product_default.jpg
                            {/if}
                        )">
                            <span title="{_p var='ynsocialstore.delete'}" class="ynstore-btn-delete" data-cartproductid="{$aItem.cartproduct_id}" onclick="return ynsocialstore.confirmdeleteOneCart(this);">
                                <i class="ico ico-close"></i>
                            </span>
                        </div>
                        <div class="ynstore-info">
                           <a class="" href="{permalink module='ynsocialstore.product' id=$aItem.product_id title=$aItem.product_name}">{$aItem.product_name|clean}</a>
                           <span>{_p('store')}: <a title="{$aItem.store_name|clean}" href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.store_name}">{$aItem.store_name|clean}</a></span>
                           {if !empty($aItem.attribute_name) && $aItem.attribute_id}
                                <span>{$aItem.attribute_name}: <a href="javascript:void(0)">{$aItem.title}</a></span>
                           {/if}
                        </div>
                        <div class="ynstore-btns">
                           {if $aItem.product_type == 'physical'}
                           <button class="btn" id="minus_quantity" data-cartproductid="{$aItem.cartproduct_id}" data-type="minus" onclick="return ynsocialstore.updateQuantityMyCartCallout(this);">
                              <i class="ico ico-minus"></i>
                           </button>
                           <input type="text" class='js_ynstore_my_cart-quantity' min="1" step="1" data-cartproductid="{$aItem.cartproduct_id}" value="{$aItem.cartproduct_quantity}">
                           <button class="btn" id="add_quantity" data-cartproductid="{$aItem.cartproduct_id}" data-type="add" onclick="return ynsocialstore.updateQuantityMyCartCallout(this);">
                              <i class="ico ico-plus"></i>
                           </button>
                           {else}
                           <input class="ynstore-fullwidth js_ynstore_my_cart-quantity" readonly type="text" step="1" value="{$aItem.cartproduct_quantity}">&nbsp;{if $aItem.product_type != 'digital' && !empty($aItem.uom_title)}{$aItem.uom_title|convert}{/if}
                           {/if}
                           <input type="hidden" class="js_ynstore_my_cart-maxquantity" value="{$aItem.real_quantity_can_add}">
                           <input type="hidden" class="js_ynstore_my_cart-price" value="{$aItem.cartproduct_price}">
                           <span>
                               {$aItem.cartproduct_price|ynsocialstore_format_price:$aItem.creating_item_currency}&nbsp;<i>{if $aItem.product_type != 'digital' && !empty($aItem.uom_title)}/ {$aItem.uom_title|convert}{/if}</i></span>
                            <span class="js_ynstore_my_cart_max_noti text-danger hide"></span>
                        </div>
                    </li>
                {/foreach}
            </ul>
            <input type="hidden" id="js_selected_currency" value="{$aItem.creating_item_currency}">
            <div class="ynstore-total-price">
                <span class="ynstore-label">{_p var='ecommerce.total'} :</span>
                <span class="ynstore-price" id='js_ynstore_my_cart-total'></span>
            </div>
        </div>

        <div class="ynstore-compare-actions ynstore-my-cart-actions">
            <a class="btn btn-primary" href="{url link='ynsocialstore.my-cart'}" onclick="">
                {_p('View My Cart')}
            </a>
            <a class="btn ynstore-btn" data-cartid="{$iCartId}" onclick="return ynsocialstore.confirmdeleteAllCart(this);">
                {_p('Remove all item from my cart')}
            </a>
        </div>
        {else}
           <div class="t_center">
              {_p var='ecommerce.there_are_no_items'}
           </div>
        {/if}
    </div>
    <div class="ynstore-icon-compare ynstore-icon-mycart">
        <div class="ynstore-toggle-compare-btn {if $boxSize == 'min'}ynstore-hide{/if}" id="ynstore_my_cart_btn" data-type="{if $boxSize == 'min'}show{else}hide{/if}" onclick="return ynsocialstore.toggleMycartDashBoard(this);">
            {if $iCount > 0}
            <span id="ynstore-total-compare-item">
                <b>{$iCount}</b>
            </span>
            {/if}
            <i class="ynstore-open ico ico-cart"></i>
            <i class="ynstore-close ico ico-close"></i>
        </div>
    </div>
</div>

