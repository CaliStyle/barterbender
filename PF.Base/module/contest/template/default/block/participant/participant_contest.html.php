<ul class="yc_view_participant">
	{foreach from=$aParticipant item=aParticipant}
		<li>
			{img user=$aParticipant suffix='_50_square' max_width=50 max_height=50}
			<a href="{url link=''}{$aParticipant.user_name}/">{$aParticipant.full_name}</a>
		</li>
			
	{/foreach}
</ul>
<div>
	  {template file='contest.block.pager'}
</div>
{if count($aParticipant)==0}
<div>
	{phrase var='contest.no_participant_found'}
</div>
{/if}
