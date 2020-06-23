<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Feed
 * @version 		$Id: content.html.php 7160 2014-02-26 17:20:13Z Fern $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if !isset($aFeed.feed_mini)}
<div class="activity_feed_header">
	<div class="activity_feed_header_info">

		{$aFeed|user:'':'':50}{if (!empty($aFeed.parent_module_id) || isset($aFeed.parent_is_app))} {_p var='shared'}{else}{if isset($aFeed.parent_user)} <span class="ico ico-caret-right"></span> {$aFeed.parent_user|user:'parent_':'':50} {/if}{if !empty($aFeed.feed_info)}<span class="feed_info"> {$aFeed.feed_info} </span>{/if}{/if}

        <!--Extra info-->
        {if (isset($aFeed.aFeeling) && !empty($aFeed.aFeeling)) || (isset($aFeed.sTagInfo) && $aFeed.sTagInfo) || (Phpfox::getParam('feed.enable_check_in') && Phpfox::getParam('core.google_api_key') != '' && isset($aFeed.location_name)  && isset($aFeed.location_latlng.latitude)) || (isset($aFeed.aBusiness) && $aFeed.aBusiness.business_id)}
            {if !empty($aFeed.feed_info) || isset($aFeed.parent_user)} - {else}{_p('was')}{/if}

            <!--Feeling-->
            {if isset($aFeed.aFeeling) && !empty($aFeed.aFeeling)}
            <span>
                <img src="{$aFeed.aFeeling.image}" class="ynfeed_feeling_icon"> {_p('feeling')}
                <span>{$aFeed.aFeeling.title_translated}</span>
            </span>
            {/if}

            {if isset($aFeed.sTagInfo) && $aFeed.sTagInfo != ''}
            <span>
                    {$aFeed.sTagInfo}
            </span>
            {/if}

            <!--Checkin location-->
            {if Phpfox::getParam('feed.enable_check_in') && Phpfox::getParam('core.google_api_key') != '' && isset($aFeed.location_name) && isset($aFeed.location_latlng.latitude) && isset($aFeed.location_latlng.longitude)}
            <span>
                 <span class="js_location_name_hover" {if isset($aFeed.location_latlng) &&
                       isset($aFeed.location_latlng.latitude)}onmouseover="" {/if}>
                    {_p var="at_lowercase"}
                        <a href="{if Phpfox::getParam('core.force_https_secure_pages')}https://{else}http://{/if}maps.google.com/maps?daddr={$aFeed.location_latlng.latitude},{$aFeed.location_latlng.longitude}"
                           target="_blank">{$aFeed.location_name}</a>
                 </span>
            </span>
            {/if}
            <!-- Map here, only show map with user status, other types will display it's main content, ex: photo-->

            {if isset($aFeed.business_id) && isset($aFeed.aBusiness) && $aFeed.aBusiness.business_id}
            <span>
                 {_p var="at_lowercase"}
                 <a href="{url link=$aFeed.aBusiness.url}" target="_blank">{$aFeed.aBusiness.name}</a>
            </span>
            {/if}
        {/if}
        <!--End extra info-->
        <div class="activity-feed-time-privacy-block">
           <time>
                <a href="{$aFeed.feed_link}" class="feed_permalink">{$aFeed.time_stamp|convert_time:'feed.feed_display_time_stamp'}</a>
                {if (isset($sponsor) && $sponsor) || (isset($aFeed.sponsored_feed) && $aFeed.sponsored_feed)}
            <span>
                <b>{_p var='sponsored'}</b>
            </span>
                {/if}
            </time>
            {if !empty($aFeed.privacy_icon_class)}
            <span class="{$aFeed.privacy_icon_class}"></span>
            {/if}
        </div>
	</div>
</div>
{/if}

<div class="activity_feed_content">
	{if (isset($aFeed.focus))}
	<div data-is-focus="1">
		{$aFeed.focus.html}
	</div>
	{else}
		{template file='ynfeed.block.focus'}
	{/if}

	{if isset($aFeed.feed_view_comment)}
		{module name='feed.comment'}
	{else}
		{template file='feed.block.comment'}
	{/if}

	{if $aFeed.type_id != 'friend'}
		{if isset($aFeed.more_feed_rows) && is_array($aFeed.more_feed_rows) && count($aFeed.more_feed_rows)}
			{if $iTotalExtraFeedsToShow = count($aFeed.more_feed_rows)}{/if}
			<a href="#" class="activity_feed_content_view_more" onclick="$(this).parents('.js_feed_view_more_entry_holder:first').find('.js_feed_view_more_entry').show(); $(this).remove(); return false;">{_p var='see_total_more_posts_from_full_name' total=$iTotalExtraFeedsToShow full_name=$aFeed.full_name|shorten:40:'...'}</a>
		{/if}
	{/if}

	{template file='ynfeed.block.share.external'}
</div>