<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 09:39
 */
?>
<div class="yncaffiliate-manage-request">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p('Search Filter')}
            </div>
        </div>
        <form method="get" enctype="multipart/form-data" action="">
            <div class="panel-body">
                <div class="form-group">
                    <label for="affiliate_name">
                        {_p var='affiliate_name'}
                    </label>
                    <input class="form-control" id="affiliate_name" type="text" name="search[affiliate_name]" value="{value type='input' id='affiliate_name'}">
                </div>
                <div class="form-group">
                    <label for="status">
                        {_p var='Status'}
                    </label>
                    <select class="form-control" id="status" name="search[status]">
                        <option value="">{_p var='any'}</option>
                        <option value="waiting" {value type='select' id='status' default = 'waiting'}>{_p var='Waiting'}</option>
                        <option value="completed" {value type='select' id='status' default = 'completed'}>{_p var='completed'}</option>
                        <option value="denied" {value type='select' id='status' default = 'denied'}>{_p var='Denied'}</option>
                    </select>
                </div>
                <div class="form-inline">
                    <div class="form-group">
                        <label for="js_from_date_listing">
                            {_p var='from_date'}:
                        </label>
                    </div>
                </div>
                <div class="form-inline">
                    <div class="form-group yncaffiliate_datetime_picker_parent">
                        <div class="form-group js_from_select">
                            {select_date prefix='from_' id='_from' start_year='-10' end_year='+10' field_separator=' / '
                            field_order='MDY' default_all=true }
                        </div>
                    </div>
                </div>
                <div class="form-inline">
                    <div class="form-group">
                        <label for="js_to_date_listing">
                            {_p var='to_date'}:
                        </label>
                    </div>
                </div>
                <div class="form-inline">
                    <div class="form-group yncaffiliate_datetime_picker_parent">
                        <div class="form-group js_to_select">
                            {select_date prefix='to_' id='_to' start_year='-10' end_year='+10' field_separator=' / '
                            field_order='MDY' default_all=true }
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <input type="submit" class="btn btn-primary" value="{_p var='submit'}"/>
                <input type="button" class="btn btn-default" value="{_p var='reset'}" onclick="window.location = '{url link='admincp.yncaffiliate.manage-request'}'"/>
            </div>
        </form>
    </div>
    {if count($aItems) > 0}
        <form method="post" id="yncaffiliate_request_list" action="">
            <div class="table-responsive">
                <table class="table table-admin" align='center'>
                    <thead>
                        <tr>
                            <th class="" style="width: 20px;"></th>
                            <th class="w100">{_p var='affiliate_name'}</th>
                            <th class="">{_p var='request_points'}</th>
                            <th class="">{_p var='request_amount'}</th>
                            <th class="">{_p var='request_currency'}</th>
                            <th class="">{_p var='status'}</th>
                            <th class="w200">{_p var='request_message'}</th>
                            <th class="w200">{_p var='payment_method'}</th>
                            <th class="w160">{_p var='request_date'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$aItems key=iKey item=aItem}
                            <tr>
                                <td class="t_center">
                                    {if $aItem.request_status == 'waiting'}
                                        <a href="#" class="js_drop_down_link" title=""></a>
                                        <div class="link_menu">
                                            <ul>
                                                <li><a href="{url link='admincp.yncaffiliate.approve-request' rid=$aItem.request_id}" class="sJsConfirm">{_p var='Approve'}</a></li>
                                                <li><a href="{url link='admincp.yncaffiliate.action-request' rid=$aItem.request_id status='denied'}" class="popup">{_p var='Deny'}</a></li>
                                                <li><a href="{url link='admincp.yncaffiliate.manage-request' delete=$aItem.request_id}" class="sJsConfirm">{_p var='Delete'}</a></li>
                                            </ul>
                                        </div>
                                    {/if}
                                </td>
                                <td class="w100">{$aItem.full_name}</td>
                                <td>{$aItem.request_points|number_format:2}</td>
                                <td>{$aItem.request_amount|number_format:2}</td>
                                <td>{$aItem.request_currency}</td>
                                <td>{_p var=$aItem.request_status}</td>
                                <td class="w200">{$aItem.request_reason|clean}</td>
                                <td class="w200">{$aItem.request_method_title|clean}</td>
                                <td class="w160">{$aItem.time_stamp|date:'core.global_update_time'}</td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
            {pager}
        </form>
    {else}
        <div class="p_4">{_p var='no_request_found'}</div>
    {/if}
</div>

{literal}
<script language="JavaScript" type="text/javascript">
    $Behavior.ynaffInitializeStatisticJs = function(){
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
    $Behavior.onLoadManagePage = function(){
        if($('.apps_menu').length == 0) return false;
        $('.apps_menu > ul').find('li:eq(8) a').addClass('active');
    }
</script>
{/literal}