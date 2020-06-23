<div class="ynauction-video-list">
	{if count($aVideos) < 1}
		<div class="extra_info">
			{phrase var='no_item_s_found'}.
		</div>
	{/if}

	<div class="ynauction-content-column3">
	{foreach from=$aVideos item=aVideo name=videos}
		<div class="ynauction-video-item">
			<div class="ynauction-video-item-image">
				<a href="{$aVideo.link}" class="js_video_title_{$aVideo.video_id}">
					{if !empty($aVideo.vidly_url_id) && Phpfox::getParam('video.vidly_support')}
						<img src="https://vid.ly/{$aVideo.vidly_url_id}/thumbnail1" alt="{$aVideo.title|clean}" style="max-width:120; max-height:90px;" class='js_mp_fix_width video_image_border' />
					{else}
					{if file_exists(sprintf($aVideo.image_path, '_12090'))}
						{img server_id=$aVideo.image_server_id path='video.url_image' file=$aVideo.image_path suffix='_12090' max_width=120 max_height=90 class='js_mp_fix_width video_image_border' title=$aVideo.title itemprop='image'}
					{else}
						{img server_id=$aVideo.image_server_id path='video.url_image' file=$aVideo.image_path suffix='_120' max_width=120 max_height=90 class='js_mp_fix_width video_image_border' title=$aVideo.title itemprop='image'}
					{/if}
					{/if}
				</a>				
			</div>
			<div class="ynauction-item-title">
				<a href="{$aVideo.link}" class="row_sub_link js_video_title_{$aVideo.video_id}" id="js_video_title_{$aVideo.video_id}" itemprop="url">{$aVideo.title|clean|shorten:30:'...'|split:20}</a>
			</div>
			<div class="ynauction-item-info">
				{if isset($sPublicPhotoView) && $sPublicPhotoView == 'most-discussed'}
					{phrase var='video.comments_total_comment' total_comment=$aVideo.total_comment}<br />
				{elseif isset($sPublicPhotoView) && $sPublicPhotoView == 'popular'}
					{phrase var='video.total_score_out_of_10' total_score=$aVideo.total_score|round} <br />
				{else}
				{if !empty($aVideo.total_view) && $aVideo.total_view > 0}
					<span itemprop="interactionCount">
						{if $aVideo.total_view == 1}
							{phrase var='video.1_view'}<br />
						{else}
							{phrase var='video.total_views' total=$aVideo.total_view}<br />
						{/if}
					</span>
				{/if}
				{/if}
				{if !defined('PHPFOX_IS_USER_PROFILEs')}
					{phrase var='video.by_full_name' full_name=$aVideo|user:'':'':20:'':'author'}
				{/if}
			</div>
		</div>
	{/foreach}
	</div>

	<div class="clear"></div>
	{module name='auction.paging'}	
</div>

