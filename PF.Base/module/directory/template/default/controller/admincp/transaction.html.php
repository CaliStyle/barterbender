<?php
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
<form class="ynfr" method="GET" action="{url link='admincp.directory.transaction'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var="directory.search_filter"}
            </div>
        </div>

        <div class="panel-body">
            <div class="form-group">
                <label for="title">{phrase var="directory.business"}:</label>
                <input class="form-control" type="text" name="search[title]" value="{value type='input' id='title'}" id="title" size="50" />
            </div>

            <div class="form-group">
                <label for="type">{phrase var='type'}:</label>
                <select name="search[type]" class="form-control">
                    <option value="0">{phrase var='all'}</option>
                    <option value="package"  {value type='select' id='type' default = 'package'}>{phrase var='package'}</option>
                    <option value="feature"  {value type='select' id='type' default = 'feature'}>{phrase var='feature'}</option>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <!-- From -->
                    <div class="form-group">
                        <label for="fromdate">{phrase var='from_date'}:</label>
                        <div class="row">
                            <div class="col-md-12 js_from_select">
                                {select_date prefix='from_' id='_from' start_year='-10' end_year='+10' field_separator=' / '
                                field_order='MDY' default_all=true }
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- To -->
                    <div class="form-group">
                        <label>{phrase var='to_date'}:</label>
                        <div class="row">
                            <div class="col-md-12 js_to_select">
                                {select_date prefix='to_' id='_to' start_year='-10' end_year='+10' field_separator=' / '
                                field_order='MDY' default_all=true }
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="payment_status">{phrase var='payment_status'}:</label>
                <select class="form-control" name="search[payment_status]" id="payment_status">
                    <option value="0">{phrase var='all'}</option>
                    <option value="pending"  {value type='select' id='payment_status' default = 'pending'}>{phrase var='pending'}</option>
                    <option value="completed"  {value type='select' id='payment_status' default = 'completed'}>{phrase var='completed'}</option>
                </select>
            </div>

            <div class="form-group">
                <label for="sort_by">{phrase var='sort_by'}:</label>
                <div class="form-inline">
                    <select class="form-control" name="search[sort_by]">
                        <option value="purchase_date"  {value type='select' id='sort_by' default = 'purchase_date'}>{phrase var='purchase_date'}</option>
                        <option value="business"  {value type='select' id='sort_by' default = 'business'}>{phrase var='business'}</option>
                    </select>
                    <select class="form-control" name="search[sort_by_vector]">
                        <option value="descending"  {value type='select' id='sort_by_vector' default = 'descending'}>{phrase var='descending'}</option>
                        <option value="ascending"  {value type='select' id='sort_by_vector' default = 'ascending'}>{phrase var='ascending'}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="panel-footer">
            <input type="submit" id="filter_submit" name="search[submit]" value="{phrase var='search'}" class="btn btn-primary">
        </div>
    </div>
</form>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {phrase var="directory.transaction"}
        </div>
    </div>
    {if count($aTransactions)}
    <div class="table-responsive flex-sortable">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="t_center">{phrase var='transaction_id'}</th>
                    <th class="t_center">{phrase var='business'}</th>
                    <th class="t_center" align="center">{phrase var='purchase_date'}</th>
                    <th class="t_center" align="center">{phrase var='fee'}</th>
                    <th class="t_center" align="center">{phrase var='payment_method'}</th>
                    <th class="t_center" align="center">{phrase var='payment_status'}</th>
                    <th class="t_center" align="center">{phrase var='description'}</th>
                </tr>
            </thead>
            <tbody>
            {foreach from = $aTransactions item = aTrans}
                <tr>
                    <td>{$aTrans.transaction_id}</td>
                    <td>{$aTrans.business_name|shorten:30:'...'}</td>
                    <td align="center">{$aTrans.purchase_date'}</td>
                    <td align="center">{$aTrans.fee'}</td>
                    <td align="center">{$aTrans.payment_method'}</td>
                    <td align="center">{$aTrans.payment_status'}</td>
                    <td align="center">{$aTrans.description'}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    <?php if ($this->getLayout('pager')): ?>
        <div class="panel-footer">
            {pager}
        </div>
    <?php endif; ?>
    {else}
    <div class="alert alert-info">
        {phrase var='no_transactions_have_been_made'}
    </div>
    {/if}
</div>
