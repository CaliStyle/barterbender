<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 16:03
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<!-- Filter Search Form Layout -->
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p('Search Filter')}
        </div>
    </div>
    <form method="get" enctype="multipart/form-data" action="">
        <div class="panel-body">
            <div class="extra_info">
                {_p var='affiliate_client_backend_description'}
            </div>
            <div class="form-group">
                <label for="affiliate_name">
                    {_p('Affiliate Name')}:
                </label>
                <input class="form-control" type="text" name="search[affiliate_name]" value="{value type='input' id='affiliate_name'}" id="affiliate_name" size="50"/>
            </div>
            <div class="form-inline">
                <div class="form-group">
                    <label for="js_from_date_listing">
                        {_p('Register From')}:
                    </label>
                    <div class="js_date_select">
                        <input class="form-control" name="search[fromdate]" id="js_from_date_listing" type="text" value="{if $aForms.fromdate}{$aForms.fromdate}{/if}" />
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
                        <input class="form-control"  name="search[todate]" id="js_to_date_listing" type="text" value="{if $aForms.todate}{$aForms.todate}{/if}" />
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
            <input type="button" value="{_p('Reset')}" class="btn btn-default" onclick="window.location = '{url link='admincp.yncaffiliate.affiliate-client'}'">
        </div>
    </form>
</div>
{if count($aItems) > 0}
    <form method="post" id="yncaffiliate_list" action="">
        <div class="table-responsive">
            <table class="table table-admin">
                <!-- Table rows header -->
                <thead>
                    <tr>
                        <th class="w180">{_p var='affiliate_name'}</th>
                        <th class="w180">{_p var='client_name'}</th>
                        <th class="">{_p var='registration_date'}</th>
                        <th class="">{_p var='referring_urls'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$aItems key=iKey item=aItem}
                        <tr>
                            <td class="w180" align="center">
                                <a href="{url link=$aItem.aff_user_name}" target="_blank">{$aItem.aff_name|clean}</a>
                            </td>
                            <td class="w180" align="center">
                                <a href="{url link=$aItem.client_user_name}" target="_blank">{$aItem.client_name|clean}</a>
                            </td>
                            <td align="center">
                                {$aItem.time_stamp|date:'core.extended_global_time_stamp'}
                            </td>
                            <td>
                                {if !empty($aItem.target_url)}
                                    {$aItem.target_url}
                                {else}
                                    {_p var='via_invitation'}
                                {/if}
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
        {pager}
    </form>
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
        $('.apps_menu > ul').find('li:eq(12) a').addClass('active');
    }
</script>
{/literal}