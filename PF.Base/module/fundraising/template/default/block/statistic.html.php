<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="ynfr-list-block">
	{if $iPage == 0 || $iIsAdmin == 1}
    <form class="ynfr" method="get" action="{url link=$sUrl}">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title mb-1">
                    {_p var='search_filter'}
                </div>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label>{phrase var='keyword'}:</label>
                    {$aFilters.keyword}
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="js_from_date_listing">{phrase var='from_date'}:</label>
                        <div class="input-group js_from_select">
                            {select_date prefix='from_' id='_from' start_year='-10' end_year='+10' field_separator=' / '
                            field_order='MDY' default_all=true }
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="js_to_date_listing">{phrase var='to_date'}:</label>
                        <div class="input-group js_to_select">
                            {select_date prefix='to_' id='_to' start_year='-10' end_year='+10' field_separator=' / '
                            field_order='MDY' default_all=true }
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <button type="submit" class="btn btn-primary">{_p var='search'}</button>
            </div>
        </div>
    </form>
	{/if}
    {if $aTransactions && isset($aTransactions) && count($aTransactions)}
    <div class="panel panel-default mt-2">
        <div class="panel-heading">
            <div class="panel-title mb-1">
                {_p var='statistic'}
            </div>
        </div>
        <div class="table-responsive">
            {if $iPage == 0 || $iIsAdmin == 1}
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{phrase var='date'}</th>
                        <th>{phrase var='amount'}</th>
                        <th>{phrase var='transaction_id'}</th>
                        <th>{phrase var='status'}</th>
                        <th>{phrase var='donor'}</th>
                        <th>{phrase var='email_address'}</th>
                        <th>{phrase var='option'}</th>
                    </tr>
                </thead>
                {/if}
                <tbody>
                {foreach from=$aTransactions key=iKey item=aTransaction}
                    <tr id="js_row{$aTransaction.transaction_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                        <td>{$aTransaction.time_stamp|date}</td>
                        <td>{$aTransaction.amount}</td>
                        <td>{$aTransaction.paypal_transaction_id}</td>
                        <td>{$aTransaction.status}</td>
                        <td>{if $aTransaction.is_guest}{$aTransaction.guest_full_name}{else}{$aTransaction.full_name}{/if}</td>
                        <td>{if $aTransaction.is_guest}{$aTransaction.guest_email_address}{else}{$aTransaction.email}{/if}</td>
                        <td><a href="{url link='current' view='detail' transaction=$aTransaction.transaction_id}">{phrase var='view_details'}</a></td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>
    {elseif $aCampaignStats && isset($aCampaignStats) && count($aCampaignStats)}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='statistic'}
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{phrase var='campaign'}</th>
                        <th>{phrase var='status'}</th>
                        <th>{phrase var='owner'}</th>
                        <th>{phrase var='donor'}</th>
                        <th>{phrase var='transaction_id'}</th>
                        <th>{phrase var='amount'}</th>
                        <th>{phrase var='date'}</th>
                        <th>{phrase var='option'}</th>
                    </tr>
                </thead>
                <tbody>
                {foreach from=$aCampaignStats key=iKey item=aCampaignStat}
                    <tr id="js_row{$aCampaignStat.transaction_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                        <td>{$aCampaignStat.title}</td>
                        <td>{$aCampaignStat.status}</td>
                        <td>{$aCampaignStat.owner}</td>
                        <td>{$aCampaignStat.donor}</td>
                        <td>{$aCampaignStat.paypal_transaction_id}</td>
                        <td>{$aCampaignStat.amount}</td>
                        <td>{$aCampaignStat.time_stamp|date}</td>
                        <td><a href="{url link='current' view='detail' transaction=$aCampaignStat.transaction_id}">{phrase var='view_details'}</a></td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>
    {pager}
    {else}
		<div class="alert alert-info">
            {phrase var='there_are_no_transaction'}
        </div>
    {/if}
</div>
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