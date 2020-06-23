<div style="font-size:12px;padding-bottom:9px;margin-bottom:10px;border-bottom:2px solid #dfdfdf;">
	{_p var='you_are_using_the_services'} "{_p var='view_resume'}" {if $view_resume==2} & "{_p var='who_viewed_me'}"{/if}
</div>
<form method="post" name='js_resume_account_form'>

<div class="account">
{_p var='tip_when_you_view_a_resume_owner_will_see_these_information'}
</div>
<div class="account">
	<span class="account_left">{required}{_p var='your_name'}</span>
	<span class="account_right"><input class="form-control" type="text" name="val[name]" value="{value type='input' id='name'}" id="name" size="40" maxlength="200" />
		<input type="checkbox" class="checkbox" {if $bIsEdit && $aForms.is_name_hidden==1}checked{/if} name='val[is_name_hidden]'/> {_p var='hide_me'}
	</span>
	<div class="account_right" style="font-size: 10px;">
		{_p var='tip_member_can_not_view_your_name_if_you_use_hide_me'}
	</div>
</div>

<div class="account">
	<span class="account_left">{required}{_p var='company_name'}</span>
	<span class="account_right"><input class="form-control" type="text" name="val[company_name]" value="{value type='input' id='company_name'}" id="company_name" size="40" maxlength="200" /></span>
</div>

<div class="account">
	<span class="account_left">{_p var='website'}</span>
	<span class="account_right"><input class="form-control" type="text" name="val[website]" value="{value type='input' id='website'}" id="website" size="40" maxlength="200" /></span>
</div> 

<div class="account">
	<span class="account_left">{required}{_p var='email'}</span>
	<span class="account_right"><input class="form-control" type="text" name="val[email]" value="{value type='input' id='email'}" id="email" size="40" maxlength="200" /></span>
</div>

<div class="account">
	<span class="account_right" style="font-size: 10px;">
		{_p var='tip_we_will_use_this_email_to_notificate_for_you_when_member_send_you_a_message'}
	</span>
</div>

<div class="account">
{_p var='tip_the_following_information_only_contact_to_admin'}
</div>

<div class="account">
	<span class="account_left">{_p var='location'}</span>
	<span class="account_right"><input class="form-control" type="text" name="val[location]" value="{value type='input' id='location'}" id="location" size="40" maxlength="200" /></span>
</div>

<div class="account">
	<span class="account_left">{_p var='zip_code'}</span>
	<span class="account_right"><input class="form-control" type="text" name="val[zip_code]" value="{value type='input' id='zip_code'}" id="zip_code" size="20" maxlength="200" /></span>
</div>

<div class="account">
	<span class="account_left">{_p var='telephone'}</span>
	<span class="account_right"><input class="form-control" type="text" name="val[telephone]" value="{value type='input' id='telephone'}" id="telephone" size="20" maxlength="200" /></span>
</div>


	{if $type!="employ"}
		<input type="hidden" value="1" name='val[view_resume]'/>
	{else}
		<input type="hidden" value="2" name='val[view_resume]'/>
	{/if}


<div class="account">
	<span class="account_right">
		<input type="submit" value="{_p var='submit'}" class="button btn btn-primary btn-sm" style="width: 100px;"/>
		{if $view_resume!=-1 && isset($aAccount.user_id)} 
			{_p var='or'} <a href="#" onclick="if(confirm( '{_p var='are_you_sure'}'))$.ajaxCall('resume.delete_service','type=employer&account_id={$aForms.account_id}');return false;">{_p var='cancel_service'}</a>
		{/if} 
		{_p var='or'} <a href="{if $itmptype==""}{url link='resume.account'}{else}{url link='resume.account'}type_employer/{/if}">{_p var='cancel'}</a>
	</span>
</div>

</form>