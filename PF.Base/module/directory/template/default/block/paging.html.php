<div class="clear"></div>
<div class="pager_yndirectory">
{if !$bNoResultText}
	<div class="result">
	{$sResultPhrase}
	</div>
{/if}
	<div class="page_list">
	<span>
	{if $bHavingPrevious}
	<a class="preview btn bt-default btn-xs" href="#" onclick="$(document).trigger('changepage', {$iPreviousPage}); return false;"> <span class="ico  ico-angle-left"></span> </a> 
	{else}
	<a class="preview not btn btn-default btn-xs" href="javascript:void(0);"><span class="ico  ico-angle-left"></span></a> 
	{/if}

	{if $bHavingNext}
	<a class="next btn btn-default btn-xs" href="#" onclick="$(document).trigger('changepage', {$iNextPage}); return false;"><span class="ico ico-angle-right"></span></a> 
	{else}
	<a class="next not btn btn-default btn-xs" href="javascript:void(0);"><span class="ico ico-angle-right"></span></a> 
	{/if}
	</span>
	</div>

</div>
<div class="clear"></div>