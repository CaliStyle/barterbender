{foreach from=$aPremiumContests  name=contest item=aItem}
		{template file='contest.block.contest.side-block-listing-item'}
{/foreach}
{if $iCntPremiumContests>$iLimit}
<div class="text-center">
    <a href="{url link='contest'}view_premium/" class="yc_view_more button btn btn-success btn-sm"> {phrase var='contest.view_more'}</a>
</div>
{/if}