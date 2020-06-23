
<div class="main_break">
    {$sCreateJs}
    <form method="post" action="{url link='current'}" id="ynap_authorizenet_form"  enctype="multipart/form-data">
        <div id="js_ap_block_main" class="page_section_menu_holder">
            <h1>{phrase var='ynadvancedpayment.purchase_with_authorize_net'}</h1>
            <div>
                <input type="hidden" name="api_login" value="{$aData.api_login}" />
                <input type="hidden" name="transaction_key" value="{$aData.transaction_key}" />
                <input type="hidden" name="transaction_settings" value="{$aData.transaction_settings}" />
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
            </div>

            <!-- ============================================================ -->
            <h3>{phrase var='ynadvancedpayment.billing_info'}</h3>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{required}{phrase var='ynadvancedpayment.first_name'}: </label>
                </div>
                <div class="table_right">
                    <input type="text" class="ynap required form-control" name="val[first_name]" value="{value type='input' id='first_name'}" id="first_name" size="60" maxlength="60" />
                </div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{required}{phrase var='ynadvancedpayment.last_name'}: </label>
                </div>
                <div class="table_right">
                    <input type="text" class="ynap required form-control" name="val[last_name]" value="{value type='input' id='last_name'}" id="last_name" size="60" maxlength="60" />
                </div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{phrase var='ynadvancedpayment.company'}: </label>
                </div>
                <div class="table_right">
                    <input type="text" class="ynap required form-control" name="val[company]" value="{value type='input' id='company'}" id="company" size="60" maxlength="50" />
                </div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{required}{phrase var='ynadvancedpayment.address'}: </label>
                </div>
                <div class="table_right">
                    <input type="text" class="ynap required form-control" name="val[address]" value="{value type='input' id='address'}" id="address" size="100" maxlength="100" />
                </div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{required}{phrase var='ynadvancedpayment.city'}: </label>
                </div>
                <div class="table_right">
                    <input type="text" class="ynap required form-control" name="val[city]" value="{value type='input' id='city'}" id="city" size="30" maxlength="30" />
                </div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{required}{phrase var='ynadvancedpayment.country'}: </label>
                </div>
                <div class="table_right">
                    <input type="text" class="ynap required form-control" name="val[country_code]" value="{value type='input' id='country_code'}" id="country_code" size="30" maxlength="30" />
                </div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{required}{phrase var='ynadvancedpayment.state'}: </label>
                </div>
                <div class="table_right">
                    <input type="text" class="ynap required form-control" name="val[state]" value="{value type='input' id='state'}" id="state" size="30" maxlength="30" />
                </div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{required}{phrase var='ynadvancedpayment.zip_code'}: </label>
                </div>
                <div class="table_right">
                    <input type="text" class="ynap required form-control" name="val[zip]" value="{value type='input' id='zip'}" id="zip" size="30" maxlength="30" />
                </div>
            </div>

            <!-- ============================================================ -->
            <h3>{phrase var='ynadvancedpayment.additional_info'}</h3>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{required}{phrase var='ynadvancedpayment.phone'}: </label>
                </div>
                <div class="table_right">
                    <input type="text" class="ynap required form-control" name="val[phone]" value="{value type='input' id='phone'}" id="phone" size="30" maxlength="30" />
                    <div class="extra_info">
                        {phrase var='ynadvancedpayment.for_example_123_123_1234'}
                    </div>                    
                </div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{phrase var='ynadvancedpayment.fax'}: </label>
                </div>
                <div class="table_right">
                    <input type="text" class="ynap required form-control" name="val[fax]" value="{value type='input' id='fax'}" id="fax" size="30" maxlength="30" />
                    <div class="extra_info">
                        {phrase var='ynadvancedpayment.for_example_123_123_1234'}
                    </div>                    
                </div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{required}{phrase var='ynadvancedpayment.email_address'}: </label>
                </div>
                <div class="table_right">
                    <input type="text" class="ynap required form-control" name="val[email_address]" value="{value type='input' id='email_address'}" id="email_address" size="30" maxlength="30" />
                    <div class="extra_info">
                        {phrase var='ynadvancedpayment.for_example_janedoe_customer_com'}
                    </div>                    
                </div>
            </div>

            <!-- ============================================================ -->
            <h3>{phrase var='ynadvancedpayment.credit_card_info'}</h3>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{required}{phrase var='ynadvancedpayment.credit_card_number'}: </label>
                </div>
                <div class="table_right">
                    <input type="text" class="ynap required form-control" name="val[credit_card_number]" value="{value type='input' id='credit_card_number'}" id="credit_card_number" size="30" maxlength="30" />
                </div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{required}{phrase var='ynadvancedpayment.card_security_code'}: </label>
                </div>
                <div class="table_right">
                    <input type="text" class="ynap required form-control" name="val[CVV2]" value="{value type='input' id='CVV2'}" id="CVV2" size="30" maxlength="30" />
                </div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{required}{phrase var='ynadvancedpayment.expiration_month'}: </label>
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
                    <label for="title">{phrase var='ynadvancedpayment.expiration_year'}: </label>
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
                <button type="submit" name="val[confirm]" value="{phrase var='ynadvancedpayment.confirm_payment'}" class="btn btn-sm btn-primary">{phrase var='ynadvancedpayment.confirm_payment'}</button>
            </div>

            {if Phpfox::getParam('core.display_required')}
            <div class="table_clear">
                {required} {phrase var='core.required_fields'}
            </div>
            {/if}            
        </div>
    </form>
</div>