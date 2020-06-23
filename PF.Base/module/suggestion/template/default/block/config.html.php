

{foreach from=$aPrivacySuggestionNotifications key=ssuggestion item=suggestiontag}
<div class="table form-group" {if (Phpfox::getUserParam('suggestion.enable_content_suggestion_popup')==0 && $ssuggestion=="suggestion.enable_content_suggestion_popup") || (Phpfox::getUserParam('suggestion.enable_friend_recommend')==0 && $ssuggestion=="suggestion.enable_system_recommendation") || (Phpfox::getUserParam('suggestion.enable_friend_suggestion_popup')==0 && $ssuggestion=="suggestion.enable_system_suggestion")}style="display:none"{/if}>
	<br>
	<div class="table_left">
		{$suggestiontag.phrase}
	</div>
	<div class="table_right">			
		<div class="item_is_active_holder" style="height: auto; line-height: normal">	
			<div class="radio-inline">
				<label>
					<input name="val[{$ssuggestion}]" 
					{if $suggestiontag.default} checked="checked"{/if} 
					value="0" type="radio">{phrase var='user.yes'}
				</label>
			</div>
			
			<div class="radio-inline">
				<label>
					<input name="val[{$ssuggestion}]" 
					{if !$suggestiontag.default} checked="checked"{/if}
					value="1" type="radio">{phrase var='user.no'}
				</label>
			</div>
		</div>
	</div>
</div>
{/foreach}

<div class="table_clear">
		<button type="button" class="button btn btn-primary btn-sm" onclick="savechangeclick();return false;">{phrase var='suggestion.save_changes'}</button>		
</div>

{literal}
<script type="text/javascript">

function savechangeclick()
{
	
	var value1=$('input:radio[name="val[suggestion.enable_content_suggestion_popup]"]:checked').val();
	var value2=$('input:radio[name="val[suggestion.enable_system_recommendation]"]:checked').val();
	var value3=$('input:radio[name="val[suggestion.enable_system_suggestion]"]:checked').val();
	$.ajaxCall("suggestion.savechangeclick","value1="+value1+"&value2="+value2+"&value3="+value3);
}
	
</script>
{/literal}

