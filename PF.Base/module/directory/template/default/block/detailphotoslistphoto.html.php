
			{if count($aPhotos) < 1}
			<div class="help-block">
				{phrase var='no_item_s_found'}.
			</div>
			{/if}
			{foreach from=$aPhotos item=aPhoto name=photos}
				<div class="{if $aPhoto.view_id == 1 && !isset($bIsInApproveMode)} row_moderate_image{/if} {if $aPhoto.is_sponsor && !Phpfox::getParam('photo.show_info_on_mouseover')} row_sponsored_image{/if}{if isset($sView) && $sView == 'featured'}{else}{if $aPhoto.is_featured && !Phpfox::getParam('photo.show_info_on_mouseover')} row_featured_image{/if}{/if} photo_row {if !Phpfox::getParam('photo.show_info_on_mouseover')}photo_row_small{else}photo_row_dynamic_view{/if}{if isset($iPhotosPerRow)} photos_per_page_{$iPhotosPerRow}{/if}" id="js_photo_id_{$aPhoto.photo_id}">
					<div class="js_outer_photo_div js_mp_fix_holder photo_row_holder"{if !Phpfox::getParam('photo.show_info_on_mouseover')} style="display: inline-block;"{/if}>

					<div class="photo_row_height image_hover_holder {if Phpfox::getParam('photo.show_info_on_mouseover')}photo_row_height_dynamic_view{/if}">
						{if Phpfox::getParam('photo.auto_crop_photo') && !Phpfox::getParam('photo.show_info_on_mouseover')}
						<div class="photo_clip_holder_main{if Phpfox::getParam('photo.show_info_on_mouseover')}_big{/if}">
							{/if}							
							{if isset($sView) && $sView == 'featured'}
							{else}
							<div class="js_featured_photo row_featured_link"{if !$aPhoto.is_featured} style="display:none;"{/if}>
							{phrase var='photo.featured'}
						</div>
						{/if}
						<div class="js_sponsor_photo row_sponsored_link"{if !$aPhoto.is_sponsor} style="display:none;"{/if}>
						{phrase var='photo.sponsored'}
					</div>
					{if isset($sView) && $sView == 'pending'}
					{else}
					<div class="js_pending_photo row_pending_link"{if $aPhoto.view_id != '1'} style="display:none;"{/if}>
					{phrase var='photo.pending'}
				</div>
				{/if}

				{if Phpfox::getUserParam('photo.can_approve_photos') || Phpfox::getUserParam('photo.can_delete_other_photos')}
				<div class="video_moderate_link"><a href="#{$aPhoto.photo_id}" class="moderate_link" rel="photo"><i class="fa"></i></a></div>
				{/if}

				{if Phpfox::getParam('photo.auto_crop_photo') && !Phpfox::getParam('photo.show_info_on_mouseover')}
				<div class="photo_clip_holder_border{if Phpfox::getParam('photo.show_info_on_mouseover')}_big{/if}">
					<a href="{$aPhoto.link}{if isset($iForceAlbumId)}albumid_{$iForceAlbumId}/{/if}{if isset($sPhotoCategory)}category_{$sPhotoCategory}/{/if}"
					   class="thickbox photo_holder_image photo_clip_holder{if Phpfox::getParam('photo.show_info_on_mouseover')}_big{/if}"
					   rel="{$aPhoto.photo_id}"
					   title="{phrase var='photo.title_by_full_name' title=$aPhoto.title|clean full_name=$aPhoto.full_name|clean}">

						{img server_id=$aPhoto.server_id path='photo.url_photo' file=$aPhoto.destination suffix='_240' max_width=240 max_height=240 title=$aPhoto.title}

					</a>
				</div>
				{if Phpfox::getParam('photo.show_info_on_mouseover')}
				{template file='photo.block.hoverinfo'}
				{/if}
				{else}
				{if ($aPhoto.mature == 0 || (($aPhoto.mature == 1 || $aPhoto.mature == 2) && Phpfox::getUserId() && Phpfox::getUserParam('photo.photo_mature_age_limit') <= Phpfox::getUserBy('age'))) || $aPhoto.user_id == Phpfox::getUserId()}
				{if Phpfox::getParam('photo.show_info_on_mouseover')}
				<a style="background-image:url('{img server_id=$aPhoto.server_id path='photo.url_photo' file=$aPhoto.destination suffix='_240' return_url=true}')" href="{$aPhoto.link}{if isset($iForceAlbumId)}albumid_{$iForceAlbumId}/{/if}{if isset($sPhotoCategory)}category_{$sPhotoCategory}/{/if}" title="{phrase var='photo.title_by_full_name' title=$aPhoto.title|clean full_name=$aPhoto.full_name|clean}" class="thickbox photo_holder_image photo_set_cover photo_clip_holder{if Phpfox::getParam('photo.show_info_on_mouseover')}_big{/if}" rel="{$aPhoto.photo_id}">{$aPhoto.title}</a>
				{else}
				<a href="{$aPhoto.link}{if isset($iForceAlbumId)}albumid_{$iForceAlbumId}/{/if}{if isset($sPhotoCategory)}category_{$sPhotoCategory}/{/if}" title="{phrase var='photo.title_by_full_name' title=$aPhoto.title|clean full_name=$aPhoto.full_name|clean}" class="thickbox photo_holder_image photo_set_cover_small" rel="{$aPhoto.photo_id}">{img defer=true server_id=$aPhoto.server_id path='photo.url_photo' file=$aPhoto.destination suffix='_240' max_height='150' max_width='150' title=$aPhoto.title}</a>
				{/if}
				{if Phpfox::getParam('photo.show_info_on_mouseover')}
				{template file='photo.block.hoverinfo'}
				{/if}
				{else}
				<a href="{$aPhoto.link}{if isset($iForceAlbumId)}albumid_{$iForceAlbumId}/{/if}"{if $aPhoto.mature == 1} onclick="tb_show('{phrase var='photo.warning' phpfox_squote=true}', $.ajaxBox('photo.warning', 'height=300&amp;width=350&amp;link={$aPhoto.link}')); return false;"{/if} class="no_ajax_link">{img theme='misc/no_access.png' alt=''}</a>
				{/if}
				{/if}

				{if Phpfox::getParam('photo.auto_crop_photo') && !Phpfox::getParam('photo.show_info_on_mouseover')}
				</div>
				{/if}
				</div>

				<div class="photo_row_info">
					{if !isset($bIsInAlbumMode) && !Phpfox::getParam('photo.show_info_on_mouseover')}
					<div class="extra_info_link">
						{phrase var='photo.by_user_info' user_info=$aPhoto|user|shorten:30:'...'|split:20}
						{if !empty($aPhoto.album_name)}
						<div>{phrase var='photo.in'} <a href="{permalink module='photo.album' id=$aPhoto.album_id title=$aPhoto.album_name}" title="{$aPhoto.album_name|clean}">{if $aPhoto.album_profile_id > 0}{phrase var='photo.profile_pictures'}{else}{$aPhoto.album_name|clean|shorten:45:'...'|split:20}{/if}</a></div>
						{/if}
					</div>
					{/if}
					{if isset($sView) && $sView == 'top-rated'}
					<div class="p_2">
						{phrase var='photo.total_rating_out_of_5' total_rating=$aPhoto.total_rating|round}
					</div>
					{elseif isset($sView) && $sView == 'top-battle'}
					<div class="p_2">
						{phrase var='photo.total_battle_win_s' total_battle=$aPhoto.total_battle}
					</div>
					{/if}
				</div>
				</div>
				</div>

				{if (!Phpfox::getParam('photo.show_info_on_mouseover') && isset($phpfox.iteration.photos) && is_int($phpfox.iteration.photos/$iPhotosPerRow))}
				<div class="clear"></div>
				{/if}

			{/foreach}


			{literal}
			<script type="text/javascript">
				$Behavior.globalInit();
			</script>
			{/literal}