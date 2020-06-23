<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package  		Module_Contact
 * @version 		$Id: index.html.php 1424 2010-01-25 13:34:36Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" action="{url link='admincp.contactimporter.statisticsbydate'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='admincp.search_filter'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>
                    {_p var='from'}
                </label>
                <div class="js_event_select " style="position: relative">
                    {select_date prefix='start_' id='_start' start_year='current_year' end_year='+1' field_separator=' / ' field_order='YMD' default_all=true  time_separator='event.time_separator'}
                </div>
            </div>
            <div class="form-group">
                <label>
                    {_p var='to'}
                </label>
                <div class="js_event_select " style="position: relative">
                    {select_date prefix='end_' id='_end' start_year='current_year' end_year='+1' field_separator=' / ' field_order='YMD' default_all=true  time_separator='event.time_separator'}
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" name="submit" value="{_p var='core.submit'}" class="btn btn-primary" />
        </div>
    </div>
</form>
{if count($items) > 0}
<form action="{url link='admincp.contactimporter.invitations'}" method="post" onsubmit="return getsubmit();" >
	<div class="panel panel-default table-responsive">
        <table class="table table-bordered ">
            <thead>
                <tr>
                    <th>{_p var='admincp_statistics_date'}</th>
                    <th>{_p var='admincp_providers_totalinvitations'}</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$items key=iKey item=date}
                <tr class="{if is_int($iKey/2)} tr{else}{/if}">
                    <td>{$date.date|clean}</td>
                    <td>{$date.total|clean}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</form>
{pager}
{else}
<div class="extra_info p-4">
	{_p var='there_are_no_invitations'}
</div>
{/if}
<script type="text/javascript">
	{ literal
	}
	$Behavior.resetDatepicker = function() {
		$('.js_event_select .js_date_picker').datepicker('option', 'maxDate', '+1y');
	}; {/literal
	}
</script>
{literal}
<style type="text/css">
	.table .table_right input[type="text"]
	{
		width: auto;
        margin: 0;
	}
</style>
{/literal}