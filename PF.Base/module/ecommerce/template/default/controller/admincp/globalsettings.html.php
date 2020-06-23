<?php 
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" id="global_form" action="{url link='admincp.ecommerce.globalsettings'}">
	<div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='global_settings'}
            </div>
        </div>

        <div class="panel-body">
            <div class="form-group">
                <label for="">{phrase var='payment_settings'}:</label>
                <div class="radio"><label><input type="radio" onclick="$('#ynecommerce_adaptive').collapse('hide');" name="val[payment_settings]" class='ynecommerce_payment_settings' value="0" checked="checked" {value type='radio' id='payment_settings' default='0'}> {phrase var='admin_will_receive_all_purchases_from_buyers_then_return_money_to_seller_based_on_their_requests'}</label></div>
                <div class="radio"><label><input type="radio" onclick="$('#ynecommerce_adaptive').collapse('show');" name="val[payment_settings]" class='ynecommerce_payment_settings' value="1" {value type='radio' id='payment_settings' default='1'}> {phrase var='admin_only_receive_commission_on_sold_products_all_the_rest_will_be_purchased_to_seller_directly'}</label></div>
            </div>
            <div id="ynecommerce_adaptive" class="collapse row {if !empty($aForms.payment_settings)}in{/if}">
                <div class="ynecommerce_gateway_setting col-md-6">
                    <label for="">{phrase var='payment_gateway_settings'}:</label>
                    <div class="checkbox">
                        <label for="gateway_id_paypal"><input id="gateway_id_paypal" class="disabled"  checked disabled type="checkbox" value="paypal"> {phrase var='paypal'}</label>
                    </div>
                    <div class="form-group">
                        <label>{required}{phrase var='username_paypal'}:</label>
                        <input class="form-control" type='text' value="{value type='input' id='username_paypal'}" name="val[username_paypal]">
                    </div>

                    <div class="form-group">
                        <label>{required}{phrase var='password_paypal'}:</label>
                        <input class="form-control" type='text' value="{value type='input' id='password_paypal'}" name="val[password_paypal]">
                    </div>

                    <div class="form-group">
                        <label>{required}{phrase var='signature_paypal'}:</label>
                        <input class="form-control" type='text' value="{value type='input' id='signature_paypal'}" name="val[signature_paypal]">
                    </div>

                    <div class="form-group">
                        <label>{required}{phrase var='application_id_paypal'}:</label>
                        <input class="form-control" type='text' value="{value type='input' id='application_id_paypal'}" name="val[application_id_paypal]">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>{phrase var='setting_about_the_next_time_for_publishing_when_item_is_denied_by_admin'}:</label>
                <div class="radio"><label><input type="radio" name="val[publish_item_fee_again]" value=1 checked="checked" {if isset($aForms.publish_item_fee_again) && $aForms.publish_item_fee_again}checked="checked"{/if}> {phrase var='still_chagre_fee'}</label></div>
                <div class="radio"><label><input type="radio" name="val[publish_item_fee_again]" value=0 {if isset($aForms.publish_item_fee_again) && !$aForms.publish_item_fee_again}checked="checked"{/if}> {phrase var='don_t_charge_fee'}</label></div>
            </div>
        </div>

        <div class="panel-footer">
            <input type="submit" value="{phrase var='submit'}" class="btn btn-primary">
        </div>
    </div>
</form>