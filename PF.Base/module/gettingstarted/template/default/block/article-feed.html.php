<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="padding-1 mt-2 item_view_content">
    <a class="activity_feed_content_link_title" href="{$link}">{$article.title}</a>
    <div class="activity_feed_content_display">
        {if strpos($article.text, '<br/>') >= 200}
        {$article.text|feed_strip|shorten:200:'feed.view_more':true|split:55|max_line}
        {else}
        {$article.text|feed_strip|split:55|max_line|shorten:200:'feed.view_more':true}
        {/if}
    </div>
</div>
