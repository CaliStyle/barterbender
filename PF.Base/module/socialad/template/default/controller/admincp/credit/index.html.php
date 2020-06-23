
<div class="panel panel-default">
    <form method="post" action="{url link='admincp.socialad.credit'}">
        <div class="panel-body">
            <div class="table form-group ynsaFixFloatDiv">
                <div class="table_left">
                    {_p var='search_by_user'}:
                </div>
                <div class="table_right">
                    <input type="text" class="form-control" name="search_user" size="30" maxlength="100" value="{if isset($search_user)}{$search_user}{/if}" />
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{_p var='submit'}" class="btn btn-primary" />
        </div>
    </form>
</div>
{if $aCreditMoney}
<div class="panel">
	<table class="ynsaTable table table-bordered" cellpadding="1" cellspacing="0">
        <thead>
        <tr>
			<th>{_p var='user'}</th>
			<th>{_p var='total_amount'} ({$aCurrentCurrency.currency_id})</th>
			<th>{_p var='remaining_amount'} ({$aCurrentCurrency.currency_id})</th>
			<th>{_p var='updated_date'}</th>
			<th></th>		
		</tr>
        </thead>
        <tbody>
        {foreach from=$aCreditMoney name=acredit item=aCredit}
            <tr{if is_int($phpfox.iteration.acredit/2)} class="on"{/if}>
                <td title="{_p var='user'}" class="t_center ynsaFirstColumn">{$aCredit|user}</td>
                <td title="{_p var='total_amount'} ({$aCurrentCurrency.currency_id})" class="t_center">{$aCredit.creditmoney_total_amount}</td>
                <td title="{_p var='remaining_amount'} ({$aCurrentCurrency.currency_id})" class="t_center">{$aCredit.creditmoney_remain_amount}</td>
                <td title="{_p var='updated_date'}" class="t_center">{$aCredit.creditmoney_time_stamp_date_phrase}</td>
                <td class="t_center ynsaLastColumn" style="text-align: center;">
                    <a href="{url link='admincp.socialad.credit.creditmoneyofuser'}userid_{$aCredit.user_id}/">{_p var='details'}</a>
                </td>
            </tr>
        {/foreach}
        </tbody>
	</table>

</div>
	{module name='socialad.paging'}
{else}
	<div class="extra_info">
		{_p var='no_credit_found'}
	</div>

{/if}
