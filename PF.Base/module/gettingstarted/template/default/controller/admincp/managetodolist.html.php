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
    .description_content ul li
    {
        list-style: square inside none;
    }
    .description_content ol li
    {
        list-style: decimal inside none;
    }
    .description_content li
    {
        list-style: disc inside none;
    }
</style>
{/literal}

<form action="{url link='admincp.gettingstarted.managetodolist'}" method="post">
    <div class="panel panel-default">
        <div class="panel-heading">
            {phrase var='gettingstarted.todo_list'}
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>
                    {phrase var='gettingstarted.language'}:
                </label>
                <select id="language_id" name="lang" onchange="$('form').submit();" class="form-control">
                    {foreach from=$aLanguages item=aLanguage}
                    <option value="{$aLanguage.language_id}" {if $aLanguage.language_id==$sLanguage_id}selected {/if} >{$aLanguage.title}</option>
                    {/foreach}
                </select>
                <span id="loading"></span>
            </div>
        </div>

        <div class="table-responsive flex-sortable">
            <table id="js_drag_drop" class="table table-bordered">
                <thead>
                <tr>
                    <th style="width:20px"></th>
                    <th style="width:220px">{phrase var='gettingstarted.title'}</th>
                    <th>{phrase var='gettingstarted.description'}</th>
                    <th style="width:70px">{phrase var='gettingstarted.actions'}</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$aTodoItems key=iKey item=aTodoItem}
                <tr {if is_int($iKey/2)} class="tr"{else}{/if} id="tr_{$iKey}">
                <td class="drag_handle" style="cursor: move;" title="{phrase var='gettingstarted.drag_to_change_order'}">
                    <input type="hidden" name="val[ordering][{$aTodoItem.todolist_id}]" value="{$aTodoItem.ordering}" />
                </td>
                <td>
                    {$aTodoItem.title}
                </td>
                <td class="description_content item_view_content">
                    {$aTodoItem.description_parsed|shorten:300:'expand':true}
                </td>
                <td>
                    <a href="{permalink module='admincp.gettingstarted.addtodolist' id=$aTodoItem.todolist_id}" title="{phrase var='core.edit'}">
                        {img theme='misc/page_white_edit.png' style='vertical-align:middle;'}</a>
                    &nbsp;
                    <a href="{url link='admincp.gettingstarted.managetodolist' delete=$aTodoItem.todolist_id lang=$sLanguage_id}" onclick="return confirm('{phrase var='core.are_you_sure'}');" title="{phrase var='core.delete'}">
                        {img theme='misc/delete.png' style='vertical-align:middle;'}</a>
                </td>
                {foreachelse}
                <tr>
                    <td colspan="4" class="t_center"> {phrase var='gettingstarted.do_not_have_todo_list_in_this_language'} </td>
                </tr>
                {/foreach}
            </tbody>
            </table>
        </div>
        <div class="panel-footer">
            <div class="table_bottom">
                <input type="submit" value="{phrase var='gettingstarted.delete_all'}" name="delete_all" class="btn btn-danger" onclick="return confirm('{phrase var='gettingstarted.are_you_sure_to_delete_all_items_in_this_language'}');" />
            </div>
        </div>
    </div>
</form>
