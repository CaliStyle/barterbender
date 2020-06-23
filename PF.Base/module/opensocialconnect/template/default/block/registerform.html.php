	<div id="js_register_step1">
		<div class="table form-group">
			<div class="table_left">
				<label for="full_name">{required}{phrase var='user.user_name'}:</label>
			</div>
			<div class="table_right">
				<input class="form-control" type="text" name="val[user_name]" id="user_name" value="{value type='input' id='user_name'}" size="30" />
			</div>			
		</div>
		<div class="table form-group">
			<div class="table_left">
				<label for="full_name">{required}{phrase var='user.full_name'}:</label>
			</div>
			<div class="table_right">
				<input class="form-control" type="text" name="val[full_name]" id="full_name" value="{value type='input' id='full_name'}" size="30" />
			</div>			
		</div>
		<div class="table form-group">
			<div class="table_left">
				<label for="email">{required}{phrase var='user.email'}:</label>
			</div>
			<div class="table_right">
				<input class="form-control" type="text" name="val[email]" id="email" value="{value type='input' id='email'}" size="30" />
			</div>			
		</div>
		{if Phpfox::getParam('opensocialconnect.allow_user_to_input_their_password')}
		<div class="table p_top_base checkbox">
			<label for="fwpasswordcheckbox"><input type="checkbox" value="1" name="val[autopassword]" id="fwpasswordcheckbox" checked="checked" />{phrase var='opensocialconnect.auto_generate_password'}</label>
		</div>
		<div class="table" id="manual_password" style="display:none;">
			<div class="table_left">
				<label for="password">{required}{phrase var='user.password'}:</label>
			</div>
			<div class="table_right">
				{if isset($bIsPosted)}
				<input class="form-control" type="password" name="val[password]" id="password" value="{value type='input' id='password'}" size="30" />
				{else}
				<input class="form-control" type="password" name="val[password]" id="password" value="" size="30" />
				{/if}
			</div>
		</div>
		{else}
		<div class="table p_top_base checkbox">
			<label for="fwpasswordcheckbox">
				<input type="hidden" value="1" name="val[autopassword]" id="fwpasswordcheckbox" checked="checked" />
				{phrase var='opensocialconnect.system_will_automatically_send_generate_password_to_your_email_after_you_sign_up'}		
			</label>
		</div>
		{/if}
		{if Phpfox::getParam('user.new_user_terms_confirmation')}
		<div id="js_register_accept">
			<div class="table form-group">
				<div class="table_clear p_top_base checkbox">
					<label for="agree">
						<input  type="checkbox" name="val[agree]" id="agree" value="1" class="checkbox v_middle" {value type='checkbox' id='agree' default='1'}/> {required}{phrase var='user.i_have_read_and_agree_to_the_a_href_id_js_terms_of_use_terms_of_use_a_and_a_href_id_js_privacy_policy_privacy_policy_a'}					
					</label>
				</div>		
			</div>
		</div>
		{/if}
        
        <input type="hidden" name="val[large_img_url]" value="{$aForms.large_img_url}" />
	</div>