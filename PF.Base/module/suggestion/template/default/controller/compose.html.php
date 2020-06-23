<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Suggestion
 * @version 		$Id: Suggestion.class.php 1 2011-11-25 15:29:17Z YOUNETCO $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{$sCreateJs}

<div id="js_ajax_compose_error_message"></div>
<div>
	{if PHPFOX_IS_AJAX}	
		<form method="post" action="{url link='suggestion.compose'}" id="js_form_mail" onsubmit="{plugin call='mail.template_controller_compose_ajax_onsubmit'} $Core.processForm('#js_mail_compose_submit'); $(this).ajaxCall('suggestion.composeProcess'); return false;">
	{else}
		<form method="post" action="{url link='suggestion.compose'}" id="js_form_mail" onsubmit="{$sGetJsForm}">
	{/if}
	{token}
	<div><input type="hidden" name="val[attachment]" class="js_attachment" value="{value type='input' id='attachment'}" /></div>
	{if PHPFOX_IS_AJAX && isset($aUser.user_id)}
	<div><input type="hidden" name="id" value="{$aUser.user_id}" /></div>
	<div><input type="hidden" name="val[to][]" value="{$aUser.user_id}" /></div>
	{else}
		<div class="table form-group">
			<div class="table_left">
				{phrase var='mail.to'}:
			</div>
			<div class="table_right">					
				<div id="js_mail_search_friend_placement" style="width:410px;"></div>
				<div id="js_mail_search_friend"></div>
				<script type="text/javascript">
				var bRun = true;
				$Behavior.loadSearchFriendsCompose = function()
				{l}
					bRun = false;
					{if Phpfox::getUserParam('mail.restrict_message_to_friends') == true}
						$Core.searchFriends({l}
							'id': '#js_mail_search_friend',
							'placement': '#js_mail_search_friend_placement',
							'width': '400px',
							'max_search': 10,
							'input_name': 'val[to]',
							'default_value': '{phrase var='mail.search_friends_by_their_name'}',
							'inline_bubble': true
						{r});		
					{else}
						$Core.searchFriends({l}
							'id': '#js_mail_search_friend',
							'placement': '#js_mail_search_friend_placement',
							'width': '400px',
							'max_search': 10,
							'input_name': 'val[to]',
							'default_value': '{phrase var='mail.search_friends_by_their_name'}',
							'inline_bubble': true,
							'is_mail' : true
						{r});		
					{/if}	
				{r}
				</script>				
			</div>
		</div>
	{/if}
		<div class="table form-group">
                    <div class="table_left">
                        {$sHeader}<BR />
                        {$sPhrases}
                    </div>
			<div class="table_left">			
				<label for="subject">{phrase var='mail.subject'}:</label>			
			</div>
			<div class="table_right">
				<input type="text" name="val[subject]" id="subject" value="{value type='input' id='subject'}" size="40" style="width:400px;" tabindex="1" />
			</div>
			<div class="clear"></div>
		</div>
		
		<div class="table form-group">
			<div class="table_left">
				<label for="message">{phrase var='mail.message'}:</label>
			</div>
			<div class="table_right" id="js_compose_new_message">
				{if PHPFOX_IS_AJAX}
				{editor id='message' rows='2' tabindex='2'}
				{else}
				{editor id='message'}
				{/if}
			</div>
			<div class="clear"></div>
		</div>
		
		{if Phpfox::isModule('captcha') && Phpfox::getUserParam('mail.enable_captcha_on_mail')}
			{module name='captcha.form' sType='mail'}
		{/if}
		
		<div class="table_clear" style="position:relative;">
			<div style="position:absolute; right:0px;">
				<label><input type="checkbox" name="val[copy_to_self]" value="1" class="v_middle" />{phrase var='mail.send_a_copy_to_myself'}</label>
			</div>
			{if PHPFOX_IS_AJAX}	
			<div id="js_mail_compose_submit">
				<ul class="table_clear_button">
					<li><input type="submit" value="{phrase var='mail.submit'}" class="button btn btn-primary btn-sm" /></li>
					<li class="table_clear_ajax"></li>
				</ul>
				<div class="clear"></div>
			</div>			
			{else}
				<input type="submit" value="{phrase var='mail.send'}" class="button btn btn-success btn-sm" />
			{/if}
		</div>
		
	</form>
</div>