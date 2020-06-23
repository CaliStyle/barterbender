<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" action="{url link='admincp.ynfeed.manage-filter'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            {_p('Manage Filters')}
        </div>
        {if count($aFilters)}
            <div class="table-responsive flex-sortable">
                <table id="_sort" class="table table-bordered" data-sort-url="{url link='ynfeed.admincp.manage-filter.order'}">
                    <thead>
                    <tr>
                        <th class="w40"></th>
                        <th class="w60"></th>
                        <th>{_p var='filter_name'}</th>
                        <th>{_p var='module'}</th>
                        <th class="t_center w80">{_p var='show'}</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$aFilters key=iKey item=aFilter}
                        <tr id="js_row{$aFilter.filter_id}" class="{if is_int($iKey/2)} tr{else}{/if}" data-sort-id="{$aFilter.filter_id}">
                            <td class="t_center">
                                <i class="fa fa-sort"></i>
                            </td>
                            <td class="t_center" style="text-align: center">
                                <a href="#" class="js_drop_down_link" title="{_p var='manage'}"></a>
                                <div class="link_menu">
                                    <ul>
                                        <li><a href="{url link='admincp.ynfeed.add-filter' edit_id=$aFilter.filter_id}">{_p var="edit"}</a></li>
                                        {if !$aFilter.is_default}
                                        <li><a href="javascript:void($.ajaxCall('ynfeed.AdminDeleteFilter','delete={$aFilter.filter_id}'));" class="sJsConfirm">{_p var='delete'}</a></li>
                                        {/if}
                                    </ul>
                                </div>
                            </td>
                            <td id="js_blog_edit_title{$aFilter.filter_id}">
                                {softPhrase var=$aFilter.title}
                            </td>
                            <td>{softPhrase var=$aFilter.module_id}</td>
                            <td class="t_center">
                                <div class="js_item_is_active"{if !$aFilter.is_show} style="display:none;"{/if}>
                                    <a href="#?call=ynfeed.toggleFilter&amp;id={$aFilter.filter_id}&amp;active=0" class="js_item_active_link" title="{_p var='hide'}"></a>
                                </div>
                                <div class="js_item_is_not_active"{if $aFilter.is_show} style="display:none;"{/if}>
                                    <a href="#?call=ynfeed.toggleFilter&amp;id={$aFilter.filter_id}&amp;active=1&amp;" class="js_item_active_link" title="{_p var='show'}"></a>
                                </div>
                            </td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
        {else}
            <div class="p_4">
                {_p var='no_filters_have_been_created'} <a href="{url link='admincp.ynfeed.add-filter'}">{_p var='add_filter'}</a>.
            </div>
        {/if}
    </div>
</form>