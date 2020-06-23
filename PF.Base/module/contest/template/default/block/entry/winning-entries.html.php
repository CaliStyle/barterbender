{if count($aEntries) > 0}
    {if $aContest.type_name == 'blog' || $aContest.type_name == 'music'}
        <div class="wrap_list_items list_items_tabs">
        {foreach from=$aEntries item=aEntry}
        	{template file='contest.block.entry.listing-item-entries'}
        {/foreach}
        </div>
    {else}
        <div class="wrap_list_items list_items_tabs">
        {foreach from=$aEntries item=aEntry}
            {template file='contest.block.entry.listing-item-entries-large'}
        {/foreach}
        </div>
    {/if}
    <div>
      {template file='contest.block.pager'}
    </div>
    {if (!isset($is_hidden_action) || $is_hidden_action!=1)}
    <div class="clear"></div>
   	{moderation}
    {/if}
{else}
    {phrase var='contest.currently_there_is_not_any_winning_entries' link=$sUrl}
{/if}
