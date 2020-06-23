<?php 
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL
 * @package        Module_Resume
 * @version        3.01
 * 
 */
?>
{if $iCount > 0}
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p var='categories'}
        </div>
    </div>
    <div class="table-responsive">
        <table id="js_drag_drop" class="table table-bordered" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th></th>
                    <th class="w40"></th>
                    <th>{phrase var='name'}</th>
                    <th class="t_center" style="width:60px;">{phrase var='active'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$aCategories key=iKey item=aCategory}
                    <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                        <td class="drag_handle"><input type="hidden" name="val[ordering][{$aCategory.category_id}]" value="{$aCategory.ordering}" /></td>
                        <td class="t_center w40">
                            <a href="#" class="js_drop_down_link" title="{phrase var='Manage'}"></a>
                            <div class="link_menu">
                                <ul>
                                    <li><a href="{url link='admincp.resume.addcategory' id=$aCategory.category_id}">{phrase var='edit'}</a></li>
                                    {if isset($aCategory.categories) && ($iTotalSub = count($aCategory.categories))}
                                    <li><a href="{url link='admincp.resume.addcategory' sub={$aCategory.category_id}">{phrase var='manage_sub_categories_total' total=$iTotalSub}</a></li>
                                    {/if}

                                    {if !empty($aCategory.numberItems)}
                                    <li><a href="" class="jsWarning" data-title="{_p('notice')}" data-message="{phrase var='you_can_not_delete_this_category_because_there_are_many_items_related_to_it'}">{phrase var='delete'}</a></li>
                                    {else}
                                    <li><a href="{url link='admincp.resume.categories' delete=$aCategory.category_id}" class="sJsConfirm" data-message="{phrase var='are_you_sure'}">{phrase var='delete'}</a></li>
                                    {/if}
                                </ul>
                            </div>
                        </td>
                        <td>
                            {softPhrase var=$aCategory.name} ({$aCategory.numberItems})
                        </td>
                        <td class="t_center">
                            <div class="js_item_is_active"{if !$aCategory.is_active} style="display:none;"{/if}>
                            <a href="#?call=resume.updateActivity&amp;id={$aCategory.category_id}&amp;active=0&amp;sub={if $bSubCategory}1{else}0{/if}" class="js_item_active_link" title="{phrase var='deactivate'}"></a>
                            </div>
                            <div class="js_item_is_not_active"{if $aCategory.is_active} style="display:none;"{/if}>
                            <a href="#?call=resume.updateActivity&amp;id={$aCategory.category_id}&amp;active=1&amp;sub={if $bSubCategory}1{else}0{/if}" class="js_item_active_link" title="{phrase var='activate'}"></a>
                            </div>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>
{else}
	<div class="extra_info">{_p var='no_categories_had_been_added'}</div>
{/if}
{literal}
<script type="text/javascript">
    $Behavior.onInitCategory = function(){
        $('.jsWarning').click(function() {
            var buttons = {};
            buttons[oTranslations['cancel']] = {
                'class': 'button dont-unbind',
                text: oTranslations['cancel'],
                click: function() {
                    $(this).dialog("close");
                }
            };
            $(document.createElement('div'))
                .attr({title: $(this).data('title'), class: 'confirm'})
                .html($(this).data('message'))
                .dialog({
                    dialogClass: 'pf_js_confirm',
                    close: function() {
                        $(this).remove();
                    },
                    buttons: buttons,
                    draggable: true,
                    modal: true,
                    resizable: false,
                    width: 'auto'
                });
            return false;
        });
    }

</script>
{/literal}
