<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="main_break"></div>
{if PHPFOX_IS_AJAX}
<div id="js_video_done" style="display:none;">
	<div class="valid_message">
		{phrase var='videochannel.video_successfully_added'}
	</div>
</div>
{/if}
<div id="js_video_error" class="error_message" style="display:none;"></div>
<form method="post" action="{url link='videochannel.share'}"{if PHPFOX_IS_AJAX} onsubmit="$(this).ajaxCall('videochannel.addShare' {if defined('PHPFOX_GROUP_VIEW')}, 'bIsGroup=true'{/if}); return false;"{/if}>
	{if $sModule}
		<div><input type="hidden" name="val[module]" value="{$sModule}" /></div>
	{/if}
	{if $iItem}
		<div><input type="hidden" name="val[item]" value="{$iItem}" /></div>
	{/if}	
	{if !empty($sEditorId)}
		<div><input type="hidden" name="editor_id" value="{$sEditorId}" /></div>
	{/if}
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="form-group">
                {required}<label for="category">{phrase var='videochannel.category'}:</label>
                {$sCategories}
            </div>

            <div class="form-group">
                <label for="">{phrase var='videochannel.video_url'}:</label>
                <input type="text" name="val[url]" value="{value type='input' id='url'}" size="40" class="form-control" />
                <div class="extra_info">
                    {*
                    {phrase var='videochannel.supported_sites'}: {$sSites|shorten:50:'View More Sites':true}
                    *}
                    Click <a href="#" onclick="$Core.box('videochannel.supportedSites', 600); return false;">here</a> to view a list of supported sites you can import videos from.

                </div>
            </div>

            {if Phpfox::isModule('privacy')}
            <div class="form-group">
                <label for=""> {phrase var='blog.privacy'}:</label>
                {module name='privacy.form' privacy_name='privacy' privacy_info='Control who can see this channel.'}
            </div>
            {/if}
        </div>

        <div class="panel-footer">
            <input type="submit" value="{phrase var='videochannel.add'}" class="btn btn-sm btn-primary" />
        </div>
    </div>
<!--	<div class="table form-group">-->
<!--		<div class="table_left">-->
<!--		{required}<label for="category">{phrase var='videochannel.category'}:</label>-->
<!--		</div>-->
<!--		<div class="table_right">-->
<!--			{$sCategories}-->
<!--		</div>-->
<!---->
<!--		<div class="table_left">-->
<!--			{phrase var='videochannel.video_url'}:-->
<!--		</div>-->
<!--		<div class="table_right">-->
<!--			<input type="text" name="val[url]" value="{value type='input' id='url'}" size="40" class="form-control" />-->
<!--			<div class="extra_info">-->
<!--			{*-->
<!--				{phrase var='videochannel.supported_sites'}: {$sSites|shorten:50:'View More Sites':true}-->
<!--			*}-->
<!--				Click <a href="#" onclick="$Core.box('videochannel.supportedSites', 600); return false;">here</a> to view a list of supported sites you can import videos from.-->
<!--			</div>-->
<!--		</div>-->
<!--	</div>	-->
<!--	-->
<!--	{if Phpfox::isModule('privacy')}-->
<!--	<div class="table form-group">-->
<!--		<div class="table_left">-->
<!--			{phrase var='blog.privacy'}:-->
<!--		</div>-->
<!--		<div class="table_right">	-->
<!--			{module name='privacy.form' privacy_name='privacy' privacy_info='Control who can see this channel.'}-->
<!--		</div>			-->
<!--	</div>-->
<!--	{/if}-->
<!--		-->
<!--	<div class="table_clear">-->
<!--		<input type="submit" value="{phrase var='videochannel.add'}" class="btn btn-sm btn-primary" />-->
<!--	</div>-->
</form>