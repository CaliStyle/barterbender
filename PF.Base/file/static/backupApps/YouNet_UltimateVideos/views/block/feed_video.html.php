{if isset($aParentFeed) && $aParentFeed.type_id == 'ultimatevideo_video'}
<div class="ultimatevideo-app ultimatevideo-feed-video core-feed-item">
    <div class="item-outer">
        <div class="item-media">
            {if isset($aParentFeed.embed_code)}{$aParentFeed.embed_code}{/if}
        </div>
        <div class="item-inner">
            <div class="item-title">
                <a href="{$aParentFeed.feed_link}" class="core-feed-title line-2">{$aParentFeed.feed_title}</a>
                <div class="item-action-container">
                    {if $aItem.total_view}
                        <div class="item-view">
                            {$aItem.total_view|short_number} <span class="p-text-lowercase">{$aItem.total_view|p_ultimatevideo_n:'view':'views'}</span>
                        </div>
                    {/if}
                    {if Phpfox::isUser() && $aItem.is_approved && $aItem.status}
                        <div class="item-action">
                            {template file='ultimatevideo.block.link_video_viewer'}
                        </div>
                    {/if}
                </div>
            </div>
            <div class="item-category">
                <div class="core-feed-minor p-seperate-dot-wrapper">
                    {if $aItem.duration}
                        <div class="p-seperate-dot-item">
                            {$aItem.duration|ultimatevideo_duration}
                        </div>
                    {/if}
                    {if $aItem.sCategory}
                        <div class="p-seperate-dot-item">
                            <span class="category-title">{_p var='category'}:</span> {$aItem.sCategory}
                        </div>
                    {/if}
                </div>
            </div>
            <div class="item-description">
                <div class="core-feed-description item_content line-2">
                    {$aParentFeed.feed_content|feed_strip|split:55|max_line|stripbb}
                </div>
            </div>
        </div>
    </div>
</div>
{else}
<div class="ultimatevideo-app ultimatevideo-feed-video core-feed-item">
    <div class="item-outer">
        <div class="item-media">
            {if isset($aFeed.embed_code)}{$aFeed.embed_code}{/if}
        </div>
        <div class="item-inner">
            <div class="item-title">
                <a href="{$aFeed.feed_link}" class="core-feed-title line-2">{$aFeed.feed_title}</a>
                <div class="item-action-container">
                    {if $aItem.total_view}
                        <div class="item-view">
                            {$aItem.total_view|short_number} <span class="p-text-lowercase">{$aItem.total_view|p_ultimatevideo_n:'view':'views'}</span>
                        </div>
                    {/if}
                    {if Phpfox::isUser() && $aItem.is_approved && $aItem.status}
                    <div class="item-action">
                        {template file='ultimatevideo.block.link_video_viewer'}
                    </div>
                    {/if}
                </div>
            </div>
            <div class="item-category">
                <div class="core-feed-minor p-seperate-dot-wrapper">
                    {if $aItem.duration}
                    <div class="p-seperate-dot-item">
                        {$aItem.duration|ultimatevideo_duration}
                    </div>
                    {/if}
                    {if $aItem.sCategory}
                        <div class="p-seperate-dot-item">
                            <span class="category-title">{_p var='category'}:</span> {$aItem.sCategory}
                        </div>
                    {/if}
                </div>
            </div>
            <div class="item-description">
                <div class="core-feed-description item_content line-2">
                    {$aFeed.feed_content|feed_strip|split:55|max_line|stripbb}
                </div>
            </div>
        </div>
    </div>
</div>
{/if}
{unset var=$aParentFeed}