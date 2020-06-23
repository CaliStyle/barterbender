<?php
?>
{if isset($aTotalGroups) and count($aTotalGroups) > 0}
	{foreach from=$aTotalGroups name=totalGroups item=totalGroups}
		<div class="table_left">{phrase var=$totalGroups.group_name}</div>
		{foreach from=$totalGroups name=aGroups item=aGroups}
			{if is_array($aGroups)}
				{$aGroups.html}
			{/if}
		{/foreach}
	{/foreach}
{/if}