<?php

defined('PHPFOX') or exit('NO DICE!');
?>
<!-- Main entry -->
<div class="yc_small_item">
    {if $aItem.image_path}
    <div class="item_left" onclick="window.location.href='{permalink module='contest' id=$aItem.contest_id title=$aItem.contest_name}'" style="background-image:url('{img server_id=$aItem.server_id return_url=true path='core.url_pic' file='contest/'.$aItem.image_path suffix=''}')"></div>
    {else}
    <div class="item_left" onclick="window.location.href='{permalink module='contest' id=$aItem.contest_id title=$aItem.contest_name}'" style="background-image:url('{$sUrlNoImagePhoto}')"></div>
    {/if}
	<div class="item_right">
		<p>
			<a class="small_title" href="{permalink module='contest' id=$aItem.contest_id title=$aItem.contest_name}" title="{$aItem.contest_name|clean}">
				{$aItem.contest_name|clean}
			</a>
			<div class="extra_info">
				<p>{phrase var='contest.participants'}: {$aItem.total_participant}</p>
				<p>{phrase var='contest.entries'}: {$aItem.total_entry}</p>
				<p>{phrase var='contest.end'}: {$aItem.end_time_parsed}</p>
			</div>
		</p>
	</div>
</div>
