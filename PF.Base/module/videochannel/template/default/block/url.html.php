<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
{if PHPFOX_IS_AJAX}
<div id="js_video_done" style="display:none;">
	<div class="valid_message">
		{phrase var='videochannel.video_successfully_added'}
	</div>
</div>
{/if}
<div id="js_video_error" class="error_message" style="display:none;"></div>
<form method="post" action="{$strPostLink}"{if PHPFOX_IS_AJAX} onsubmit="$(this).ajaxCall('videochannel.addShare'); return false;"{/if}>
	{if $sModule}
		<div><input type="hidden" name="val[callback_module]" value="{$sModule}" /></div>
	{/if}
	{if $iItem}
		<div><input type="hidden" name="val[callback_item_id]" value="{$iItem}" /></div>
	{/if}	
	{if !empty($sEditorId)}
		<div><input type="hidden" name="editor_id" value="{$sEditorId}" /></div>
	{/if}

<div class="panel panel-default">
    <div class="panel-body">
        <div class="form-group">
            <label for="category">{required} {phrase var='videochannel.category'}:</label>
            {$sCategories}
        </div>
        <div class="form-group">
            {required} {phrase var='videochannel.video_url'}:
            <input type="text" name="val[url]" value="{value type='input' id='url'}" size="40" class="form-control" />
        </div>

        <div id="js_custom_privacy_input_holder_video">
            {if isset($aForms.video_id)}
            {module name='privacy.build' privacy_item_id=$aForms.video_id privacy_module_id='videochannel'}
            {/if}
        </div>

        {if !$sModule}
        {if Phpfox::isModule('privacy')}
        <div class="form-group">
            <label for="">{phrase var='videochannel.privacy'}:</label>
            {module name='privacy.form' privacy_name='privacy' privacy_info='videochannel.control_who_can_see_this_video' privacy_custom_id='js_custom_privacy_input_holder_video' default_privacy='videochannel.display_on_profile'}
        </div>
        {/if}
        {/if}
    </div>

    <div class="panel-footer">
        <input type="submit" value="{phrase var='videochannel.add'}" class="btn btn-sm btn-primary" />
    </div>
</div>
</form>