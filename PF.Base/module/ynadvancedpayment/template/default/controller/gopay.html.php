
<div class="main_break">
    {$sCreateJs}
    {if isset($step2) && $step2 }
        <h1>{phrase var='ynadvancedpayment.purchase_with_gopay'}</h1>
        <form action="{$gw_url}" method="post" id="gopay-payment-button">
            ​<button name="pay" type="submit">{phrase var='ynadvancedpayment.pay'}</button>
    ​     </form>
    {else}
    <form method="post" action="{url link='current'}" id="ynap_gopay_form"  enctype="multipart/form-data">
        <div id="js_ap_block_main" class="page_section_menu_holder">
            <h1>{phrase var='ynadvancedpayment.purchase_with_gopay'}</h1>
            <div>
                <input type="hidden" name="val[item_name]" value="{$aData.item_name}" />
                <input type="hidden" name="val[item_number]" value="{$aData.item_number}" />
                <input type="hidden" name="val[currency_code]" value="{$aData.currency_code}" />
                <input type="hidden" name="val[notify_url]" value="{$aData.notify_url}" />
                <input type="hidden" name="val[return]" value="{$aData.return}" />
                <input type="hidden" name="val[cmd]" value="{$aData.cmd}" />
                <input type="hidden" name="val[amount]" value="{$aData.amount}" />
                <input type="hidden" name="val[recurring_cost]" value="{$aData.recurring_cost}" />
                <input type="hidden" name="val[recurrence]" value="{$aData.recurrence}" />
                <input type="hidden" name="val[recurrence_type]" value="{$aData.recurrence_type}" />
            </div>

            <!-- ============================================================ -->
            <h3>{phrase var='ynadvancedpayment.billing_info'}</h3>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{phrase var='ynadvancedpayment.first_name'}: </label>
                </div>
                <div class="table_right">
                    <input type="text" class="ynap required form-control" name="val[first_name]" value="{value type='input' id='first_name'}" id="first_name" size="60" maxlength="60" />
                </div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{phrase var='ynadvancedpayment.last_name'}: </label>
                </div>
                <div class="table_right">
                    <input type="text" class="ynap required form-control" name="val[last_name]" value="{value type='input' id='last_name'}" id="last_name" size="60" maxlength="60" />
                </div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{phrase var='ynadvancedpayment.address'}: </label>
                </div>
                <div class="table_right">
                    <input type="text" class="ynap required form-control" name="val[address]" value="{value type='input' id='address'}" id="address" size="100" maxlength="100" />
                </div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{phrase var='ynadvancedpayment.city'}: </label>
                </div>
                <div class="table_right">
                    <input type="text" class="ynap required form-control" name="val[city]" value="{value type='input' id='city'}" id="city" size="30" maxlength="30" />
                </div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{phrase var='ynadvancedpayment.country_code'}: </label>
                </div>
                <div class="table_right">
                    <input type="text" class="ynap required form-control" name="val[country_code]" value="{value type='input' id='country_code'}" id="country_code" size="30" maxlength="30" />
                </div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{phrase var='ynadvancedpayment.postal_code'}: </label>
                </div>
                <div class="table_right">
                    <input type="text" class="ynap required form-control" name="val[zip]" value="{value type='input' id='zip'}" id="zip" size="30" maxlength="30" />
                </div>
            </div>

            <!-- ============================================================ -->
            <h3>{phrase var='ynadvancedpayment.additional_info'}</h3>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{phrase var='ynadvancedpayment.phone'}: </label>
                </div>
                <div class="table_right">
                    <input type="text" class="ynap required" name="val[phone]" value="{value type='input' id='phone'}" id="phone" size="30" maxlength="30" />
                    <div class="extra_info">
                        {phrase var='ynadvancedpayment.for_example_420777456123'}
                    </div>                    
                </div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{phrase var='ynadvancedpayment.email_address'}: </label>
                </div>
                <div class="table_right">
                    <input type="text" class="ynap required form-control" name="val[email_address]" value="{value type='input' id='email_address'}" id="email_address" size="30" maxlength="30" />
                    <div class="extra_info">
                        {phrase var='ynadvancedpayment.for_example_zbynek_zak_gopay_cz'}
                    </div>                    
                </div>
            </div>

            <div class="table_clear">
                <button type="submit" name="val[confirm]" value="{phrase var='ynadvancedpayment.confirm_payment'}" class="btn btn-sm btn-primary">{phrase var='ynadvancedpayment.confirm_payment'}</button>
            </div>
         
        </div>
    </form>
    {/if}
</div>