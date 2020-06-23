{if count($aParticipants)>0}
<div class="participant-container">
{foreach from=$aParticipants  name=participant item=aItem}
	{template file='contest.block.participant.listing-item'}
{/foreach}
</div>
<div class="text-center">
    <a href="{$sViewMoreUrl}" class="yc_view_more button btn btn-success btn-sm"> {phrase var='contest.view_more'}</a>
</div>
{/if}
