{if $bIsAdminManage}
    <div class="panel panel-default">
        <div class="panel-body">
{/if}
{if isset($aCreditMoney.creditmoney_id) && $aCreditMoney.creditmoney_total_amount > 0}
    {if $bIsAdminManage}<div class="form-group">{/if}
	<button
		id="js_ynsa_addcreditmoneyrequest_btn"
		class="btn btn-primary btn-sm"
		{if Phpfox::isAdminPanel()}
			onclick="tb_show('{phrase var='add_request'}', $.ajaxBox('socialad.showAddRequestPopupInAdmin', 'height=400&width=350&userid={$yncm_user_id}')); return false;"
		{else}
			onclick="tb_show('{phrase var='add_request'}', $.ajaxBox('socialad.showAddRequestPopup', 'height=400&width=350')); return false;"
		{/if}
		 >{phrase var='add_request'}</button>
    {if $bIsAdminManage} </div>{/if}
{/if}

<form id="js_ynsa_creditmoney_list_form"
	data-ajax-action="socialad.changeCreditMoneyRequestListFilter"
	data-result-div-id="js_ynsa_creditmoney_list"
	data-custom-event="ondatachanged">

	{if isset($aCreditMoney.creditmoney_id)}
		<input type="hidden" value="{$aCreditMoney.creditmoney_id}" name="js_ynsa_creditmoney_id" id="js_ynsa_creditmoney_id">
	{/if}
	{if Phpfox::isAdminPanel()}
		<input type="hidden" value="{$yncm_user_id}" name="yncm_user_id" id="yncm_user_id">
	{/if}
	<div class="table form-group ynsaFixFloatDiv">

		<div class="table_right">
			{phrase var='total_credit'}:
			{if isset($aCreditMoney.creditmoney_id)}
				{$aCreditMoney.creditmoney_total_amount|currency:$aCurrentCurrency.currency_id:2}
			{else}
				0
			{/if}
		</div>
	</div>
	<div class="table form-group ynsaFixFloatDiv">
		<div class="table_right">
			{phrase var='remaining'}:
			{if isset($aCreditMoney.creditmoney_id)}
				{$aCreditMoney.creditmoney_remain_amount|currency:$aCurrentCurrency.currency_id:2}
			{else}
				0
			{/if}
		</div>
	</div>

	<div class="table form-group ynsaFixFloatDiv">
		<div class="table_left">
			{phrase var='status'}:
		</div>
		<div class="table_right">
			<select class="form-control ynsaMultipleChosen ynsaSelectMethod" name="val[creditmoneyrequest_status_id]" id="">
				<option value="0" >{phrase var='all'}</option>
				{foreach from=$aCreditMoneyRequestStatus item=aStatus}
					<option value="{$aStatus.id}" >{$aStatus.phrase}</option>
				{/foreach}
			</select>
		</div>
	</div>

</form>
{if $bIsAdminManage}
        </div>
</div>
{/if}
<div class="clear"></div>

<script type="text/javascript">
$Behavior.ynsaInitCreditMoneyListForm = function() {l}
	$("#js_ynsa_creditmoney_list_form").ajaxForm();
{r}
</script>
