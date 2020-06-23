<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 11/9/16
 * Time: 09:24
 */
?>
{if $aProduct.product_status == 'running'}
    {if $aProduct.product_type != 'digital'}
        {if !$isNoAttribute}
            <!-- NOTE: use class js_selected_attribute ynstore-active to active attribute when click, don't change this -->
            <div id="ynstore_product_detail_attribute">
                <div class="yn-title">{$aAttributeInfo.name|clean}</div>

                {if $aAttributeInfo.type == 1}
                <div id="ynstore_product_detail_attribute-text" class="ynstore-attr-items">
                    {foreach from=$aElements key=iKey item=aElement}
                    <div class="{if $iKey == 0}js_selected_attribute ynstore-active{/if} ynstore-attr-item" id="js_product_attribute-{$iKey}">
                        <a href="" class="ynstore-att-item-link" onclick="return ynsocialstore.selectAttributeInDetail(this);" data-toggle="tooltip"  data-placement="bottom" title="{$aElement.title} ({$sDefaultSymbol}{$aElement.price})" data-elementid="{$aElement.attribute_id}" data-quantity="{$aElement.quantity}" data-remain="{$aElement.remain_of_attribute}" data-realremain="{$aElement.remain}" data-price="{$aElement.price}" id="js_ynstore_product_element-{$aElement.attribute_id}">
                            <span class="btn btn-default">{$aElement.title|clean}</span>
                        </a>
                    </div>
                    {/foreach}
                </div>

                {elseif $aAttributeInfo.type == 2}
                <div id="ynstore_product_detail_attribute-image" class="ynstore-attr-items">
                    {foreach from=$aElements key=iKey item=aElement}
                    <div class="{if $iKey == 0}js_selected_attribute ynstore-active{/if} ynstore-attr-item" id="js_product_attribute-{$iKey}">
                        <a href="" class="ynstore-att-item-link" onclick="return ynsocialstore.selectAttributeInDetail(this);" data-toggle="tooltip" data-placement="bottom" title="{$aElement.title} ({$sDefaultSymbol}{$aElement.price})" data-elementid="{$aElement.attribute_id}" data-quantity="{$aElement.quantity}" data-remain="{$aElement.remain_of_attribute}" data-realremain="{$aElement.remain}" data-price="{$aElement.price}" id="js_ynstore_product_element-{$aElement.attribute_id}">
                            {if !empty($aElement.image_path)}
                                {img server_id=$aElement.server_id path='core.url_pic' class='ynstore-attr-img' file=$aElement.image_path suffix='_90_square'}
                            {elseif !empty($aElement.color)}
                                <div class="ynstore-attr-img" style="background:{$aElement.color}"></div>
                            {else}
                                <img class="ynstore-attr-img" src="{$sCorePath}module/ynsocialstore/static/image/no-compare-product.jpg"/>
                            {/if}
                        </a>
                    </div>
                    {/foreach}
                </div>
                {elseif $aAttributeInfo.type == 3}
                <div id="ynstore_product_detail_attribute-image-text" class="ynstore-attr-items">
                    {foreach from=$aElements key=iKey item=aElement}
                    <div class="{if $iKey == 0}js_selected_attribute ynstore-active{/if} ynstore-attr-item" id="js_product_attribute-{$iKey}">
                        <a href="" class="ynstore-att-item-link" onclick="return ynsocialstore.selectAttributeInDetail(this);" data-toggle="tooltip" data-placement="bottom" title="{$aElement.title} ({$sDefaultSymbol}{$aElement.price})" data-elementid="{$aElement.attribute_id}" data-quantity="{$aElement.quantity}" data-remain="{$aElement.remain_of_attribute}" data-realremain="{$aElement.remain}" data-price="{$aElement.price}" id="js_ynstore_product_element-{$aElement.attribute_id}">
                            <div class="ynstore-att-item-content">
                                    {if !empty($aElement.image_path)}
                                        {img server_id=$aElement.server_id path='core.url_pic' class='ynstore-attr-img' file=$aElement.image_path suffix='_90_square'}
                                    {elseif !empty($aElement.color)}
                                        <div class="ynstore-attr-img" style="background:{$aElement.color};"></div>
                                    {else}
                                    <img class="ynstore-attr-img" src="{$sCorePath}module/ynsocialstore/static/image/no-compare-product.jpg"/>
                                    {/if}
                                    <span>
                                        {$aElement.title|clean}
                                    </span>
                            </div>
                        </a>
                    </div>
                    {/foreach}
                </div>
                {/if}
            </div>
        {/if}
        {if Phpfox::getUserId() != $aProduct.user_id && Phpfox::getUserId() > 0}
            <div id="ynstore_product_checkout_block-physical" style="clear: both;" class="form-inline">
                <input type="hidden" id="enable_inventory" value="{$aProduct.enable_inventory}">
                <input type="hidden" id="currency_symbol" value="{$sDefaultSymbol}">
                <input type="hidden" id="max_quantity_can_add" value="{$aProduct.max_quantity_can_add}">
                <input type="hidden" id="min_order" value="{$aProduct.min_order}">
                <input type="hidden" id="max_order" value="{$aProduct.max_order}">
                <input type="hidden" id="max_order_by_attribute" value="{$aProduct.max_quantity_can_add}">
                <input type="hidden" id="current_price" value="{$aProduct.discount_display}">

                
                <div class="ynstore-product-quanlity-block">
                    <div class="" id="js_ynstore_product_detail-total-price">
                        <div>{_p var='ynsocialstore.total_price'}:</div>

                        <span>{$sDefaultSymbol}</span>

                        <span id="product_total_price">
                            {if $aItem.discount_percentage && ($aItem.discount_timeless || ($aItem.discount_start_date <= PHPFOX_TIME && $aItem.discount_end_date >= PHPFOX_TIME))}{$aProduct.discount_display|number_format:2}
                            {else}{$aProduct.product_price|number_format:2}{/if}
                        </span>
                    </div>

                    <div class="ynstore-product-quanlity">
                        <div id="js_ynstore_product_checkout-quantity">
                            <button class="btn bt-default" id="quantity_minus" >-</button>
                            <input type="text" name="quantity" class="" id="current_quantity" value="1"/>
                            {if !empty($aProduct.uom_title)}<span class="ynstore-uom">{$aProduct.uom_title|convert}</span>{/if}
                            <button class="btn bt-default" id="quantity_add">+</button>
                        </div>

                        <div class="ynstore-btns">
                            <a href="#" class="btn btn-primary" onclick="ynsocialstore.addToCart(this,{$aProduct.product_id},'{$aProduct.product_type}',false,'buynow'); return false;">{_p var='ynsocialstore.buy_now'}</a>
                            <a href="#" class="btn btn-success" onclick="ynsocialstore.addToCart(this,{$aProduct.product_id},'{$aProduct.product_type}',false); return false;"><i class="ico ico-cart-plus"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        {/if}
    {elseif $aProduct.product_type == 'digital' && Phpfox::getUserId() != $aProduct.user_id && Phpfox::isUser()}
        {if !$isAlreadyBuy}
        <div class="ynstore-product-checkout-btn">
            <a href="#" class="btn btn-primary" onclick="ynsocialstore.addToCart(this,{$aProduct.product_id},'{$aProduct.product_type}',false,'buynow'); return false;">{_p var='ynsocialstore.buy_now'}</a>
            <a href="#" class="btn btn-success" onclick="ynsocialstore.addToCart(this,{$aProduct.product_id},'{$aProduct.product_type}',false); return false;"><i class="ico ico-cart-plus"></i></a>
        </div>
        {else}
        <div class="ynstore-product-digital-download">
            <!-- <textarea name="link" id="" cols="50" rows="2" readonly>{$aProduct.link}</textarea> -->
            <span>
                <i class="ico ico-check-circle-o"></i>
                {_p('You have already purchased for this product')}
            </span>
            <a href="{$aProduct.link}" title="{_p var='ynsocialstore.download'}" target="_blank" class="btn btn-primary btn-icon no_ajax">
                <i class="ico ico-download mr-1"></i>
                {_p var='ynsocialstore.download'}
            </a>
        </div>
        {/if}
    {/if}
{elseif $aProduct.product_status == 'paused' && Phpfox::getUserId() != $aProduct.user_id && Phpfox::getUserId() > 0}
<div class="ynstore-product-email-block">
    {_p var='ynsocialstore.sorry_this_item_is_temporarily_closed_because_it_s_out_of_stock_or_by_some_reasons_leave_your_email_contact_to_be_notified_as_soon_as_it_come_back'}.
    <form action="{url link='current'}" class="form-inline" method="POST">
        <input type="email" id="ynstore_email_inform" name="val[email_inform]" placeholder="{_p('Enter your email address')}" class="form-control">
        <button type="submit" class="btn btn-primary">{_p var='ynsocialstore.submit'}</button>
    </form>
</div>
{/if}
{literal}
<script type="text/javascript">
    var isInit = false;
    $Behavior.onLoadAttributeDetail = function(){
        if($('#js_product_attribute-0').length != 0)
        {
            if(isInit == true) return;
            isInit = true;
            $('#js_product_attribute-0').find('a').trigger('click');
        }
    }
</script>
{/literal}