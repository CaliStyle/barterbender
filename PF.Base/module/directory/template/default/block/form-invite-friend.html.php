
<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="js_directory_block_invite_friends" class="js_fundraising_block page_section_menu_holder">
    <form method="post" action="{$sUrl}" id="ynfr_edit_invite_friend_form" onsubmit="" enctype="multipart/form-data">
    <div class="block">
        <div class="form-group">
            <label for="js_find_friend">{_p var='invite_friends'}</label>
            {if isset($aBusiness.business_id)}
            <div id="js_selected_friends" class="hide_it"></div>
            {module name='friend.search' input='invite' hide=true friend_item_id=$aBusiness.business_id friend_module_id='directory' }
            {/if}
        </div>
        <div class="form-group invite-friend-by-email">
            <label for="emails">{_p var='invite_people_via_email'}</label>
            <input name="val[emails]" id="emails" class="form-control" data-component="tokenfield" data-type="email" >
            <p class="help-block">{_p var='separate_multiple_emails_with_a_comma'}</p>
        </div>
        <div class="form-group">
            <label for="">{_p var='subject'}:</label>
            <input class="form-control" type="text" name="val[subject]" value="{$sSubject}" id="email_subject" size="60" style="" />
        </div>
        <div class="form-group">
            <label for="personal_message">{_p var='add_a_personal_message'}</label>
            <textarea rows="8" name="val[personal_message]" id="personal_message" class="form-control" placeholder="{_p var='write_message'}">{$sMessage}</textarea>
        </div>
        <div class="form-group">
            <input type="submit" name="val[submit_invite]" value="{_p var='send_invitations'}" class="btn btn-primary" />
        </div>
    </div>
    </form>
</div>

