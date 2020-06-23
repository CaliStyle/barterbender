<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="ajax-response-custom">
</div>
<script type="text/javascript" src="{$corePath}/assets/jscript/admin.js"></script>
<script type="text/javascript" src="{$corePath}/assets/jscript/jquery.magnific-popup.js"></script>
<link href="{$corePath}/assets/css/magnific-popup.css" rel='stylesheet' type='text/css'>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p('manage_custom_field_groups')}
        </div>
    </div>

    {if count($aGroups)}
    <div id="js_menu_drop_down" style="display:none;">
        <div class="link_menu dropContent" style="display:block;">
            <ul>
                <li><a href="#" onclick="return $Core.ultimatevideo.actionCustomField(this, 'edit','{$sUrl}');">{_p('Edit')}</a></li>
                <li><a href="#active" onclick="return $Core.ultimatevideo.actionCustomField(this, 'active','{$sUrl}');">{_p('set_to_inactive')}</a></li>
                <li><a href="#" onclick="return $Core.ultimatevideo.actionCustomField(this, 'delete','{$sUrl}');">{_p('Delete')}</a></li>
            </ul>
        </div>
    </div>
    <form method="post" id="ynuv_custom_field_manage" action="" onsubmit="return onUpdateOrderCustomField(this);">
        <div class="panel-body">
            <div class="table-responsive flex-sortable">
                <div class="sortable">
                    <ul>
                    {foreach from=$aGroups key=mGroup name=groups item=aGroup}
                        <li class="{if $mGroup !== 'PHPFOX_EMPTY_GROUP'}group{/if}{if $phpfox.iteration.groups == 1} first{/if}" style="background: #ffffff;">
                            {if $mGroup === 'PHPFOX_EMPTY_GROUP'}{_p('General')}{else}
                                <div style="display:none;"><input type="hidden" name="group[{$aGroup.group_id}]" value="{$aGroup.ordering}" /></div>
                                <a href="#?id={$aGroup.group_id}&amp;type=group" class="js_drop_down" id="js_group_{$aGroup.group_id}">{img theme='misc/draggable.png' alt='' class='v_middle'}{if !$aGroup.is_active}<del>{/if}{{phrase var=$aGroup.phrase_var_name}{if !empty($aGroup.user_group_name)} ({$aGroup.user_group_name|clean}){/if}</a>{if !$aGroup.is_active}</del>{/if}{{/if}</a>
                            {if isset($aGroup.child)}
                            <ul>
                            {foreach from=$aGroup.child name=fields item=aField}
                                <li class="field">
                                    <div style="display:none;"><input type="hidden" name="field[{$aField.field_id}]" value="{$aField.ordering}" /></div>
                                    <a href="javascript:void(0)" id="js_field_{$aField.field_id}">{img theme='misc/draggable.png' alt='' class='v_middle'} {if !$aField.is_active}<del>{/if}{phrase var=$aField.phrase_var_name}{if !empty($aGroup.user_group_name)} ({$aGroup.user_group_name|clean}){/if}{if !$aField.is_active}</del>{/if}</a>
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
            <input type="submit" value="{_p('update_order')}" class="btn btn-primary" />
        </div>
    </form>
</div>
{else}
<div class="extra_info">
	{_p('no_custom_groups_have_been_added')}
	<ul class="action">
		<li><a href="#" onclick="$('.toolbar-top .btn-group a:eq(4)').trigger('click'); return false;">{_p('add_a_new_custom_group')}</a></li>
	</ul>
</div>
{/if}
{literal}
<script type="text/javascript">
	function onUpdateOrderCustomField(obj){
        $.ajaxCall('ultimatevideo.AdminUpdateOrderCustomField',$(obj).serialize(),'post');
        return false;
    }
</script>
{/literal}