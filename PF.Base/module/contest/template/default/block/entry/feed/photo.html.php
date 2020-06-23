<?php
defined('PHPFOX') or exit('NO DICE!');

?>
<div class="contest-blog-feed {if $aItem.image_path}has-image{/if}">
    {if $aItem.image_path}
    <div class="blog-feed-image" onclick="window.location.href='{$aItem.feed_link}';return false;">
        <span style="background-image: url('{$aItem.image_path}')"></span>
    </div>
    {/if}
    <div class="blog-feed-info">
        <div class="blog-title"><a href="{$aItem.url}">{$aItem.title}</a></div>
        <div class="blog-info-general">
            <span class="blog-datetime">{$aItem.time_stamp|convert_time:'core.global_update_time'}</span>
        </div>
        <div class="blog-content item_content">{$aItem.summary|stripbb|feed_strip|split:55|max_line}</div>
    </div>
</div>