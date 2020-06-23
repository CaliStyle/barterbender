<?php

defined('PHPFOX') or exit('NO DICE!');
?>

<!-- Main entry -->
<div class="yc_small_item">
	<div class="item_left">
		<a href="{permalink module='contest' id=$aItem.contest_id title=$aItem.contest_name}entry_{$aItem.entry_id}/" title="{$aItem.title|clean}">
			{if $aItem.image_path!=''}
				{if $aItem.type!=3}
				{img server_id=$aItem.server_id path='core.url_pic' file=$aItem.image_path suffix='_200' max_width='150' max_height='90' class='js_mp_fix_width'}
				{else}
				{img server_id=$aItem.server_id path='core.url_pic' file=$aItem.image_path suffix='_120' max_width='150' max_height='90' class='js_mp_fix_width'}
				{/if}
			{else}
				{img server_id=$aItem.server_id path='core.url_pic' file="user/".$aItem.user_image suffix='_200' max_width=150 max_height='90' class='js_mp_fix_width'} 
			{/if}
		</a>
	</div>
	<div class="item_right">
		<p>
			<a class="small_title" href="{permalink module='contest' id=$aItem.contest_id title=$aItem.contest_name}entry_{$aItem.entry_id}/" title="{$aItem.title|clean}">
				{$aItem.title}
			</a>
			<div class="extra_info">
				<p>{phrase var='contest.by'} <a href="{url link=''}{$aItem.user_name}/">{$aItem.full_name}</a></p>
				<p>{phrase var='contest.prize'} {$aItem.rank}</p>
				<p>{$aItem.award}</p>
				<p>{phrase var='contest.vote'}: {$aItem.total_vote}</p>
			</div>
		</p>
	</div>
</div>
