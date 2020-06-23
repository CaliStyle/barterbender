{if !empty($reviewers)}
    {foreach from=$reviewers item=reviewer}
        {template file='advancedmarketplace.block.reviewer-entry'}
    {/foreach}
    {if $canContinuePaging}
        {pager}
    {/if}
{/if}
