
{if $aCreditMoneyRequest}
<div class="panel">
	<table class="ynsaTable table table-bordered" cellpadding="1" cellspacing="0">
		<thead>
            <tr>
                <th>{_p var='request_date'}</th>
                <th>{_p var='user'}</th>
                <th>{_p var='amount'}</th>
                <th>{_p var='description'}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$aCreditMoneyRequest name=arequest item=aRequest}
                <tr{if is_int($phpfox.iteration.arequest/2)} class="on"{/if}>
                    <td title="{_p var='request_date'}" class="t_center ynsaFirstColumn">{$aRequest.creditmoneyrequest_request_date_phrase}</td>
                    <td title="{_p var='user'}" class="t_center">{$aRequest|user}</td>
                    <td title="{_p var='amount'}" class="t_center">{$aRequest.creditmoneyrequest_amount_text}</td>
                    <td title="{_p var='description'}" class="t_center">{$aRequest.creditmoneyrequest_reason|clean|shorten:50:'...'}</td>
                    <td class="t_center ynsaLastColumn" style="text-align: center;">
                        <a href="#" onclick="ynsocialad.addrequest.acceptPendingCreditMoneyRequest({$aRequest.creditmoneyrequest_id}, {$aRequest.creditmoneyrequest_creditmoney_id}); return false;">{_p var='accept'}</a>
                        | <a href="#" onclick="ynsocialad.addrequest.rejectPendingCreditMoneyRequest({$aRequest.creditmoneyrequest_id}, {$aRequest.creditmoneyrequest_creditmoney_id}); return false;">{_p var='reject'}</a>
                        | <a href="#" onclick="tb_show('{_p var='details'}', $.ajaxBox('socialad.showCreditMoneyRequestDetailPopup', 'height=400&width=350&id={$aRequest.creditmoneyrequest_id}')); return false;">{_p var='view'}</a>
                    </td>
                </tr>
            {/foreach}
        </tbody>
	</table>

</div>
	{module name='socialad.paging'}
{else}

	<div class="extra_info">
		{_p var='no_request_found'}
	</div>

{/if}
