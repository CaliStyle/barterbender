{if !empty($sError)}
    {$sError}
{else}
{if !PHPFOX_IS_AJAX}
<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<script src="{$sCorePath}module/ecommerce/static/jscript/orders_paging.js" type="text/javascript"></script>
{literal}

<script type="text/javascript">
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
<form id="ynecommerce_manage_orders_search" class="ynstore-search-filter-block" action="{url link='ynsocialstore.product-sales'}id_{$iProductId}" method="post">
    <div class="row">
        <div class="col-sm-6 ynstore-paddingright-5">
            <div class="form-group">
                <label for="product_title">{_p var='ynsocialstore_buyer'}:</label>
                <input id="buyer" value="{value type='input' id='buyer'}" type="text"
                name="search[buyer]" class="form-control">
            </div>
        </div>

        <div class="col-sm-6 ynstore-paddingleft-5">
            <div class="form-group">
                <label>{_p var='ecommerce.order_id'}:</label>
                <input id="order_id" value="{value type='input' id='order_id'}" type="text" name="search[order_id]"
                class="form-control">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 ynstore-paddingright-5">
            <div class="form-group">
                <label for="">{_p var='ecommerce.order_from'}:</label>
                <div class="input-group dont-unbind-children">
                    <input name="search[fromdate]" id="js_from_date_listing" class="form-control" type="text" value="{if $sFromDate}{$sFromDate}{/if}"/>
                    <div class="input-group-addon">
                        <a href="#" id="js_from_date_listing_anchor">
                            <i class="ico ico-calendar"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 ynstore-paddingleft-5">
            <div class="form-group">
                <label for="">{_p var='ecommerce.order_to'}:</label>
                <div class="input-group dont-unbind-children">
                    <input name="search[todate]" id="js_to_date_listing" class="form-control" type="text" value="{if $sToDate}{$sToDate}{/if}"/>
                    <div class="input-group-addon">
                        <a href="#" id="js_to_date_listing_anchor">
                            <i class="ico ico-calendar"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 ynstore-paddingright-5">
            <div class="form-group">
                <label for="order_status">{_p var='ecommerce.status'}:</label>
                <select id="order_status" class="form-control" name="search[order_status]">
                    <option value="all" {value type='select' id='order_status' default='all' }>{_p var='ecommerce.all'}
                    </option>
                    <option value="new" {value type='select' id='order_status' default='new' }>{_p var='ecommerce.new'}
                    </option>
                    <option value="shipped" {value type='select' id='order_status' default='shipped' }>{_p
                        var='ecommerce.shipped'}
                    </option>
                    <option value="cancel" {value type='select' id='order_status' default='cancel' }>{_p
                        var='ecommerce.cancel'}
                    </option>
                </select>
            </div>
        </div>

        <div class="col-sm-6 ynstore-paddingleft-5">
            <div class="form-group">
                <label for="item_type">{_p var='ecommerce.payment_status'}:</label>
                <select id="payment_status" class="form-control" name="search[payment_status]">
                    <option value="all" {value type='select' id='order_status' default='all' }>{_p var='ecommerce.all'}
                    </option>
                    <option value="initialized" {value type='select' id='payment_status' default='initialized' }>{_p var='ecommerce.initialized'}
                    </option>
                    <option value="expired" {value type='select' id='payment_status' default='expired' }>{_p var='ecommerce.expired'}
                    </option>
                    <option value="pending" {value type='select' id='payment_status' default='pending' }>{_p
                        var='ecommerce.pending'}
                    </option>
                    <option value="completed" {value type='select' id='payment_status' default='completed' }>{_p
                        var='ecommerce.completed'}
                    </option>
                    <option value="cancel" {value type='select' id='payment_status' default='cancel' }>{_p
                        var='ecommerce.cancel'}
                    </option>
                </select>
            </div>
        </div>
    </div>

    <div class="t_right">
        <button name="search[submit]" id="btn_search" class="btn btn-primary" type="submit">{_p var='ecommerce.search'}</button>
    </div>

</form>
{/if}

{if $aManageOrdersRows}
{if !PHPFOX_IS_AJAX}
<div id="ynecommerce_orders_information" class="ynstore-product-all-sales">
    <div>
        {_p var='ecommerce.total_amount'} :
        <span class="ynauction-price">
            {$iTotalAmount|ynsocialstore_format_price:$aManageOrdersRows.0.order_currency}
        </span>
    </div>
    <div>
        {_p('Total quantity')} :
        <span class="ynauction-price">
            {$iTotalQuantity}
        </span>
    </div>
</div>

<div class="table-responsive ynstore-table-filter">
    {/if}
    {if !PHPFOX_IS_AJAX}
    <table id="tableOrders" class="table table-bordered">
        <thead>
            <th class="order_id">
                {_p var='ecommerce.order_id'}
                <div class="ynstore-sort-btn">
                    <a href="{$sCustomBaseLink}sortfield_order_id/sorttype_asc/" class="up"></a>
                    <a href="{$sCustomBaseLink}sortfield_order_id/sorttype_desc/" class="down"></a>
                </div>
            </th>
            <th class="order_id">
                {_p var='ecommerce.buyer'}
                    <span class="ynstore-sort-btn">
                        <a class="up" href="{$sCustomBaseLink}sortfield_buyer/sorttype_asc/"></a>
                        <a class="down" href="{$sCustomBaseLink}sortfield_buyer/sorttype_desc/"></a>
                    </span>
            </th>
            <th class="shipping_info">
                {_p var='ecommerce.shipping_info'}
            </th>
            <th class="order_date">
                {_p var='ecommerce.order_date'}
                    <span class="ynstore-sort-btn">
                        <a href="{$sCustomBaseLink}sortfield_order_date/sorttype_asc/" class="up"></a>
                        <a href="{$sCustomBaseLink}sortfield_order_date/sorttype_desc/" class="down"></a>
                    </span>
            </th>
            <th class="order_total price">
                {_p('total_price')}
                <div class="ynstore-sort-btn">
                    <a class="up" href="{$sCustomBaseLink}sortfield_order_total/sorttype_asc/"></a>
                    <a class="down" href="{$sCustomBaseLink}sortfield_order_total/sorttype_desc/"></a>
                </div>
            </th>
            <th class="commission price">{_p('Quantity')}
                <div class="ynstore-sort-btn">
                    <a href="{$sCustomBaseLink}sortfield_order_quantity/sorttype_asc/" class="up"></a>
                    <a href="{$sCustomBaseLink}sortfield_order_quantity/sorttype_desc/" class="down"></a>
                </div>
            </th>
            <th class="status">{_p var='ecommerce.status'}</th>
            <th class="payment_status">{_p var='ecommerce.payment_status'}</th>
            <th class="options">{_p var='ecommerce.options'}</th>
        </thead>
        {else}
        <table id="page2" style="display: none" class="table table-bordered">
            {/if}
            {foreach from=$aManageOrdersRows item=aManageOrdersRow}
            <tr>
                <td class="order_id"><a href="{permalink module='ecommerce.order-detail' id=$aManageOrdersRow.order_id}">{$aManageOrdersRow.order_code}</a></td>
                <td class="buyer">{$aManageOrdersRow|user}</td>
                <td class="shipping_info">{$aManageOrdersRow.sLocation|clean|shorten:100:'...'|split:10}</td>
                <td class="order_date">{$aManageOrdersRow.order_creation_datetime}</td>
                <td class="order_total price">{$aManageOrdersRow.total_product_price|number_format:2} {$aManageOrdersRow.order_currency}</td>
                <td class="order_total price">{$aManageOrdersRow.orderproduct_product_quantity}</td>
                <td class="status">
                    {$aManageOrdersRow.order_status}</td>
                <td class="payment_status">{$aManageOrdersRow.order_payment_status}</td>
                </td>

                <td class="options">
                    <a href="{permalink module=$sModule.'.order-detail' id=$aManageOrdersRow.order_id}"><span class="view_icon"></span>{_p var='ecommerce.view'}</a>
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
<div>{_p var='ecommerce.no_orders_found'}</div>
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
{/if}
