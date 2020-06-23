<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
?>

{literal}
<script type="text/javascript">

    $Core.remakePostUrl = function(){

        var sProductTitle = $("#product_title").val();
        var sStatus = $("#order_status").val();
        var sOrderId = $("#order_id").val();
        
        var sFromDatePicker = $('[name=js_from__datepicker]').val();
        if (sFromDatePicker != '')
        {
            var iFromMonth = $("#from_month").val();
            var iFromDay = $("#from_day").val();
            var iFromYear = $("#from_year").val();
            var sFromDate = iFromDay.concat('-' + iFromMonth, '-' + iFromYear);
        }
        else
        {
            var sFromDate = '';
        }
        
        var sToDatePicker = $('[name=js_from__datepicker]').val();
        if (sToDatePicker != '')
        {
            var iToMonth = $("#to_month").val();
            var iToDay = $("#to_day").val();
            var iToYear = $("#to_year").val();
            var sToDate = iToDay.concat('-' + iToMonth, '-' + iToYear);
        }
        else
        {
            var sToDate = '';
        }
        var url = window.location.href;

        if (url.match(/\/advsearch_.*?\//g))
        {

        }
        else
        {
            url = url.replace(/\/my\-orders\//g, '/my-orders/advsearch_true/');
        }

        if (url.match(/\/product-title_.*?\//g))
        {
            url = url.replace(/\/product-title_.*?\//g, '/product-title_' + sProductTitle + '/');
        }
        else
        {
            url += 'product-title_' + sProductTitle + '/';
        }

        if (url.match(/\/status_.*?\//g))
        {
            url = url.replace(/\/status_.*?\//g, '/status_' + sStatus + '/');
        }
        else
        {
            url += 'status_' + sStatus + '/';
        }

        if (url.match(/\/order-id_.*?\//g))
        {
            url = url.replace(/\/order-id_.*?\//g, '/order-id_' + sOrderId + '/');
        }
        else
        {
            url += 'order-id_' + sOrderId + '/';
        }
        
        if (url.match(/\/from_.*?\//g))
        {
            url = url.replace(/\/from_.*?\//g, '/from_' + sFromDate + '/');
        }
        else
        {
            url += 'from_' + sFromDate + '/';
        }

        if (url.match(/\/to_.*?\//g))
        {
            url = url.replace(/\/to_.*?\//g, '/to_' + sToDate + '/');
        }
        else
        {
            url += 'to_' + sToDate + '/';
        }

        $("#ynecommerce_advsearch").attr('action', url);
    }
    
</script>
{/literal}

<div class="ynfe adv-search-block" id="ynecommerce_adv_search" {if !isset($aForms.advancedsearch)} style="display: none;" {/if}>
    <form id="ynecommerce_advsearch" action="#" method="post" onsubmit="$Core.remakePostUrl(); if($('#search_keywords').val()=='{phrase var='keywords'}...'){l}$('#search_keywords').val('');{r}">
        <input type="hidden" value="1" name="search[submit]">
        <input type="hidden" name="search[advsearch]" value="1" />
        
        <input type="hidden" id="form_flag" name="search[form_flag]" value="0">
        
        <div class="adv-search-block-item form-group">
            <div><label for="product_title">{phrase var='product_title'}:</label></div>
            <div>
                <input class="form-control" id="product_title" value="{value type='input' id='product_title'}" type="text" name="search[product_title]" class="product_title">
            </div>
        </div>
        
        <div class="adv-search-block-item form-group">
            <div>{phrase var='order_id'}:</div>
            <div>
                <input class="form-control" id="order_id" value="{value type='input' id='order_id'}" type="text" name="search[order_id]" class="order_id">
            </div>
        </div>
        
        <div class="adv-search-block-item form-group">
            <div><label for="">{phrase var='order_from'}:</label></div>
            <div style="position: relative;" class="js_from_select">
                {select_date prefix='from_' id='_from' start_year='-2' end_year='+2' field_separator=' / ' field_order='MDY' default_all=true }
            </div>
        </div>
	
        <div class="adv-search-block-item form-group">
            <div><label for="">{phrase var='order_to'}:</label></div>
            <div style="position: relative;" class="js_to_select">
                {select_date prefix='to_' id='_to' start_year='-2' end_year='+2' field_separator=' / ' field_order='MDY' default_all=true }
            </div>
        </div>
        
        <div class="adv-search-block-item form-group">
            <div><label for="order_status">{phrase var='status'}:</label></div>
            <div>
                <select class="form-control" id="order_status" name="search[order_status]">
                    <option value=""></option>
                    <option value="new" {value type='select' id='order_status' default='new'}>{phrase var='new'}</option>
                    <option value="shipped" {value type='select' id='order_status' default='shipped'}>{phrase var='shipped'}</option>
                    <option value="cancel" {value type='select' id='order_status' default='cancel'}>{phrase var='cancel'}</option>
                </select>
            </div>
        </div>

        <div class="adv-search-block-item form-group">
            <div></div>
            <div>
                <button name="search[submit]" class="btn btn-sm btn-primary" type="submit" >{phrase var='auction.search'}</button>
            </div>
        </div>    
    </form>
</div> 