<?php
defined('PHPFOX') or exit('NO DICE!');

?>
<div class="social-ad-feed {if !empty($aFeedAd.full_ad_image)}has-image{/if}">
    {if !empty($aFeedAd.full_ad_image)}
    <div class="social-ad-feed-image">
    	<a href="{$aFeedAd.feed_link}">
        	<span class="item-media" style="background-image: url({$aFeedAd.full_ad_image})"></span>
    	</a>
    </div>
    {/if}
    <div class="social-ad-feed-info">
        <div class="social-ad-title"><a href="{$aFeedAd.feed_link}" class="js_add_feed_title">{$aFeedAd.feed_title}</a></div>
        <div class="social-ad-content item_content js_add_feed_text">{$aFeedAd.full_ad_info|stripbb|feed_strip|split:55|max_line}</div>
    </div>
</div>