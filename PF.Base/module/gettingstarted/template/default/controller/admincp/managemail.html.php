<?php
/**
 *
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_GettingStarted
 * @version          2.01
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{literal}
<style type="text/css">
#public_message, #core_js_messages
{
	margin-top:30px;
}
</style>
{/literal}

<form method="get" accept-charset="utf-8"  action="{url link='admincp.gettingstarted.managemail'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var ='gettingstarted.search_filters'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>
                    {phrase var='gettingstarted.keyword'}
                </label>
                {$aFilters.title}
            </div>
            <div class="form-group">
                <label>
                    {phrase var='gettingstarted.module'}:
                </label>
                {$aFilters.type}
            </div>
        </div>
        <div class="panel-footer">
            <input type="hidden" value="search_" name="se"/>
            <input type="submit" name="search[submit]" value="{phrase var='core.submit'}" class="btn btn-primary" />
            <input type="button" onclick="window.location.href = '{url link='admincp.gettingstarted.managemail'}';" name="search[reset]" value="{phrase var='core.reset'}" class="btn btn-default" />
        </div>
    </div>
</form>

{if count($aCategories)}

<form method="post" action="{url link='admincp.gettingstarted.managemail'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='gettingstarted.manage_mails'}
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th style="width:10px;"><input type="checkbox" name="val[id]" value="" id="js_check_box_all" class="main_checkbox" /></th>
                    <th>{phrase var='gettingstarted.module'}</th>
                    <th>{phrase var='gettingstarted.time'}</th>
                    <th>{phrase var='gettingstarted.subtitle'}</th>
                    <th>{phrase var='gettingstarted.message'}</th>
                    <th>{phrase var='gettingstarted.active'}</th>
                    <th>{phrase var='gettingstarted.edit'}</th>
                </tr>
                </thead>
                <tbody>
                    {foreach from=$aCategories key=iKey item=aCategory}
                    <tr id="js_row{$aCategory.scheduledmail_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                        <td><input type="checkbox" name="id[]" class="checkbox" value="{$aCategory.scheduledmail_id}" id="js_id_row{$aCategory.scheduledmail_id}" /></td>
                        <td>{$aCategory.scheduledmail_name}</td>
                        <td>{$aCategory.time}</td>
                        <td>{$aCategory.name}</td>
                        <td>{$aCategory.message_parsed|shorten:300:'expand':true}</td>
                        <td style="width: 60px;text-align: center;">
                            <div class="js_item_is_active" {if $aCategory.active==0}style="display: none"{else}style="display: block"{/if}>
                                <a title="{phrase var='gettingstarted.deactivate'}" class="js_item_active_link" href="#?call=gettingstarted.updateScheduledActivity&amp;id={$aCategory.scheduledmail_id}&amp;active=0"></a>
                            </div>
                            <div class="js_item_is_not_active" {if $aCategory.active==1}style="display: none"{else}style="display: block"{/if}>
                                <a title="{phrase var='gettingstarted.activate'}" class="js_item_active_link" href="#?call=gettingstarted.updateScheduledActivity&amp;id={$aCategory.scheduledmail_id}&amp;active=1"></a>
                            </div>
                        </td>
                        <td style="width: 45px;"><a href="{url link='admincp/gettingstarted/editscheduledmail'}id_{$aCategory.scheduledmail_id}">{phrase var='gettingstarted.edit'}</a></td>
                     </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
            <div class="table_bottom">
                <input type="submit" name="delete" value="{phrase var='gettingstarted.delete_selected'}" class="sJsConfirm delete btn btn-danger sJsCheckBoxButton disabled" disabled="true" />
            </div>
        </div>
    </div>
</form>
{pager}
{else}
	{if $bIsSearch}
		<div class="error-message">{phrase var='gettingstarted.no_search_results_found'}</div>
	{else}
	<div class="p_4">
            {phrase var='gettingstarted.no_mails_have_been_created'} <a href="{url link='admincp.gettingstarted.addscheduledmail'}">{phrase var='gettingstarted.create_one_now'}</a>
	</div>
	{/if}
{/if}