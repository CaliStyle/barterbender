<div class="renew-payment-method">
    <div class="introduce">
        {_p var='subscribe_renew_title'}
    </div>
    <div class="selection pt-2">
        <div><label><input type="radio" class="mr-1" value="1" name="renew-method" checked="checked">{_p var='subscribe_auto_renew'}</label></div>
        <div><label><input type="radio" class="mr-1" value="2" name="renew-method">{_p var='subscribe_manual_renew'}</label></div>
    </div>

    <div class="selection-button mt-1">
        <button type="button" class="btn btn-primary" onclick="tb_show('{_p var='select_payment_gateway' phpfox_squote=true}', $.ajaxBox('subscribe.upgrade', 'height=400&amp;width=400&amp;id={$iPackageId}&amp;renew_type='+ $('input[name=renew-method]:checked').val())); js_box_remove(this);">{_p var='next'}</button>
    </div>
</div>
