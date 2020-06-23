<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="js_fundraising_block_invite_friends" class="js_fundraising_block page_section_menu_holder" style="display:none;">
	<form method="post" action="{if isset($aForms)}{url link='current'}{else}{$sUrl}{/if}" id="ynfr_edit_invite_friend_form" onsubmit="" enctype="multipart/form-data">
		<div>
			{if Phpfox::isModule('friend')}
			<div>
                <h3>{phrase var='invite_friends'}</h3>
                <div class="form-group">
                    <div id="js_selected_friends" class="hide_it"></div>
                    {if isset($aForms.campaign_id)}
                        {module name='friend.search' input='invite' hide=true friend_item_id=$aForms.campaign_id friend_module_id='fundraising' in_form=true}
                    {else}
                        {module name='friend.search' input='invite' hide=true friend_item_id=$aCampaign.campaign_id friend_module_id='fundraising' in_form=true}
                    {/if}
                </div>
			</div>
			{/if}
            <div class="ynfr_invite_fr_bot clearfix">
                <h3>{phrase var='invite_people_via_email'}</h3>
                <div class="">
                    <textarea class="form-control" cols="40" rows="8" name="val[emails]"></textarea>
                    <div class="extra_info">
                        {phrase var='separate_multiple_emails_with_a_comma'}
                    </div>
                </div>
                <h3>{phrase var='add_a_personal_message'}</h3>

                <div class="form-group">
                    <label for="email_subject">{phrase var='subject'}:</label>
                    <input class="form-control" type="text" name="val[subject]" value="{$aMessage.subject}" id="email_subject" size="60">
                </div>

                <div class="form-group">
                    <label for="personal_message">{phrase var='message'}:</label>
                    <textarea class="form-control" cols="40" rows="8" name="val[personal_message]" id="personal_message">
                            {$aMessage.message}
                    </textarea>
                </div>

                {module name='fundraising.keyword-placeholder'}
                <div class="p_top_8">
                    <button type="submit" name="val[submit_invite]" value="{phrase var='send_invitations'}"
                            class="btn btn-sm btn-primary">{phrase var='send_invitations'}
                    </button>
                </div>
            </div>
		</div>	
	</form>
</div>

<script type="text/javascript">
    $Behavior.setupInviteLayout = function() {l}
		if(!$('#select_all_friends').length && $('#js_friend_search_content').find('.extra_info').length == 0) {l}
            $('<a href="javascript:void(0)" onclick="ynfundraising.ClickAll()" role="button" id="select_all_friends" class="">' + oTranslations['select_all'] + '</a>').insertAfter('#selected_friends_list');
		{r}
    {r}
</script>
