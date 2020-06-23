<div id='yndirectory_manage_role_settings'>		
<div class="clear"></div> 
	<div class="yndirectory-hiddenblock">
		<input type="hidden" value="managerolesetting" id="yndirectory_pagename" name="yndirectory_pagename">
	</div>
	<form method="post" action="{url link='directory.member-role-settings.id_'.$iBusinessid.'.view_'.$view_role_id}" id="js_manage_role_settings" onsubmit="" enctype="multipart/form-data">

			<input type="hidden" name="val[business_id]" value="{$iBusinessid}" >
			<input type="hidden" name="val[view_role_id]" value="{if isset($view_role_id)}{$view_role_id}{/if}" >

			<div class=" form-group yndirectory-manage-rolemember">
				<div class="item-title">
					{phrase var='role_member'}
				</div>
				<div class="item-select">
						<select class="form-control" name="val[role_id]" id="yndirectory_manage_role_id">
						{foreach from=$aRoles item=aRole}
							<option value="{$aRole.role_id}" 
							{if $view_role_id != 0 && $aRole.role_id == $view_role_id}
								selected
							{/if}
							{if $view_role_id == 0 && $aRole.type == 'admin'}
								selected
							{/if}
							>{$aRole.role_title}</option>
						{/foreach}
						</select>
				</div>
			</div>

			{foreach from=$aMemberRoleSettings item=aMemberRoleSetting}
				<div id="yndirectory_role_block_{$aMemberRoleSetting.role_id}" class="yndirectory-role-block" 
					{if $view_role_id == 0}
						{if $aMemberRoleSetting.type == 'admin'}
							style="display:block;"
						{else}
							style="display:none;"
						{/if}
					{else}
						{if $aMemberRoleSetting.role_id == $view_role_id}
							style="display:block;"
						{else}
							style="display:none;"
						{/if}
					{/if}
				>
				<h4 class="yndirectory-doashboard-title">{$aMemberRoleSetting.role_title}</h4>

				{foreach from=$aMemberRoleSetting.settings item=aSetting}
					<div class="table form-group">
						<div class="privacy-block-content yndirectory-toggle-privacy-button">
							<div class="item_is_active_holder">	
								<span class="js_item_active item_is_active">
									<input type="radio" class="radio_yes form-control" name="val[{$aMemberRoleSetting.role_id}][{$aSetting.setting_id}]" value="1"  {if $aSetting.status == 'yes'}checked="checked"{/if} > {phrase var='yes'}</span>
								<span class="js_item_active item_is_not_active">
									<input type="radio" class="radio_no form-control" name="val[{$aMemberRoleSetting.role_id}][{$aSetting.setting_id}]" value="0" {if $aSetting.status == 'no'}checked="checked"{/if}> {phrase var='no'}</span>
							</div>
							<label>{$aSetting.setting_title|convert}</label>
						</div>
					</div>
				{/foreach}
				
				</div>
			{/foreach}

			<div class="yndirectory-button">
				<button type="submit" class="btn btn-sm btn-primary" name="val[submit_role_setting]" id="submit_role_setting" value="{phrase var='save_changes'}">{phrase var='save_changes'}</button>
			</div>
		</form>
</div>