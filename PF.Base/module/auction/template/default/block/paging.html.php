
<div class="clear"></div>
<div class="pager_ynauction">
{if !$bNoResultText}
	<div class="result">
	{$sResultPhrase}
	</div>
{/if}
	<div class="page_list">
	<span>
	{if $bHavingPrevious}
	<a class="preview" href="#" onclick="$(document).trigger('changepage', {$iPreviousPage}); return false;"> Previous </a> 
	{else}
	<a class="preview not" href="javascript:void(0);"> Previous </a> 
	{/if}

	{if $bHavingNext}
	<a class="next" href="#" onclick="$(document).trigger('changepage', {$iNextPage}); return false;"> Next </a> 
	{else}
	<a class="next not" href="javascript:void(0);"> Next </a> 
	{/if}
	</span>
	</div>

</div>
<div class="clear"></div>