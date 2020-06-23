<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
{if isset($aOrder)}
    {if $bCanViewOrder}
        <div class="order_detail">
            <div class="order_headline">
                <div class="order_id_group">
                    {phrase var='id'}: <span class="order_id">{$aOrder.order_code}</span> {phrase var='on'} {$aOrder.order_creation_datetime|date:'core.global_update_time'}
                </div>
                {if !empty($aOrder.order_buyfrom_id) && !empty($aOrder.order_buyfrom_name)}
                <div class="order_msg_group">
                    {phrase var=$aOrder.module_id.'.'.$aOrder.order_buyfrom_type}: <a href="{permalink module=$aOrder.module_id.'.'.$aOrder.order_buyfrom_type id=$aOrder.order_buyfrom_id title=$aOrder.order_buyfrom_name}">{$aOrder.order_buyfrom_name|clean|shorten:75:'...'|split:10}</a>
                </div>
                {/if}
                {if $aOrder.seller_user_id != $iViewerId}
                <div class="order_msg_group">
                    <a href="javascript:;" onclick="$Core.composeMessage({l}user_id: {$aOrder.seller_user_id}{r}); return false;"><span class="message_icon"></span>{phrase var='message'}</a>
                </div>
                <div class="order_seller_group">
                    {phrase var='seller'}: <a href="{url link=$aOrder.seller_user_name}" title="{$aOrder.seller_full_name|clean|shorten:75:'...'|split:10}">{$aOrder.seller_full_name|clean|shorten:75:'...'|split:10}</a>
                </div>
                {else}
                <div class="order_msg_group">
                    <a href="javascript:;" onclick="$Core.composeMessage({l}user_id: {$aOrder.buyer_user_id}{r}); return false;"><span class="message_icon"></span>{phrase var='message'}</a>
                </div>
                <div class="order_buyer_group">
                    {phrase var='buyer'}: <a href="{url link=$aOrder.buyer_user_name}" title="{$aOrder.buyer_full_name|clean|shorten:75:'...'|split:10}">{$aOrder.buyer_full_name|clean|shorten:75:'...'|split:10}</a>
                </div>
                {/if}
            </div>

            <div class="order_body">
                <div class="order_item">
                    {foreach from=$aOrderDetails item=aProduct}
                    {php}
                        $aProduct = $this->_aVars['aProduct'];
                    {/php}
                    <div class="order_row">
                        <div class="product-img">
                            <a href="{permalink module=$aProduct.orderproduct_module.'.detail' id=$aProduct.orderproduct_product_id title=$aProduct.orderproduct_product_name}">
                                {if $isSocialStore}
                                    {if isset($aProduct.logo_path)}
                                        {img ynsocialstore_overridenoimage=true server_id=$aProduct.server_id path='core.url_pic' file=$aProduct.logo_path suffix='_100'}
                                    {else}
                                        <img src="{if !empty($aProduct.default_product_image)}{$aProduct.default_product_image}{else}{$sDefaultImage}{/if}" alt="">
                                    {/if}
                                {else}
                                    {if isset($aProduct.logo_path)}
                                        {img server_id=$aProduct.server_id path='core.url_pic' file=$aProduct.logo_path suffix='_100' max_width=60 max_height=60}
                                    {else}
                                        <img src="{if !empty($aProduct.default_product_image)}{$aProduct.default_product_image}{else}{$sDefaultImage}{/if}" alt="">
                                    {/if}
                                {/if}
                            </a>
                        </div>
                        <div class="product_info">
                            <div class="product_title">
                                <a href="{permalink module=$aProduct.orderproduct_module.'.detail' id=$aProduct.orderproduct_product_id title=$aProduct.orderproduct_product_name}">
                                    {$aProduct.orderproduct_product_name|clean|shorten:75:'...'|split:10}{if isset($aProduct.attribute_name)} ({$aProduct.attribute_name}){/if}
                                </a>
                            </div>
                            <div class="product_quantity">
                                {if isset($aProduct.uom_title)}{$aProduct.orderproduct_product_quantity} (<?php echo Phpfox::getLib('locale')->convert($aProduct['uom_title']);?>){/if}
                            </div>
                            {php}
                            $iOrderproduct_product_price =  $this->_aVars['aProduct']['orderproduct_product_price'];
                            $iOrderproduct_product_quantity =  $this->_aVars['aProduct']['orderproduct_product_quantity'];
                            $iProductPrice = $iOrderproduct_product_price * $iOrderproduct_product_quantity;
                            $this->_aVars['iProductPrice'] = $iProductPrice;
                            {/php}
                            <div class="product_price">
                                {$aProduct.sSymbolCurrency}{$iProductPrice|number_format:2}
                            </div>
                        </div>
                    </div>
                    {/foreach}
                </div>
                <div class="shipping_info">
                    <div class="shipping_label">{phrase var='shipping_information'}:</div>
                    <div class="shipping_details">
                        <div class="details">
                            <div class="delivery_name">{$aOrder.order_delivery_name|clean}</div>
                            {if !empty($aOrder.sLocation)}
                            <div class="address">
                                <span class="title">{phrase var='location'}:</span> <span>{$aOrder.sLocation|clean}<span>
                            </div>
                            {/if}
                            {if !empty($aOrder.order_delivery_postal_code)}
                            <div class="zipcode">
                                <span class="title">{phrase var='zipcode'}:</span> <span>{$aOrder.order_delivery_postal_code|clean}<span>
                            </div>
                            {/if}
                            {if !empty($aOrder.order_delivery_phone_number)}
                            <div class="phone">
                                <span class="title">{phrase var='phone'}:</span> <span>{$aOrder.order_delivery_phone_number|clean}<span>
                            </div>
                            {/if}
                            {if !empty($aOrder.order_delivery_mobile_number)}
                            <div class="phone">
                                <span class="title">{phrase var='mobile'}:</span> <span>{$aOrder.order_delivery_mobile_number|clean}<span>
                            </div>
                            {/if}
                        </div>
                        <div class="summary">
                            <div class="subtotal">{phrase var='total'}: <span class="currency">{$aProduct.sSymbolCurrency}{$fSubTotal|number_format:2}</span></div>
                            {if $aOrder.buyer_user_id == $iViewerId}
                            <div class="status_title">{phrase var='status'}: <span class="status_value">{$aOrder.status_title}</span></div>
                            {else}
                            <div class="update_order_status">
                                <div class="order_state">
                                    <select class="order_status" id="order_status_{$aOrder.order_id}" name="val[order_status]">
                                        {foreach from=$aOrderStatus key=sStatusValue item=sStatusLabel}
                                        <option value="{$sStatusValue}" {if $aOrder.order_status == $sStatusValue} selected {/if}>{$sStatusLabel}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="order_button">
                                    <button id="popup_order_status_button_{$aOrder.order_id}" type="button" name="val[update]" class="btn btn-sm btn-primary" onclick="updateOrderStatus({$aOrder.order_id});">{phrase var='update'}</button>
                                    <div class="popup_order_status_loading_{$aOrder.order_id}" style="display: none;">{img theme='ajax/add.gif'}</div>
                                </div>
                            </div>
                            {/if}
                        </div>
                    </div>

                    <div class="message_info">
                        <div class="message_label">{phrase var='message'}:</div>
                        <div class="message_content">{$aOrder.order_note_parsed|clean}</div>
                    </div>
                </div>
            </div>

        </div>
    {else}
        <div class="error_message">{phrase var='you_dont_have_permission_to_view_this_order'}</div>
    {/if}
{else}
    <div class="error_message">{phrase var='order_is_not_valid'}</div>
{/if}

{literal}
<script type="text/javascript">
    function updateOrderStatus(iOrderId)
    {
        $('#popup_order_status_button_' + iOrderId).prop("disabled", true);
        $('.popup_order_status_loading_' + iOrderId).show();
        
        var sStatus = $("#order_status_" + iOrderId).val();
        
        $.ajaxCall('ecommerce.updateOrderStatus', 'status=' + sStatus + '&id=' + iOrderId);
    }
</script>
{/literal}