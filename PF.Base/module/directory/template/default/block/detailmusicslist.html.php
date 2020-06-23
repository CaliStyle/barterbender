<div>
	{if count($aMusics) < 1}
		<div class="help-block">
			{phrase var='no_item_s_found'}.
		</div>
	{/if}
	<div class="yndirectory-content-row">
	{foreach from=$aMusics name=songs item=aSong}
		<div class="yndirectory-music-item">			
			<div class="yndirectory-content-row-image">
				{img user=$aSong suffix='_50_square' max_width=50 max_height=50}
			</div>
			<div class="yndirectory-content-row-info">
				<div>
					<div class="yndirectory-music-item-play">
						<a href="{permalink module='music' id=$aSong.song_id title=$aSong.title}" title="{phrase var='music.play'}: {$aSong.title|clean phpfox_squote=true}"><img
                                    src="{$sIconPath}" alt=""></a>
					</div>
					<div class="yndirectory-item-title">
						<a href="{permalink module='music' id=$aSong.song_id title=$aSong.title}" class="link" title="{$aSong.title|clean}" {if defined('PHPFOX_IS_POPUP')} onclick="window.opener.location.href=this.href; return false;"{/if}>{$aSong.title|clean|shorten:35:'...'|split:20}</a>						
					</div>
					{if !empty($aSong.album_name)}
					<div class="yndirectory-item-info extra_info">
						<a href="{permalink module='music.album' id=$aSong.album_id title=$aSong.album_name}" title="{$aSong.album_name|clean}">{$aSong.album_name|clean|shorten:55:'...'|split:40}</a>
					</div>
					{/if}					
				</div>
				<div>
					<ul class="extra_info_middot"><li>{$aSong.time_stamp|convert_time} {phrase var='music.by_lowercase'} {$aSong|user:'':'':50}</li>{if $aSong.total_play > 1}<li><span>&middot;</span></li><li>{phrase var='music.total_plays' total=$aSong.total_play}</li>{/if}</ul>
				</div>
				{*
				{module name='feed.comment' aFeed=$aSong.aFeed}
				*}
			</div>
		</div>
	{/foreach}
	</div>
	
	<div class="clear"></div>
	{module name='directory.paging'}
</div>

{if PHPFOX_IS_AJAX}
{literal}
<script type="text/javascript">
	$Core.loadInit();
</script>
{/literal}
{/if}