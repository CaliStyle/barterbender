{if count($aRecentContests)>0}
<div class="block yc_content_block">
    <div class="title">
    	<span>{phrase var='contest.recent_contests'}</span>
    </div>
    <div class="content">
        <div class="wrap_list_items">
        	{foreach from=$aRecentContests  name=contest item=aItem}
        		{template file='contest.block.entry.listing-item'}
        	{/foreach}
        </div>
        {if $iCntRecentContests>$iLimit}
        <div class="text-center">
            <a href="{url link='contest'}sort_latest/" class="yc_view_more button btn btn-success btn-sm"> {phrase var='contest.view_more'}</a>
        </div>
        {/if}
    </div>
</div>
{/if}
