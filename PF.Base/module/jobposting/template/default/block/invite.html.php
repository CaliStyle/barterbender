<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          AnNT
 * @package         Module_jobposting
 */
?>

<form method="post" action="{if $sType=='job'}{permalink module='jobposting' id=$aItem.job_id title=$aItem.title}{/if}{if $sType=='company'}{permalink module='jobposting.company' id=$aItem.company_id title=$aItem.name}{/if}" id="js_jp_invite_friend_form" enctype="multipart/form-data">
    <div id="js_jp_block_invite_friends" class="js_jobposting_block page_section_menu_holder">
		<div>
            {if Phpfox::isModule('friend')}
            <h3>{phrase var='invite_friends'}</h3>
			<div>
                <div id="js_selected_friends" class="hide_it"></div>
                {if $sType=='job'}
                    {module name='friend.search' input='invite' hide=true friend_item_id=$aItem.job_id friend_module_id='jobposting' in_form=true}
                {/if}
                {if $sType=='company'}
                    {module name='friend.search' input='invite' hide=true friend_item_id=$aItem.company_id friend_module_id='jobposting' in_form=true}
                {/if}
			</div>
			{/if}
			
            <h3>{phrase var='invite_people_via_email'}</h3>
			<div class="table_right">
				<textarea cols="40" rows="8" name="val[emails]" class="form-control"></textarea>
				<div class="extra_info">
					{phrase var='separate_multiple_emails_with_a_comma'}
				</div>
			</div>
            
            <h3>{phrase var='add_a_personal_message'}</h3>
            <div class="form-group">
                <label>
                    {phrase var='subject'}:
                </label>
                <input type="text" name="val[subject]" class="form-control" value="{$sSubject}" id="subject" maxlength="255"    />
            </div>
            <div class="form-group">
                <label>
                    {phrase var='message'}:
                </label>
                <textarea cols="40" rows="8" name="val[personal_message]" class="form-control">{$sMessage}</textarea>
            </div>
			
			<div class="p_top_8">
				<input type="submit" name="val[submit_invite]" id="btn_invitations_submit" value="{phrase var='send_invitations'}" class="btn btn-sm btn-primary" />
			</div>
		</div>

        <div class="clear"></div>
    </div>
</form>

{literal}
<script type="text/javascript">
$Behavior.setupInviteLayout = function() {
    if(!$('.yncontest-form-btn').length){
        $("#js_friend_loader").append('<div class="yncontest-form-btn"><a role="button" onclick="ynjobposting.invite.clickAll();">Select All</a></div>');
        $("#js_friend_loader").parent().css('height','');
    }
}
</script>
{/literal}
