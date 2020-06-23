<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if $confirm}
<form method="post" action="{url link='admincp.ecommerce.request-approved'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='approve_request'}
            </div>
        </div>

        <div class="panel-body">
            <p>
                {phrase var='money_request_desc'}
            </p>
            <div class="form-group">
                <strong>{phrase var='price'}:</strong>
                {$request.creditmoneyrequest_amount|clean} <?php $aCurrentCurrencies = Phpfox::getService('ecommerce.helper')->getCurrentCurrencies();
                echo isset($aCurrentCurrencies[0]['currency_id']) ? $aCurrentCurrencies[0]['currency_id'] : 'USD'; ?>
            </div>
            <div>
                <div><input type="hidden" name="id" value="{$request.creditmoneyrequest_id}"/></div>
                <div><input type="hidden" name="message" value="{$message}"/></div>
                <div><input type="hidden" name="process" value="1"/></div>
            </div>
            <div class="form-group">
                <label>{phrase var='reason_to_approve'}:</label>
                <textarea class="form-control" title="{_p('reason_to_approve')}" name="val[text]" id="text" cols="50"
                          rows="12"></textarea>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{phrase var='approve'}" class="btn btn-primary">
        </div>
    </div>
</form>
{else}
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {phrase var='payment_methods'}
        </div>
    </div>
    <div class="panel-body">
        {module name='api.gateway.form'}
    </div>
</div>
{/if}
