<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{url link='admincp.ecommerce.uom'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='manage_uom'}
            </div>
        </div>

        <div class="table-responsive flex-sortable">
            <table id="js_drag_drop" class="table table-bordered">
                <thead>
                <tr>
                    <th class="w60"></th>
                    <th class="w60"></th>
                    <th>{phrase var='name'}</th>
                    <th class="t_center w100">{phrase var='active'}</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$aCategories key=iKey item=aCategory}
                    <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                        <td class="drag_handle"><input type="hidden" name="val[ordering][{$aCategory.uom_id}]" value="{$aCategory.ordering}" /></td>
                        <td class="t_center">
                            <a href="#" class="js_drop_down_link" title="{phrase var='Manage'}"></a>
                            <div class="link_menu">
                                <ul>
                                    <li><a href="{url link='admincp.ecommerce.uom.add' id=$aCategory.uom_id}">{phrase var='edit'}</a></li>
                                    <li><a href="{url link='admincp.ecommerce.uom' delete=$aCategory.uom_id}" class="sJsConfirm" data-message="{phrase var='are_you_sure_this_will_delete_all_products_that_belong_to_this_uom_and_cannot_be_undone'}">{phrase var='delete'}</a></li>
                                </ul>
                            </div>
                        </td>
                        <td>
                            {$aCategory.title|convert}
                        </td>
                        <td class="t_center">
                            <div class="js_item_is_active" style="{if !$aCategory.is_active}display:none;{/if}">
                                <a href="#?call=ecommerce.updateUomActivity&amp;id={$aCategory.uom_id}&amp;active=0" class="js_item_active_link" title="{phrase var='deactivate'}"></a>
                            </div>
                            <div class="js_item_is_not_active" style="{if $aCategory.is_active}display:none;{/if}">
                                <a href="#?call=ecommerce.updateUomActivity&amp;id={$aCategory.uom_id}&amp;active=1" class="js_item_active_link" title="{phrase var='activate'}"></a>
                            </div>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>
</form>