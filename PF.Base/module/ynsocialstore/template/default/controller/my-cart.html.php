<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/21/16
 * Time: 7:35 PM
 */
?>
<div id="ynstore_mycart" class="ynstore-mycart-page">
    {if count($aMyCart)}
    <div class="ynstore-count-status">
        <b>{$iCount}</b>
        {if $iCount == 1}
        {_p('item found in')}
        <b>{_p('my cart')}</b>
        {else}
        {_p('items found in')}
        <b>{_p('my cart')}</b>
        {/if}
    </div>

    <input type="hidden" id='ynstore_cartid' value="{$iCartId}">

    <div class="ynstore-mycart-block mt-1">
        <div class="ynstore_cart_section">
            <div class="ynstore_cart_item">
                <div class="ynstore_item_info ynstore_item_info_labels">
                    <div class="ynstore_item_title ynstore-label">{_p var='ynsocialstore.products'}</div>
                    <div class="ynstore_item_type ynstore-label">{_p var='ynsocialstore.attributes'}</div>
                    <div class="ynstore_item_quantity ynstore-label">{_p var='ecommerce.quantity'}</div>
                    <div class="ynstore_item_extra ynstore-label">{_p var='ynsocialstore.item_price'}</div>
                </div>
            </div>
        </div>

        {foreach from=$aMyCart key=KeySeller item=aItemsSeller}
        <div class="ynstore_cart_section">
            {if isset($aItemsSeller.store)}
            <!-- Note: Don't remove this div -->
            <div class="js_ynstore_error error_message hide" id="js_error_message_store_{$aItemsSeller.store.store_id}"></div>

            <div class="ynstore_cart_header">
                {_p var='ynsocialstore.from_s'}:&nbsp;
                <a href="{permalink module='ynsocialstore.store' id=$aItemsSeller.store.store_id title=$aItemsSeller.store.name}">
                    {$aItemsSeller.store.name}
                </a>
                &nbsp;(<?php echo count($this->_aVars['aItemsSeller']['items']) ?> {if count($aItemsSeller.items) == 1}{_p('item')}{else}{_p('items')}{/if})
            </div>
            {/if}

            <div class="ynstore_card_body">
                {foreach from=$aItemsSeller.items item=aItem}
                <div class="ynstore_cart_item" id="js_cart_item_{$aItem.cartproduct_id}" data-currency="{$aItem.creating_item_currency}">

                    <div class="ynstore_item_info">
                        <div class="ynstore_item_title">
                            <div class="item_photo ynstore_item_bg" style="
                                background-image: url({if !empty($aItem.logo_path)}
                                    {img server_id=$aItem.product_server_id path='core.url_pic' file=$aItem.logo_path return_url='true' suffix='_400'}
                                {else}
                                    {$sCorePath}module/ynsocialstore/static/image/product_default.jpg
                                {/if})">
                            </div>

                            <a href="{permalink module='ynsocialstore.product' id=$aItem.product_id title=$aItem.name}">{$aItem.name|clean}</a>
                        </div>

                        <div class="ynstore_item_type {if $aItem.attribute_style == 2}ynstore-attr-style-2{elseif $aItem.attribute_style == 3}ynstore-attr-style-3{/if}">
                            {if $aItem.product_type == 'physical' && count($aItem.element_list)}
                            <span>{$aItem.attribute_name|clean}:</span>

                            <select name="attribute_{$aItem.attribute_id}" id="js_attribute_on_cart-{$aItem.cartproduct_id}" class="js_attribute_on_cart" style="width:100px;" onchange="ynsocialstore.changeAttributeInMyCart(this);">
                                {if $aItem.attribute_style == 1}
                                {foreach from=$aItem.element_list key=iKeyAttribute item=aAttr}
                                <option
                                    {if $aAttr.real_quantity_can_add === 0}disabled{/if} value="{$aAttr.attribute_id}" data-price="{$aAttr.price}" data-attrid="{$aAttr.attribute_id}" data-cartid="{$aItem.cartproduct_id}" data-remain="{$aAttr.real_quantity_can_add}" {if $aAttr.attribute_id == $aItem.cartproduct_attribute_id}selected{/if}>{$aAttr.title|clean}
                                </option>
                                {/foreach}
                                {elseif $aItem.attribute_style == 2}
                                    {foreach from=$aItem.element_list key=iKeyAttribute item=aAttr}
                                        {if !empty($aAttr.image_path)}
                                            <option {if $aAttr.real_quantity_can_add === 0}disabled{/if} value="{$aAttr.attribute_id}" data-price="{$aAttr.price}" data-attrid="{$aAttr.attribute_id}" data-cartid="{$aItem.cartproduct_id}" data-remain="{$aAttr.real_quantity_can_add}" data-image="{img server_id=$aAttr.server_id path='core.url_pic' file=$aAttr.image_path suffix='_90_square' return_url=true}" {if $aAttr.attribute_id == $aItem.cartproduct_attribute_id}selected{/if}>
                                        {elseif !empty($aAttr.color)}
                                            <option {if $aAttr.real_quantity_can_add === 0}disabled{/if} value="{$aAttr.attribute_id}" data-price="{$aAttr.price}" data-attrid="{$aAttr.attribute_id}" data-cartid="{$aItem.cartproduct_id}" data-remain="{$aAttr.real_quantity_can_add}" data-imagecss="background-color:{$aAttr.color};width:50px;height: 50px;" {if $aAttr.attribute_id == $aItem.cartproduct_attribute_id}selected{/if}>
                                        {else}
                                            <option {if $aAttr.real_quantity_can_add === 0}disabled{/if} value="{$aAttr.attribute_id}" data-price="{$aAttr.price}" data-attrid="{$aAttr.attribute_id}" data-cartid="{$aItem.cartproduct_id}" data-remain="{$aAttr.real_quantity_can_add}" data-image="{$sCorePath}module/ynsocialstore/static/image/no-compare-product.jpg" {if $aAttr.attribute_id == $aItem.cartproduct_attribute_id}selected{/if}>
                                        {/if}
                                    {/foreach}
                                {elseif $aItem.attribute_style == 3}
                                    {foreach from=$aItem.element_list key=iKeyAttribute item=aAttr}
                                        {if !empty($aAttr.image_path)}
                                            <option {if $aAttr.real_quantity_can_add === 0}disabled{/if} value="{$aAttr.attribute_id}" data-description="{$aAttr.title|clean}" data-cartid="{$aItem.cartproduct_id}" data-attrid="{$aAttr.attribute_id}" data-price="{$aAttr.price}" data-remain="{$aAttr.real_quantity_can_add}" data-image="{img server_id=$aAttr.server_id path='core.url_pic' file=$aAttr.image_path suffix='_90_square' return_url=true}" {if $aAttr.attribute_id == $aItem.cartproduct_attribute_id}selected{/if}>
                                        {elseif !empty($aAttr.color)}
                                            <option {if $aAttr.real_quantity_can_add === 0}disabled{/if} value="{$aAttr.attribute_id}" data-description="{$aAttr.title|clean}" data-cartid="{$aItem.cartproduct_id}" data-attrid="{$aAttr.attribute_id}" data-price="{$aAttr.price}" data-remain="{$aAttr.real_quantity_can_add}" data-imagecss="background-color:{$aAttr.color};width:50px;height: 50px;" {if $aAttr.attribute_id == $aItem.cartproduct_attribute_id}selected{/if}>
                                        {else}
                                            <option {if $aAttr.real_quantity_can_add === 0}disabled{/if} value="{$aAttr.attribute_id}" data-description="{$aAttr.title|clean}" data-cartid="{$aItem.cartproduct_id}"  data-price="{$aAttr.price}" data-remain="{$aAttr.real_quantity_can_add}" data-image="{$sCorePath}module/ynsocialstore/static/image/no-compare-product.jpg" {if $aAttr.attribute_id == $aItem.cartproduct_attribute_id}selected{/if}>
                                        {/if}
                                    {/foreach}
                                {/if}
                            </select>

                            {/if}
                        </div>

                        <div class="ynstore_item_quantity" id="js_cart_product_id-{$aItem.cartproduct_id}">
                            <div class="ynstore-quantity-click" data-symbol="{$aItem.sSymbolCurrency}">
                            {if $aItem.cartproduct_type == 'buy'}
                                {if $aItem.product_type == 'physical'}
                                    <button class="btn btn-default" id="minus_quantity" data-cartpid="{$aItem.cartproduct_product_id}" data-type="minus" onclick="return ynsocialstore.updateQuantityInMyCart(this);"><i class="ico ico-minus"></i></button>

                                    <input type="text" class='mycart_quantity_product mycart_seller_{$aItemsSeller.store.store_id}' min="1" max="{$aItem.product_quantity}" step="1" value="{$aItem.cartproduct_quantity}" >

                                    <button class="btn btn-default" id="add_quantity" data-cartpid="{$aItem.cartproduct_product_id}" data-type="add" onclick="return ynsocialstore.updateQuantityInMyCart(this);"><i class="ico ico-plus"></i></button>

                                {else}
                                    <input readonly type="text" class='mycart_quantity_product mycart_seller_{$aItemsSeller.store.store_id}' min="1" max="{$aItem.product_quantity}" step="1" value="{$aItem.cartproduct_quantity}" >
                                {/if}

                                <input type="hidden" class='mycart_price_product' value="{$aItem.cartproduct_price}">
                                <input type="hidden" class='mycart_productid' value="{$aItem.cartproduct_product_id}">
                                <input type="hidden" class='mycart_attributeid' value="{$aItem.cartproduct_attribute_id}">
                                <input type="hidden" class='mycart_cartproductid' value="{$aItem.cartproduct_id}">
                                <input type="hidden" class='mycart_maxquantity' value="{$aItem.real_quantity_can_add}">
                                <input type="hidden" class='mycart_seller' value="{$aItemsSeller.store.store_id}">
                            {/if}
                            </div>
                            {if $aItem.product_type != 'digital' && !empty($aItem.uom_title)}
                                <span>{$aItem.uom_title|convert}</span>
                            {/if}
                            <span class="mycart_max_quantity_noti text-danger hide"></span>
                        </div>

                        <div class="ynstore_item_extra">
                            <div class="ynstore_item_price item_price_{$aItem.cartproduct_id}">{$aItem.cartproduct_price|ynsocialstore_format_price:$aItem.creating_item_currency}</div>
                            <div class="ynstore_cart_remove">
                                <span onclick="return ynsocialstore.tempRemoveCart(this,{$aItem.cartproduct_id},{$aItemsSeller.store.store_id});">
                                    <i class="ico ico-close-circle"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                {/foreach}

                <div class="ynstore_item_footer">
                    <div class="ynstore_buy_all_this">
                        <a class="btn btn-sm btn-default" href="javascript:void(0)" onclick="return ynsocialstore.checkMinOrder(this,{$aItemsSeller.store.store_id},'mycart');">
                            <i class="ico ico-cart"></i>
                            {_p var='ynsocialstore.buy_all_from_this_store'}
                        </a>
                    </div>
                    <div class="ynstore_sum_label">{_p var='ynsocialstore.sub_total'}{if isset($aItemsSeller.store.tax) && $aItemsSeller.store.tax} ({_p var='ynsocialstore.include_tax_tax' tax=$aItemsSeller.store.tax}){/if}:</div>
                    <input type="hidden" value="{$aItem.creating_item_currency}" id="js_selected_currency_seller_{$aItemsSeller.store.store_id}">
                    <div class="ynstore_item_total2">
                        <span class="ynstore_price" id='mycart_total_seller_{$aItemsSeller.store.store_id}'></span>
                    </div>
                </div>
            </div>
        </div>
        {/foreach}
        <input type="hidden" id="js_selected_currency" value="{$aItem.creating_item_currency}">
        <div class="ynstore_card_end">
           <div class="ynstore_sum_label">{_p var='ecommerce.total'} :</div>
            <div class="ynstore_item_total">
                <span class="ynstore_price" id="mycart_total"></span>
            </div>
        </div>
    </div>

    <div class="ynstore-btns">
        <span id="ynstore_mycart_loading" style="display:none;font-size: 20px;position: relative;top: 3px;" class="t_center"><i class="fa fa-spin fa-circle-o-notch"></i></span>
        <a class="btn btn-default" href="{url link=$sModule}">
            {_p var='ecommerce.continue_shopping'}
        </a>
        <a class="btn btn-success" href="javascript:void(0)" onclick=" return ynsocialstore.updateMyCartData(0);">
            <i class="ico ico-refresh-o"></i>
            {_p var='ynsocialstore.update_cart'}
        </a>
        <a class="btn btn-primary" id="ynstore_mycart_buy_all" href="javascript:void(0)" onclick="return ynsocialstore.checkMinOrder(this,0,'mycart');">
            <i class="ico ico-cart"></i>
            {_p var='ecommerce.buy_all'}
        </a>
    </div>

    {else}
        {_p var='ecommerce.there_are_no_items'}
    {/if}

    {literal}
    <script type="text/javascript">
        $Behavior.ynStoreUpdateQuantity = function()
        {
            $(".js_attribute_on_cart").msDropdown();
            ynsocialstore.myCartLimit.rawLimit = {/literal}{$aMaxElement}{literal};
            ynsocialstore.myCartLimit.realLimit = {/literal}{$aMaxElement}{literal};
            ynsocialstore.initMyCartUpdateQuantity();
        }
    </script>
    {/literal}
</div>
