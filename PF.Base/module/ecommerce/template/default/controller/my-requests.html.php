<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if $iPage == 0}
<div class="ecom-manage-request">

    <div class="my_requests_balance">{phrase var='balance'}</div>
    <div class="my_requests_statistic">
        <div class="statistic_row">
            <div class="statistic_number">{$fTotalSold|number_format:2}</div>
            <div class="statistic_info">
                <div class="statistic_title">{phrase var='total_sold'}</div>
                <div class="statistic_description">{phrase
                    var='ecommerce.we_only_count_deals_which_got_tip_and_exclude_the_offline_payments'}
                </div>
            </div>
        </div>
        <div class="statistic_row">
            <div class="statistic_number">{$fTotalCommissions|number_format:2}</div>
            <div class="statistic_info">
                <div class="statistic_title">{phrase var='total_commissions'}</div>
                <div class="statistic_description">{phrase var='the_commissions_for_all_sold_deals'}</div>
            </div>
        </div>
        <div class="statistic_row">
            <div class="statistic_number">{$aCreditMoney.creditmoney_remain_amount|number_format:2}</div>
            <div class="statistic_info">
                <div class="statistic_title">{phrase var='available_amount'}</div>
                <div class="statistic_description">{phrase
                    var='ecommerce.total_available_amount_you_can_request_to_get_real_money'}
                </div>
            </div>
        </div>
        <div class="statistic_row">
            <div class="statistic_number">{$fTotalPendingAmount|number_format:2}</div>
            <div class="statistic_info">
                <div class="statistic_title">{phrase var='pending_amount'}</div>
                <div class="statistic_description">{phrase
                    var='ecommerce.total_amount_you_requested_to_exchange_to_real_money'}
                </div>
            </div>
        </div>
        <div class="statistic_row">
            <div class="statistic_number">{$fTotalReceivedAmount|number_format:2}</div>
            <div class="statistic_info">
                <div class="statistic_title">{phrase var='received_amount'}</div>
                <div class="statistic_description">{phrase var='total_real_money_you_have_received'}</div>
            </div>
        </div>
    </div>

    <div class="manage_requests_title">{phrase var='manage_requests'}</div>
    <div class="manage_requests clearfix">
        <div class="search_from_holder">
            <form method="post" action="{url link=$sModule.'.my-requests'}" id="js_request_search_form">
                <div class="request_from">
                    <div class="request_from_label">{phrase var='request_from'}</div>
                    <div style="position: relative;" class="js_from_select">
                        {select_date prefix='from_' id='_from' start_year='-2' end_year='+2' field_separator=' / '
                        field_order='MDY' default_all=true }
                    </div>
                </div>
                <div class="request_to">
                    <div class="request_to_label">{phrase var='request_to'}</div>
                    <div style="position: relative;" class="js_to_select">
                        {select_date prefix='to_' id='_to' start_year='-2' end_year='+2' field_separator=' / '
                        field_order='MDY' default_all=true }
                    </div>
                </div>
                <div class="request_submit">
                    <button type="submit" name="submit" class="btn btn-primary">{phrase var='submit'}</button>
                </div>
                {if $fMinimumAmountToRequest > $aCreditMoney.creditmoney_remain_amount}
                <div class="message_minimu_request">{phrase
                    var='ecommerce.your_available_amount_is_not_enough_to_make_a_request'
                    minimum=$sMinimumAmountToRequest}
                </div>
                {else}
                {if $aGateway}
                <div class="request_money">
                    <button type="button" name="request" class="btn btn-success"
                           onclick="requestMoney();">{phrase var='request_money'}</button>
                </div>
                {else}
                <div class="message_update_gateway">{phrase
                    var='ecommerce.please_update_your_payment_account_in_order_to_request_money'}
                </div>
                {/if}
                {/if}
            </form>
        </div>

		{/if}
        <div class="list_result_holder">
            {if $aCreditMoneyRequests}
           <!--  <div class="ynecommerce_responsive_table">
                {foreach from=$aCreditMoneyRequests item=aCreditMoneyRequest}
                <div class="item-section">
                    <div class="item-row">
                        <div class="item-label">{phrase var='request_date'}</div>
                        <div class="item-value">{$aCreditMoneyRequest.creditmoneyrequest_creation_datetime|date:'core.global_update_time'}</div>
                    </div>
                    <div class="item-row">
                        <div class="item-label">{phrase var='amount'}</div>
                        <div class="item-value price">{$aCreditMoneyRequest.creditmoneyrequest_amount|number_format:2}</div>
                    </div>
                    <div class="item-row">
                        <div class="item-label">{phrase var='status'}</div>
                        <div class="item-value">{$aCreditMoneyRequest.status_title|clean}</div>
                    </div>
                    <div class="item-row">
                        <div class="item-label">{phrase var='request_message'}</div>
                        <div class="item-value">{$aCreditMoneyRequest.creditmoneyrequest_reason|clean}</div>
                    </div>
                    <div class="item-row">
                        <div class="item-label">{phrase var='response_date'}</div>
                        <div class="item-value">
                            {if $aCreditMoneyRequest.creditmoneyrequest_modification_datetime > 0}
                            {$aCreditMoneyRequest.creditmoneyrequest_modification_datetime|date:'core.global_update_time'}
                            {/if}
                        </div>
                    </div>
                    <div class="item-row">
                        <div class="item-label">{phrase var='response_message'}</div>
                        <div class="item-value">
                            {$aCreditMoneyRequest.creditmoneyrequest_response|clean}
                        </div>
                    </div>
                    <div class="item-row">
                        <div class="item-label">{phrase var='action'}</div>
                        <div class="item-value">
                            {if $aCreditMoneyRequest.creditmoneyrequest_status == "pending"}
                            <span class="cancel_icon"></span><a href="javascript:;"onclick="ynecommerce.confirmCancelRequest({$aCreditMoneyRequest.creditmoneyrequest_id});">{phrase
                                var='ecommerce.cancel_request'}</a>
                            {/if}
                        </div>
                    </div>
                </div>
                {/foreach}
            </div> -->
            <div class="table-responsive">
                <table class="ynecommerce_full_table table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th class="request_date ynauction-paddingright">
                            {phrase var='request_date'}
                            <div class="ynauction_column_sort_up_down">
                                <a href="{$sCustomBaseLink}sortfield_request-date/sorttype_asc/" class="up"></a>
                                <a href="{$sCustomBaseLink}sortfield_request-date/sorttype_desc/" class="down"></a>
                            </div>
                        </th>
                        <th class="amount ynauction-paddingright">
                            {phrase var='amount'}
                            <div class="ynauction_column_sort_up_down">
                                <a href="{$sCustomBaseLink}sortfield_amount/sorttype_asc/" class="up"></a>
                                <a href="{$sCustomBaseLink}sortfield_amount/sorttype_desc/" class="down"></a>
                            </div>
                        </th>
                        <th class="status">
                            {phrase var='status'}
                        </th>
                        <th class="request_message">
                            {phrase var='request_message'}
                        </th>
                        <th class="response_date ynauction-paddingright">
                            {phrase var='response_date'}
                            <div class="ynauction_column_sort_up_down">
                                <a href="{$sCustomBaseLink}sortfield_response-date/sorttype_asc/" class="up"></a>
                                <a href="{$sCustomBaseLink}sortfield_response-date/sorttype_desc/" class="down"></a>
                            </div>
                        </th>
                        <th class="response_message">
                            {phrase var='response_message'}
                        </th>
                        <th class="action">
                            {phrase var='action'}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$aCreditMoneyRequests item=aCreditMoneyRequest}
                    <tr>
                        <td class="request_date">
                            {$aCreditMoneyRequest.creditmoneyrequest_creation_datetime|date:'core.global_update_time'}
                        </td>
                        <td class="amount">
                            {$aCreditMoneyRequest.creditmoneyrequest_amount|number_format:2}
                        </td>
                        <td class="status">
                            {$aCreditMoneyRequest.status_title|clean}
                        </td>
                        <td class="request_message">
                            {$aCreditMoneyRequest.creditmoneyrequest_reason|clean}
                        </td>
                        <td class="response_date">
                            {if $aCreditMoneyRequest.creditmoneyrequest_modification_datetime > 0}
                            {$aCreditMoneyRequest.creditmoneyrequest_modification_datetime|date:'core.global_update_time'}
                            {/if}
                        </td>
                        <td class="response_message">
                            {$aCreditMoneyRequest.creditmoneyrequest_response|clean}
                        </td>
                        <td class="action">
                            {if $aCreditMoneyRequest.creditmoneyrequest_status == "pending"}
                            <span class="cancel_icon"></span><a href="javascript:;"onclick="ynecommerce.confirmCancelRequest({$aCreditMoneyRequest.creditmoneyrequest_id});">{phrase
                                var='ecommerce.cancel_request'}</a>
                            {/if}
                        </td>
                    </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
          	{pager}
            {else}
            	{if $iPage == 0}
            		<div class="request_message">{phrase var='no_requests_found'}</div>
            	 {/if}
            {/if}
        </div>
        {if $iPage == 0}
        </div>
    </div>
{/if}

{literal}
<script type="text/javascript">
    function requestMoney() {
        tb_show('', $.ajaxBox('ecommerce.getRequestMoneyForm', 'height=300&width=500'));
    }
</script>
{/literal}