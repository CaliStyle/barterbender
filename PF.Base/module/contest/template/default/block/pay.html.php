
<h3 > {$aPayContestParam.sHeader} </h3>

{if !$aPayContestParam.bIsIncludePublish && ! $aPayContestParam.bIsAlreadyPublished}
	<div class="message js_moderation_off" id="js_approve_message">
		{phrase var='contest.you_do_not_need_to_pay_for_publishing_this_contest'}
	</div>
{/if}


<form method="post" action="{url link='current'}" id='yncontest_pay_form'>

	{if $aPayContestParam.bIsIncludePublish}
		<input type="hidden"  ynchecked='checked' class='yncontest_fee entry' fee_value= '{$aPayContestParam.aFees.publish.value}' name='val[{$aPayContestParam.aFees.publish.name}]'>
	{/if}

	<ul class="checklist_grp vertical">
	{foreach from=$aPayContestParam.aFees key=sKey item=aFee}
		{if $sKey != 'publish'}
			<li><label><input style="margin-right: 5px;" type="checkbox"  class='yncontest_fee entry' fee_value= '{$aFee.value}' name='val[{$aFee.name}]'>{$aFee.phrase}</label></li>
		{/if}
	{/foreach}
	</ul>

	{if count($aPayContestParam.aFees) == 0}
		{phrase var='contest.you_are_not_required_to_pay_any_fee_to_publish_this_contest'}
	{/if}
	<div class='yncontest_fee total_fee'>
	{phrase var='contest.total_fee'} : <span id='yn_contest_total_fee'> {if $aPayContestParam.bIsIncludePublish} {$aPayContestParam.aFees.publish.value} {else} 0 {/if} </span> {$aPayContestParam.sDefaultCurrency}
	</div>

	<div class="table_clear">
			<button type='button' class="button btn btn-primary btn-sm" name='val[publish]' id='yncontest_pay_publish' onclick='yncontest.addContest.submitPayForm({$aPayContestParam.iContestId});'>{if $aPayContestParam.bIsAlreadyPublished}{phrase var='contest.request'}{else}{phrase var='contest.publish'}{/if}</button> </li>
			<button type='button' class="button btn btn-default btn-sm" name='val[cancel]' id='yncontest_pay_cancel' onclick="location.href='{permalink module='contest' id=$aPayContestParam.iContestId}'">{phrase var='contest.cancel'}</button> </li>
	</div>

	<div id ="yn_contest_waiting_pay" style='display:none'> {img theme='ajax/add.gif'} {phrase var='contest.please_waiting_for_processing_transaction'} </div>

</form>

<script type="text/javascript">

$Behavior.initializeYnContestAddRemoveFee = function() {l}

	yncontest.pay.bindOnclickAddRemoveFees();
{r}

yncontest.pay.bindOnclickAddRemoveFees();
</script>