<div id="ajax-response-custom">
</div>
<div class="panel panel-default">
    <div class="panel-heading">
    <div class="panel-title">
        {if isset($bIsEditGroup) && $bIsEditGroup }
            {_p('edit_custom_field_groups')}
        {else}
            {_p('add_custom_field_groups')}
        {/if}
    </div>
    </div>


	<form onsubmit="return onSubmitValid(this);" action="" method="post" id="js_add_group_name_form" name="js_add_group_name_form" >
        <div class="panel-heading">
            <div class="panel-title">
                {required}{_p('group_name')}
            </div>
        </div>
        <div class="panel-body">
            {foreach from=$aLanguages item=aLanguage}
                <input type="text" class="ynuv_add_group_name form-control" name="val[group_name]{if isset($aLanguage.phrase_var_name)}[{$aLanguage.phrase_var_name}]{/if}[{$aLanguage.language_id}]{if isset($sMode)}[{$sMode}]{/if}" value="{$aLanguage.post_value|htmlspecialchars}" />
                <div class="extra_info">{$aLanguage.title}</div>
            {/foreach}
        </div>

{if isset($bIsEditGroup) && $bIsEditGroup }
        <div class="panel-heading">
            <div class="panel-title">
                {_p('mapping_categories')}
            </div>
        </div>

        <div class="panel-body">
            <div id="mapping_categories">
                {foreach from=$aCategories key=iKeyCate item=aCategory}
                <div>
                    {if (($iKeyCate	) % 3 == 0 ) }
                    <div>
                        {/if}
                        <input type="checkbox" name="val[categories][]" value="{$aCategory.category_id}"
                               {if isset($bIsEditGroup) && $bIsEditGroup}
                               {if in_array($aCategory.category_id,$aGroup.categories)}
                               checked="checked"
                               {/if}
                        {/if}
                        > {$aCategory.title|convert|clean}
                        {if (($iKeyCate + 1) % 3 == 0 || ($iKeyCate + 1) == $totalCategory) }
                    </div>
                    {/if}
                </div>
                {/foreach}
            </div>
        </div>

	{if $aGroup.customfield}
        <div class="panel-title">
			{_p('custom_fields')}
		</div>
		<table>
			<tr>
				<th>{_p('custom_field_name')}</th>
				<th align="center">{_p('Option')}</th>
			</tr>
			{foreach from=$aGroup.customfield key=iKey item=iField}
				<tr>
					<input type="hidden" name="val[customfield][]" value="{$iField.field_id}">
					<td>{phrase var=$iField.phrase_var_name}</td>
					<td align="center">
						<a href="#" onclick="editCustomField({$iField.field_id});">{_p('Edit')}</a>
						/
						<a href="#" onclick="deleteCustomField({$iField.field_id});">{_p('Delete')}</a>
					</td>
				</tr>
			{/foreach}
		</table>
	{/if}
	<div class="js_mp_parent_holder" id="js_mp_holder">
		{if isset($bIsEditGroup) && $bIsEditGroup}
			<a href="#" onclick="tb_show('{_p('add_custom_field')}', $.ajaxBox('ultimatevideo.AdminAddCustomFieldBackEnd', 'height=300&width=300&action=add&iGroupId={$aGroup.group_id}')); return false;">{_p('add_custom_field')}</a>
		{/if}
	</div>
{/if}
	<div class="panel-footer">
		<input type="submit" value="{_p('Submit')}" class="btn btn-primary" />
	</div>
</form>
{literal}
<script type="text/javascript">
function deleteCustomField(iCustomFieldId){
	if (confirm('Are you sure?')){
		$.ajaxCall('ultimatevideo.AdminDeleteCustomField','fieldId='+ iCustomFieldId,'post');
	}
}

function editCustomField(iCustomFieldId){
	tb_show('Edit Custom Field', $.ajaxBox('ultimatevideo.AdminAddCustomFieldBackEnd', 'height=300&amp;width=300&action=edit&id='+iCustomFieldId));
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
    	$.ajaxCall('ultimatevideo.AdminAddCustomFieldGroup',$(obj).serialize(),'post');
    	return false;
    }
    return true;
}

</script>
{/literal}