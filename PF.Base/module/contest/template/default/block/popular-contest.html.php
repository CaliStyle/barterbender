
{foreach from=$aPopularContests  name=contest item=aItem}
		{template file='contest.block.contest.side-block-listing-item'}
{/foreach}
{if $iCntPopularContests>$iLimit}
<div class="text-center">
    <a href="{url link='contest'}sort_most-viewed/" class="yc_view_more button btn btn-success btn-sm"> {phrase var='contest.view_more'}</a>
</div>
{/if}