<div id="js_contest_block_invite_friends" class="js_contest_block page_section_menu_holder" style="display:none;">

	<form method="post" action="{url link='current'}" class="yns_invite_form" id="ynfr_edit_invite_friend_form" onsubmit="" enctype="multipart/form-data">
		{if $iEntryId}
			<input type='hidden' name='val[contest_entry_id]' value = '{$iEntryId}'/>
		{/if}

		<div class="contest_invite_friends_form">

			{if Phpfox::isModule('friend')}
				<div class="yns_invite_frdlist">
					<h3>{phrase var='contest.invite_friends'}</h3>
					<div id="div_invitefriend" style="height:370px;">
						{if isset($aForms.contest_id)}
						{module name='friend.search' input='invite' hide=true friend_item_id=$aForms.contest_id friend_module_id='contest'}
						{else}
						{module name='friend.search' input='invite' hide=true friend_item_id=$aContest.contest_id friend_module_id='contest'}
						{/if}
					</div>
				</div>
				<div class="clear"></div>
			{/if}
			<div class="table form-group">
				<div class="table_left"><h3>{phrase var='contest.invite_people_via_email'}</h3></div>
				<div class="table_right">
					<textarea  class="form-control" cols="40" rows="8" name="val[emails]" style="height:60px;"></textarea>
					<div class="extra_info">
						{phrase var='contest.separate_multiple_emails_with_a_comma'}
					</div>
				</div>
			</div>

			<div class="table form-group">
				<div class="table_left">
					<h3>{phrase var='contest.add_a_personal_message'}</h3>
					{phrase var='contest.subject'}:
				</div>
				<div class="table_right label_hover">
					<input type="text" class="form-control" name="val[subject]" value="{$aMessage.subject}" id="email_subject" size="60" style="height: 26px" />
				</div>
			</div>

			<div class="table form-group">
				<div class="table_left">
					{phrase var='contest.message'}:
				</div>
				<div class="table_right label_hover">
					<textarea class="form-control" cols="40" rows="8" name="val[personal_message]" style="height:250px;">
						{$aMessage.message}
					</textarea>
				</div>
			</div>

			{module name='contest.keyword-placeholder'}
			<div id="error_message_invite_friend" class="public_message"></div>
			<div class="p_top_8">
				{if $bIsPopup}
					<input type="button" name="val[submit_invite]"  value="{phrase var='contest.send_invitations'}"id='yncontest_invite_friend_button' class="button btn btn-primary btn-sm" onclick=" $('#yncontest_invite_friend_button').attr('disabled', true); $('#ynfr_edit_invite_friend_form').ajaxCall('contest.submitInviteForm', 'contest_id={$aContest.contest_id}');return false;"/>
				{else}
					<input type="submit" name="val[submit_invite]"  value="{phrase var='contest.send_invitations'}" class="button btn btn-primary btn-sm" />
				{/if}

			</div>
		</div>
	</form>
</div>

<script type="text/javascript">
    $Behavior.yncontestSetupInviteLayout = function() {l}
	     if ($('#div_invitefriend').length)
	     {l}
			var mLoad = setInterval(function() {l}
	         $('#div_invitefriend img').each(function(){l}
	                $(this).attr('src',$(this).data('src'));
	            {r});

	        {r}, 3000);
            if(!$('.yncontest-form-btn').length){l}
                $("#js_friend_loader").append('<div class="yncontest-form-btn"><a role="button" onclick="yncontest.invite.clickAll();">{_p var="core.select_all"}</a></div>');
                $("#js_friend_loader").parent().css('height','');
            {r}
	     {r}
         $(".js_box_title").html('Invite');
    {r}
</script>
