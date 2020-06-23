<?php

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="ynvideochannel-block-listing">
	<div class="miniblock_pic">
		<a href="{permalink module='videochannel' id=$aMiniVideo.video_id title=$aMiniVideo.title}">{img server_id=$aMiniVideo.image_server_id path='core.url_pic' file=$aMiniVideo.image_path suffix='_120' width='200' height='100' class='js_mp_fix_width' title=$aMiniVideo.title}</a>
	</div>
	<div class="miniblock_text">
		<a href="{permalink module='videochannel' id=$aMiniVideo.video_id title=$aMiniVideo.title}" class="row_sub_link" title="{$aMiniVideo.title}">{$aMiniVideo.title|clean}</a>
		<div class="extra_info_link">
			{phrase var='videochannel.by_lowercase'} {$aMiniVideo|user}<br />
			{if $aMiniVideo.total_view <= 1}
				{phrase var='videochannel.1_view'}
		      {else}
				{$aMiniVideo.total_view|number_format} {phrase var='videochannel.views'}
		      {/if}
		</div>
	</div>
</div>