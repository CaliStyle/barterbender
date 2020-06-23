<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 16:03
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<link rel="stylesheet" href="{param var='core.path_actual'}PF.Site/Apps/ync-affiliate/assets/css/default/default/backend.css">
<form method="get" enctype="multipart/form-data" action="">
    <div class="panel panel-default">
        <!-- Filter Search Form Layout -->
        <div class="panel-heading">
            <div class="panel-title">
                {_p var="search_filter"}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="affiliate_name">{_p('Affiliate Name')}:</label>
                <input class="form-control" type="text" name="search[affiliate_name]" value="{value type='input' id='affiliate_name'}" id="affiliate_name" size="50"/>
            </div>

            <div class="form-group">
                <label for="status">
                    {_p('Status')}:
                </label>
                <select name="search[status]" id="status" class="form-control">
                    <option value="">
                        {_p('Any')}
                    </option>
                    <option value="pending" {value type='select' id='status' default = 'pending'}>
                        {_p('Pending')}
                    </option>
                    <option value="denied" {value type='select' id='status' default = 'denied'}>
                        {_p('Denied')}
                    </option>
                    <option value="approved" {value type='select' id='status' default = 'approved'}>
                        {_p('Active')}
                    </option>
                    <option value="inactive" {value type='select' id='status' default = 'inactive'}>
                        {_p('Inactive')}
                    </option>
                </select>
            </div>
            <div class="form-inline">
                <div class="form-group">
                    <label for="js_from_date_listing">
                        {_p('Register From')}:
                    </label>
                    <div class="js_date_select">
                        <input name="search[fromdate]" class="form-control" id="js_from_date_listing" type="text" value="{if $aForms.fromdate}{$aForms.fromdate}{/if}" />
                        <a href="#" id="js_from_date_listing_anchor">
                            <img src="<?php echo Phpfox::getLib('template')->getStyle('image', 'jquery/calendar.gif'); ?>" />
                        </a>
                    </div>
                </div>
            </div>
            <div class="form-inline">
                <div class="form-group">
                    <label for="js_to_date_listing">
                        {_p('Register To')}:
                    </label>
                    <div class="js_date_select">
                        <input name="search[todate]" class="form-control" id="js_to_date_listing" type="text" value="{if $aForms.todate}{$aForms.todate}{/if}" />
                        <a href="#" id="js_to_date_listing_anchor">
                            <img src="<?php echo Phpfox::getLib('template')->getStyle('image', 'jquery/calendar.gif'); ?>" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Submit Buttons -->
        <div class="panel-footer">
            <input type="submit" id="yn_filter_affiliate_submit" name="search[submit]" value="{_p('Submit')}" class="btn btn-primary"/>
            <input id="yn_filter_affiliate_reset" type="button" value="{_p('Reset')}" class="btn btn-default" onclick="window.location = '{url link='admincp.yncaffiliate.manage-affiliate'}'">
        </div>
    </div>
</form>
{if count($aItems) > 0}
<div class="panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p('Affiliates')}
        </div>
    </div>
    <form method="post" id="yncaffiliate_list" action="">
        <div class="table-responsive ">
            <table class='table table-admin'>
                <!-- Table rows header -->
                <thead>
                    <tr>
                        <th class="table_row_header w20" ></th>
                        <th class="w20">
                            <input type="checkbox" name="val[yaid]" value="" id="js_check_box_all" class="main_checkbox" />
                        </th>
                        <th class="w180">{_p('User Name')}</th>
                        <th class="">{_p('Email')}</th>
                        <th class="">{_p('Status')}</th>
                        <th class="">{_p var='registration_date'}</th>
                    </tr>
                </thead>
                <tbody>
                {foreach from=$aItems key=iKey item=aItem}
                    <tr id="ync_affiliates_row_{$aItem.account_id}">
                        <td class="t_center">
                            <a href="#" class="js_drop_down_link" title=""></a>
                            <div class="link_menu">
                                <ul>
                                    {if $aItem.status == 'pending'}
                                    <li><a href="{url link='admincp.yncaffiliate.manage-affiliate' aid=$aItem.account_id status='approved'}" class="sJsConfirm">{_p var='Approve'}</a></li>
                                    <li><a href="{url link='admincp.yncaffiliate.manage-affiliate' aid=$aItem.account_id status='denied'}" class="sJsConfirm">{_p var='Deny'}</a></li>
                                    {elseif $aItem.status == 'approved'}
                                    <li><a href="{url link='admincp.yncaffiliate.manage-affiliate' aid=$aItem.account_id status='inactive'}" class="sJsConfirm">{_p var='Deactivate'}</a></li>
                                    {elseif $aItem.status == 'inactive'}
                                    <li><a href="{url link='admincp.yncaffiliate.manage-affiliate' aid=$aItem.account_id status='approved'}" class="sJsConfirm">{_p var='Reactivate'}</a></li>
                                    {else}
                                    <li><a href="{url link='admincp.yncaffiliate.manage-affiliate' aid=$aItem.account_id status='approved'}" class="sJsConfirm">{_p var='Approve'}</a></li>
                                    {/if}
                                    {if $aItem.status == 'pending'}
                                    <li><a href="{url link='admincp.yncaffiliate.manage-affiliate' delete=$aItem.account_id}" class="sJsConfirm">{_p var='Delete'}</a></li>
                                    {else}
                                    <li><a href="{url link='affiliate.statistics' id=$aItem.user_id}" class="no_ajax" target="_blank">{_p var='statistics'}</a></li>
                                    <li><a href="{url link='affiliate.network-clients' id=$aItem.user_id}" class="no_ajax" target="_blank">{_p var='network_clients'}</a></li>
                                    {/if}
                                    <li><a href="" onclick="return showAccountDetail({$aItem.user_id});">{_p var='view_contact_information'}</a></li>
                                </ul>
                            </div>
                        </td>
                        <td>
                          <input type="checkbox" name="yaid[]" class="checkbox" value="{$aItem.account_id}" id="js_id_row{$aItem.account_id}" />
                        </td>
                        <td class="w180" >
                            {$aItem.contact_name|clean}
                        </td>
                        <td >
                            {$aItem.contact_email}
                        </td>
                        <td>
                            {if $aItem.status == 'approved'}{_p var='active'}{else}{_p var=$aItem.status}{/if}
                        </td>
                        <td>
                            {$aItem.time_stamp|date:'core.extended_global_time_stamp'}
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
        {pager}
        <!-- Delete selected button -->
        <div class="panel-footer">
            <input type="submit" name="val[approve_selected]" id="approve_selected" disabled value="{_p('Approve Selected')}" class="sJsConfirm sJsCheckBoxButton btn btn-primary disabled"/>
            <input type="submit" name="val[deny_selected]" id="deny_selected" disabled value="{_p('Deny Selected')}" class="sJsConfirm sJsCheckBoxButton btn btn-danger disabled"/>
            <input type="submit" name="val[reactivate_selected]" id="reactivate_selected" disabled value="{_p('Reactivate Selected')}" class="sJsConfirm sJsCheckBoxButton btn btn-primary disabled"/>
            <input type="submit" name="val[deactivate_selected]" id="deactivate_selected" disabled value="{_p('Deactivate Selected')}" class="sJsConfirm sJsCheckBoxButton btn btn-danger disabled"/>
        </div>
    </form>
</div>
{else}
{_p('No accounts found')}
{/if}

<script type="text/javascript" src="{$corePath}/assets/jscript/manage.js"></script>
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
        $('.apps_menu > ul').find('li:eq(2) a').addClass('active');
    }
    var showAccountDetail = function(id)
    {
        tb_show(oTranslations['contact_information'],$.ajaxBox('yncaffiliate.showAccountDetail','user_id='+id));
        return false;
    }
</script>
{/literal}