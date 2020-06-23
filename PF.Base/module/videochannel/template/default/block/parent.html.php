<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="main_break"></div>
{if count($aVideos)}
<div id="js_video_edit_form_outer" style="display:none;">	
	<form method="post" action="#" onsubmit="$(this).ajaxCall('videochannel.viewUpdate'); return false;">
		<div id="js_video_edit_form"></div>
		<div class="table_clear">
			<input type="submit" value="{phrase var='videochannel.update'}" class="button btn-primary" /> 
			- <a href="#" id="js_video_go_advanced">{phrase var='videochannel.go_advanced'}</a>
			- <a href="#" onclick="$('#js_video_edit_form_outer').hide(); $('#js_video_outer_body').show(); return false;">{phrase var='videochannel.cancel'}</a>
		</div>
	</form>
</div>

<div id="js_video_outer_body">
	
	{foreach from=$aVideos name=videos item=aVideo}
		{template file='videochannel.block.entry'}
	{/foreach}
	<div class="clear"></div>
</div>
{else}
<div class="extra_info">
	
	{phrase var='videochannel.no_videos_added_yet_link_to_add' sAddNewVideoLink=$sAddNewVideoLink}

</div>
{/if}