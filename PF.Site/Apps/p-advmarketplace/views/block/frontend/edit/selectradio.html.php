{if $aField.is_required}
	{required}
	<input type="hidden" name="customfield_req[{$aField.field_id}]" value="{$aField.phrase_var_name}" />
	<div style="display: none;" id="msg_{$aField.field_id}" class="validstp">{phrase var=$aField.phrase_var_name}</div>
{/if}
<label for="title">{phrase var=$aField.phrase_var_name}</label>

{foreach from=$aField.options key=iKey item=aOption}
	<div class="radio p-radio-custom">
		<label for="custom_field_{$aField.field_id}_{$iKey}">
			<input class="" id="custom_field_{$aField.field_id}_{$iKey}"{if $aField.data == $aOption} checked="checked"{/if} name="customfield[{$aField.field_id}]" type="radio" value="{$aOption}" />
			<i class="ico ico-circle-o mr-1"></i>
			{phrase var=$aOption}
		</label>
	</div>
{/foreach}

