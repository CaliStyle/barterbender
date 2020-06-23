{if $iPage == 0}
<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{literal}
<script language="JavaScript" type="text/javascript">
    $Behavior.ynecommercecInitializeStatisticJs = function(){
        $("#js_from_date_listing").datepicker({
            dateFormat: '{/literal}{$sFormatDatePicker}{literal}',
            onSelect: function(dateText, inst) {
                var $dateTo = $("#js_to_date_listing").datepicker("getDate");
                var $dateFrom = $("#js_from_date_listing").datepicker("getDate");
                if($dateTo)
                {
                    $dateTo.setHours(0);
                    $dateTo.setMilliseconds(0);
                    $dateTo.setMinutes(0);
                    $dateTo.setSeconds(0);
                }

                if($dateFrom)
                {
                    $dateFrom.setHours(0);
                    $dateFrom.setMilliseconds(0);
                    $dateFrom.setMinutes(0);
                    $dateFrom.setSeconds(0);
                }

                if($dateTo && $dateFrom && $dateTo < $dateFrom) {
                    tmp = $("#js_to_date_listing").val();
                    $("#js_to_date_listing").val($("#js_from_date_listing").val());
                    $("#js_from_date_listing").val(tmp);
                }
                return false;
            }
        });
$("#js_to_date_listing").datepicker({
    dateFormat: '{/literal}{$sFormatDatePicker}{literal}',
    onSelect: function(dateText, inst) {
        var $dateTo = $("#js_to_date_listing").datepicker("getDate");
        var $dateFrom = $("#js_from_date_listing").datepicker("getDate");

        if($dateTo)
        {
            $dateTo.setHours(0);
            $dateTo.setMilliseconds(0);
            $dateTo.setMinutes(0);
            $dateTo.setSeconds(0);
        }

        if($dateFrom)
        {
            $dateFrom.setHours(0);
            $dateFrom.setMilliseconds(0);
            $dateFrom.setMinutes(0);
            $dateFrom.setSeconds(0);
        }

        if($dateTo && $dateFrom && $dateTo < $dateFrom) {
            tmp = $("#js_to_date_listing").val();
            $("#js_to_date_listing").val($("#js_from_date_listing").val());
            $("#js_from_date_listing").val(tmp);
        }
        return false;
    }
});

$("#js_from_date_listing_anchor").click(function() {
    $("#js_from_date_listing").focus();
    return false;
});

$("#js_to_date_listing_anchor").click(function() {
    $("#js_to_date_listing").focus();
    return false;
});
};
</script>
{/literal}

<form id="ynecommerce_my_orders_search" action="{url link=$sModule.'.my-orders'}" method="post">
    <div class="search_row">
        <div class="form-group">
            <div class="product_title_group">
                <div class="product_title_label"><label for="product_title">{phrase var='product_title'}:</label></div>
                <div class="product_title_control">
                    <input id="product_title" value="{value type='input' id='product_title'}" type="text" name="search[product_title]" class="product_title form-control">
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="product_order_group">
                <div class="product_order_label"><label for="order_id">{phrase var='order_id'}:</label></div>
                <div class="product_order_control">
                    <input id="order_id" value="{value type='input' id='order_id'}" type="text" name="search[order_id]" class="order_id form-control">
                </div>
            </div>
        </div>
    </div>
    <div class="search_row">
        <div class="form-group">
            <div class="product_from_group">
                <div class="product_from_label"><label for="">{phrase var='order_from'}:</label></div>
                <div class="product_from_control">
                    {select_date prefix='start_time_' id='_from' start_year='current_year' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true }
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="product_to_group">
                <div class="product_to_label"><label for="">{phrase var='order_to'}:</label></div>
                <div class="product_to_control">
                    {select_date prefix='end_time_' id='_end_time' start_year='current_year' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true }
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="product_status_group">
                <div class="product_status_label"><label for="order_status">{phrase var='status'}:</label></div>
                <div class="product_status_control">
                    <select class="form-control" style="height: 35px;" id="order_status" name="search[order_status]">
                        <option value="all" {value type='select' id='order_status' default='all' }>{phrase var='all'}
                        </option>
                        <option value="new" {value type='select' id='order_status' default='new' }>{phrase var='new'}
                        </option>
                        <option value="shipped" {value type='select' id='order_status' default='shipped' }>{phrase
                            var='ecommerce.shipped'}
                        </option>
                        <option value="cancel" {value type='select' id='order_status' default='cancel' }>{phrase
                            var='ecommerce.cancel'}
                        </option>
                    </select>
                </div>
            </div>
        </div>
        <div class="product_type_group">
            <div class="product_type_label"><label for="item_type">{phrase var='item_type'}:</label></div>
            <div class="product_type_control">
                <select class="form-control" style="height: 35px;" id="item_type" name="search[item_type]">
                    <option value="all" {value type='select' id='item_type' default='all' }>{phrase var='all'}
                    </option>
                    {if in_array($sModule, array('auction', 'ecommerce'))}
                    <option value="auction" {value type='select' id='item_type' default='auction' }> {phrase
                        var='ecommerce.auction'}
                    </option>
                    {/if}
                    {if in_array($sModule, array('ynsocialstore', 'ecommerce'))}
                    <option value="ynsocialstore_product" {value type='select' id='item_type' default='ynsocialstore_product' }> {phrase
                        var='ynsocialstore.product'}
                    </option>
                    {/if}
                </select>
            </div>
        </div>
        <div class="product_submit_group">
            <div class="product_from_label">&nbsp;</div>
            <div class="product_submit_control">
                <button name="search[submit]" id="btn_search" class="btn btn-sm btn-primary" type="submit">{phrase var='search'}</button>
            </div>
        </div>
    </div>

</form>
{/if}
{if $aMyOrderRows}
    {if !PHPFOX_IS_AJAX}
    <div class="table-responsive mt-1">
        <table id="tableOrders" class="ynecommerce_full_table table">
            <tr>
                <th class="order_id ynauction-paddingright">
                    {phrase var='order_id'}
                    <div class="ynauction_column_sort_up_down">
                        <a href="{$sCustomBaseLink}sortfield_order_id/sorttype_asc/" class="up"></a>
                        <a href="{$sCustomBaseLink}sortfield_order_id/sorttype_desc/" class="down"></a>
                    </div>
                </th>
                <th class="shipping_info ynauction-paddingright">
                    {phrase var='products'}
                </th>
                <th class="shipping_info ynauction-paddingright">
                    {phrase var='item_type'}
                </th>
                <th class="shipping_info ynauction-paddingright">
                    {phrase var='shipping_info'}
                </th>
                <th class="order_date ynauction-paddingright">
                    {phrase var='order_date'}
                    <div class="ynauction_column_sort_up_down">
                        <a href="{$sCustomBaseLink}sortfield_order_date/sorttype_asc/" class="up"></a>
                        <a href="{$sCustomBaseLink}sortfield_order_date/sorttype_desc/" class="down"></a>
                    </div>
                </th>
                <th class="order_total price ynauction-paddingright">
                    {phrase var='order_total'}
                    <div class="ynauction_column_sort_up_down">
                        <a href="{$sCustomBaseLink}sortfield_order_total/sorttype_asc/" class="up"></a>
                        <a href="{$sCustomBaseLink}sortfield_order_total/sorttype_desc/" class="down"></a>
                    </div>
                </th>
                <th class="status">{phrase var='status'}</th>
                <th class="payment_status">{phrase var='payment_status'}</th>
                <th class="options">{phrase var='options'}</th>
            </tr>

    {else}
        <table id="page2" style="display: none" class="ynecommerce_full_table">
    {/if}
            {foreach from=$aMyOrderRows item=aMyOrderRow}
                <tr>
                    <td class="order_id"><a href="{permalink module=$aMyOrderRow.module_id.'.order-detail' id=$aMyOrderRow.order_id}">{$aMyOrderRow.order_code}</a></td>
                    <td class="products">
                        <?php $aMyOrderRow = $this->_aVars['aMyOrderRow']; $lastKey = end($aMyOrderRow['products']); ?>
                        {foreach from = $aMyOrderRow.products key = iKey item = aOrderItem}
                        <a href="{permalink module=$aOrderItem.orderproduct_module.'.detail' id=$aOrderItem.orderproduct_product_id title=$aOrderItem.orderproduct_product_name}">{$aOrderItem.orderproduct_product_name}{if isset($aOrderItem.attribute_name)} ({$aOrderItem.attribute_name}){/if}</a> <?php if ($lastKey != $this->_aVars['aOrderItem']) echo ',' ?>
                        {/foreach}
                    </td>
                    <td class="item_type">
                        {_p var=$aMyOrderRow.module_id.'_product'}
                    </td>
                    <td class="shipping_info">{$aMyOrderRow.sLocation|clean|shorten:100:'...'|split:10}</td>
                    <td class="order_date">{$aMyOrderRow.order_creation_datetime}</td>
                    <td class="order_total price">{$aMyOrderRow.order_total_price|currency:$aMyOrderRow.order_currency}</td>
                    <td class="status">
                        {if $aMyOrderRow.order_status == 'new'}{phrase var='new'}{/if}
                        {if $aMyOrderRow.order_status == 'shipped'}{phrase var='shipped'}{/if}
                        {if $aMyOrderRow.order_status == 'cancel'}{phrase var='cancel'}{/if}
                    </td>
                    <td class="payment_status"> {$aMyOrderRow.order_payment_status}</td>
                    <td class="options">
                        <a href="{permalink module=$aMyOrderRow.module_id.'.order-detail' id=$aMyOrderRow.order_id}"><span class="view_icon"></span>{phrase var='view'}</a>
                    </td>
                </tr>
            {/foreach}
        </table>
        {pager}
    {if !PHPFOX_IS_AJAX}
    </div>
    {/if}
{else}
	{if $iPage == 0}
    	<div class="mt-1">{phrase var='no_orders_found'}</div>
	{/if}
{/if}

{literal}
<script type="text/javascript">
    $Behavior.loadMoreManageOrders = function () {
        if ($('#page2').length > 0 && $('#page2 tbody').length > 0 && $('#tableOrders tbody').length > 0)
        {
            $('#tableOrders tbody').append($('#page2 tbody').html());
            $('#page2').remove();
        }
    }
</script>
{/literal}