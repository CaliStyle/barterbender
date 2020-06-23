<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<form method="post" action="{url link='current'}" class="ynfr_add_edit_form" id="ynfr_edit_campaign_contact_information_form" onsubmit="return ynfr_checkEmails('#email_address')" enctype="multipart/form-data">
	<div id="js_fundraising_block_contact_information" class="js_fundraising_block page_section_menu_holder" style="display:none;">
        <h3>{phrase var='personal_information'}</h3>
        <div class="table form-group">
            <div class="table_left">
                <label for="full_name">{phrase var='full_name'}: </label>
            </div>
            <div class="table_right">
                <input class="form-control" type="text" name="val[contact_full_name]" value="{value type='input' id='contact_full_name'}" id="contact_full_name" size="60" />
            </div>
        </div>

        <div class="table form-group">
            <div class="table_left">
                <label for="phone">{phrase var='phone'}: </label>
            </div>
            <div class="table_right">
                <input type="text" class="ynfr form-control" name="val[contact_phone]" value="{value type='input' id='contact_phone'}" id="contact_phone" size="60" />
            </div>
        </div>

        <div class="table form-group">
            <div class="table_left">
                <label for="email_address">{phrase var='email'}: </label>
            </div>
            <div class="table_right">
                <input type="text" class="ynfr email form-control" name="val[contact_email_address]" value="{value type='input' id='contact_email_address'}" id="contact_email_address" size="60" />
            </div>
        </div>

        <div class="table form-group">
            <div class="table_left">
                <label for="country_iso">{phrase var='country'}:</label>
            </div>
            <div class="table_right">
                {select_location}
            </div>
        </div>

        <div class="table form-group">
            <div class="table_left">
                <label for="state">{phrase var='state'}:</label>
            </div>
            <div class="table_right">
                <input class="form-control" type="text" name="val[contact_state]" value="{value type='input' id='contact_state'}" id="contact_state" size="20" maxlength="200" />
            </div>
        </div>

        <div class="table form-group">
            <div class="table_left">
                <label for="city">{phrase var='city'}:</label>
            </div>
            <div class="table_right">
                <input class="form-control" type="text" name="val[contact_city]" value="{value type='input' id='contact_city'}" id="contact_city" size="20" maxlength="200" />
            </div>
        </div>

        <div class="table form-group">
            <div class="table_left">
                <label for="street">{phrase var='street'}</label>
            </div>
            <div class="table_right">
                <input class="form-control" type="text" name="val[contact_street]" value="{value type='input' id='contact_street'}" id="contact_street" size="30" maxlength="200" />
            </div>
        </div>

        <div class="table form-group">
            <div class="table_left">
                <label for="about_me">{phrase var='about_me'}: </label>
            </div>
            <div class="table_right">
                {editor id='contact_about_me' value='$aForms.contact_about_me'}
            </div>
        </div>

        <div class="table_clear">
            <button type="submit" name="val[submit_contact_information]" value="{phrase var='save'}" class="btn btn-sm btn-primary">{phrase var='save'}</button>
            {if $bIsEdit && $aForms.is_draft == 1}
            <button type="submit" name="val[publish_contact_information]" value="{phrase var='publish'}" class="btn btn-sm btn-danger">{phrase var='publish'}</button>
            {/if}
        </div>
	</div>
</form>
