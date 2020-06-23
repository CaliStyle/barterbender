<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="panel panel-default dont-unbind-children">
    <div class="panel-heading">
        <div class="panel-title">
            {phrase var='categories'}
        </div>
    </div>
    <div class="panel-body">
        <div class="table-responsive flex-sortable">
            <table id="js_drag_drop" class="table table-bordered">
                <thead>
                <tr>
                    <th></th>
                    <th class="w20"></th>
                    <th>{phrase var='name'}</th>
                    {if !$bSubCategory}
                    <th class="t_center w160">{phrase var='image'}</th>
                    {/if}
                    <th class="t_center w60">{phrase var='active'}</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$aCategories key=iKey item=aCategory}
                <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                    <td class="drag_handle"><input type="hidden" name="val[ordering][{$aCategory.category_id}]" value="{$aCategory.ordering}" /></td>
                    <td class="t_center">
                        <a href="#" class="js_drop_down_link" title="{phrase var='Manage'}"></a>
                        <div class="link_menu">
                            <ul>
                                <li><a href="{url link='admincp.advancedmarketplace.add' id=$aCategory.category_id}">{phrase var='edit'}</a></li>
                                {if isset($aCategory.categories) && ($iTotalSub = count($aCategory.categories))}
                                <li><a href="{url link='admincp.advancedmarketplace' sub={$aCategory.category_id}">{phrase var='manage_sub_categories_total' total=$iTotalSub}</a></li>
                                {/if}
                                {if !empty($aCategory.numberItems)}
                                <li><a href="" class="sJsConfirm" data-message="{phrase var='you_can_not_delete_this_category_because_there_are_many_items_related_to_it'}">{phrase var='delete'}</a></li>
                                {else}
                                <li><a href="{url link='admincp.advancedmarketplace' delete=$aCategory.category_id}" class="sJsConfirm" data-message="{phrase var='are_you_sure'}">{phrase var='delete'}</a></li>
                                {/if}
                                <li><a href="#" onclick="return $Core.advancedmarketplace.action(this, 'manage_customfield',{$aCategory.category_id});">{phrase var='advancedmarketplace.manage_custom_fields'}</a></li>
                            </ul>
                        </div>
                    </td>
                    <td>
                        {$aCategory.name}
                    </td>
                    {if !$bSubCategory}
                    <td class="text-center">
                        {if $aCategory.server_id == '-1'}
                            {img path='core.path_actual' file='PF.Site/Apps/p-advmarketplace/assets/image/default/'.$aCategory.image_path}
                        {else}
                            {if !empty($aCategory.image_path)}
                            <a href="{img server_id=$aCategory.server_id path='advancedmarketplace.url_pic' file=$aCategory.image_path suffix='_250_square' return_url=true}" class="thickbox">
                                {img server_id=$aCategory.server_id path='advancedmarketplace.url_pic' file=$aCategory.image_path suffix='_120_square' style='width: 50px; height: 50px;' max_width='50' max_height='50'}
                            </a>
                            {else}
                            {img path='core.path_actual' file='PF.Site/Apps/p-advmarketplace/assets/image/default/default_category.png' max_width='50' max_height='50'}
                            {/if}
                        {/if}
                    </td>
                    {/if}
                    <td class="t_center">
                        <div class="js_item_is_active"{if !$aCategory.is_active} style="display:none;"{/if}>
                        <a href="#?call=advancedmarketplace.updateActivity&amp;id={$aCategory.category_id}&amp;active=0" class="js_item_active_link" title="{phrase var='deactivate'}"></a>
        </div>
        <div class="js_item_is_not_active"{if $aCategory.is_active} style="display:none;"{/if}>
        <a href="#?call=advancedmarketplace.updateActivity&amp;id={$aCategory.category_id}&amp;active=1" class="js_item_active_link" title="{phrase var='activate'}"></a>
    </div>
    </td>
    </tr>
    {/foreach}
    </tbody>
    </table>
</div>
</div>
</div>
