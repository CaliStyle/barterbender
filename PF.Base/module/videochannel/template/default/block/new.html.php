<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aVideos)}
{foreach from=$aVideos name=videos item=aVideo}
	{template file='videochannel.block.entry'}
{/foreach}
<div class="clear"></div>
{else}
<div class="extra_info">
	{phrase var='videochannel.no_videos_have_been_added_yet'}
	<ul class="action">
		<li><a href="{url link='videochannel.upload'}">{phrase var='videochannel.be_the_first_to_add_a_video'}</a></li>
	</ul>
</div>
{/if}