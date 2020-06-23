<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
?>


<script type="text/javascript">
{literal}
	$Behavior.ynfrInitializeValidatorDonateForm = function() {
		ynfundraising.initializeValidator($("#ynfr_donate_form"));
	};
{/literal}

</script>

<form method="post" id="ynfr_donate_form" action="{url link='current'}" id="ynfr_donate_campaign_form" onsubmit="" enctype="multipart/form-data">
	<input type="hidden" name="val[campaign_id]" value="{$aCampaign.campaign_id}">
	{template file='fundraising.block.donate.campaign-info}

	<div class="clear"> </div>
	{template file='fundraising.block.donate.select-amount}
	{template file='fundraising.block.donate.donor-info}
	{template file='fundraising.block.donate.terms-conditions}
	<div class="ynfr-donate">
		<div id="sign_now_{$aCampaign.campaign_id}">
			<a  href="#" onclick="$('#ynfr_donate_form').submit(); return false;" >{phrase var='donate'}</a>
		</div>
	</div>

</form>