<div class="yconstest_detail_voted {if $aEntry.offaction==0} has-action {/if}">
	<div class="ycs_item_list">
		<div class="large_item_info">
			<div>
				<a href="{permalink module='contest' id=$aEntry.contest_id title=$aEntry.contest_name}entry_{$aEntry.entry_id}/">
                    <h2 class="yc_view_contest_name fw-bold">{$aEntry.title}</h2>
                </a>
				<div class="extra_info">
					<p>{phrase var='contest.posted_by'}: {$aEntry|user}</p>
				</div>
                <div class="large_item_action">
                    {if $aEntry.is_winning && !empty($aEntry.award)}
                    <div class="ycaward">{phrase var='contest.award'}: <strong>{$aEntry.award}</strong></div>
                    {/if}
                    <div class="ycvotes">
                        <strong>{$aEntry.total_vote}</strong>
                    </div>
                    <div class="ycviews">
                        <strong>{$aEntry.total_view}</strong>
                    </div>
                </div>
			</div>
            {if $aEntry.offaction==0}
            <div class="item_bar">
                <div class="item_bar_action_holder">
                    <a role="button" data-toggle="dropdown" class="item_bar_action"><i class="ico ico-gear-o"></i></a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        {template file='contest.block.entry.action-link'}
                    </ul>
                </div>
            </div>
            {/if}
		</div>
		<div class="next_voted">
            <div class="pager_links_holder">
    			<div class="pager_links">
    				{if empty($aEntry.previous)}
                    <a class="pager_previous_link pager_previous_link_not" href="#" onclick="return false;" title="{phrase var='contest.previous'}">{phrase var='contest.previous'}</a>
                    {else}
                    <a class="pager_previous_link" href="{permalink module='contest' id=$aEntry.contest_id title=$aEntry.contest_name}entry_{$aEntry.previous.entry_id}/" title="{phrase var='contest.previous'}">{phrase var='contest.previous'}</a>
                    {/if}
                    {if empty($aEntry.next)}
    				<a class="pager_next_link pager_next_link_not" href="#" onclick="return false;" title="{phrase var='contest.next'}">{phrase var='contest.next'}</a>
                    {else}
                    <a class="pager_next_link" href="{permalink module='contest' id=$aEntry.contest_id title=$aEntry.contest_name}entry_{$aEntry.next.entry_id}/" title="{phrase var='contest.next'}">{phrase var='contest.next'}</a>
                    {/if}
                    <div class="clear"></div>
    			</div>
            </div>
			{if $aEntry.can_vote_entry}
                {if !$aEntry.is_voted}
                <a class="yvoted button btn btn-primary btn-sm" href="#" onclick="$.ajaxCall('contest.addVote','entry_id={$aEntry.entry_id}&is_voted=0'); return false;">{phrase var='contest.vote_for_this_entry'}</a>
                {else}
                <a class="yvoted button btn btn-primary btn-sm" href="#" onclick="$.ajaxCall('contest.addVote','entry_id={$aEntry.entry_id}&is_voted=1'); return false;">{phrase var='contest.un_vote_this_entry'}</a>
                {/if}
            {/if}
            {if $aEntry.is_winning}
        	<div class="entries_win">
        		{$aEntry.rank}
        	</div>
        	{/if}
		</div>
	</div>
    <div class="clear"></div>
	<div class="table_content">
		{module name=$sTemplateViewPath aYnEntry=$aEntry}
		<p class="yc_promote">
			{phrase var='contest.promote_entry'}
			<input type="text" class="form-control" value="{$aEntry.bitlyUrl}" />
		</p>
	</div>
	<div class="line_sep"></div>
	<div class="ydetail_description">
		<h3>{phrase var='contest.description'}</h3>
		{$aEntry.summary|parse|shorten:'550':'comment.view_more':true|split:550}
	</div>
</div>

<div {if $aEntry.status_entry != 1}style="display:none;" class="js_moderation_on"{/if}>
	{module name='feed.comment'}
</div>
