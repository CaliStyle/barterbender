<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 09:31
 */
?>
{if !$bIsCanSignup}
<div class="error_message">
    {_p var='you_do_not_have_permission_to_register_become_affiliate'}
</div>
{else}
    {if !$bIsPending && !$bIsDenied}
    <div class="yncaffiliate_sign_up">
        <form action="{url link='affiliate'}" class="form-group" id="yncaffiliate_register_affiliate_form" data-js="{$corePath}/assets/jscript/yncaffiliate.js" data-validjs="{$corePath}/assets/jscript/jquery.validate.js" method="post">
            <input type="hidden" name="val[user_id]" value="{$aUser.user_id}">
            <div class="yncaffiliate_sign_up_inner clearfix">
                <div class="col-md-6 col-xs-12 yncaffiliate_item form-group">
                    <label class="capitalize fw-400">{_p var="contact_name"}</label>
                    <input type="text" id="name" name="val[name]" maxlength="100" value="{$aUser.full_name}">
                </div>
                <div class="col-md-6 col-xs-12 yncaffiliate_item form-group">
                    <label class="capitalize fw-400">{_p var="contact_email"}</label>
                    <input type="text" id="email" name="val[email]" maxlength="100" value="{$aUser.email}">
                </div>
                <div class="col-md-6 col-xs-12 yncaffiliate_item form-group">
                    <label class="capitalize fw-400">{_p var="contact_address"}</label>
                    <input type="text" id="address" name="val[address]" maxlength="200">
                </div>
                <div class="col-md-6 col-xs-12 yncaffiliate_item form-group">
                    <label class="capitalize fw-400">{_p var="contact_phone"}</label>
                    <input type="text" id="phone" name="val[phone]" maxlength="100">
                </div>
            </div>
            <div class="yncaffiliate_sign_up_footer clearfix">
                <div class="pull-left">
                    <label class="fw-400">
                        <input type="checkbox" name="val[terms]" id="terms_and_service">{_p var="i_have_read_and_agree_to_the"}
                    </label>
                    <a href="" id="yncaffiliate_show_term" onclick="return yncaffiliate.showTerm();">{_p var="terms_of_service"}</a>
                </div>
                <button class="btn btn-primary pull-right">{_p var="submit"}</button>
            </div>
        </form>
    </div>
    {elseif $bIsPending}
        <div class="message">
            {_p var='your_registry_need_to_be_approved'}
        </div>
    {elseif $bIsDenied}
        <div class="error_message ">
            {_p var='your_registry_has_been_denied'}
        </div>
    {/if}
{/if}