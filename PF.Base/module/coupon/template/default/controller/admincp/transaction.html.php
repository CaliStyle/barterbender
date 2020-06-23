<?php
/**
 * [PHPFOX_HEADER]
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_Coupon
 * @version        3.01
 */
defined('PHPFOX') or exit('NO DICE!');
?>

{literal}
<script language="JavaScript" type="text/javascript">
    $Behavior.ynfrInitializeStatisticJs = function(){
        $("#js_from_date_listing").datepicker({
            dateFormat: 'mm/dd/yy',
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
            dateFormat: 'mm/dd/yy',
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



<!-- Filter Search Form Layout -->
<form class="ynfr" method="post" action="{url link='admincp.coupon.transaction'}">
    <!-- Form Header -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var="search_filter"}
            </div>
        </div>
        <div class="panel-body">
            <!-- Coupon Name-->
            <div class="form-group">
                <label for="">{phrase var="coupon_name"}:</label>
                <input class="form-control" type="text" name="search[title]" value="{value type='input' id='title'}" id="title" size="50" />
            </div>
            <!-- Coupon Username -->
            <div class="form-group">
                <label for="">{phrase var="username"}:</label>
                <input class="form-control" type="text" name="search[username]" value="{value type='input' id='username'}" id="username" size="50" />
            </div>
            <!-- From -->
            <div class="form-group dont-unbind-children">
                <label>{phrase var="from"}:</label>
                <input name="search[fromdate]" id="js_from_date_listing" type="text" value="{if $aForms.fromdate}{$aForms.fromdate}{/if}" />
                <a href="#" id="js_from_date_listing_anchor">
                    <img src="<?php echo Phpfox::getLib('template')->getStyle('image', 'jquery/calendar.gif'); ?>" />
                </a>
            </div>
            <!-- To -->
            <div class="form-group dont-unbind-children">
                <label for="">{phrase var="to"}:</label>
                <input name="search[todate]" id="js_to_date_listing" type="text" value="{if $aForms.todate}{$aForms.todate}{/if}" />
                <a href="#" id="js_to_date_listing_anchor">
                    <img src="<?php echo Phpfox::getLib('template')->getStyle('image', 'jquery/calendar.gif'); ?>" />
                </a>
            </div>
            <!-- Payment Type -->
            <div class="form-group">
                <label for="">{phrase var="payment_type"}:</label>
                <select class="form-control" name="search[payment_type]">
                    <option value="0">{phrase var="all"}</option>
                    <option value="1"  {value type='select' id='payment_type' default = 1}>{phrase var="publishing_coupon"}</option>
                    <option value="2"  {value type='select' id='payment_type' default = 2}>{phrase var="featuring_coupon"}</option>
                    <option value="3"  {value type='select' id='payment_type' default = 3}>{phrase var="publishing_and_featuring_coupon"}</option>
                </select>
            </div>
            <!-- Payment Status -->
            <div class="form-group">
                <label for="">{phrase var="status"}:</label>
                <select class="form-control" name="search[payment_status]">
                    <option value="0">{phrase var="all"}</option>
                    <option value="1"  {value type='select' id='payment_status' default = 1}>{phrase var="initialized_upper"}</option>
                    <option value="2"  {value type='select' id='payment_status' default = 2}>{phrase var="successed_upper"}</option>
                    <option value="3"  {value type='select' id='payment_status' default = 3}>{phrase var="pending_upper"}</option>
                    <option value="4"  {value type='select' id='payment_status' default = 4}>{phrase var="denied_upper"}</option>
                </select>
            </div>
        </div>
        <!-- Submit Buttons -->
        <div class="panel-footer">
            <input type="submit" id="filter_submit" name="search[submit]" value="{phrase var='search'}" class="btn btn-primary" />
            <input type="submit" id="filter_submit" name="search[reset]" value="{phrase var='reset'}" class="btn btn-default" />
        </div>
    </div>
</form>
<br/>

<!-- Transaction Listing Space -->
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {phrase var="transactions"}
        </div>
    </div>
    {if count($aTransactions) >0}
    <div class="panel-body">
        <div class="table-responsive flex-sortable">
            <table  class="table table-bordered">
                <!-- Table rows header -->
                <thead>
                <tr>
                    <th class="table_row_header t_center">ID</th>
                    <th class="table_row_header t_center">{phrase var='coupon_name'}</th>
                    <th class="table_row_header t_center">{phrase var='payer'}</th>
                    <th class="table_row_header t_center">{phrase var='purchased_date'}</th>
                    <th class="table_row_header t_center">{phrase var='payment_type'}</th>
                    <th class="table_row_header t_center">{phrase var='amount'}</th>
                    <th class="table_row_header t_center">{phrase var='tracking_id'}</th>
                    <th class="table_row_header t_center">{phrase var='status'}</th>
                </tr>
                </thead>
                <tbody>
                {foreach from = $aTransactions item = aTrans}
                <tr>
                    <td class="t_center">{$aTrans.transaction_id}</td>
                    <td class="t_center">{$aTrans.coupon_name|shorten:30:'...'}</td>
                    <td class="t_center">{$aTrans|user}</td>
                    <td class="t_center">{$aTrans.time_stamp|date:'core.global_update_time'}</td>
                    <td class="t_center">
                        {if $aTrans.payment_type == 1}
                        {phrase var="publishing_coupon"}
                        {elseif $aTrans.payment_type == 2}
                        {phrase var="featuring_coupon"}
                        {elseif $aTrans.payment_type == 3}
                        {phrase var="publishing_and_featuring_coupon"}
                        {else}
                        -
                        {/if}
                    </td>
                    <td class="t_center">{$aTrans.amount} {$aTrans.currency}</td>
                    <td class="t_center">{$aTrans.paypal_transaction_id}</td>
                    <td class="t_center">
                        {if $aTrans.status == 1}
                        {phrase var="initialized_upper"}
                        {elseif $aTrans.status == 2}
                        {phrase var="successed_upper"}
                        {elseif $aTrans.status == 3}
                        {phrase var="pending_upper"}
                        {elseif $aTrans.status == 4}
                        {phrase var="denied_upper"}
                        {else}
                        -
                        {/if}
                    </td>
                </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
        <div class="form-group">
            {pager}
        </div>
    </div>
    {else}
    <div class="extra_info">
        {phrase var="no_transaction_had_been_made"}
    </div>
    {/if}
</div>