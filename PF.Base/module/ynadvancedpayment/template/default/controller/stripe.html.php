
<div class="main_break">
    {$sCreateJs}
    <form method="post" action="{url link='current'}" id="ynap_stripe_form"  enctype="multipart/form-data">
        <div id="js_ap_block_main" class="page_section_menu_holder">
            <h1>{_p var='purchase_with_stripe'}</h1>
            <div>
                <input type="hidden" name="stripe_secret_key" value="{$aData.stripe_secret_key}" />
                <input type="hidden" name="stripe_public_key" value="{$aData.stripe_public_key}" />
                <input type="hidden" name="is_test" value="{$aData.is_test}" />
                <input type="hidden" name="item_name" value="{$aData.item_name}" />
                <input type="hidden" name="item_number" value="{$aData.item_number}" />
                <input type="hidden" name="currency_code" value="{$aData.currency_code}" />
                <input type="hidden" name="notify_url" value="{$aData.notify_url}" />
                <input type="hidden" name="return" value="{$aData.return}" />
                <input type="hidden" name="cmd" value="{$aData.cmd}" />
                <input type="hidden" name="amount" value="{$aData.amount}" />
                <input type="hidden" name="recurring_cost" value="{$aData.recurring_cost}" />
                <input type="hidden" name="recurrence" value="{$aData.recurrence}" />
                <input type="hidden" name="recurrence_type" value="{$aData.recurrence_type}" />
                <input type="hidden" id="stripeToken" name="stripeToken" />
            </div>
            <!-- ============================================================ -->
            <h3>{_p var='credit_card_info'}</h3>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{required}{_p var='credit_card_number'}: </label>
                </div>
                <div class="table_right">
                    <input type="text" class="ynap required form-control" name="val[credit_card_number]" value="{value type='input' id='credit_card_number'}" id="credit_card_number" size="30" maxlength="30" />
                </div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{required}{_p var='card_security_code'}: </label>
                </div>
                <div class="table_right">
                    <input type="text" class="ynap required form-control" name="val[CVV2]" value="{value type='input' id='CVV2'}" id="CVV2" size="30" maxlength="30" />
                </div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{required}{_p var='expiration_month'}: </label>
                </div>
                <div class="table_right">
                    <select class="form-control" id="expiration_month" name='val[expiration_month]' >
                        {foreach from=$months key=code item=month}
                            <option value="{$code}">{$month}</option>
                        {/foreach}
                    </select>                    
                </div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{_p var='expiration_year'}: </label>
                </div>
                <div class="table_right">
                    <select class="form-control" id="expiration_year" name='val[expiration_year]' >
                        {foreach from=$years key=code item=year}
                            <option value="{$year}">{$year}</option>
                        {/foreach}
                    </select>                    
                </div>
            </div>

            <div class="table_clear">
                <button type="submit" name="val[confirm]" value="{_p var='confirm_payment'}" class="btn btn-sm btn-primary" id="submitBtn">{_p var='confirm_payment'}</button>
            </div>

            {if Phpfox::getParam('core.display_required')}
            <div class="table_clear">
                {required} {_p var='required_fields'}
            </div>
            {/if}            
        </div>
    </form>
</div>
{literal}
<script type="text/javascript">
    $Behavior.onLoadStripeForm = function(){
        Stripe.setPublishableKey("{/literal}{$aData.stripe_public_key}{literal}");
    }
</script>
{/literal}