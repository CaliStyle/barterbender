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
            <a href="{url link='admincp.advancedfooter.social'}">{_p('manage_categories')}</a>
        </div>
        <div style="margin: 10px 0;">
            <a href="{url link='admincp.advancedfooter.addsocial'}" class="button btn btn-primary popup">
                {_p var='Add Social icon'}
            </a>
        </div>
    </div>
    {if !empty($aCategories)}
        <table id="_sort" data-sort-url="{url link='advancedfooter.admincp.social.order'}" class="table table-admin">
            <thead>
            <tr>
                <th style="width:20px"></th>
                <th style="width:20px"></th>
                <th>{_p('Icon')}</th>
                <th>{_p('Link')}</th>
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
                                <li><a class="popup" href="{url link='admincp.advancedfooter.addsocial' edit=$aCategory.category_id}">{_p('Edit')}</a></li>
                                <li>
                                    <a class="popup" href="{url link='admincp.advancedfooter.delete-social' delete=$aCategory.category_id}">{_p('Delete')}</a>
                                </li>
                            </ul>
                        </div>
                    </td>
                    <td>
                        {$aCategory.icon}
                    </td>
                    <td>
                        {$aCategory.link}
                    </td>
                    <td class="text-center on_off">
                        <div class="js_item_is_active"{if !$aCategory.is_active} style="display:none;"{/if}>
                            <a href="#?call=advancedfooter.updateActivity&amp;id={$aCategory.category_id}&amp;active=0" class="js_item_active_link" title="{_p var='Deactivate'}"></a>
                        </div>
                        <div class="js_item_is_not_active"{if $aCategory.is_active} style="display:none;"{/if}>
                            <a href="#?call=advancedfooter.updateActivity&amp;id={$aCategory.category_id}&amp;active=1" class="js_item_active_link" title="{_p var='Activate'}"></a>
                        </div>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    {else}
        <div style="padding: 15px;">
            {_p var='There are no social icons added'}
        </div>
    {/if}
</div>
