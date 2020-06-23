<div style="font-size:12px;padding-bottom:9px;margin-bottom:10px;border-bottom:2px solid #dfdfdf;">
	{_p var='you_are_using_the_services'} "{_p var='who_viewed_me'}"
</div>
<form method="post" name='js_resume_account_form'>
	
<div class="account">
	<span class="account_left">{required}{_p var='your_name'}</span>
	<span class="account_right"><input class="form-control" type="text" name="val[name]" value="{value type='input' id='name'}" id="name" size="40" maxlength="200" />
	</span>
</div>

<div class="account">
	<span class="account_left">{required}{_p var='email'}</span>
	<span class="account_right"><input class="form-control" type="text" name="val[email]" value="{value type='input' id='email'}" id="email" size="40" maxlength="200" /></span>
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

{if $view_resume==-1 || $view_resume==0}
	<input type="hidden" value="0" name='val[view_resume]'/>
{else}
	<input type="hidden" value="2" name='val[view_resume]'/>
{/if}

<div class="account">
	<span class="account_right">
		<input type="submit" value="{_p var='submit'}" class="button btn btn-primary btn-sm" style="width: 100px;"/>{if $view_resume==0} {_p var='or'} <a href="#" onclick="if(confirm( '{_p var='are_you_sure'}'))$.ajaxCall('resume.delete_service','type=employer&account_id={$aForms.account_id}');return false;">{_p var='cancel_service'}</a> {/if} {_p var='or'} <a href="{if $itmptype==""}{url link='resume.account'}{else}{url link='resume.account'}type_employee/{/if}">{_p var='cancel'}</a>
	</span>
</div>

</form>