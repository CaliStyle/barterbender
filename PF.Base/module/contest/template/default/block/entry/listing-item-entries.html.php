	<div class="yc large_item  ycs_item_list list_items_blogmusic image_hover_holder moderation_row">
        {if isset($showaction) && $showaction==true && (!isset($is_hidden_action) || $is_hidden_action!=1)}
            <div class="moderation_row" style="position: absolute;top: 0;">
                <label class="item-checkbox">
                    <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aEntry.entry_id}" id="check{$aEntry.entry_id}" />
                    <i class="ico ico-square-o"></i>
                </label>
            </div>
        {/if}
		<div class="ycs_item_list_inner">
			<div class="yc_view_image">
                {if !empty($aEntry.image_path)}
                {img server_id=$aEntry.server_id path='core.url_pic' file=$aEntry.image_path suffix='' style='width: 50px;height: 50px;'}
                {else}
                {img user=$aEntry suffix='_50_square'}
                {/if}

				{if $aEntry.status_entry==0}
				<span class="small_pending">{phrase var='contest.pending'}</span>
				{elseif $aEntry.status_entry==2}
				<span class="small_pending denied">{phrase var='contest.denied'}</span>
				{elseif $aEntry.status_entry==3}
				<span class="small_pending draft">{phrase var='contest.draft'}</span>
				{/if}
			</div>
			<div class="large_item_info">
				{if isset($aEntry.contest_name)}

					<a class="small_title" href="{permalink module='contest' id=$aEntry.contest_id title=$aEntry.contest_name}entry_{$aEntry.entry_id}/" title="{$aEntry.title}">
						{$aEntry.title|clean|shorten:18:'...'|split:18}
					</a>

			        {if (isset($bInHomepage) && $bInHomepage) || (isset($bIsEntryIndex) && $bIsEntryIndex)}
			        	<p>{phrase var='contest.in'} <a href="{permalink module='contest' id=$aEntry.contest_id title=$aEntry.contest_name}" title="{$aEntry.contest_name}">{$aEntry.contest_name|clean|shorten:25:'...'|split:25}</a></p>
			        {/if}
		        {/if}
		        {if isset($sView) && $sView == 'winning'}
				<div class="extra_info">
					<p title="{$aEntry.award|clean}">{$aEntry.award|clean|shorten:50:'...'|split:50}</p>
				</div>
		        {/if}
				<ul class="large_item_action">
					<li class="ycvotes">
						<p>{$aEntry.total_vote}</p>
					</li>
					<li class="ycviews">
						<p>{$aEntry.total_view}</p>
					</li>
				</ul>
			</div>
			{if isset($sView) && $sView == 'winning'}
			<div class="entries_win">
				{$aEntry.rank}
			</div>
			{/if}
		    {if isset($sView) && $sView=='pending_entries' && $aEntry.have_action_on_entry}
			<a href="#" class="image_hover_menu_link">{phrase var='contest.link'}</a>
			<div class="image_hover_menu">
				<ul>
					{template file='contest.block.entry.action-link'}
				</ul>
			</div>
			{/if}
		</div>
	</div>
