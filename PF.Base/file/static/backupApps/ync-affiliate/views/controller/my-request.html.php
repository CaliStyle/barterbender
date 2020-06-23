<div class="yncaffiliate_my_request">
    {if $iPage <=1 }
    <div class="yncaffiliate_my_request_contact">
        <h4>{_p var='contact_information'}</h4>
        <span class="yncaffiliate_edit_contact pull-right" onclick="return editContact({$aAccount.user_id});">
            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
        </span>
        <ul class="clearfix">
            <li class="col-md-6"><span>{_p var='contact_name'}:</span><strong> {$aAccount.contact_name|clean}</strong></li>
            <li class="col-md-6"><span>{_p var='contact_email'}:</span><strong> {$aAccount.contact_email|clean}</strong></li>
            <li class="col-md-6"><span>{_p var='contact_address'}:</span><strong> {$aAccount.contact_address|clean}</strong></li>
            <li class="col-md-6"><span>{_p var='contact_phone'}:</span><strong> {$aAccount.contact_phone|clean}</strong></li>
        </ul>
    </div>
	<div class="yncaffiliate_my_request_balance">
		<h4>{_p var='balance'}</h4>
		<ul  class="balance_items clearfix">
        <li class="col-md-12 col-xs-12">
            <div>
                <strong>{_p var='current_currency'}: </strong><span>{$sDefaultCurrency}</span></div>
            <div>
                <strong>{_p var='points_convert_rate'}: </strong><span>1 {_p var='points'} = {$fConvertValue} {$sDefaultCurrency}</span></div>
        </li>
			<li class="col-md-6 col-xs-12">
				<div class="balance_item_inner clearfix">
					<div class="block_left text-center fw-bold col-md-4">{$iTotalEarning|number_format:2}</div>
					<div class="block_right col-md-8">
						<p class="fw-bold">{_p var='total_earnings'}</p>
						<div>{_p var='total_points_you_can_earn_by_affiliate_program_which_are_validated'}</div>
					</div>
				</div>
			</li>
			<li class="col-md-6 col-xs-12">
				<div class="balance_item_inner clearfix">
					<div class="block_left text-center fw-bold col-md-4">{$iTotalAvailable|number_format:2}</div>
					<div class="block_right col-md-8">
						<p class="fw-bold">{_p var='available_points'}</p>
						<div>{_p var='total_available_points_you_can_request_to_get_real_money'}</div>
					</div>
				</div>
			</li>
			<li class="col-md-6 col-xs-12">
				<div class="balance_item_inner clearfix">
					<div class="block_left text-center fw-bold col-md-4">{$iTotalPending|number_format:2}</div>
					<div class="block_right col-md-8">
						<p class="fw-bold">{_p var='pending_points'}</p>
						<div>{_p var='total_points_you_request_to_exchange_to_real_money'}</div>
					</div>
				</div>
			</li>
			<li class="col-md-6 col-xs-12">
				<div class="balance_item_inner clearfix">
					<div class="block_left text-center fw-bold col-md-4">{$iTotalRecieved|number_format:2}</div>
					<div class="block_right col-md-8">
						<p class="fw-bold">{_p var='received_points'}</p>
						<div>{_p var='total_points_you_have_received'}</div>
					</div>
				</div>
			</li>
		</ul>
	</div>
	<div class="yncaffiliate_my_request_manage">
		<h4>{_p var='manage_requests'}</h4>
		<div class="yncaffiliate_search_form form-inline">
            <form action="{url link='affiliate.my-request'}" method="post">
                <div class="yncaffiliate_search_form_inner clearfix">
                    <div class="form-group padding flextwo">
                        <label>{_p var='request_from'}</label>
                        <div class="form-inline">
                            <div class="form-group js_from_select">
                                {select_date prefix='from_' id='_from' start_year='-10' end_year='+10' field_separator=' / '
                                field_order='MDY' default_all=true }
                            </div>
                        </div>
                    </div>
                    <div class="form-group padding flextwo">
                        <label>{_p var='request_to'}</label>
                        <div class="form-inline">
                            <div class="form-group js_to_select">
                                {select_date prefix='to_' id='_to' start_year='-10' end_year='+10' field_separator=' / '
                                field_order='MDY' default_all=true }
                            </div>
                        </div>
                    </div>
                    <div class="form-group padding flexzero">
                        <label class="none-text"></label>
                        <div class="form-inline">
                            <button type="submit" class="btn btn-primary fw-bold">{_p var='search'}</button>
                        </div>
                    </div>
                    <div class="form-group padding flexzero">
                        <label class="none-text"></label>
                        <div class="form-inline">
                            <button class="btn btn-default fw-bold capitalize" onclick="return requestMoney();" {if $iMinRequestAmount > $iTotalAvailable}disabled{/if}>{_p var='request_money'}</button>
                        </div>
                    </div>
                </div>
            </form>
		</div>
        {if $iMinRequestAmount > $iTotalAvailable}
		    <p>{_p var='your_available_points_is_not_enough_to_make_a_request' minimum=$iMinRequestAmount}</p>
        {/if}
    {/if}

        {if count($aRequests) > 0}
        {if !PHPFOX_IS_AJAX}
		<div class="table-responsive" id="tableMyRequest">
            <table class="table table-bordered yncaffiliate_table" >
                <thead>
                    <tr>
                        <th class="short">
                            {_p var='request_date'}
                            {if $sSortField == 'request-date' && $sSortType == 'desc'}
                                <a href="{$sCustomBaseLink}sortfield_request-date/sorttype_asc/"></a>
                                <i class="fa fa-caret-down"></i>
                            {else}
                                <a href="{$sCustomBaseLink}sortfield_request-date/sorttype_desc/"></a>
                                <i class="fa fa-caret-up"></i>
                            {/if}
                        </th>
                        <th class="short">
                            {_p var='amount'}
                            {if $sSortField == 'amount' && $sSortType == 'desc'}
                                <a href="{$sCustomBaseLink}sortfield_amount/sorttype_asc/"></a>
                                <i class="fa fa-caret-down"></i>
                            {else}
                                <a href="{$sCustomBaseLink}sortfield_amount/sorttype_desc/"></a>
                                <i class="fa fa-caret-up"></i>
                            {/if}
                        </th>
                        <th class="short">
                            {_p var='request_points'}
                            {if $sSortField == 'point' && $sSortType == 'desc'}
                                <a href="{$sCustomBaseLink}sortfield_point/sorttype_asc/"></a>
                                <i class="fa fa-caret-down"></i>
                            {else}
                                <a href="{$sCustomBaseLink}sortfield_point/sorttype_desc/"></a>
                                <i class="fa fa-caret-up"></i>
                            {/if}
                        </th>
                        <th>{_p var='status'}</th>
                        <th>{_p var='request_message'}</th>
                        <th class="short">
                            {_p var='response_date'}
                            {if $sSortField == 'response-date' && $sSortType == 'desc'}
                                <a href="{$sCustomBaseLink}sortfield_response-date/sorttype_asc/"></a>
                                <i class="fa fa-caret-down"></i>
                            {else}
                                <a href="{$sCustomBaseLink}sortfield_response-date/sorttype_desc/"></a>
                                <i class="fa fa-caret-up"></i>
                            {/if}
                        </th>
                        <th>{_p var='response_message'}</th>
                        <th>{_p var='payment_method'}</th>
                        <th>{_p var='action'}</th>
                    </tr>
                </thead>
                {else}
                    <table id="page2" style="display: none" class="table table-bordered yncaffiliate_table">
                {/if}
                <tbody>
                    {foreach from=$aRequests item=aItem key=iKey}
                    <tr>
                        <td>{$aItem.time_stamp|date:'core.global_update_time'}</td>
                        <td>{$aItem.currency_symbol}{$aItem.request_amount|number_format:2}</td>
                        <td>{$aItem.request_points|number_format:2}</td>
                        <td>{_p var=$aItem.request_status}</td>
                        <td>{$aItem.request_reason|clean}</td>
                        <td>{if $aItem.modify_time > 0}{$aItem.modify_time|date:'core.global_update_time'}{/if}</td>
                        <td>{$aItem.request_response|clean}</td>
                        <td>{$aItem.request_method_title|clean}</td>
                        <td>{if $aItem.request_status == 'waiting'}<a href="{url link='affiliate.my-request' delete=$aItem.request_id}" class="sJsConfirm" data-message="{_p var='are_you_sure_you_want_to_cancel_this_request'}">{_p var='cancel_request'}</a>{/if}</td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
	    </div>
        {pager}
        {elseif $iPage <= 1}
            <div class="p_4">{_p var='no_request_found'}</div>
        {/if}
    {if $iPage <= 1}
	</div>
    {/if}
</div>

{literal}
<script language="JavaScript" type="text/javascript">
    $Behavior.loadMoreLinkTracking = function () {
        if ($('#page2').length > 0 && $('#page2 tbody').length > 0 && $('#tableMyRequest tbody').length > 0)
        {
            $('#tableMyRequest tbody').append($('#page2 tbody').html());
            $('#page2').remove();
        }
    }
    function requestMoney() {
        tb_show('', $.ajaxBox('yncaffiliate.getRequestMoneyForm', 'height=300&width=500'));
        return false;
    }
    function editContact(id) {
        tb_show('',$.ajaxBox('yncaffiliate.editContactForm', 'user_id='+id));
    }
</script>
{/literal}