<!--CHECK IF ERROR IS EXITS-->
{if !empty($sError)}
    {$sError}
{else}
<!--SHOW ORDERS IF ERROR NOT EXITS-->
{if !PHPFOX_IS_AJAX}
<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<script src="{$core_path}module/ecommerce/static/jscript/orders_paging.js" type="text/javascript"></script>
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
{/if}

{if $sModule != 'ecommerce' && !PHPFOX_IS_AJAX}
<div class="ynauction-clearfix"></div>
{/if}
{if !PHPFOX_IS_AJAX}
<form id="ynecommerce_manage_orders_search" class="ynecommerce-search-filter-block" action="{url link=$sModule.'.manage-orders'}" method="post">
    <div class="row">
        <div class="col-md-6 ynecommerce-paddingright-5">
            <div class="form-group">
                <label for="product_title">{phrase var='product_title'}:</label>
                <input id="product_title" value="{value type='input' id='product_title'}" type="text"
                name="search[product_title]" class="form-control">
            </div>
        </div>

        <div class="col-md-6 ynecommerce-paddingleft-5">
            <div class="form-group">
                <label>{phrase var='order_id'}:</label>
                <input id="order_id" value="{value type='input' id='order_id'}" type="text" name="search[order_id]"
                class="form-control">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 ynecommerce-paddingright-5 dont-unbind-children">
            <div class="form-group">
                <label for="">{phrase var='order_from'}:</label>
                <div class="input-group">
                    <input name="search[fromdate]" id="js_from_date_listing" class="form-control" type="text" value="{if $sFromDate}{$sFromDate}{/if}"/>
                    <div class="btn input-group-addon">
                        <a href="#" id="js_from_date_listing_anchor">
                            <i class="ynicon yn-calendar"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 ynecommerce-paddingleft-5 dont-unbind-children">
            <div class="form-group">
               <label for="">{phrase var='order_to'}:</label>
               <div class="input-group">
                  <input name="search[todate]" id="js_to_date_listing" class="form-control" type="text" value="{if $sToDate}{$sToDate}{/if}"/>
                  <div class="btn input-group-addon">
                     <a href="#" id="js_to_date_listing_anchor">
                        <i class="ynicon yn-calendar"></i>
                     </a>
                  </div>
               </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 ynecommerce-paddingright-5">
            <div class="form-group">
                <label for="order_status">{phrase var='status'}:</label>
                <select id="order_status" class="form-control" name="search[order_status]">
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

        <div class="col-md-6 ynecommerce-paddingleft-5">
            <div class="form-group">
                <label for="item_type">{phrase var='item_type'}:</label>
                <select id="item_type" class="form-control" name="search[item_type]">
                    <option value="all" {value type='select' id='item_type' default='all' }>{phrase var='all'}
                    </option>
                    {if in_array($sModule, array('auction', 'ecommerce'))}
                    <option value="auction" {value type='select' id='item_type' default='auction' }>
                        {phrase var='auction'}
                    </option>
                    {/if}
                    {if in_array($sModule, array('ynsocialstore', 'ecommerce'))}
                    <option value="ynsocialstore_product" {value type='select' id='item_type' default='ynsocialstore_product' }>
                        {phrase var='product'}
                    </option>
                    {/if}
                </select>
            </div>
        </div>
    </div>

    <div class="t_right">
        <button name="search[submit]" id="btn_search" class="btn btn-primary" type="submit">{phrase var='search'}</button>
    </div>
</form>
{/if}

{if $aManageOrdersRows}
	{if !PHPFOX_IS_AJAX}
    <div id="ynecommerce_orders_information" class="ynecommerce_orders_information">
        <div>
            {phrase var='total_amount'} :  <span class="ynauction-price">
            {$iTotalAmount|currency:$aManageOrdersRows.0.order_currency}</span>
        </div>
        <div>
            {phrase var='commission'} : <span class="ynauction-price">{$iTotalCommission|currency:$aManageOrdersRows.0.order_currency}</span>
        </div>
    </div>

    <div class="table-responsive">
    {/if}
    {if !PHPFOX_IS_AJAX}
        <table id="tableOrders" class="table table-bordered ynecommerce-table-filter">
            <thead>
                <th class="order_id">
                    {phrase var='order_id'}
                    <span class="ynecommerce-sort-btn">
                        <a href="{$sCustomBaseLink}sortfield_order_id/sorttype_asc/" class="up"></a>
                        <a href="{$sCustomBaseLink}sortfield_order_id/sorttype_desc/" class="down"></a>
                    </span>
                </th>
                <th class="shipping_info">
                    {phrase var='products'}
                </th>
                <th class="shipping_info ynauction-paddingright">
                    {phrase var='item_type'}
                </th>
                <th class="order_id">
                    {phrase var='buyer'}
                    <span class="ynecommerce-sort-btn">
                        <a class="up" href="{$sCustomBaseLink}sortfield_buyer/sorttype_asc/"></a>
                        <a class="down" href="{$sCustomBaseLink}sortfield_buyer/sorttype_desc/"></a>
                    </span>
                </th>
                <th class="shipping_info">
                    {phrase var='shipping_info'}
                </th>
                <th class="order_date">
                    {phrase var='order_date'}
                    <span class="ynecommerce-sort-btn">
                        <a href="{$sCustomBaseLink}sortfield_order_date/sorttype_asc/" class="up"></a>
                        <a href="{$sCustomBaseLink}sortfield_order_date/sorttype_desc/" class="down"></a>
                    </span>
                </th>
                <th class="order_total price">
                    {phrase var='order_total'}
                    <div class="ynecommerce-sort-btn">
                        <a class="up" href="{$sCustomBaseLink}sortfield_order_total/sorttype_asc/"></a>
                        <a class="down" href="{$sCustomBaseLink}sortfield_order_total/sorttype_desc/"></a>
                    </div>
                </th>
                <th class="commission price">{phrase var='commission'}
                    <div class="ynecommerce-sort-btn">
                        <a href="{$sCustomBaseLink}sortfield_order_commission/sorttype_asc/" class="up"></a>
                        <a href="{$sCustomBaseLink}sortfield_order_commission/sorttype_desc/" class="down"></a>
                    </div>
                </th>
                <th class="status">{phrase var='status'}</th>
                <th class="payment_status">{phrase var='payment_status'}</th>
                <th class="options">{phrase var='options'}</th>
            </thead>
    {else}
            <table id="page2" style="display: none" class="table table-bordered ynecommerce-table-filter">
    {/if}
            {foreach from=$aManageOrdersRows item=aManageOrdersRow}
            <tr>
                <td class="order_id"><a href="{permalink module=$aManageOrdersRow.module_id.'.order-detail' id=$aManageOrdersRow.order_id}">{$aManageOrdersRow.order_code}</a></td>
                <td class="products">
                    <?php $aManageOrdersRow = $this->_aVars['aManageOrdersRow']; $lastKey = end($aManageOrdersRow['products']); ?>
                    {foreach from = $aManageOrdersRow.products key = iKey item = aOrderItem}
                    <a href="{permalink module=$aOrderItem.orderproduct_module.'.detail' id=$aOrderItem.orderproduct_product_id title=$aOrderItem.orderproduct_product_name}">{$aOrderItem.orderproduct_product_name}{if isset($aOrderItem.attribute_name)} ({$aOrderItem.attribute_name}){/if}</a> <?php if ($lastKey != $this->_aVars['aOrderItem']) echo ',' ?>
                    {/foreach}
                </td>
                <td class="item_type">
                    {_p var=$aManageOrdersRow.module_id.'_product'}
                </td>
                <td class="buyer">{$aManageOrdersRow|user}</td>
                <td class="shipping_info">{$aManageOrdersRow.sLocation|clean|shorten:100:'...'|split:10}</td>
                <td class="order_date">{$aManageOrdersRow.order_creation_datetime}</td>
                <td class="order_total price">{$aManageOrdersRow.order_total_price|currency:$aManageOrdersRow.order_currency}</td>
                <td class="order_total price">{$aManageOrdersRow.order_commission_value|currency:$aManageOrdersRow.order_currency}</td>
                <td class="status">
                 <select id="order_status" onchange="$(this).ajaxCall('ecommerce.updateStatusManageOrders', 'order_id={$aManageOrdersRow.order_id}&amp;status='+$(this).val());">
                    <option value="new" {if $aManageOrdersRow.order_status == 'new'}selected{/if}  >{phrase var='new'}</option>
                    <option value="shipped"    {if $aManageOrdersRow.order_status == 'shipped'}selected{/if}>{phrase var='shipped'}</option>
                    <option value="cancel" {if $aManageOrdersRow.order_status == 'cancel'}selected{/if} >{phrase var='cancel'}</option>
                </select>
                <td class="payment_status">{$aManageOrdersRow.order_payment_status}</td>
                </td>

                <td class="options">
                    <a href="{permalink module=$aManageOrdersRow.module_id.'.order-detail' id=$aManageOrdersRow.order_id}"><span class="view_icon"></span>{phrase var='view'}</a>
                </td>
            </tr>
            {/foreach}
        </table>
            {pager}
    {if !PHPFOX_IS_AJAX}
    </div>
    {/if}
{else}
	{if !PHPFOX_IS_AJAX}
    	<div>{phrase var='no_orders_found'}</div>
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
<!--END IF CHECK ERROR-->
{/if}
