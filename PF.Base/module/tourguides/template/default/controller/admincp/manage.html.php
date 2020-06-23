<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>

{literal}
<style>
    .t_center{
        vertical-align:middle;
    }
    .table_right input
    {
        width:250px;
    }
    .no_tour
    {
        margin:10px;
        padding:4px;
    }
</style>
{/literal}
<form method="get" action="{url link='admincp.tourguides.manage'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='search_filter'}
            </div>
        </div>
        <div class="panel-body">
        <div class="form-group">
            <label>
                {_p var='search_for_text'}:
            </label>
                {$aFilters.search}
        </div>
        </div>
        <div class="panel-footer">
            <input type="submit" name="search[submit]" value="{phrase var='core.submit'}" class="btn btn-primary" />
            <input type="button" onclick="window.location.href = '{url link='admincp.tourguides.manage'}'" name="search[reset]" value="{phrase var='core.reset'}" class="btn btn-default" />
        </div>
    </div>
</form>
{if count($aTours) <= 0 }
    {if $bSearch}
        <div class="extra_info">{_p var='no_tour_found'}</div>
    {else}
        <div class="extra_info">{_p var='no_tour_added' url=$sUrlAdded}</div>
    {/if}
{else}
    {pager}
    <form action="{url link ='admincp.tourguides.manage'}" method="post">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    {_p var='mange_tour_guides'}
                </div>
            </div>
            <div class="table-responsive">
                <table id="js_drag_drop" class="table table-bordered" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="w20 t_center"><input type="checkbox" name="val[id]" value="" id="js_check_box_all" class="main_checkbox" /></th>
                            <th class="t_center w120"  style="width:140px;">{_p var='name'}</th>
                            <th class="t_center w200">{_p var='url'}</th>
                            <th class="t_center w80">{_p var='status'}</th>
                            <th class="t_center w120" >{_p var='actions'}</th>
                            <th class="t_center w80">{_p var='active'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$aTours key=iKey item=aTour}
                        <tr id="js_row{$aTour.id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                            <td class="t_center w20"><input type="checkbox" name="idtour[]" class="checkbox" value="{$aTour.id}" id="js_id_row{$aTour.id}" /></td>
                            <td class="w120">{$aTour.name} (<strong>{$aTour.total_steps}</strong>)</td>
                            <td class="">
                               <a href="{$aTour.url}" target="_blank" title="{$aTour.url}">{$aTour.url|shorten:30:'...'}</a>
                            </td>
                            <td class="t_center">
                                {if isset($aTour.is_complete) && $aTour.is_complete == 1}
                                    <a href="{url link='admincp.tourguides.manage' complete=$aTour.id}" title="{_p var='uncompleted_this_tour_guide'}">{_p var='completed'}</a>
                                {else}
                                     <a href="{url link='admincp.tourguides.manage' uncomplete=$aTour.id}" title="{_p var='complete_this_tour_guide'}">{_p var='uncompleted'}</a>
                                {/if}
                            </td>
                            <td class="t_center">
                                <a href="{url link='admincp.tourguides.add' id=$aTour.id}">{phrase var='core.edit'}</a> | <a  href="{url link='admincp.tourguides.manage' delete=$aTour.id}" class="sJsConfirm">{phrase var='core.delete'}</a> | <a  href="{url link='admincp.tourguides.manage' reset=$aTour.id }" title="">{phrase var='core.reset'}</a>
                            </td>
                            <td class="t_center">
                                <div class="js_item_is_active"{if !$aTour.is_active} style="display:none;"{/if}>
                                    <a href="#?call=tourguides.updateActivity&amp;id={$aTour.id}&amp;active=0" class="js_item_active_link" title="{_p var='deactivate'}"></a>
                                </div>
                                <div class="js_item_is_not_active"{if $aTour.is_active} style="display:none;"{/if}>
                                    <a href="#?call=tourguides.updateActivity&amp;id={$aTour.id}&amp;active=1" class="js_item_active_link" title="{_p var='activate'}"></a>
                                </div>
                            </td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
            <div class="panel-footer">
                <input type="submit" name="delete" value="{_p var='delete_selected'}" class="sJsConfirm delete btn btn-danger sJsCheckBoxButton disabled" disabled="true" />
            </div>
        </div>
    </form>
    {pager}
{/if}