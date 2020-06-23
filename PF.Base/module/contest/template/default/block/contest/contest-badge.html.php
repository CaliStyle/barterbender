<div class="yc large_item">
	{if $bIsShowContestPhoto}
		<div class="yc_view_image">
			<div class="large_item_image">
				<a class="contest_thumb" href="{permalink module='contest' id=$aContest.contest_id title=$aContest.contest_name}" title="{$aContest.contest_name|clean}" style="background-image:url({img return_url=true server_id=$aContest.server_id path='core.url_pic' file='contest/'.$aContest.image_path suffix='' max_width=150})">
				</a>
			</div>
		</div>
	{/if}

	<div class="large_item_info">
		<a class="small_title" href="{permalink module='contest' id=$aContest.contest_id title=$aContest.contest_name}" title="{$aContest.contest_name|clean}" target="_blank" style="padding-bottom: 5px; text-decoration: none;">
			{$aContest.contest_name|clean|shorten:50:'...'}
		</a>

		{if $bIsShowDescription}
		<div class="extra_info item_view_content" style="word-wrap: break-word;">
			{$aContest.short_description|shorten:200:'...'}
		</div>
		{/if}
	</div>

 	<button type='button' class="contest_button" name='val[join]' target="_blank" onclick="window.open( '{permalink module='contest' id=$aContest.contest_id title=$aContest.contest_name}'); return false;">{phrase var='contest.join'} {phrase var='contest.contest'}</button>


</div>
