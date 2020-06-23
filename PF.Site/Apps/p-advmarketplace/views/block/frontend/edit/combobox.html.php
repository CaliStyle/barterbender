
{if $aField.is_required}
	{required}
	<input type="hidden" name="customfield_req[{$aField.field_id}]" value="{$sPhraseVarName}" />
	<div style="display: none;" id="msg_{$aField.field_id}" class="validstp">{phrase var=$sPhraseVarName}</div>
{/if}
<label for="">{phrase var=$sPhraseVarName}</label>

<select class="form-control" id="custom_field_{$aField.field_id}" name="customfield[{$aField.field_id}]">
	{*<option value="">{phrase var=$sPhraseVarName}...</option>*}
	{foreach from=$aField.options key=iKey item=aOption}
		<option value="{$aOption}"{if $aField.data == $aOption} selected="selected"{/if}>{phrase var=$aOption}</option>
	{/foreach}
</select>
