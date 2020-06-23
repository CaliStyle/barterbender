<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<div id="js_coupon_block_invite_friends" class="js_fundraising_block page_section_menu_holder">
    {if Phpfox::isModule('friend')}
    <form method="post" action="{$sUrl}" id="ynfr_edit_invite_friend_form" onsubmit="" enctype="multipart/form-data">
        <h3>{phrase var='invite_friends'}</h3>
        <div class="form-group">
            <div id="js_selected_friends" class="hide_it"></div>
            {module name='friend.search' input='invite' hide=true friend_item_id=$aCoupon.coupon_id  friend_module_id='coupon' in_form=true}
        </div>
        {/if}
        <div class="clear"></div>
        <h3 style="margin-top:40px;">{phrase var='invite_people_via_email'}</h3>
        <div class="p_4">
            <textarea class="form-control" cols="40" rows="8" name="val[emails]" style="height:60px;"></textarea>
            <div class="extra_info">
                {phrase var='separate_multiple_emails_with_a_comma'}
            </div>
        </div>
        <h3>{phrase var='add_a_personal_message'}</h3>

        <div class="table form-group">
            <div class="table_left">
                {phrase var='subject'}:
            </div>
            <div class="table_right label_hover">
                <input type="text" class="form-control" name="val[subject]" value="{$sSubject}" id="email_subject" size="60" style="width: 100%; height: 26px" />
            </div>
        </div>

        <div class="table form-group">
            <div class="table_left">
                {phrase var='message'}:
            </div>
            <div class="table_right label_hover">
                <textarea class="form-control" cols="40" rows="8" name="val[personal_message]" style="height:250px;">{$sMessage}</textarea>
            </div>
        </div>
        <div class="p_top_8">
            <input type="submit" name="val[submit_invite]"  value="{phrase var='send_invitations'}" class="button btn btn-primary" />
        </div>
    </form>

</div>
<script type="text/javascript">
    $Behavior.setupInviteLayout = function() {l};
        if(!$('#selectAll').length)
        {l}
            $("#js_friend_search_content").append('<div class="clear" style="padding:5px 0px 10px 0px;"><button type="button" id="selectAll" class="btn btn-success btn-sm" onclick="yncoupon.ClickAll();">{phrase var="select_all"}</button></div><br/>');
        {r}
        $("#js_friend_search_contentjs_friend_search_contentjs_friend_search_content").parent().parent().css('height', '');
    {r}
</script>
