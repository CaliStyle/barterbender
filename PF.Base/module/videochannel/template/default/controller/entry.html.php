<?php 
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{plugin call='videochannel.template_block_entry_1'}
	<div class="js_video_parent main_video_div_container {if isset($aVideo.is_sponsor) && $aVideo.is_sponsor}row_sponsored_image{/if} {if isset($aVideo.is_featured) && $aVideo.is_featured}row_featured_image{/if}" id="js_video_id_{$aVideo.video_id}">	
		<div class="video_width_holder">
			<div class="video_height_holder">
				<div class="js_outer_video_div js_mp_fix_holder image_hover_holder">
				{if ((Phpfox::getUserParam('videochannel.can_edit_own_video') && $aVideo.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('videochannel.can_edit_other_video'))
					|| ((Phpfox::getUserParam('videochannel.can_delete_own_video') && $aVideo.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('videochannel.can_delete_other_video'))
					|| (defined('PHPFOX_IS_PAGES_VIEW') && Phpfox::getService('pages')->isAdmin('' . $aPage.page_id . ''))
				}				
					<a href="#" class="image_hover_menu_link">{phrase var='videochannel.link'}</a>
					<div class="image_hover_menu">
						<ul class="dropdown-menu">
						 
						{if Phpfox::getUserParam('videochannel.can_approve_videos') && $aVideo.view_id == 2 }
							<li class="item_approve"><a href="#" title="{phrase var='videochannel.approve'}" onclick="$.ajaxCall('videochannel.approve', 'inline=true&amp;video_id={$aVideo.video_id}'); $(this).parent().hide();
$(this).parents('.js_video_parent:first').find('.row_pending_link:first').hide(); return false;">{phrase var='videochannel.approve'}</a>
							</li>
						{/if}
						
						{if ((Phpfox::getUserParam('videochannel.can_delete_own_video') && $aVideo.user_id == Phpfox::getUserId()) || (Phpfox::getUserParam('videochannel.can_delete_other_video') && $aVideo.user_id != Phpfox::getUserId()))
						|| (defined('PHPFOX_IS_PAGES_VIEW') && Phpfox::getService('pages')->isAdmin('' . $aPage.page_id . ''))
						}
							<li class="item_delete"><a href="#" title="{phrase var='videochannel.delete_this_video'}" onclick="if (confirm('{phrase var='videochannel.are_you_sure' phpfox_squote=true}')) $.ajaxCall('videochannel.delete', 'video_id={$aVideo.video_id}'); return false;">{phrase var='videochannel.delete'}</a></li>
						{/if}						
						
						{if !defined('PHPFOX_IS_PAGES_VIEW')}						
						{if $aVideo.view_id != 2}
						{if Phpfox::getUserParam('videochannel.can_feature_videos_')}
							<li id="js_feature_{$aVideo.video_id}"{if $aVideo.is_featured} style="display:none;"{/if}><a href="#" title="{phrase var='videochannel.feature_this_video'}" onclick="$(this).parent().hide(); $('#js_unfeature_{$aVideo.video_id}').show(); $(this).parents('.js_video_parent:first').addClass('row_featured_image').find('.js_featured_video:first').show(); $.ajaxCall('videochannel.feature', 'video_id={$aVideo.video_id}&amp;type=1'); return false;">{phrase var='videochannel.feature'}</a></li>
							<li id="js_unfeature_{$aVideo.video_id}"{if !$aVideo.is_featured} style="display:none;"{/if}><a href="#" title="{phrase var='videochannel.un_feature_this_video'}" onclick="$(this).parent().hide(); $('#js_feature_{$aVideo.video_id}').show(); $(this).parents('.js_video_parent:first').removeClass('row_featured_image').find('.js_featured_video:first').hide(); $.ajaxCall('videochannel.feature', 'video_id={$aVideo.video_id}&amp;type=0'); return false;">{phrase var='videochannel.un_feature'}</a></li>
						{/if}			
						{/if}
						{/if}
						{if (Phpfox::getUserParam('videochannel.can_edit_own_video') && $aVideo.user_id == Phpfox::getUserId()) || (Phpfox::getUserParam('videochannel.can_edit_other_video') && $aVideo.user_id != Phpfox::getUserId())}
							<li><a href="#" title="{phrase var='videochannel.edit_this_video'}" onclick="$.ajaxCall('videochannel.edit', 'video_id={$aVideo.video_id}'); return false;">{phrase var='videochannel.edit'}</a></li>
						{/if}					
						{plugin call='videochannel.template_block_entry_3'}
						</ul>
					</div>				
				{/if}				
				{if !empty($aVideo.duration)}
					<div class="video_duration">
						{$aVideo.duration}
					</div>
				{/if}					
			
					{plugin call='videochannel.template_block_entry_2'}
					<div class="js_spotlight_video row_featured_link"{if !$aVideo.is_spotlight} style="display:none;"{/if}>
					  {phrase var='video.spotlight'}
					</div>							
					{if isset($sPublicPhotoView) && $sPublicPhotoView == 'featured'}
					{else}
					<div class="js_featured_video row_featured_link"{if !$aVideo.is_featured} style="display:none;"{/if}>
						{phrase var='videochannel.featured'}
					</div>					
					{/if}
					<div class="row_pending_link"{if $aVideo.view_id != 2} style="display:none;"{/if}>
						{phrase var='videochannel.pending'}
					</div>
					<div class="js_sponsor_video row_sponsored_link"{if !$aVideo.is_sponsor} style="display:none;"{/if}>
						{phrase var='videochannel.sponsored'}
					</div>					
					{if Phpfox::getUserParam('videochannel.can_approve_videos') || (Phpfox::getUserParam('videochannel.can_delete_other_video') && $aVideo.user_id != Phpfox::getUserId() )}					
					<div class="video_moderate_link"><a href="#{$aVideo.video_id}" class="moderate_link" rel="videochannel">{phrase var='videochannel.moderate'}</a>					  
					</div>				
					{/if}
					<a href="{$aVideo.link}" class="js_video_title_{$aVideo.video_id}">{img server_id=$aVideo.image_server_id path='core.url_pic' file=$aVideo.image_path suffix='_120' max_width=120 max_height=90 class='js_mp_fix_width video_image_border' title=$aVideo.title}</a>
				</div>
			</div>			
			{plugin call='videochannel.template_block_entry_4'}
			<a href="{$aVideo.link}" class="row_sub_link js_video_title_{$aVideo.video_id}" id="js_video_title_{$aVideo.video_id}">{$aVideo.title|clean|shorten:30:'...'|split:20}</a>
			<div class="extra_info_link">
				{if isset($sPublicPhotoView) && $sPublicPhotoView == 'most-discussed'}
					{phrase var='videochannel.comments_total_comment' total_comment=$aVideo.total_comment}<br />
				{elseif isset($sPublicPhotoView) && $sPublicPhotoView == 'popular'}				
					{phrase var='videochannel.total_score_out_of_10' total_score=$aVideo.total_score|round} <br />
				{else}
				{if $aVideo.total_view == 0}
					  {phrase var='videochannel.0_view'}<br />
				{elseif $aVideo.total_view == 1}
					  {phrase var='videochannel.1_view'}<br />
				{else}
					{phrase var='videochannel.total_views_views' total_views=$aVideo.total_view}<br />
				{/if}				
				{/if}
				{if !defined('PHPFOX_IS_USER_PROFILEs')}			
					{phrase var='videochannel.by_full_name' full_name=$aVideo|user}			
				{/if}
				{plugin call='videochannel.template_block_entry_5'}
			</div>			
		</div>
	</div>
	{plugin call='videochannel.template_block_entry_6'}