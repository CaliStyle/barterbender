<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div id="ajax-response-custom"></div>
<form onsubmit="return onSubmitValid(this);" action="" method="post" id="js_add_group_name_form" name="js_add_group_name_form" >
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {if !empty($bIsEditGroup) }
                    {phrase var='Edit New Review Custom Field Groups'}
                {else}
                    {phrase var='Add New Review Custom Field Groups'}
                {/if}
            </div>
        </div>

        <div class="panel-body">
            <div class="form-group">
                <label>{required}{_p('group_name')}</label>
                {foreach from=$aLanguages item=aLanguage}
                    <input type="text" class="ynuv_add_group_name form-control" name="val[group_name][{$aLanguage.language_id}]" value="{$aLanguage.post_value|htmlspecialchars}" />
                    <p class="help-block">{$aLanguage.title}</p>
                {/foreach}
            </div>

            {if !empty($bIsEditGroup)}
                {if $aGroup.customfield}
                    <div class="table-responsive">
                        <label>{_p('custom_fields')}</label>
                        <table class="table table-bordered">
                            <tr>
                                <th>{_p('custom_field_name')}</th>
                                <th class="t_center">{_p('Option')}</th>
                            </tr>
                            {foreach from=$aGroup.customfield key=iKey item=iField}
                                <tr>
                                    <input type="hidden" name="val[customfield][]" value="{$iField.field_id}">
                                    <td>{phrase var=$iField.phrase_var_name}</td>
                                    <td class="t_center">
                                        <a href="#" onclick="editCustomField({$iField.field_id});">{_p('Edit')}</a>
                                        /
                                        <a href="#" onclick="deleteCustomField({$iField.field_id});">{_p('Delete')}</a>
                                    </td>
                                </tr>
                            {/foreach}
                        </table>
                    </div>
                {/if}
                <div class="js_mp_parent_holder" id="js_mp_holder">
                {if isset($bIsEditGroup) && $bIsEditGroup}
                    <a href="#" onclick="tb_show('{_p('add_custom_field')}', $.ajaxBox('ynmember.AdminAddCustomFieldBackEnd', 'height=300&width=300&action=add&iGroupId={$aGroup.group_id}')); return false;">{_p('add_custom_field')}</a>
                {/if}
                </div>
            {/if}
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-primary">{_p('Submit')}</button>
        </div>
    </div>
</form>
{literal}
<script type="text/javascript">
function deleteCustomField(iCustomFieldId){
	if (confirm('Are you sure?')){
		$.ajaxCall('ynmember.AdminDeleteCustomField','fieldId='+ iCustomFieldId,'post');
	}
}

function editCustomField(iCustomFieldId){
	tb_show('Edit Custom Field', $.ajaxBox('ynmember.AdminAddCustomFieldBackEnd', 'height=300&amp;width=300&action=edit&id='+iCustomFieldId));
}

var MissingAddName = "{/literal}{_p('group_name_cannot_be_empty')}{literal}";
function onSubmitValid(obj){
	var error = false;
	$(".ynuv_add_group_name").each(function(){
		if($(this).val() == ""){
			error = true;
		}
	})
    if(error == true)
    {
        $('#ajax-response-custom').html('<div class="error_message">' + MissingAddName +' </div>');
        return false;
    }
    else {
    	$.ajaxCall('ynmember.AdminAddCustomFieldGroup',$(obj).serialize(),'post');
    	return false;
    }
    return true;
}

</script>
{/literal}