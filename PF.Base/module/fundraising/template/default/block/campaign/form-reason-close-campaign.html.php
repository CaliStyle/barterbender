<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
?>

{literal}
<script type="text/javascript">
	validate_reason_close_form = function() {
		if(trim($('#ynfr_close_reason_text').val()) == '')	
		{
			$('#ynfr_empty_reason').show();
			return false;
		}
		else
		{
			return true;
		}
	}
</script>
{/literal}
<form method="post" onsubmit="if(validate_reason_close_form()) {l}$(this).ajaxCall('fundraising.closeCampaign', 'campaign_id={$aCampaign.campaign_id}&amp;is_owner=0&amp;submit_reason=1');js_box_remove(this);{r}return false;">
	<div id="ynfr_empty_reason" class="error_message" style="display:none">
		{phrase var='please_enter_the_reason'}
	</div>
	<div class="table form-group">
		<div class="table_left">
			{phrase var='reason'}:
		</div>
		<div class="table_right">
			<textarea class="form-control" cols="59" rows="10" name="message" id="ynfr_close_reason_text"></textarea>
		</div>
		<div class="extra_info" >
			* {phrase var='send_reason_notice'}
		</div>
		<button type="submit" class="btn btn-sm btn-primary" value="{phrase var='close_this_campaign'}">{phrase var='close_this_campaign'}</button>

	</div>
</form>