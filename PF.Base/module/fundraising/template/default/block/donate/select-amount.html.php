<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>

<div class="ynfr donate select-amount">
	
	{if $aCampaign.predefined_amount_list}
		<span class="select-amount extra_info">{phrase var='select_an_amount'} {if $aCampaign.minimum_amount && $aCampaign.minimum_amount != 0.00} ( {phrase var='minimum_amount_upper'} : {$aCampaign.minimum_amount} {$aCampaign.currency}){/if}</span>
		<div class="amount-total">
			{foreach from=$aCampaign.predefined_amount_list item=iAmount}

			<div class="ynfr donate amount_entry" onclick="ynfundraising.donate.selectPredefinedValue({$iAmount});" >{$iAmount} </div>
			{/foreach}

			<div class="ynfr donate amount_entry" onclick="ynfundraising.donate.selectOtherValue();" >{phrase var='other_upper'}</div>
		</div>
	{/if}
	<div class="ynfr donate amount_input">
	<p><span class="ynfr extra_info">{phrase var='your_donation'}:</span> <span> <input type="text" class="ynfr required number ynfr_donate_amount" id="ynfr_donate_amount" name="val[amount]" size="30" style="width: 114px;height:38px;display: initial;"/> <span class="ynfr-donate-currency"> {$aCampaign.currency} </span>  </span></p>
	</div>

	{if $aCampaign.allow_anonymous}
		<div class="checkbox"><label><input type="checkbox" style="margin-right: 5px" name="val[is_anonymous]" >{phrase var='make_donation_anonymous'}</label> <span  title="{phrase var='anonymous_tooltip'}"> <span style="margin-bottom:-6px"class="ynfr-question-tooltip js_hover_title" ></span> </span> </div>
	{/if}

</div>
<script type="text/javascript">
	$Behavior.ynfrInitializeCustomValidatorDonateForm = function () {l}
		ynfundraising.donate.selectPredefinedValue({$iSponsorAmount});
		$.validator.addClassRules("ynfr_donate_amount", {l}range:[{$aCampaign.minimum_amount},10000000]{r});
		jQuery.validator.messages.range = "{phrase var='please_enter_an_amount_greater_or_equal'}" + ' {l}0{r} ' + "{$aCampaign.currency}" ;
	{r}
</script>