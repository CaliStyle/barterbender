<div id='ynecommerce_checkout'>

{if isset($bPlaceOrder) && $bPlaceOrder}
	<div>
		<h3>{phrase var='payment_method'}</h3>
		{module name='api.gateway.form'}			
	</div>
{else}

{if count($aCheckout)}
<form method="post" action="{url link='ecommerce.checkout'}"  id="ynecommerce_checkout_form">
	<input type="hidden" id='ynecommerce_cartid' value="{$iCartId}">
	<div id='ynecommerce_select_your_shipping_information'>
    <input type="hidden" name="module_id" value="{$sModule}">
	<div class="shipping_information_title">
		<span class="circle">1</span>
        {phrase var='select_your_shipping_information'}
        <a class="btn btn-sm btn-primary pull-right" href="javascript:void(0);" onclick="$Core.box('ecommerce.addNewAddress',400,''); return false;">{phrase var='add_new_address'}</a>
	</div>
		<div class="contact_list">
            {if count($aAddresses)}

            {foreach from=$aAddresses key=KeyAddress item=aAddress}
            <div class="contact_item">
                <div class="contact_content">
                    <div class="item_header">
                        <input type='radio' name="val[selected_address]" value={$aAddress.address_id} {if $KeyAddress == 0 }checked{/if}>
                        <span>{$aAddress.address_user_name}</span>
                        <a style="position: absolute;right: 5px;top: 0px;font-size: 16px;color: #ba0000;" href="#" onclick="$Core.jsConfirm({l}message : '{phrase var='are_you_sure_want_to_delete_this_address_this_cannot_be_undone'}' {r}, function(){l}$.ajaxCall('ecommerce.deleteAddress','address_id={$aAddress.address_id}');{r}, function(){l}{r}); return false;" ><i class="fa fa-times" aria-hidden="true"></i></a>
                    </div>
                    <div class="item_row">
                        <span class="item_label">{phrase var='location'}:</span>
                        <span class="item_value">
                            {$aAddress.sLocation}
                        </span>
                    </div>
                    <div class="item_row">
                        <span class="item_label">{phrase var='zip_code'}:</span>
                        <span class="item_value">
                                {$aAddress.address_customer_postal_code}
                            </span>
                    </div>

                    {if isset($aAddress.address_customer_phone_number)}
                    <div class="item_row">
	                    <span class="item_label">{phrase var='phone'}:</span>
	                    <span class="item_value">
	                    	{$aAddress.address_customer_country_code}&nbsp;
	                    	{$aAddress.address_customer_city_code}&nbsp;
	                    	{$aAddress.address_customer_phone_number}
	                    </span>
                    </div>
                    {/if}

                    {if isset($aAddress.address_customer_phone_number)}
                    <div class="item_row">
                        <span class="item_label">{phrase var='mobile'}:</span>
                        <span class="item_value">{$aAddress.address_customer_mobile_number}</span>
                    </div>
                    {/if}
                    <div class="item_footer">
                        <a href="javascript:void(0);" onclick="$Core.box('ecommerce.editAddress',400,'address_id={$aAddress.address_id}'); return false;">
                            <span class="fa fa-edit"></span>   {phrase var='edit'}
                        </a>
                    </div>
                    <div class="selected_this">
                        <span>{phrase var='you_select_it'}</span>
                    </div>
                </div>
            </div>
            {/foreach}
            {/if}
        </div>
	</div>
	<div id='ynecommerce_review_and_checkout'>
    <div class="shipping_information_title">
        <span class="circle">2</span>
        {phrase var='review_and_cofirm_your_order_itotalitem_items' iTotalItem=$iTotalItem}
    </div>

    <div id="ynecommerce_checkout_cart">


	{foreach from=$aCheckout key=KeySeller item=aItemsSeller}
        <div class="cart_section">
            <div class="cart_header">
                {phrase var='seller'}&nbsp;{$aItemsSeller.0|user}
            </div>
            <div class="card_body" id="seller_{$aItemsSeller.0.user_id}">

    		{foreach from=$aItemsSeller item=aItem}
    			{php}
					$aItem = $this->_aVars['aItem'];
				{/php}
                <div class="cart_item">
                    <div class="item_photo">
                        {if isset($aItem.logo_path)}
                            {img server_id=$aItem.server_id path='core.url_pic' file=$aItem.logo_path suffix='_400' max_width=60 max_height=60}
                        {else}
                            <img src="{$sDefaultImage}" alt="">
                        {/if}
                    </div>
                    <div class="cart_item_info">
                        <div class="item_title">
                            <a href="{url link=''.$aItem.product_creating_type.'.detail.'.$aItem.product_id}">{$aItem.name}</a>
                        </div>
                        <div class="item_type">
                            {phrase var=''.$aItem.product_creating_type.''}
                        </div>
                        <div class="item_quantity">
                            <input type="hidden" class='checkout_symbol' value="{$aItem.sSymbolCurrency}"/>
                            {if $aItem.cartproduct_type == 'offer' || $aItem.cartproduct_type == 'bid'}
                            1&nbsp;(<?php echo Phpfox::getLib('locale')->convert($aItem['uom_title']);?>)
                            <input type="hidden" name='val[{$aItemsSeller.0.user_id}][ynecommerce_checkout_quantity][]' class='checkout_quantity_product checkout_seller_{$aItem.user_id}' value="1">
                            <input type="hidden" name='val[{$aItemsSeller.0.user_id}][ynecommerce_checkout_price][]' class='checkout_price_product' value="{$aItem.cartproduct_price}">
                            <input type="hidden" name='val[{$aItemsSeller.0.user_id}][ynecommerce_checkout_productid][]' class='checkout_product_id' value="{$aItem.cartproduct_product_id}">
                            <input type="hidden" class='checkout_seller' value="{$aItem.user_id}">
                            {else}
                            <input type="number" name='val[{$aItemsSeller.0.user_id}][ynecommerce_checkout_quantity][]' class='checkout_quantity_product checkout_seller_{$aItem.user_id}' min="1" max="{$aItem.product_quantity}" step="1" value="{$aItem.cartproduct_quantity}" >&nbsp;(<?php echo Phpfox::getLib('locale')->convert($aItem['uom_title']);?>)
                            <input type="hidden" name='val[{$aItemsSeller.0.user_id}][ynecommerce_checkout_price][]' class='checkout_price_product' value="{$aItem.cartproduct_price}">
                            <input type="hidden" name='val[{$aItemsSeller.0.user_id}][ynecommerce_checkout_productid][]' class='checkout_product_id' value="{$aItem.cartproduct_product_id}">
                            <input type="hidden" class='checkout_seller' value="{$aItem.user_id}">
                            {/if}
                        </div>
                        <div class="item_extra">
                            <div class="item_price item_price_{$aItem.cartproduct_product_id}">{$aItem.sSymbolCurrency}{$aItem.cartproduct_subtotal}</div>
                        </div>
                    </div>
            </div>
		{/foreach}
        <div class="item_footer">
            <div class="msg_group">
                <div class="msg_label">{phrase var='leave_message_for_seller'}</div>
                <textarea name='val[{$aItemsSeller.0.user_id}][ynecommerce_checkout_message]' id='comment_on_seller_{$aItem.user_id}'></textarea>
            </div>
            <div class="item_total">
                <span class="sum_label">{phrase var='total'}:</span>
                <span class="price">{$aItem.sSymbolCurrency}</span>
                <span class="price" id='checkout_total_seller_{$aItemsSeller.0.user_id}'></span>
            </div>
        </div>
        <input type="hidden" name='val[ynecommerce_currency]' value="{$aItem.cartproduct_currency}">
    </div>
    </div>
	{/foreach}

    <div class="card_end">
        <div class="item_total">
            <span class="sum_label">{phrase var='total'} :</span>
            <span class="price">{$aItem.sSymbolCurrency}</span>
            <span class="price" id='checkout_total'></span>
        </div>
        <div class="buy_all_this">
            <button class="btn btn-sm btn-primary" type="submit" name='submit_checkout'>{phrase var='place_order'}</button>
        </div>
    </div>
	</div>
</form>
{else}
	{phrase var='there_are_no_items'}
{/if}
</div>
{/if}

{literal}
<script type="text/javascript">
	$Behavior.countryIsoChangeAddNewAddress = function()
	{
		$('#country_iso').bind('change',function()
		{	
			var sChildValue = $('#js_country_child_id_value').val();
			var sExtra = '';
			$('#js_country_child_id').html('');
			$('#country_iso').after('<span id="js_cache_country_iso">' + $.ajaxProcess('no_message') + '</span>');
			if ($('#js_country_child_is_search').length > 0)
			{
				sExtra += '&country_child_filter=true';
			}		
			$.ajaxCall('core.getChildren', 'country_iso=' + this.value + '&country_child_id=' + sChildValue + sExtra, 'GET');
		});	
	}

  	$Behavior.ynEcommerceUpdateQuantity = function()
  	{
	     ynecommerce.initYnEcommerceCheckoutUpdateQuantity();
  	}
</script>	
{/literal}
</div>