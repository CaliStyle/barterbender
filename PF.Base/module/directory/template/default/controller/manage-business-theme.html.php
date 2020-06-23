<div id='yndirectory_manage_business_theme'>	
	<form method="post" action="{url link='directory.manage-business-theme.id_'.$iBusinessid}" id="js_manage_business_theme" onsubmit="" enctype="multipart/form-data">

			<input type="hidden" name="val[business_id]" value="{$iBusinessid}" >

			<div id="yndirectory_theme">
			{foreach from=$aPackage.themes key=Id item=theme}
				<div class="yndirectory-theme-item">
					<div>
						<a href="
								{if $theme.theme_id == 1}
									{$core_path}module/directory/static/image/theme_1.png
								{elseif $theme.theme_id == 2}
									{$core_path}module/directory/static/image/theme_2.png
								{/if}										
						" target="_blank">
							<img 
								src="
								{if $theme.theme_id == 1}
									{$core_path}module/directory/static/image/theme_1.png
								{elseif $theme.theme_id == 2}
									{$core_path}module/directory/static/image/theme_2.png
								{/if}
								" />
						</a>
					</div>
					<div class="radio ync-radio-custom"><label><input type="radio" name="val[theme]" value="{$theme.theme_id}"
    	                	{if isset($aBusiness) && ($aBusiness.theme_id == $theme.theme_id)}
	                			checked="checked"
    	                	{/if}
    	                /><i class="ico ico-circle-o"></i></label>
    	            </div>
				</div>
			{/foreach}
			</div>

			<div class="yndirectory-button">
				<button type="submit" class="btn btn-sm btn-primary" name="val[apply_theme]" id="apply_theme" value="{phrase var='apply_theme'}">{phrase var='apply_theme'}</button>
			</div>
	</form>
</div>