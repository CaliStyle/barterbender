{if $iPage <= 1}{module name='jobposting.company.search'}{/if}
{if $bInHomepage}
    {module name='jobposting.company.featured-slideshow'}
{/if}
{if !PHPFOX_IS_AJAX}
<div>
{/if}
{if !count($aCompanies) && $iPage <= 1}
	<div>{phrase var='no_companies_found'}</div>
{else}
{foreach from=$aCompanies item=aCompany}
	{template file='jobposting.block.company.entry'}
{/foreach}

{if $bIsShowModerator}
{moderation}
{/if}


{/if}
    {if !PHPFOX_IS_AJAX}
    {pager}
</div>
{/if}
