<div class="yc large_item">
	<div class="large_item_image ele_relative" >
		{if $aItem.image_path}
		<div class="contest_thumb" onclick="window.location.href='{permalink module='contest' id=$aItem.contest_id title=$aItem.contest_name}'" style="background-image:url('{img server_id=$aItem.server_id return_url=true path='core.url_pic' file='contest/'.$aItem.image_path suffix=''}')"></div>
		{else}
		<div class="contest_thumb" onclick="window.location.href='{permalink module='contest' id=$aItem.contest_id title=$aItem.contest_name}'" style="background-image:url('{$sUrlNoImagePhoto}')"></div>
		{/if}
		<span class="entype {$aItem.style_type}"></span> <!-- enblog // enphoto // envideo // enmusic -->

        {if empty($sView) || $sView!='ending_soon'}
			<ul class="list_itype">
				{if $aItem.is_feature}<li class="itype enfeatured">{phrase var='contest.featured'}</li>{/if}
				{if $aItem.is_premium}<li class="itype enpremium">{phrase var='contest.premium'}</li>{/if}
				{if $aItem.is_ending_soon}<li class="itype endinsoon">{phrase var='contest.ending_soon'}</li>{/if}
			</ul>
		{/if}

        <div class="large_item_info">
			<a class="small_title" href="{permalink module='contest' id=$aItem.contest_id title=$aItem.contest_name}" title="{$aItem.contest_name|clean}">
				{$aItem.contest_name|clean}
			</a>
			<div class="extra_info">
                {if isset($sView) && $sView=='ending_soon'}
				<p class="time_left">{$aItem.contest_countdown}</p>
                {/if}
			</div>
		</div>
	</div>

	<ul class="large_item_action">
		<li>
			<p>{phrase var='contest.participants'}</p>
			<strong class="act_numcounter">{$aItem.total_participant}</strong>
		</li>
		<li>
			<p>{phrase var='contest.entries'}</p>
			<strong class="act_numcounter">{$aItem.total_entry}</strong>
		</li>
	</ul>
	<div class="large_item_info large_hover" onclick="window.location.href='{permalink module='contest' id=$aItem.contest_id title=$aItem.contest_name}'">
			<a class="small_title" href="{permalink module='contest' id=$aItem.contest_id title=$aItem.contest_name}" title="{$aItem.contest_name|clean}">
				{$aItem.contest_name|clean}
			</a>
			<div class="extra_info">
				<p>{phrase var='contest.end'}: {$aItem.end_time_parsed}</p>
				<p>{phrase var='contest.created_by'}: {$aItem|user}</p>
			</div>
		</div>
</div>


