{if $bIsAdminManage}
<div class="panel">
{/if}
{if $aCreditMoneyRequest}

	<table class="ynsaTable {if $bIsAdminManage}table table-bordered{/if}" cellpadding="1" cellspacing="0">
		<tr>
			<th>{phrase var='request_date'}</th>
			<th>{phrase var='status'}</th>
			<th>{phrase var='amount'}</th>
			<th>{phrase var='description'}</th>
			<th>{phrase var='update_date'}</th>
		</tr>
	{foreach from=$aCreditMoneyRequest name=arequest item=aRequest}
		<tr{if is_int($phpfox.iteration.arequest/2)} class="on"{/if}>
			<td title="{phrase var='request_date'}" class="t_center ynsaFirstColumn">{$aRequest.creditmoneyrequest_request_date_phrase}</td>
			<td title="{phrase var='status'}" class="t_center">{$aRequest.creditmoneyrequest_status_phrase}</td>
			<td title="{phrase var='amount'}" class="t_center">{$aRequest.creditmoneyrequest_amount_text}</td>
			<td title="{phrase var='description'}" class="t_center">
				<a href="#" onclick="tb_show('{phrase var='details'}', $.ajaxBox('socialad.showCreditMoneyRequestDetailPopup', 'height=400&width=350&id={$aRequest.creditmoneyrequest_id}')); return false;">{$aRequest.creditmoneyrequest_reason|clean|shorten:50:'...'}</a>
			</td>
			<td title="{phrase var='update_date'}" class="t_center ynsaLastColumn">{$aRequest.creditmoneyrequest_update_date_phrase}</td>
		</tr>
	{/foreach}
	</table>
{if $bIsAdminManage}
</div>
{/if}
	{module name='socialad.paging'}

{else}

	<div class="clear"></div>
	<div class="extra_info">
		{phrase var='no_request_found'}

	</div>

{/if}
