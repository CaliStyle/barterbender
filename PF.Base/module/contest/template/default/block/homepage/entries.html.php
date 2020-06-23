<script>
$Behavior.initEntriesTab = function() {l}
	$( "#tabs" ).tabs();
{r};
</script>

<style>
.ui-tabs{l}
	padding: 0px;
{r}
</style>

<div class='yncontest_tab'>
  <div id="tabs">
    <ul>
        <li><a href="#tabs-1">{phrase var='contest.photo_entries'}</a></li>
        <li><a href="#tabs-2">{phrase var='contest.video_entries'}</a></li>
        <li><a href="#tabs-3">{phrase var='contest.blog_entries'}</a></li>
        <li><a href="#tabs-4">{phrase var='contest.music_entries'}</a></li>
    </ul>
    <div class='filter_entries'>
    <select class="form-control" id="entries-filter" onchange="yncontest.homepage.changeFilter()">
        <option value="recent">{phrase var='contest.recent_entries'}</option>
        <option value="most_voted">{phrase var='contest.most_voted_entries'}</option>
    </select>
    </div>
    <div id="tabs-1">
        {if count($aRecentEntries.photo)}
        <div class="wrap_list_items list_items_tabs recent-entries">
            {foreach from=$aRecentEntries.photo name=entry item=aEntry}
                {template file='contest.block.entry.listing-item-entries-large'}
            {/foreach}
        </div>
        {/if}
        {if count($aMostVotedEntries.photo)}
        <div class="wrap_list_items list_items_tabs most-voted-entries" style="display: none;">
            {foreach from=$aMostVotedEntries.photo name=entry item=aEntry}
                {template file='contest.block.entry.listing-item-entries-large'}
            {/foreach}
        </div>
        {/if}
        <div class="clear"></div>
    </div>

    <div id="tabs-2">
        {if count($aRecentEntries.video)}
        <div class="wrap_list_items list_items_tabs recent-entries">
            {foreach from=$aRecentEntries.video name=entry item=aEntry}
                {template file='contest.block.entry.listing-item-entries-large'}
            {/foreach}
        </div>
        {/if}
        {if count($aMostVotedEntries.video)}
        <div class="wrap_list_items list_items_tabs most-voted-entries" style="display: none;">
            {foreach from=$aMostVotedEntries.video name=entry item=aEntry}
                {template file='contest.block.entry.listing-item-entries-large'}
            {/foreach}
        </div>
        {/if}
        <div class="clear"></div>
    </div>

    <div id="tabs-3">
        {if count($aRecentEntries.blog)}
        <div class="recent-entries wrap_list_items">
            {foreach from=$aRecentEntries.blog name=entry item=aEntry}
                {template file='contest.block.entry.listing-item-entries'}
            {/foreach}
        </div>
        {/if}
        {if count($aMostVotedEntries.blog)}
        <div class="most-voted-entries wrap_list_items" style="display: none;">
            {foreach from=$aMostVotedEntries.blog name=entry item=aEntry}
                {template file='contest.block.entry.listing-item-entries'}
            {/foreach}
        </div>
        {/if}
        <div class="clear"></div>
    </div>

    <div id="tabs-4">
        {if count($aRecentEntries.music)}
        <div class="recent-entries wrap_list_items">
            {foreach from=$aRecentEntries.music name=entry item=aEntry}
                {template file='contest.block.entry.listing-item-entries'}
            {/foreach}
        </div>
        {/if}
        {if count($aMostVotedEntries.music)}
        <div class="most-voted-entries wrap_list_items" style="display: none;">
            {foreach from=$aMostVotedEntries.music name=entry item=aEntry}
                {template file='contest.block.entry.listing-item-entries'}
            {/foreach}
        </div>
        {/if}
        <div class="clear"></div>
    </div>
  </div>
</div>
