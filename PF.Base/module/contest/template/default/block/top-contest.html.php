
{foreach from=$aTopContests  name=contest item=aItem}
		{template file='contest.block.contest.side-block-listing-item'}
{/foreach}
{if $iCntTopContests>$iLimit}
<div class="text-center">
	<a href="{url link='contest'}sort_most-participant/" class="yc_view_more button btn btn-success btn-sm"> {phrase var='contest.view_more'}</a>
</div>
{/if}