{if count($aEndingSoonContests)>0}
<div class="block yc_content_block">
	<div class="title">
		<span>{phrase var='contest.ending_soon'}</span>
	</div>
	<div class="content">
    	<div class="wrap_list_items">
    		{foreach from=$aEndingSoonContests  name=contest item=aItem}
    			{template file='contest.block.entry.listing-item'}
    		{/foreach}
    	</div>
	</div>
    <div class="clear text-center">
    	{if $iCntEndingSoonContests>$iLimit}
        <a href="{url link='contest'}view_ending_soon/" class="yc_view_more button btn btn-success btn-sm"> {phrase var='contest.view_more'}</a>
    	{/if}
    </div>
</div>
{/if}
