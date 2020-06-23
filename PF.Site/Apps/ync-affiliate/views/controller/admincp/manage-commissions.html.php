<div class="yncaffiliate_manage_transaction">
    <form method="get" enctype="multipart/form-data" action="">
        <div class="panel panel-default">
            <!-- Filter Search Form Layout -->
            <div class="panel-heading">
                <div class="panel-title">
                    {_p('Search Filter')}
                </div>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="client_name">
                        {_p var='client_name'}
                    </label>
                    <input id="client_name" class="form-control" type="text" name="search[client_name]" value="{value type='input' id='client_name'}">
                </div>
                <div class="form-group">
                    <label for="affiliate_name">
                        {_p var='affiliate_name'}
                    </label>
                    <input class="form-control" id="affiliate_name" type="text" name="search[affiliate_name]" value="{value type='input' id='affiliate_name'}">
                </div>
                <div class="form-group">
                    <label for="payment_type">
                        {_p var='payment_type'}
                    </label>
                    <select name="search[payment_type]" id="payment_type" class="form-control">
                        <option value="">{_p var='any'}</option>
                        {if count($aRules)}
                            {foreach from=$aRules item=aRule key=iKey}
                                <option value="{$aRule.rule_id}" {value type='select' id='payment_type' default=$aRule.rule_id}>{_p var=$aRule.rule_title}</option>
                            {/foreach}
                        {/if}
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">
                        {_p var='Status'}
                    </label>
                    <select class="form-control" id="status" name="search[status]">
                        <option value="">{_p var='any'}</option>
                        <option value="delaying" {value type='select' id='status' default = 'delaying'}>{_p var='Delaying'}</option>
                        <option value="waiting" {value type='select' id='status' default = 'waiting'}>{_p var='Waiting'}</option>
                        <option value="approved" {value type='select' id='status' default = 'approved'}>{_p var='Approved'}</option>
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
                <input type="button" class="btn btn-default" value="{_p var='reset'}" onclick="window.location = '{url link='admincp.yncaffiliate.manage-commissions'}'"/>
            </div>
        </div>
    </form>
    {if count($aItems) > 0}
    <form method="post" id="yncaffiliate_commission_list" action="">
        <div class="table-responsive">
            <table class='table table-admin'>
                <thead>
                    <tr>
                        <th class="w20" style="width: 20px;"></th>
                        <th class="w20" style="width: 20px;">
                            <input type="checkbox" name="val[ycid]" value="" id="js_check_box_all" class="main_checkbox" /></th>
                        </th>
                        <th class="w100">{_p var='client_name'}</th>
                        <th class="w100">{_p var='affiliate_name'}</th>
                        <th class="w100">{_p var='payment_type'}</th>
                        <th class="">{_p var='purchased_date'}</th>
                        <th class="">{_p var='purchased_currency'}</th>
                        <th class="">{_p var='purchased_amount'}</th>
                        <th class="">{_p var='commission_rate'}</th>
                        <th class="">{_p var='commission_amount'}</th>
                        <th class="">{_p var='commission_points'}</th>
                        <th class="w100">{_p var='status'}</th>
                        <th class="w200" >{_p var='reason'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$aItems key=iKey item=aItem}
                        <tr  id="ync_affiliates_row_{$aItem.commission_id}">
                            <td class="t_center">
                                {if $aItem.status != 'approved' && $aItem.status != 'denied'}
                                <a href="#" class="js_drop_down_link" title=""></a>
                                <div class="link_menu">
                                    <ul>
                                        {if $aItem.status == 'delaying'}
                                            {if $aItem.time_update == 0}
                                                <li><a href="{url link='admincp.yncaffiliate.manage-commissions' cid=$aItem.commission_id status='approved'}" class="sJsConfirm">{_p var='Approve'}</a></li>
                                            {/if}
                                            <li><a href="{url link='admincp.yncaffiliate.action-commission' cid=$aItem.commission_id status='denied'}" class="popup">{_p var='Deny'}</a></li>
                                        {elseif $aItem.status == 'waiting'}
                                            <li><a href="{url link='admincp.yncaffiliate.manage-commissions' cid=$aItem.commission_id status='approved'}" class="sJsConfirm">{_p var='Approve'}</a></li>
                                            <li><a href="{url link='admincp.yncaffiliate.action-commission' cid=$aItem.commission_id status='denied'}" class="popup">{_p var='Deny'}</a></li>
                                        {/if}
                                    </ul>
                                </div>
                                {/if}
                            </td>
                            <td align="center">
                            {if $aItem.status != 'approved' && $aItem.status != 'denied'}
                                <input type="checkbox" name="ycid[]" class="checkbox" value="{$aItem.commission_id}" id="js_id_row{$aItem.commission_id}" />
                            {/if}
                            </td>
                            <td class="w100"><a href="{url link=$aItem.client_username}">{$aItem.client_name|clean}</a></td>
                            <td class="w100"><a href="{url link=$aItem.affiliate_username}">{$aItem.affiliate_name|clean}</a></td>
                            <td class="w100">{_p var=$aItem.rule_title}</td>
                            <td>{$aItem.time_stamp|date:'core.extended_global_time_stamp'}</td>
                            <td>{$aItem.purchase_currency}</td>
                            <td align="center">{$aItem.purchase_amount|number_format:2}</td>
                            <td align="center">{$aItem.commission_rate|number_format:2}%</td>
                            <td align="center">{$aItem.commission_amount|number_format:2}</td>
                            <td align="center">{$aItem.commission_points}</td>
                            <td style="white-space: nowrap;">{_p var=$aItem.status}</td>
                            <td class="w200">{$aItem.reason|clean}</td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
        {pager}
        <div class="panel-footer">
            <textarea name="val[reason]" id="ynaf_reason_for_multi" cols="30" rows="10" hidden></textarea>
            <input type="hidden" name="val[is_multi]" id="ynaf_is_multi_submit" value="0"/>
            <input type="submit" name="val[approve_selected]" id="approve_selected" disabled value="{_p('Approve Selected')}" class="sJsConfirm sJsCheckBoxButton btn btn-primary disabled"/>
            <input type="button" name="val[deny_selected]" id="deny_selected" disabled value="{_p('Deny Selected')}" class="sJsCheckBoxButton btn btn-danger disabled popup" href="{url link='admincp.yncaffiliate.action-commission' cid=$aItem.commission_id status='multi_denied'}"/>
        </div>
    </form>
    {else}
        {_p var='no_commissions_found'}
    {/if}
</div>
{literal}
<script language="JavaScript" type="text/javascript">
    $Behavior.ynaffInitializeStatisticJs = function(){
        if (!$('input[name="ycid[]"]').length) {
            var parent = $('#yncaffiliate_commission_list');
            if (parent.length) {
                parent.find('.table.table-admin tr').find('th:eq(0), th:eq(1)').remove();
                parent.find('.table.table-admin tr').find('td:eq(0), td:eq(1)').remove();
                parent.find('.panel-footer').remove();
            }
        }
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
        $('.apps_menu > ul').find('li:eq(4) a').addClass('active');
    }
</script>
{/literal}
