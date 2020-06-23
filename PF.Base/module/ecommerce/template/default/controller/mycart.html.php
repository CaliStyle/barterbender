<div id='ynecommerce_mycart'>
    {if count($aMyCart)}
	<input type="hidden" id='ynecommerce_cartid' value="{$iCartId}">

    <!--start card section-->
	{foreach from=$aMyCart key=KeySeller item=aItemsSeller}
    <div class="cart_section">
		{if isset($aItemsSeller.0)}
        <div class="cart_header">
            {phrase var='seller'}&nbsp;{$aItemsSeller.0|user}
        </div>
        {/if} 

        <!--cart body end-->
        <div class="card_body">
		{foreach from=$aItemsSeller item=aItem}
        <!-- item -->
        <div class="cart_item">
            <div class="item_photo">
                {if isset($aItem.logo_path)}
                    {img server_id=$aItem.server_id path='core.url_pic' file=$aItem.logo_path suffix='_400' max_width=60 max_height=60}
                {else}
                    <img src="{$sDefaultImage}" alt="">
                {/if}</div>
            <div class="cart_item_info">
                <div class="item_title">
                    <a href="{url link=''.$aItem.product_creating_type.'.detail.'.$aItem.product_id}">{$aItem.name}</a>
                </div>
                <div class="item_type">
                    {phrase var=''.$aItem.product_creating_type.''}
                </div>
                <div class="item_quantity">
                    <input type="hidden" class='mycart_symbol' value="{$aItem.sSymbolCurrency}"/>
                    {if $aItem.cartproduct_type == 'offer' || $aItem.cartproduct_type == 'bid'}

                    1&nbsp;({$aItem.uom_title|convert})
                    <input type="hidden" class='mycart_quantity_product mycart_seller_{$aItem.user_id}' value="1">
                    <input type="hidden" class='mycart_price_product' value="{$aItem.cartproduct_price}">
                    <input type="hidden" class='mycart_productid' value="{$aItem.cartproduct_product_id}">
                    <input type="hidden" class='mycart_seller' value="{$aItem.user_id}">
                    {else}

                    <input type="number" class='mycart_quantity_product mycart_seller_{$aItem.user_id}' min="1" max="{$aItem.product_quantity}" step="1" value="{$aItem.cartproduct_quantity}" onchange="$.ajaxCall('ecommerce.updateSessionQuantity', 'val[iSellerId] ={$aItem.user_id}&val[iProductId] ={$aItem.cartproduct_product_id}&val[iQuantity] = '+$(this).val());">&nbsp;({$aItem.uom_title|convert})
                    <input type="hidden" class='mycart_price_product' value="{$aItem.cartproduct_price}">
                    <input type="hidden" class='mycart_productid' value="{$aItem.cartproduct_product_id}">
                    <input type="hidden" class='mycart_seller' value="{$aItem.user_id}">

                    {/if}
                </div>
                <div class="item_extra">
                    <div class="item_price item_price_{$aItem.product_id}">{$aItem.sSymbolCurrency}{$aItem.cartproduct_subtotal}</div>
                    <div class="cart_remove">
                        <a href="{url link=$sModule.'.mycart.remove_'.$aItem.cartproduct_id}">
                            <i class="fa fa-close"></i>
                            <span>{phrase var='remove'}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- item end-->
		{/foreach}
        <!--cart body end-->

        <div class="item_footer">
            <div class="item_total2">
                <span class="sum_label">{phrase var='total'}:</span>
                <span class="price">{$aItem.sSymbolCurrency}</span>
                <span class="price" id='mycart_total_seller_{$aItemsSeller.0.user_id}'></span>
            </div>
            <div class="buy_all_this">
                <a class="btn btn-sm btn-primary" href="{url link=$sModule.'.checkout.sellerid_'.$aItem.user_id}">
                    {phrase var='buy_all_from_this_seller'}
                </a>
            </div>
        </div>
    </div>
	{/foreach}
    </div>
    <!--end cart section-->

	<div class="card_end">
        <div class="item_total">
            <span class="sum_label">{phrase var='total'} :</span>
            <span class="price">{$aItem.sSymbolCurrency}</span>
            <span class="price" id='mycart_total'></span>
        </div>
        <div class="buy_all_this">
            <a class="btn btn-sm btn-primary" href="{url link=$sModule}">
                {phrase var='continue_shopping'}
            </a>
            <a class="btn btn-sm btn-warning" href="{url link=$sModule.'.checkout'}">
                {phrase var='buy_all'}
            </a>
        </div>
    </div>
{else}
	{phrase var='there_are_no_items'}
{/if}

{literal}
<script type="text/javascript">
	  $Behavior.ynEcommerceUpdateQuantity = function()
	  {
	  	  ynecommerce.initYnEcommerceMyCartUpdateQuantity();
      }
</script>	
{/literal}
</div>