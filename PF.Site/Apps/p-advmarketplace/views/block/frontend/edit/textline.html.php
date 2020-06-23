{if $aField.is_required}
	{required}
	<input type="hidden" name="customfield_req[{$aField.field_id}]" value="{$sPhraseVarName}" />
	<div style="display: none;" id="msg_{$aField.field_id}" rel="{$aField.field_id}" class="validstp">{phrase var=$sPhraseVarName}</div>
{/if}
<label>{phrase var=$sPhraseVarName}</label>

{$sDisplay}
