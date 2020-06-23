<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{literal}
<script type="text/javascript">
    $Behavior.coreDragInit = function() {
        Core_drag.init({
            table: '#js_drag_drop', ajax: 'fevent.categoryOrdering'
        });
    }
</script>
{/literal}

{if !count($aCategories)}
    <div class="alert alert-danger">
        {_p var='no_categories_found'}
    </div>
{else}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <a href="{url link='admincp.app' id='Core_Events'}">
                    {_p var='categories'}
                </a>
            </div>
        </div>
        <div class="table-responsive flex-sortable">
            <table class="table table-bordered" id="js_drag_drop" cellpadding="0" cellspacing="0">
                <thread>
                    <tr>
                        <th class="w30"></th>
                        <th class="w30"></th>
                        <th>{_p var='name'}</th>
                        <th class="t_center w140">{_p var='sub_categories'}</th>
                        <th class="t_center w140">{_p var='total_events'}</th>
                        <th class="t_center" style="width:60px;">{_p var='active'}</th>
                    </tr>
                </thread>
                <tbody>
                    {foreach from=$aCategories key=iKey item=aCategory}
                        <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                            <td class="drag_handle"><input type="hidden" name="val[ordering][{$aCategory.category_id}]" value="{$aCategory.ordering}" /></td>
                            <td class="t_center">
                                <a href="#" class="js_drop_down_link" title="{_p var='Manage'}"></a>
                                <div class="link_menu">
                                    <ul>
                                        <li><a href="{url link='admincp.fevent.add' id=$aCategory.category_id}">{_p var='edit'}</a></li>
                                        {if isset($aCategory.categories) && ($iTotalSub = count($aCategory.categories))}
                                        <li><a href="{url link='admincp.fevent' sub={$aCategory.category_id}">{_p var='manage_sub_categories_total' total=$iTotalSub}</a></li>
                                        {/if}
                                        <li><a href="{url link='admincp.fevent' delete=$aCategory.category_id}" class="sJsConfirm" data-message="{_p var='are_you_sure'}">{_p var='delete'}</a></li>

                                    </ul>
                                </div>
                            </td>
                            <td class="td-flex">
                                {softPhrase var=$aCategory.name}
                            </td>
                            <td class="t_center w140">
                                {if isset($aCategory.categories) && ($iTotalSub = count($aCategory.categories))}
                                <a href="{url link='admincp.fevent' sub={$aCategory.category_id}" class="">{$iTotalSub}</a>
                                {else}
                                0
                                {/if}
                            </td>
                            <td class="t_center">
                                <a href="{$aCategory.link}">{$aCategory.numberItems}</a>
                            </td>
                            <td class="w80 on_off">
                                <div class="js_item_is_active"{if !$aCategory.is_active} style="display:none;"{/if}>
                                <a href="#?call=fevent.updateActivity&amp;id={$aCategory.category_id}&amp;active=0&amp;sub={if $bSubCategory}1{else}0{/if}" class="js_item_active_link" title="{_p var='deactivate'}"></a>
                                </div>
                                <div class="js_item_is_not_active"{if $aCategory.is_active} style="display:none;"{/if}>
                                <a href="#?call=fevent.updateActivity&amp;id={$aCategory.category_id}&amp;active=1&amp;sub={if $bSubCategory}1{else}0{/if}" class="js_item_active_link" title="{_p var='activate'}"></a>
                                </div>
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>

{/if}