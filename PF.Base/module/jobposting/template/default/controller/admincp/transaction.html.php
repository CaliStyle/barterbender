<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<!-- Search -->
<form method="GET" action="{url link='admincp.jobposting.transaction'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='search_filter'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>{phrase var='company'}:</label>
                <input class="form-control" title="{phrase var='company'}" type="text" name="search[company]" value="{value type='input' id='company'}">
            </div>
            <div class="form-group">
                <label>{phrase var='type'}:</label>
                <select class="form-control" title="{phrase var='type'}" name="search[type]">
                    <option value="0" {if $aForms.type==0}selected{/if}>{phrase var='all'}</option>
                    <option value="2" {if $aForms.type==2}selected{/if}>{phrase var='package'}</option>
                    <option value="4" {if $aForms.type==4}selected{/if}>{phrase var='feature'}</option>
                    <option value="1" {if $aForms.type==1}selected{/if}>{phrase var='sponsor'}</option>
                </select>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label>{phrase var='search_from'}:</label>
                    <div class="js_from_select">
                        {select_date prefix='from_' id='_from' start_year='-10' end_year='+10' field_separator=' / '
                        field_order='MDY' default_all=true }
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label>{phrase var='to_date'}:</label>
                    <div class="js_to_select">
                        {select_date prefix='to_' id='_to' start_year='-10' end_year='+10' field_separator=' / '
                        field_order='MDY' default_all=true }
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>{phrase var='status'}:</label>
                <select class="form-control" title="{phrase var='status'}" name="search[status_pay]">
                    <option value="0">{phrase var='all'}</option>
                    <option value="2" {if $aForms.status_pay==2}selected{/if}>{phrase var='pending'}</option>
                    <option value="3" {if $aForms.status_pay==3}selected{/if}>{phrase var='completed'}</option>
                </select>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" class="btn btn-primary" value="{phrase var='submit'}">
        </div>
    </div>
</form>

{if count($aTransactions) > 0}
<form action="{url link='current'}" method="post" id="karaoke_recording_list">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='manage_transactions'}
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <!-- Table rows header -->
                <thead>
                    <tr>
                        <th class="">{phrase var='company_name'}</th>
                        <th class="t_center w140">{phrase var='type'}</th>
                        <th class="t_center w140">{phrase var='package'}</th>
                        <th class="t_center w140">{phrase var='job'}</th>
                        <th class="t_center w140">{phrase var='purchased_date'}</th>
                        <th class="t_center w180">{phrase var='fee'}</th>
                        <th class="t_center w120">{phrase var='payment_status'}</th>
                    </tr>
                </thead>
                <tbody>
                <!-- Request rows -->
                {foreach from=$aTransactions key=iKey item=aTransaction}
                    <tr id="resume_view_{$aTransaction.transaction_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                        <td>
                            <a href="{permalink module='jobposting.company' id=$aTransaction.company_id title=$aTransaction.name}">
                                {$aTransaction.name}
                            </a>
                        </td>
                        <!-- Feature -->
                        <td class="t_center w140">
                            {$aTransaction.type_text}
                        </td>
                        <td class="t_center w140">
                            {$aTransaction.invoice_text}
                        </td>
                        <!-- Statistic -->
                        <td class="t_center w140">
                            {if $aTransaction.is_job_text}
                            <a href="{permalink module='jobposting' id=$aTransaction.job_id title=$aTransaction.title}">
                                {$aTransaction.job_text}
                            </a>
                            {else}
                                {$aTransaction.job_text}
                            {/if}
                        </td>
                        <td class="t_center w140">
                            {$aTransaction.time_stamp_text}
                        </td>
                        <td class="t_right w140">{$aTransaction.amount|currency:$aTransaction.currency}</td>
                        <td class="t_center w120">{if $aTransaction.status_pay!=3}{phrase var='pending'}{else}{phrase var='completed'}{/if}</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>

        <!-- Delete selected button -->
        {if count($total_money)>0}
        <div class="panel-footer">
            {phrase var='total_fee'}:
            {foreach from=$total_money key=key item=item}
                {if $key != 0}&#124;{/if} {$item|currency:$key}
            {/foreach}
        </div>
        {/if}
    </div>
</form>
{pager}

{else}
<div class="alert alert-info">
	{phrase var='no_transaction_found'}
</div>
{/if}
