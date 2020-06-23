<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{$sJs}
<div id="TB_ajaxContent"></div>
{if isset($aChannels)}
	  {if !count($aChannels)}
	  <div class="extra_info">
		  {phrase var='videochannel.no_videos_found'}
	  </div>
	  {else}
	        {foreach from=$aChannels key=count item=channel}
				{template file='videochannel.block.channel.entry'}
		    {/foreach}
	  {/if}
{/if}
{if isset($aVideos)}
	  {if !count($aVideos) && $iPage <= 1}
	  <div class="extra_info">
		  {phrase var='videochannel.no_videos_found'}
	  </div>
	  {else}
	  <div id="js_video_edit_form_outer" style="display:none;">
		  <form method="post" action="#" onsubmit="$(this).ajaxCall('videochannel.viewUpdate'); return false;">
			  <div id="js_video_edit_form"></div>
			  <div class="table_clear">
				  <ul class="table_clear_button">
					  <li><input type="submit" value="{phrase var='videochannel.update'}" class="button" /></li>
					  <li><a href="#" id="js_video_go_advanced" class="button_off_link">{phrase var='videochannel.go_advanced_uppercase'}</a></li>
					  <li><a href="#" onclick="$('#js_video_edit_form_outer').hide(); $('#js_video_outer_body').show(); return false;" class="button_off_link">{phrase var='videochannel.cancel_uppercase'}</a></li>
				  </ul>
				  <div class="clear"></div>
			  </div>
		  </form>
	  </div>
	  <div class="block">
	  <div id="js_video_outer_body" class="js_video_outer_body">
		  {foreach from=$aVideos name=videos item=aVideo}
			  {template file='videochannel.block.entry'}
		  {/foreach}
		  <div class="clear"></div>
		  {if Phpfox::getUserParam('videochannel.can_approve_videos') || Phpfox::getUserParam('videochannel.can_delete_other_video')}
		  {moderation}
		  {/if}
          {if count($aVideos)}
		  {pager}
          {/if}
	  </div>
	</div>
	  {/if}
{/if}
