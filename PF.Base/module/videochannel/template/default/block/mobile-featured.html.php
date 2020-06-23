<?php 
 
defined('PHPFOX') or exit('NO DICE!'); 

?>

<div class="js_video_parent main_videochannel_div_container">
	<div class="video_width_holder">
		<div class="video_height_holder">
			<div class="js_outer_video_div js_mp_fix_holder image_hover_holder">
				{if !empty($aVideo.duration)}
					<div class="video_duration">
						{$aVideo.duration}
					</div>
				{/if}	
				<a href="{permalink module='videochannel' id=$aVideo.video_id title=$aVideo.title}"
				   class="js_video_title_{$aVideo.video_id}">
					{img server_id=$aVideo.image_server_id path='core.url_pic' file=$aVideo.image_path suffix='_120' max_width=120 max_height=90 class='js_mp_fix_width video_image_border' title=$aVideo.title}
				</a>
			</div>
			<div class="jhslider-info-detail">
				<a href="{permalink module='videochannel' id=$aVideo.video_id title=$aVideo.title}"><strong style="text-transform:uppercase;">{$aVideo.title|clean|shorten:50:"...":false}</strong></a>
				<div> 
					{$aVideo.total_view} {phrase var='videochannel.views'} - {phrase var='videochannel.by_lowercase'}: {$aVideo|user|shorten:20:'...'|split:20}
				</div>			
			</div>
		</div>
	</div>
</div>