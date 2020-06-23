<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="ajax-response-custom">
</div>
<script type="text/javascript" src="{$corePath}/assets/jscript/admin.js"></script>
<script type="text/javascript" src="{$corePath}/assets/jscript/jquery.magnific-popup.js"></script>
<link href="{$corePath}/assets/css/magnific-popup.css" rel='stylesheet' type='text/css'>
{literal}
<script type="text/javascript">
    function onSubmitOrderCategory(obj){
        $.ajaxCall('ultimatevideo.AdminUpdateOrderCategory',$(obj).serialize(),'post');
        return false;
    }
</script>
{/literal}

<div class="panel panel-default">
    <div class="panel-heading" id="ynuv_category_manage">
        <div class="panel-title">
            {phrase var='categories'}
        </div>
    </div>

    <div class="table-responsive flex-sortable">
        <table class="table table-bordered" id="_sort" data-sort-url="{url link='ultimatevideo.admincp.category.order'}">
            <thead>
            <tr>
                <th class="w40"></th>
                <th class="w60"></th>
                <th>{phrase var='name'}</th>
                <th class="t_center w80">{phrase var='active'}</th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$aCategories key=iKey item=aCategory}
                <tr data-sort-id="{$aCategory.category_id}">
                    <td class="t_center">
                        <i class="fa fa-sort"></i>
                    </td>
                    <td class="t_center">
                        <a href="#" class="js_drop_down_link" title="{phrase var='Manage'}"></a>
                        <div id="js_menu_drop_down">
                            <div class="link_menu dropContent">
                                <ul class="dropdown-menu">
                                    <li><a href="#?id={$aCategory.category_id}" onclick="return $Core.ultimatevideo.actioncategory(this, 'showcustomfields','{$sUrl}');">{_p('show_custom_field_groups')}</a></li>
                                    <li><a href="#?id={$aCategory.category_id}" onclick="return $Core.ultimatevideo.actioncategory(this, 'edit','{$sUrl}');">{phrase var='edit'}</a></li>
                                    {if isset($aCategory.categories) && ($iTotalSub = count($aCategory.categories))}
                                    <li><a href="#?id={$aCategory.category_id}" onclick="return $Core.ultimatevideo.actioncategory(this, 'sub','{$sUrl}');">{phrase var='manage_sub_categories_total' total=$iTotalSub}</a></li>
                                    {/if}

                                    {if !empty($aCategory.numberItems)}
                                    <li><a href="#?id={$aCategory.category_id}" class="jsWarning" data-title="{_p('notice')}" data-message="{_p('you_can_not_delete_this_category_because_there_are_many_items_related_to_it')}">{phrase var='delete'}</a></li>
                                    {else}
                                    <li><a href="#?id={$aCategory.category_id}" onclick="return $Core.ultimatevideo.actioncategory(this, 'delete','{$sUrl}');" class="" data-message="{_p('are_you_sure')}">{phrase var='delete'}</a></li>
                                    {/if}
                                </ul>
                            </div>
                        </div>
                    </td>
                    <td class="td-flex">
                        {softPhrase var=$aCategory.title}
                    </td>
                    <td class="t_center">
                        <div class="js_item_is_active"{if !$aCategory.is_active} style="display:none;"{/if}>
                            <a href="#?call=ultimatevideo.updateActivity&amp;id={$aCategory.category_id}&amp;active=0&amp;sub={if $bSubCategory}1{else}0{/if}" class="js_item_active_link" title="{phrase var='deactivate'}"></a>
                        </div>
                    <div class="js_item_is_not_active"{if $aCategory.is_active} style="display:none;"{/if}>
                        <a href="#?call=ultimatevideo.updateActivity&amp;id={$aCategory.category_id}&amp;active=1&amp;sub={if $bSubCategory}1{else}0{/if}" class="js_item_active_link" title="{phrase var='activate'}"></a>
                    </div>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
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