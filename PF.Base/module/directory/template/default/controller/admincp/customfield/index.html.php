<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

{literal}
<style>
    div.sortable ul li {
        padding-right: 5px;
    }
</style>
{/literal}
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {phrase var='manage_custom_field_groups'}
        </div>
    </div>

    {if count($aGroups)}
    <form method="post" action="{url link='admincp.directory.customfield'}">
        <div class="panel-body">
            <div id="js_menu_drop_down" style="display:none;">
                <div class="link_menu dropContent" style="display:block;">
                    <ul>
                        <li><a href="#" onclick="return $Core.customgroup.action(this, 'edit');">{phrase var='edit'}</a></li>
                        <li><a href="#active" onclick="return $Core.customgroup.action(this, 'active');">{phrase var='set_to_inactive'}</a></li>
                        <li><a href="#" onclick="return $Core.customgroup.action(this, 'delete');">{phrase var='delete'}</a></li>
                    </ul>
                </div>
            </div>
            <div>
                <div class="sortable">
                    <ul>
                    {foreach from=$aGroups key=mGroup name=groups item=aGroup}
                        <li class="{if $mGroup !== 'PHPFOX_EMPTY_GROUP'}group{/if}{if $phpfox.iteration.groups == 1} first{/if}">
                            {if $mGroup === 'PHPFOX_EMPTY_GROUP'}{phrase var='general'}{else}
                                <div style="display:none;"><input type="hidden" name="group[{$aGroup.group_id}]" value="{$aGroup.ordering}" /></div>
                                <a href="#?id={$aGroup.group_id}&amp;type=group" class="js_drop_down" id="js_group_{$aGroup.group_id}">{img theme='misc/draggable.png' alt='' class='v_middle'}{if !$aGroup.is_active}<del>{/if}{{phrase var=$aGroup.phrase_var_name}{if !empty($aGroup.user_group_name)} ({$aGroup.user_group_name|clean}){/if}</a>{if !$aGroup.is_active}</del>{/if}{{/if}</a>
                            {if isset($aGroup.child)}
                            <ul>
                            {foreach from=$aGroup.child name=fields item=aField}
                                <li class="field">
                                    <div style="display:none;"><input type="hidden" name="field[{$aField.field_id}]" value="{$aField.ordering}" /></div>
                                    <a href="#" id="">{img theme='misc/draggable.png' alt='' class='v_middle'} {if !$aField.is_active}<del>{/if}{phrase var=$aField.phrase_var_name}{if !empty($aGroup.user_group_name)} ({$aGroup.user_group_name|clean}){/if}{if !$aField.is_active}</del>{/if}</a>
                                </li>
                            {/foreach}
                            </ul>
                            {/if}
                        </li>
                    {/foreach}
                    </ul>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{phrase var='update_order'}" class="btn btn-primary">
        </div>
    </form>
    {else}
    <div class="alert alert-info">
        {phrase var='no_custom_groups_have_been_added'}
        <a href="{url link='admincp.directory.customfield.add'}">{phrase var='add_a_new_custom_group'}</a>
    </div>
    {/if}
</div>