<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/7/17
 * Time: 17:42
 */
    defined('PHPFOX') or exit('NO DICE!');
?>


{literal}
<style>
    #page_ecommerce_admincp_request-approved .apps_content{
        background: #FFF;
    }
    #page_ecommerce_admincp_request-approved .apps_content .row2.row_first .p_4{
        padding: 0;
    }
    #page_ecommerce_admincp_request-approved .apps_content .row2.row_first{
        padding: 20px;
    }
</style>
{/literal}
<div class="panel panel-default">
        {if !$bIsPayment}
        <div class="panel-body">
            <div class="table">
                <div class="table_left">{_p var='money_request_desc'}</div>
                <div class="table_left">
                    {_p var='request_amount'}: {$aRequest.request_amount|number_format:2} {$aRequest.request_currency}
                </div>
                <div class="table_left">
                    {_p var='account_request'}: <a href="{url link=$aUser.user_name}">{$aUser.full_name|clean}</a>
                </div>
            </div>
            <form method="post" action="{url link='admincp.yncaffiliate.approve-request'}">
                <div><input type="hidden" name="rid" value="{$aRequest.request_id}" /></div>
                <div class="form-group">
                    <label>
                        {_p var='message_response'}:
                    </label>
                    <textarea class="form-control" name="val[response]" id="" cols="30" rows="10"></textarea>
                </div>
                <input type="submit" value="{_p var='approve'}" name="process" class="btn btn-primary" />
            </form>
        </div>
        {else}
            <div class="panel-heading">
                <div class="panel-title">{_p var='payment_methods'}</div>
            </div>
            <div class="panel-body">
                {module name='api.gateway.form'}
            </div>

        {/if}
</div>
