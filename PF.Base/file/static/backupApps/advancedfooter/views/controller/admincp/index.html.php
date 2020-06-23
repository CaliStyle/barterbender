<?php
/**
 * @copyright		[FOXEXPERT_COPYRIGHT]
 * @author  		Belan Ivan
 * @package  		App_AdvancedFooter
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            <a href="{url link='admincp.advancedfooter.index'}">{_p('Manage Menu')}</a>
            {if isset($sParentCategory)}
                » {$sParentCategory}
            {/if}
        </div>
    </div>
    <div style="margin:10px;">
        !Important - Add Maximum 4 main level menus, as more numbers will not work incorrectly. <br/>
        Sub menus you may find in left cog icon under main level menus.<br/>
        Leave Link / Direct link empty if you do not want add link to main level menu
    </div>
    <div style="margin: 10px;">
        <a href="{url link='admincp.advancedfooter.addmenu'}" class="button btn btn-primary popup">
            {_p var='Add Menu'}
        </a>
    </div>
    {if !empty($aCategories)}
    <table id="_sort" data-sort-url="{url link='advancedfooter.admincp.menu.order'}" class="table table-admin">
        <thead>
        <tr>
            <th style="width:20px"></th>
            <th style="width:20px"></th>
            <th>{_p('Name')}</th>
            <th>{_p('Link')}</th>
            <th>{_p('Direct Link')}</th>
            <th class="text-center" style="width:60px;">{_p var='Active'}</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$aCategories key=iKey item=aCategory}
        <tr class="tr" data-sort-id="{$aCategory.category_id}">
            <td class="t_center">
                <i class="fa fa-sort"></i>
            </td>
            <td class="text-center">
                <a class="js_drop_down_link" title="Manage"></a>
                <div class="link_menu">
                    <ul>
                        <li><a class="popup" href="{url link='admincp.advancedfooter.addmenu' edit=$aCategory.category_id}">{_p('Edit')}</a></li>
                        {if isset($aCategory.sub) && ($iTotalSub = count($aCategory.sub))}
                        <li><a href="{url link='admincp.advancedfooter.index' sub={$aCategory.category_id}">{_p('Manage Sub Menus')} <span class="badge" style="display: initial;">{$iTotalSub}</span></a></li>
                        {/if}
                        <li>
                            <a class="popup" href="{url link='admincp.advancedfooter.delete-menu' delete=$aCategory.category_id}">{_p('Delete')}</a>
                        </li>
                    </ul>
                </div>
            </td>
            <td>
                {softPhrase var=$aCategory.name}
            </td>
            <td>
                {$aCategory.link}
            </td>
            <td>
                {$aCategory.direct_link}
            </td>
            <td class="text-center on_off">
                <div class="js_item_is_active"{if !$aCategory.is_active} style="display:none;"{/if}>
                    <a href="#?call=advancedfooter.updateMenuActivity&amp;id={$aCategory.category_id}&amp;active=0" class="js_item_active_link" title="{_p var='Deactivate'}"></a>
                </div>
                <div class="js_item_is_not_active"{if $aCategory.is_active} style="display:none;"{/if}>
                    <a href="#?call=advancedfooter.updateMenuActivity&amp;id={$aCategory.category_id}&amp;active=1" class="js_item_active_link" title="{_p var='Activate'}"></a>
                </div>
            </td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    {else}
        <div style="margin:15px;">
                {_p var='There are no menu, you may add them'}
        </div>
    {/if}
</div>
