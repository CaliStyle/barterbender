{if $iPage <= 1}{module name='jobposting.job.search'}{/if}
{if $bInHomepage }
    {module name='jobposting.job.featured-slideshow'}
{/if}
{if !PHPFOX_IS_AJAX}
<div>
{/if}
{if !count($aJobs) && $iPage <= 1}
	<div>{phrase var='no_jobs_found'}</div>
{else}

    {foreach from=$aJobs item=aJob}
        {template file='jobposting.block.job.entry'}
    {/foreach}

    {if $bIsShowModerator}
        {moderation}
    {/if}
{/if}


{if !PHPFOX_IS_AJAX}
    {pager}
</div>
{/if}
