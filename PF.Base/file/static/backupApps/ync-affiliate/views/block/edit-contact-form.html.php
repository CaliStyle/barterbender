<?php

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="request_error_message"></div>
<form method="post" action="#" onclick="" id="js_contact_form" onsubmit="return false;">
    <input type="hidden" name="val[user_id]" value="{$aForms.user_id}">
    <div class="table">
        <div class="form-group">
            <label class="capitalize fw-400">{required}{_p var="contact_name"}</label>
            <input class="form-control" type="text" id="name" name="val[name]" maxlength="100" value="{$aForms.contact_name}">
        </div>
        <div class="form-group">
            <label class="capitalize fw-400">{required}{_p var="contact_email"}</label>
            <input class="form-control" type="text" id="email" name="val[email]" maxlength="100" value="{$aForms.contact_email}">
        </div>
        <div class="form-group">
            <label class="capitalize fw-400">{required}{_p var="contact_address"}</label>
            <input class="form-control" type="text" id="address" name="val[address]" maxlength="200" value="{$aForms.contact_address}">
        </div>
        <div class="form-group">
            <label class="capitalize fw-400">{required}{_p var="contact_phone"}</label>
            <input class="form-control" type="text" id="phone" name="val[phone]" maxlength="100" value="{$aForms.contact_phone}">
        </div>
    </div>
    <div class="table form-group">
        <div class="table_right">
            <div class="edit_contact_loading t_center" style="display: none;font-size:20px;"><i class="fa fa-spin fa-circle-o-notch"></i></div>
            <div id="edit_contact_submit">
                <button type="button"  class="btn btn-primary" onclick="submitEditContactForm();">{phrase var='update'}</button>
                <button type="button"  class="btn btn-default" onclick="js_box_remove(this);">{phrase var='cancel'}</button>
            </div>
        </div>
    </div>
</form>
{literal}
<script type="text/javascript">
    function submitEditContactForm()
    {
        $('#edit_contact_submit').hide();
        $('.edit_contact_loading').show();

        $('#js_contact_form').ajaxCall('yncaffiliate.editContactInformation');
    }
</script>
{/literal}