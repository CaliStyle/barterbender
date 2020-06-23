<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !empty($sVideoMessage)}
<div class="message">
	{$sVideoMessage}
</div>
<div class="main_break"></div>
{/if}
{literal}
<style>
	.edit_advanced a{
		color: #ff0000 !important;
	}
</style>
{/literal}
<form method="post" action="{url link='videochannel.edit'}" onsubmit="return startProcess(true, false);" enctype="multipart/form-data">
	<div><input type="hidden" name="id" value="{$aForms.video_id}" /></div>
	{if $sStep}
	<div><input type="hidden" name="val[step]" value="{$sStep}" /></div>
	{/if}
	{if !$sStep}
	<div><input type="hidden" name="val[action]" value="{$sAction}" id="js_video_add_action" /></div>
	{/if}	
	
	<div id="js_video_block_detail" class="js_video_block page_section_menu_holder">
		{template file='videochannel.block.form'}
		
		<div class="table_clear">
			<input type="submit" value="{phrase var='videochannel.save'}" class="btn btn-primary" />
		</div>		
	</div>
	
	<div id="js_video_block_photo" class="js_video_block page_section_menu_holder">
        {if !empty($aForms.image_path)}
            {module name='core.upload-form' type='videochannel' current_photo=$aForms.current_image id=$aForms.video_id}
            <input type="hidden" name="val[image_path]" value="{value type='input' id='image_path'}">
            <input type="hidden" name="val[image_server_id]" value="{value type='input' id='image_server_id'}">
        {else}
            {module name='core.upload-form' type='videochannel' current_photo=''}
        {/if}
        <div id="js_submit_upload_image" class="table_clear">
            <input type="submit" value="{phrase var='videochannel.save'}" class="btn btn-primary" />
        </div>
	</div>
	

</form>