<div class="filter_entries">
{if $iPage == 0}
    <select class="form-control" id="js_select_type" onchange="yncontest.homepage.changeType()">
        <option value="blog"{if isset($sType) && $sType == 'blog'} selected="selected"{/if}>{phrase var='contest.blog'}</option>
        <option value="music"{if isset($sType) && $sType == 'music'} selected="selected"{/if}>{phrase var='contest.music'}</option>
        <option value="photo"{if isset($sType) && $sType == 'photo'} selected="selected"{/if}>{phrase var='contest.photo'}</option>
        <option value="video"{if isset($sType) && $sType == 'video'} selected="selected"{/if}>{phrase var='contest.video'}</option>
    </select>
    <div class="clear"></div>
{/if}
</div>
{if count($aEntries) > 0}
    {if $sType == 'blog' || $sType == 'music'}
        <div class="wrap_list_items">
            {foreach from=$aEntries name=entry item=aEntry}
                {template file='contest.block.entry.listing-item-entries'}
            {/foreach}
        </div>
    {else}
        <div class="wrap_list_items list_items_tabs">
        {foreach from=$aEntries name=entry item=aEntry}
            {template file='contest.block.entry.listing-item-entries-large'}
        {/foreach}
        </div>
    {/if}
    {pager}
{else}
    {if $iPage <=1}
        <div>
            {phrase var='contest.no_entry_found'}
        </div>
    {/if}
    <div class="clear"></div>
{/if}
