<form method="POST" id="js_ynsa_edit_campaign_form">
	<input class="form-control" type="text" name="val[campaign_name]" value="{value type='input' id='campaign_name'}" id="campaign_name" size="37" />
	<input type="hidden" name="val[campaign_id]" value="{value type='input' id='campaign_id'}" />
	<input type="submit" value="{phrase var='save'}" style="margin-top: 5px;" class="btn btn-primary btn-sm" onclick="
			if($('#campaign_name').val().trim().length <= 0){l}
				alert('{phrase var='please_input_valid_name'}');
			{r} else {l}
				$('#js_ynsa_edit_campaign_form').ajaxCall('socialad.actionCampaign', 'action=edit_campaign'); 
			{r}
			return false;
		"/>
</form>
