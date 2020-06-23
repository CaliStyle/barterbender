<?php 
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_FoxFeedsPro
 * @version        3.02
 * 
 */
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {phrase var='categories'}
        </div>
    </div>

    <div class="table-responsive flex-sortable">
    <table class="table table-bordered" id="js_drag_drop" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th></th>
                <th class="w20"></th>
                <th>{phrase var='name'}</th>
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
                            <li><a href="{url link='admincp.foxfeedspro.addcategory' id=$aCategory.category_id}">{phrase var='edit'}</a></li>
                            {if isset($aCategory.categories) && ($iTotalSub = count($aCategory.categories))}
                            <li><a href="{url link='admincp.foxfeedspro.categories' sub={$aCategory.category_id}">{phrase var='manage_sub_categories_total' total=$iTotalSub}</a></li>
                            {/if}
                            <li><a href="{url link='admincp.foxfeedspro.categories' delete=$aCategory.category_id}" class="sJsConfirm" data-message="{phrase var='are_you_sure'}">{phrase var='delete'}</a></li>
                        </ul>
                    </div>
                </td>
                <td>
                    {if Phpfox::isPhrase($this->_aVars['aCategory']['name'])}
                    {phrase var=$aCategory.name}
                    {else}
                    {$aCategory.name|convert}
                    {/if}
                </td>
                <td class="t_center">
                    <div class="js_item_is_active"{if !$aCategory.is_active} style="display:none;"{/if}>
                        <a href="#?call=foxfeedspro.updateActivity&amp;id={$aCategory.category_id}&amp;active=0&amp;sub={if $bSubCategory}1{else}0{/if}" class="js_item_active_link" title="{phrase var='deactivate'}"></a>
                    </div>
                    <div class="js_item_is_not_active"{if $aCategory.is_active} style="display:none;"{/if}>
                        <a href="#?call=foxfeedspro.updateActivity&amp;id={$aCategory.category_id}&amp;active=1&amp;sub={if $bSubCategory}1{else}0{/if}" class="js_item_active_link" title="{phrase var='activate'}"></a>
                    </div>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>
