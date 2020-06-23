{if count($aRows)}
    <div class="tag_cloud">
        {foreach from=$aRows item=aRow}
            <a href="{$aRow.link }">{$aRow.text}</a>
        {/foreach}
    </div>
{else}
    <div class="message">
        {phrase var='tag.no_tags_have_been_found'}
    </div>
{/if}