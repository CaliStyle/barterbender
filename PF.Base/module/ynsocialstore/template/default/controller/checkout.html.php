<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/21/16
 * Time: 7:35 PM
 */
?>
<div id='ynstore_checkout'>
{if isset($bPlaceOrder) && $bPlaceOrder}
<div>
    <h3>{_p var='ecommerce.payment_method'}</h3>
    {module name='api.gateway.form'}
</div>
{else}
{if count($aCheckout)}
<form method="post" action="{url link='ynsocialstore.checkout'}"  id="ynstore_checkout_form">
    <input type="hidden" id='ynecommerce_cartid' value="{$iCartId}">
    <input type="hidden" id='ynstore_isdigital' name="val[only_digital]" value="{$bIsOnlyDigital}">
    {if !$bIsOnlyDigital}
    <div id='ynecommerce_select_your_shipping_information ynstore_select_your_shipping_information'>
        <div class="ynstore_shipping_information_title clearfix">
            <span class="circle">1</span>{_p var='ecommerce.select_your_shipping_information'}
            <a class="btn btn-primary" href="javascript:void(0);" onclick="$Core.box('ecommerce.addNewAddress',400,''); return false;">
               {_p var='ecommerce.add_new_address'}
            </a>
        </div>

        <div class="ynstore_contact_list">
            {if count($aAddresses)}
            {foreach from=$aAddresses key=KeyAddress item=aAddress}
            <div class="ynstore_contact_item">
                <div class="ynstore_contact_content">
                    <div class="ynstore_item_header">
                        <input type='radio' name="val[selected_address]" value={$aAddress.address_id} {if $KeyAddress == 0 }checked{/if}>
                        <span>{$aAddress.address_user_name}</span>
                        <a class="" href="#" onclick="$Core.jsConfirm({l}message: '{_p var='ecommerce.are_you_sure_want_to_delete_this_address_this_cannot_be_undone'}'{r}, function(){l}$.ajaxCall('ecommerce.deleteAddress','address_id={$aAddress.address_id}');{r}); return false;" >
                           <i class="fa fa-times" aria-hidden="true"></i>
                        </a>
                    </div>

                    <div class="ynstore_item_row">
                        <span class="ynstore_item_label">{_p var='ecommerce.location'}:</span>
                        <span class="ynstore_item_value">
                            {$aAddress.sLocation}
                        </span>
                    </div>

                    <div class="ynstore_item_row">
                        <span class="ynstore_item_label">{_p var='ecommerce.zip_code'}:</span>
                        <span class="ynstore_item_value">
                          {$aAddress.address_customer_postal_code}
                        </span>
                    </div>

                    {if isset($aAddress.address_customer_phone_number)}
                    <div class="ynstore_item_row">
                        <span class="ynstore_item_label">{_p var='ecommerce.phone'}:</span>
	                    <span class="ynstore_item_value">
	                    	{$aAddress.address_customer_country_code}&nbsp;
	                    	{$aAddress.address_customer_city_code}&nbsp;
	                    	{$aAddress.address_customer_phone_number}
	                    </span>
                    </div>
                    {/if}

                    {if isset($aAddress.address_customer_phone_number)}
                    <div class="ynstore_item_row">
                        <span class="ynstore_item_label">{_p var='ecommerce.mobile'}:</span>
                        <span class="ynstore_item_value">{$aAddress.address_customer_mobile_number}</span>
                    </div>
                    {/if}
                    <div class="ynstore_item_footer">
                        <a href="javascript:void(0);" onclick="$Core.box('ecommerce.editAddress',400,'address_id={$aAddress.address_id}'); return false;">
                            <span class="fa fa-edit"></span>
                            {_p var='ecommerce.edit'}
                        </a>
                    </div>
                    <div class="selected_this ynstore_selected_this">
                        <span>{_p var='ynsocialstore.you_select_it'}</span>
                    </div>
                </div>
            </div>
            {/foreach}
            {/if}
        </div>
    </div>
    {/if}

    <div id='ynecommerce_review_and_checkout ynstore_review_and_checkout' class="ynstore-mycart-page">
        <div class="ynstore-count-status ynstore_shipping_information_title">
            {if !$bIsOnlyDigital}<span class="circle">2</span>{/if}{_p var='ecommerce.review_and_cofirm_your_order_itotalitem_items' iTotalItem=$iCount}
        </div>
        <div id="ynecommerce_checkout_cart" class="ynstore_checkout_cart ynstore-mycart-block">
            <input type="hidden" id='ynstore_cartid' value="{$iCartId}">
            <!--start card section-->
            <div class="ynstore_cart_section">
                <div class="ynstore_cart_item" >
                    <div class="ynstore_item_info ynstore_item_info_labels">
                        <div class="ynstore_item_title ynstore-label">
                            {if $bUsingAdaptive}<input type="checkbox" name="" value="" id="js_check_box_all" class="main_checkbox" />{/if}
                            {_p var='ynsocialstore.products'}
                        </div>
                        <div class="ynstore_item_type ynstore-label">{_p var='ynsocialstore.attributes'}</div>
                        <div class="ynstore_item_quantity ynstore-label">{_p var='ecommerce.quantity'}</div>
                        <div class="ynstore_item_extra ynstore-label">{_p var='ynsocialstore.item_price'}</div>
                    </div>
                </div>
            </div>
            {foreach from=$aCheckout key=KeySeller item=aItemsSeller}
            <?php $this->_aVars['iCntSelect'] = $this->_aVars['iCntSelect'] + 1; ?>
            <div class="ynstore_cart_section">
                {if isset($aItemsSeller.store)}
                <div class="js_ynstore_error error_message hide" id="js_error_message_store_{$aItemsSeller.store.store_id}"></div>

                <div class="ynstore_cart_header">
                    {if $bUsingAdaptive}
                    <input type="checkbox" {if $iCntSelect < 5}checked{/if} name="val[{$aItemsSeller.store.store_id}][ynecommerce_select_to_checkout][]" class="mycart_select_to_checkout checkbox">{/if}
                    {_p var='ynsocialstore.from_s'}:&nbsp;<a href="{permalink module='ynsocialstore.store' id=$aItemsSeller.store.store_id title=$aItemsSeller.store.name}">{$aItemsSeller.store.name}</a>
                </div>
                {/if}

                <div class="ynstore_card_body">
                    {foreach from=$aItemsSeller.items item=aItem}
                    <div class="ynstore_cart_item" id="js_cart_item_{$aItem.cartproduct_id}" data-currency="{$aItem.creating_item_currency}">
                        <div class="ynstore_item_info" style="overflow: inherit !important;">
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
                                {if $aItem.attribute_style == 1}
                                    <div class="ddTitle borderRadiusTp">
                                        <span class="divider"></span>
                                        <span class="ddArrow arrowoff"></span>
                                        <span class="ddTitleText " id="js_attribute_on_cart-141_title">
                                            <span class="ddlabel"></span>
                                            <span class="description">{$aItem.title|clean}</span>
                                        </span>
                                    </div>
                                {elseif $aItem.attribute_style == 2}
                                    <div class="ddTitle borderRadiusTp">
                                        <span class="divider"></span>
                                        <span class="ddArrow arrowoff"></span>
                                            <span class="ddTitleText " id="js_attribute_on_cart-141_title">
                                                {if !empty($aItem.image_path)}
                                                    {img server_id=$aAttr.product_server_id path='core.url_pic' file=$aItem.image_path suffix='_90_square'}
                                                {elseif !empty($aItem.color)}
                                                    <div style="background:{$aItem.color};width:70px;height: 70px;"></div>
                                                {else}
                                                    <img src="{$sCorePath}module/ynsocialstore/static/image/no-compare-product.jpg"/>
                                                {/if}
                                                <span class="ddlabel"></span>
                                            </span>
                                    </div>
                                {elseif $aItem.attribute_style == 3}
                                    <div class="ddTitle borderRadiusTp">
                                        <div class="ddTitleText " id="js_attribute_on_cart-141_title">
                                            {if !empty($aItem.image_path)}
                                                {img server_id=$aItem.product_server_id path='core.url_pic' file=$aItem.image_path suffix='_90_square'}
                                            {elseif !empty($aItem.color)}
                                                <div style="background:{$aItem.color};width:50px;height: 50px;"></div>
                                            {else}
                                                <img src="{$sCorePath}module/ynsocialstore/static/image/no-compare-product.jpg"/>
                                            {/if}
                                            <span class="ddlabel"></span>
                                            <span class="description">{$aItem.title|clean}</span>
                                        </div>
                                    </div>
                                {/if}
                                {/if}
                            </div>

                            <div class="ynstore_item_quantity" id="js_cart_product_id-{$aItem.cartproduct_id}">
                                <input type="hidden" class='mycart_symbol' value="{$aItem.sSymbolCurrency}"/>
                               <b style="display:none" class="ynstore-showmobile">{_p var='ecommerce.quantity'}:</b>
                                {if $aItem.cartproduct_type == 'buy'}
                                    {$aItem.cartproduct_quantity}&nbsp;{if $aItem.product_type != 'digital' && !empty($aItem.uom_title)}{$aItem.uom_title|convert}{/if}
                                    <input type="hidden" name='val[{$aItemsSeller.store.store_id}][ynecommerce_checkout_quantity][]' class='mycart_quantity_product mycart_seller_{$aItemsSeller.store.store_id}' min="1" max="{$aItem.product_quantity}" step="1" value="{$aItem.cartproduct_quantity}" style="width: 60px">
                                    <input type="hidden" name='val[{$aItemsSeller.store.store_id}][ynecommerce_checkout_price][]' class='mycart_price_product' value="{$aItem.cartproduct_price}">
                                    <input type="hidden" name='val[{$aItemsSeller.store.store_id}][ynecommerce_checkout_productid][]' class='mycart_productid' value="{$aItem.cartproduct_product_id}">
                                    <input type="hidden" name='val[{$aItemsSeller.store.store_id}][ynecommerce_checkout_attributeid][]' class='mycart_attributeid' value="{$aItem.cartproduct_attribute_id}">
                                    <input type="hidden" class='mycart_cartproductid' value="{$aItem.cartproduct_id}">
                                    <input type="hidden" class='mycart_maxquantity' value="{$aItem.real_quantity_can_add}">
                                    <input type="hidden" class='mycart_seller' value="{$aItemsSeller.store.store_id}">
                                {/if}
                                <span class="mycart_max_quantity_noti text-danger hide"></span>
                            </div>

                            <div class="ynstore_item_extra">
                                <div class="ynstore_item_price item_price_{$aItem.cartproduct_id}">
                                   {$aItem.cartproduct_price|currency:$aItem.creating_item_currency}
                                </div>
                            </div>
                        </div>
                    </div>
                    {/foreach}

                    <div class="ynstore_item_footer">
                        <div class="msg_group ynstore_msg_group ynstore_buy_all_this">
                            <div class="msg_label ynstore_msg_label">{_p var='ecommerce.leave_message_for_seller'}</div>
                            <textarea name='val[{$aItemsSeller.store.store_id}][ynecommerce_checkout_message]' id='comment_on_seller_{$aItemsSeller.store.store_id}'></textarea>
                            <input type="hidden" name='val[{$aItemsSeller.store.store_id}][ynecommerce_checkout_ownerid]' class='mycart_ownerid' value="{$aItemsSeller.store.user_id}">
                        </div>

                        <span class="ynstore_sum_label">{_p var='ynsocialstore.sub_total'}{if isset($aItemsSeller.store.tax) && $aItemsSeller.store.tax} ({_p var='ynsocialstore.include_tax_tax' tax=$aItemsSeller.store.tax}){/if}:</span>
                        <div class="ynstore_item_total2">
                            <span class="ynstore_price" id='checkout_total_seller_{$aItemsSeller.store.store_id}'></span>
                        </div>
                    </div>
                </div>
                <input type="hidden" name='val[ynecommerce_currency]' value="{$aItem.cartproduct_currency}">
            </div>
            {/foreach}

            <input type="hidden" id="js_selected_currency" value="{$aItem.creating_item_currency}">

            <div class="ynstore_card_end">
               <span class="ynstore_sum_label">{_p var='ecommerce.total'} :</span>
                <div class="ynstore_item_total">
                    <span class="ynstore_price" id='checkout_total'></span>
                </div>
            </div>
            <div class="ynstore_place_order">
                <span id="ynstore_mycart_loading" style="display:none;font-size: 20px;position: relative;top: 3px;margin-right: 5px;" class="t_center"><i class="fa fa-spin fa-circle-o-notch"></i></span>
               <button id="ynstore_checkout_place_order" class="btn btn-primary {if $bUsingAdaptive}disabled{/if} sJsCheckBoxButton" onclick="return ynsocialstore.checkMinOrder(this,0,'checkout');" name='submit_checkout' >{_p var='ecommerce.place_order'}</button>
            </div>
        </div>

        {literal}
        <script type="text/javascript">
            $Behavior.ynStoreUpdateQuantity = function()
            {
                $(".js_attribute_on_cart").msDropdown();
                ynsocialstore.myCartChanged.deletedcart = '';
                ynsocialstore.myCartLimit.rawLimit = {/literal}{$aMaxElement}{literal};
                ynsocialstore.myCartLimit.realLimit = {/literal}{$aMaxElement}{literal};
                ynsocialstore.initMyCartUpdateQuantity();
            }
        </script>
        {/literal}
    </div>
</form>
{else}
{_p var='ecommerce.there_are_no_items'}
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
        //init place order
        var iCnt = 0;
        $("input:checkbox").each(function()
        {
            if (this.checked)
            {
                iCnt++;
            }
        });
        if (iCnt > 0)
        {
            $('.sJsCheckBoxButton').removeClass('disabled');
            $('.sJsCheckBoxButton').attr('disabled', false);
        }

        $("#js_check_box_all").click(function()
        {
            var bStatus = this.checked;

            if (bStatus)
            {
                $('.checkRow').addClass('is_checked');
                $('.sJsCheckBoxButton').removeClass('disabled');
                $('.sJsCheckBoxButton').attr('disabled', false);
            }
            else
            {
                $('.checkRow').removeClass('is_checked');
                $('.sJsCheckBoxButton').addClass('disabled');
                $('.sJsCheckBoxButton').attr('disabled', true);
            }

            $("input:checkbox").each(function()
            {
                this.checked = bStatus;
            });
        });
        $('.checkbox').click(function()
        {

            var iCnt = 0;
            $("input:checkbox").each(function()
            {
                if (this.checked)
                {
                    iCnt++;
                }
            });

            if (iCnt > 0)
            {
                $('.sJsCheckBoxButton').removeClass('disabled');
                $('.sJsCheckBoxButton').attr('disabled', false);
            }
            else
            {
                $('.sJsCheckBoxButton').addClass('disabled');
                $('.sJsCheckBoxButton').attr('disabled', true);
            }
        });

    }

</script>
{/literal}
{/if}
</div>
