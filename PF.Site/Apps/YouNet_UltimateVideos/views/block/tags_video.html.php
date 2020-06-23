{if count($aRows)}
    <div class="ultimatevideo-tag-container">
        {foreach from=$aRows item=aRow}
            <a href="{$aRow.link }" class="ultimatevideo-tag-item">{$aRow.text}</a>
        {/foreach}
    </div>
{else}
    <div class="message">
        {phrase var='tag.no_tags_have_been_found'}
    </div>
{/if}
