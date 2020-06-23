<?php

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {phrase var='categories'}
        </div>
    </div>
    <div class="table-responsive">
        <table id="js_drag_drop" class="table table-bordered">
            <thead>
                <tr>
                    <th></th>
                    <th style="width:20px;"></th>
                    <th>{phrase var='name'}</th>
                    <th class="t_center" style="width:60px;">{phrase var='active'}</th>
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
                                <li><a href="{url link='admincp.jobposting.addcatjob' id=$aCategory.category_id}">{phrase var='edit'}</a></li>
                                {if isset($aCategory.categories) && ($iTotalSub = count($aCategory.categories))}
                                <li><a href="{url link='admincp.jobposting.managecatjob' sub={$aCategory.category_id}">{phrase var='manage_sub_categories_total' total=$iTotalSub}</a></li>
                                {/if}

                                {if !empty($aCategory.numberItems)}
                                <li><a href="" class="sJsConfirm" data-message="{phrase var='you_can_not_delete_this_category_because_there_are_many_items_related_to_it'}">{phrase var='delete'}</a></li>
                                {else}
                                <li><a href="{url link='admincp.jobposting.managecatjob' delete=$aCategory.category_id}" class="sJsConfirm" data-message="{phrase var='are_you_sure'}">{phrase var='delete'}</a></li>
                                {/if}
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
                        <div class="js_item_is_active" style="{if !$aCategory.is_active}display:none;{/if}">
                            <a href="#?call=jobposting.updateActivity&amp;id={$aCategory.category_id}&amp;active=0&amp;jobcat=1" class="js_item_active_link" title="{phrase var='deactivate'}"></a>
                        </div>
                        <div class="js_item_is_not_active" style="{if $aCategory.is_active}display:none;{/if}">
                            <a href="#?call=jobposting.updateActivity&amp;id={$aCategory.category_id}&amp;active=1&amp;jobcat=1" class="js_item_active_link" title="{phrase var='activate'}"></a>
                        </div>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
