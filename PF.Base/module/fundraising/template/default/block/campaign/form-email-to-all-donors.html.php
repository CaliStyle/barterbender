<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
?>

{literal}
<script type="text/javascript">
	validate_send_mail_to_all_donors_form = function() {
		if(trim($('#ynfr_send_email_all_donors_message').val()) == '' || trim($('#ynfr_send_email_all_donors_subject').val()) == '')	
		{
			$('#ynfr_empty_subject_or_message').show();
			return false;
		}
		else
		{
			return true;
		}
		
	}
</script>
{/literal}
<form method="post" id ='ynfr_email_all' onsubmit="if(validate_send_mail_to_all_donors_form()) {l}$(this).ajaxCall('fundraising.sendMailToAllDonors', 'campaign_id={$aCampaign.campaign_id}&amp;submit=1');js_box_remove(this); {r} return false;" >
	<div id="ynfr_empty_subject_or_message" class="error_message" style="display:none">
		{phrase var='please_enter_both_subject_and_message'}
	</div>
	 <div class="table form-group">
		<div class="table_left">
			* {phrase var='subject'}:
		</div>
		<div class="table_right label_hover">
			<input class="form-control" type="text" id="ynfr_send_email_all_donors_subject" name="subject" size="50" style="width: 60%;" >
		</div>
	 </div>
	
	<div class="table form-group">
		<div class="table_left">
			* {phrase var='message'}:
		</div>
		<div class="table_right label_hover">
			<textarea class="form-control" cols="40" id="ynfr_send_email_all_donors_message" rows="8" name="message" style="width:68%; height:120px;">
			</textarea>			
		</div>
	 </div>
		{module name='fundraising.keyword-placeholder'}
	<button type="submit" class="btn btn-sm btn-primary" value="{phrase var='send_upper'}">{phrase var='send_upper'}</button>
</form>