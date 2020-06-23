<?php

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="request_error_message"></div>
<form method="post" action="#" onclick="" id="js_request_body" onsubmit="return false;">
    <input type="hidden" value="{$sMax}" name="val[maximum]"/>
    {phrase var='your_available_amount_is_price' price=$sAvailableAmount}
    <div class="p_4">
        <div class="p_top_8">
            <div class="table form-group">
                <div class="table_right">
                    <input class="form-control" type="text" name="val[amount]" id="request_amount" value="" /> {$sCurrencySymbol}
                </div>
                <div class="sub_script">{phrase var='your_request_has_to_be_between_maximum_and_minimum' maximum=$sMaximum minimum=$sMinimum}</div>
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

        $('#js_request_body').ajaxCall('ecommerce.addRequestMoney');
    }
</script>
{/literal}