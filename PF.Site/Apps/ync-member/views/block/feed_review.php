<div class="feed_block_title_content  activity_feed_content_float">
    <a href="{$aFeed.feed_link}" class="activity_feed_content_link_title">{$aFeed.feed_title}</a>
    <div class="activity_feed_content_display twa_built">
        {if strpos($aFeed.feed_content, '<br />') >= 200}
        {$aFeed.feed_content|feed_strip|shorten:200:'feed.view_more':true|split:55|max_line}
        {else}
        {$aFeed.feed_content|feed_strip|split:55|max_line|shorten:200:'feed.view_more':true}
        {/if}
    </div>
</div>