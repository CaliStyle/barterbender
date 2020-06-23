<!-- Large Item -->
<div class="yncontest_add_entry_item yc large_item yc_vew_entries">
	<a class="large_item_image" href="{permalink module='contest' id=$aItem.contest_id title=$aItem.contest_name}entry_{$aItem.entry_id}/" title="{$aItem.title|clean}" style="background-image:url('{if $aItem.image_path!=''}{if $aItem.type!=3}{img server_id=$aItem.server_id return_url=true path='core.url_pic' file=$aItem.image_path suffix='_200' max_width=150}{else}{img return_url=true path='core.url_pic' file=$aItem.image_path suffix='_120' max_width=150}{/if}{else}{img return_url=true path='core.url_pic' file='user/'.$aItem.user_image suffix='_120' max_width=200}{/if}')">

	</a>
	<div class="large_item_action">
		<div>
			<p>{phrase var='contest.votes'}</p>
			<p>{$aItem.total_vote}</p>
		</div>
		<div>
			<p>{phrase var='contest.like_s'}</p> 
			<p>{$aItem.total_like}</p>
		</div>
		<div>
			<p>{phrase var='contest.view_s'}</p>
			<p>{$aItem.total_view}</p>
		</div>
	</div>
	<div class="large_item_info">
		<p>
			<a class="small_title" href="{permalink module='contest' id=$aItem.contest_id title=$aItem.contest_name}entry_{$aItem.entry_id}/" title="{$aItem.title|clean}">
				{$aItem.title|clean|shorten:20:'...'|split:20}
			</a>
			<div class="extra_info">
				<p>{phrase var='contest.by'} <a href="{url link=''}{$aItem.user_name}/">{$aItem.full_name}</a></p>
				Contest: <a class="" href="{permalink module='contest' id=$aItem.contest_id title=$aItem.contest_name}" title="{$aItem.contest_name|clean}">
					{$aItem.contest_name|clean|shorten:15:'...'|split:20}
				</a>
				<p>{phrase var='contest.prize'} {$aItem.rank}</p>
				<p>{phrase var='contest.award'}: {$aItem.award}</p>
			</div>
		</p>
	</div>
</div>


