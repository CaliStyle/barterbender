
<div class="clear"></div>
<div class="pager_ads">
{if !$bNoResultText}
	<div class="result">
	{$sResultPhrase}
	</div>
{/if}
    {if $bHavingPrevious || $bHavingNext}
        <div class="page_list">
            <span>
            {if $bHavingPrevious}
            <a class="preview btn btn-default" href="#" onclick="$(document).trigger('changepage', {$iPreviousPage}); return false;"> Previous </a>
            {/if}

            {if $bHavingNext}
            <a class="next btn btn-default" href="#" onclick="$(document).trigger('changepage', {$iNextPage}); return false;"> Next </a>
            {/if}
            </span>
        </div>
    {/if}
</div>
<div class="clear"></div>