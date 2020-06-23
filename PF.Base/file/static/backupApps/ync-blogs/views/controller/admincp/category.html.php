<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 30/12/2016
 * Time: 18:34
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p('Categories')}
        </div>
    </div>
    <div class="table-responsive flex-sortable">
        <table class="table table-bordered" id="_sort" data-sort-url="{url link='ynblog.admincp.category.order'}">
            <thead>
                <tr>
                    <th class="w40"></th>
                    <th class="w60"></th>
                    <th>{_p('Name')}</th>
                    <th class="t_center w120">{_p var='total_blogs'}</th>
                    <th class="t_center w80">{_p var='Active'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$aCategories key=iKey item=aCategory}
                <tr class="{if is_int($iKey/2)} tr{else}{/if}" data-sort-id="{$aCategory.category_id}">
                    <td class="t_center w40">
                        <i class="fa fa-sort"></i>
                    </td>
                    <td class="t_center w60">
                        <a href="javascript:void(0)" class="js_drop_down_link" title="Manage"></a>
                        <div class="link_menu">
                            <ul>
                                <li><a class="popup" href="{url link='ynblog.admincp.add-category' id=$aCategory.category_id}">{_p var='Edit'}</a></li>
                                {if isset($aCategory.categories) && ($iTotalSub = count($aCategory.categories))}
                                <li><a href="javascript:void(0)" onclick="$.ajaxCall('ynblog.getsubcategory','id={$aCategory.category_id}');">{_p var='Manage Sub-Categories (!<< total >>!)' total=$iTotalSub}</a></li>
                                {/if}
                                <li><a href="javascript:$.ajaxCall('ynblog.AdminDeleteCategory','delete={$aCategory.category_id}');" class="sJsConfirm">{_p var='Delete'}</a></li>
                            </ul>
                        </div>
                    </td>
                    <td class="td_flex">
                        {softPhrase var=$aCategory.name}
                    </td>
                    <td class="t_center w140">{if $aCategory.used > 0}<a href="{permalink module='ynblog.category' id =$aCategory.category_id title=$aCategory.name|clean}" id="js_category_link{$aCategory.category_id}">{$aCategory.used}</a>{else}{_p var='none'}{/if}</td>
                    <td class="on_off w80">
                        <div class="js_item_is_active"{if !$aCategory.is_active} style="display:none;"{/if}>
                            <a href="#?call=ynblog.updateActivity&amp;id={$aCategory.category_id}&amp;active=0" class="js_item_active_link" title="{_p var='Deactivate'}"></a>
                        </div>
                        <div class="js_item_is_not_active"{if $aCategory.is_active} style="display:none;"{/if}>
                            <a href="#?call=ynblog.updateActivity&amp;id={$aCategory.category_id}&amp;active=1" class="js_item_active_link" title="{_p var='Activate'}"></a>
                        </div>
                    </td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>

