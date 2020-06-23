<form method="post" action="" id="js_add_new_role" onsubmit="$(this).ajaxCall('directory.addNewRole'); return false;">
	<input type="hidden" name="role_id" id="role_id" value="{$role_id}">
	<input type="hidden" name="business_id" id="business_id" value="{$business_id}">
	{if isset($aRole.role_title)}
	<div class="table form-group yndirectory_role_title">
		<div class="table_left">{phrase var='current_title'}:</div>
		<div class="table_right">{$aRole.role_title}</div>
	</div>
	{/if}
	<div class="table form-group">
		<div class="table_left">{phrase var='role_title'}</div>
		<div class="table_right"><input class="form-control" type="text" name="role_title" id="role_title" value=""></div>
	</div>
	<div class="yndirectory-button">
		<button type="submit" name="add_role" class="btn btn-sm btn-primary" id="add_role" value="{phrase var='save_changes'}">{phrase var='save_changes'}</button>
	</div>
	<div class="yndirectory-message" id='message'></div>
</form>
