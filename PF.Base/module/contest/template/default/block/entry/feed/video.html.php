<div class="contest-v-feed-video feed_block_title_content feed-video-upload">
    <div class="{if $aItem.is_stream}fb_video_iframe{else}fb_video_player{/if}">{if isset($aItem.embed_code)}{$aItem.embed_code}{/if}
    </div>
    <div class="v-feed-inner">
        <a href="{$aItem.url}" class="v-feed-title activity_feed_content_link_title">{$aItem.title}</a>
        <div class="v-feed-description item_view_content">{$aItem.summary|feed_strip|split:55|max_line|stripbb}</div>
    </div>
</div>