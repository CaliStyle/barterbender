{if !empty($aItems)}
<div class="ultimatevideo-grid clearfix">
    {foreach from=$aItems name=video item=aItem}
    {template file='ultimatevideo.block.entry'}
    {/foreach}
</div>
{/if}