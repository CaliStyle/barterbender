
{if $aTransactions}
<div {if $isAdmin}class="panel"{/if} >
    <table class="ynsaTable {if $isAdmin}table table-bordered{/if}" cellpadding="1" cellspacing="0">
        <tr>
            <th class="w20"></th>
            <th>{_p var='transaction_id'}</th>
            <th>{_p var='start_date'}</th>
            <th>{_p var='status'}</th>
            <th>{_p var='payment_method'}</th>
            <th>{_p var='amount'}</th>
            <th>{_p var='package'}</th>
            <th>{_p var='ad'}</th>
        </tr>
        {foreach from=$aTransactions name=atransaction item=aTransaction}
            <tr{if is_int($phpfox.iteration.atransaction/2)} class="on"{/if}>
                <td class="t_center ynsaFirstColumn w20">{template file='socialad.block.payment.action}</td>
                <td title="{_p var='transaction_id'}" class="t_center">
                    {if isset($aTransaction.gateway_transaction_id) && $aTransaction.gateway_transaction_id}
                        {$aTransaction.gateway_transaction_id}
                    {else}
                        {$aTransaction.transaction_id}
                    {/if}
                 </td>
                <td title="{_p var='start_date'}" class="t_center">{$aTransaction.transaction_start_date_phrase}</td>
                <td title="{_p var='status'}" class="t_center">{$aTransaction.transaction_status_phrase}</td>
                <td title="{_p var='payment_method'}" class="t_center">{$aTransaction.transaction_payment_method_phrase}</td>
                <td title="{_p var='amount'}" class="t_center">{$aTransaction.transaction_amount_text}</td>
                <td title="{_p var='package'}" class="t_center"><a href="#" onclick="tb_show('{_p var='package'}', $.ajaxBox('socialad.showPackagePopup', 'height=400&width=900&package_id={$aTransaction.package.package_id}')); return false;">{$aTransaction.package.package_name|clean|shorten:50:'...'} </a></td>
                <td title="{_p var='ad'}" class="t_center ynsaLastColumn"><a href="{url link='socialad.ad.detail' id=$aTransaction.transaction_ad_id}"> {$aTransaction.ad_title|clean} </a> </td>
            </tr>
        {/foreach}
    </table>
</div>
{module name='socialad.paging'}

{else}

<div class="clear"></div>
<div class="extra_info">
	{_p var='no_transaction_found'}

</div>

{/if}
