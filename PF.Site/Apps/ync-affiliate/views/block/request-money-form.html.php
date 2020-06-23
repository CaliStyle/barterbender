<?php

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="request_error_message"></div>
<form method="post" action="#" onclick="" id="js_request_body" onsubmit="return false;">
    <input type="hidden" value="{$iMaxRequestPoints}" name="val[maximum]"/>
    {phrase var='your_available_points_is_price' price=$iTotalAvailableAmount}
    <div class="p_4">
        <div class="p_top_8">
            <div class="table form-group">
                <div class="table_right">
                    <input class="form-control" type="text" name="val[amount]" id="request_amount" value="" />
                </div>
                <div class="extra_info">{phrase var='your_request_has_to_be_between_maximum_and_minimum' maximum=$iMaxRequestPoints minimum=$iMinRequestPoints}</div>
                <div class="table_left">
                    {phrase var='payment_methods'}:
                </div>
                <div class="table_right">
                    {foreach from=$aAllowGateways item=aGateway key=iKey}
                    <div>
                        <input type="radio" name="val[method]" id="{$aGateway.gateway_id}" value="{$aGateway.gateway_id}" {if $iKey == 0}checked{/if}>
                        <label for="{$aGateway.gateway_id}">{$aGateway.title}</label>
                    </div>

                    {/foreach}
                </div>
                <div class="extra_info">{_p('admin_will_send_money_or_points_to_you_via_payment_method_you_choose')}</div>
                <div class="table_left">
                    {phrase var='your_message'}:
                </div>
                <div class="table_right">
                    <textarea class="form-control" name="val[reason]" id="request_reason" cols="50" rows="8"></textarea>
                </div>
            </div>
            <div class="table form-group">
                <div class="table_left"></div>
                <div class="table_right">
                    <div class="add_request_loading" style="display: none;">{img theme='ajax/add.gif'}</div>
                    <button id="request_money_submit" type="button"  class="btn btn-primary" onclick="submitRequestForm();">{phrase var='submit'}</button>
                </div>
            </div>
        </div>
    </div>
</form>

{literal}
<script type="text/javascript">
    function submitRequestForm()
    {
        $('#request_money_submit').prop("disabled", true);
        $('.add_request_loading').show();

        $('#js_request_body').ajaxCall('yncaffiliate.addRequestMoney');
    }
</script>
{/literal}