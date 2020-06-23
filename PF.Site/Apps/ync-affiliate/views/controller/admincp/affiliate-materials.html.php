<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 2/20/17
 * Time: 16:20
 */
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p('Manage Codes')}
        </div>
    </div>
    <div class="panel-body">
        <div class="extra_info">
            {_p var='add_the_appropriate_code_and_paste_it_and_make_it_available_to_pontential'}
        </div>
        {if count($aMaterials)}
            <div class="table-responsive flex-sortable">
                <table class="table table-bordered" id="_sort" data-sort-url="{url link='yncaffiliate.admincp.materials.order'}">
                    <thead>
                        <tr>
                            <th class="w20"></th>
                            <th class="w20"></th>
                            <th>{_p var='title'}</th>
                            <th class="w80">{_p var='enable'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$aMaterials key=iKey item=aItem}
                            <tr class="{if is_int($iKey/2)} tr{else}{/if}" data-sort-id="{$aItem.material_id}">
                                <td class="w20 t_center">
                                    <i class="fa fa-sort"></i>
                                </td>
                                <td class="w20 t_center">
                                    <a href="#" class="js_drop_down_link" title="Manage"></a>
                                    <div class="link_menu">
                                        <ul>
                                            <li><a class="popup" href="{url link='admincp.yncaffiliate.add-material' idMaterial=$aItem.material_id}">{_p var='Edit'}</a></li>
                                            <li><a href="{url link='admincp.yncaffiliate.affiliate-materials' delete=$aItem.material_id}" class="sJsConfirm">{_p var='Delete'}</a></li>
                                        </ul>
                                    </div>
                                </td>
                                <td class="td-flex">
                                    {$aItem.material_name|clean}
                                </td>
                                <td class="w80">
                                    <div class="js_item_is_active"{if $aItem.is_active == 0} style="display:none;"{/if}>
                                    <a href="#?call=yncaffiliate.updateMaterialStatus&amp;id={$aItem.material_id}&amp;active=0" class="js_item_active_link" title="{_p var='Unable'}"></a>
                                    </div>
                                    <div class="js_item_is_not_active"{if $aItem.is_active == 1} style="display:none;"{/if}>
                                    <a href="#?call=yncaffiliate.updateMaterialStatus&amp;id={$aItem.material_id}&amp;active=1" class="js_item_active_link" title="{_p var='Enable'}"></a>
                                    </div>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        {else}
            <div class="p_4">
                {_p var='no_codes_found'}
            </div>
        {/if}
    </div>
    <div class="panel-footer">
        <a href="{url link='admincp.yncaffiliate.add-material'}" class="btn btn-primary popup">{_p var='add_new_code'}</a>
    </div>
</div>
