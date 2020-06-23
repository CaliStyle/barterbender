<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if empty($aFeed.empty_content) }
<div class="activity_feed_content_text{if isset($aFeed.comment_type_id) && $aFeed.comment_type_id == 'poll'} js_parent_module_feed_{$aFeed.comment_type_id}{/if} {if !empty($aFeed.status_background)}ync-statusbg-feed" style="background-image: url('{$aFeed.status_background}');"{else}"{/if}>
    {if !empty($aFeed.feed_mini_content)}
    <div class="activity_feed_content_status">
        <div class="activity_feed_content_status_left">
            <img src="{$aFeed.feed_icon}" alt="" class="v_middle"/> {$aFeed.feed_mini_content}
        </div>
        <div class="activity_feed_content_status_right">
            {template file='feed.block.link'}
        </div>
        <div class="clear"></div>
    </div>
    {/if}

    {if isset($aFeed.feed_status) && (!empty($aFeed.feed_status) || $aFeed.feed_status == '0')}
    <!-- Don't break line, layout issue -->
    <div class="activity_feed_content_status">{if mb_strlen(strip_tags($aFeed.feed_status)) > 200}{if strpos($aFeed.feed_status, '<br />') >= 200}{$aFeed.feed_status|ynfeed_strip|shorten:200:'feed.view_more':true|split:55|max_line}{else}{$aFeed.feed_status|ynfeed_strip|split:55|max_line|shorten:200:'feed.view_more':true}{/if}{else}{$aFeed.feed_status|ynfeed_strip|split:55|max_line}{/if}<br></div>
    {/if}

    <!--Extra info here-->

    {if Phpfox::getParam('feed.enable_check_in') && Phpfox::getParam('core.google_api_key') != '' &&
    isset($aFeed.location_name) && isset($aFeed.location_latlng.latitude)}
    {if $aFeed.type_id == 'user_status' || substr($aFeed.type_id, -8) == '_comment'}
    <div id="ynfeed_map_canvas_{$aFeed.feed_id}" class="ynfeed_map_canvas"
    data-lat="{$aFeed.location_latlng.latitude}" data-lng="{$aFeed.location_latlng.longitude}"></div>
    {/if}
    {/if}
    <!--End checkin location-->

    <!--Business-->
    {if isset($aFeed.business_id) && isset($aFeed.aBusiness) && $aFeed.aBusiness.business_id}
    <!-- Business template, only show map with user status, other types will display it's main content, ex: photo-->
    {if $aFeed.type_id == 'user_status' || substr($aFeed.type_id, -8) == '_comment'}
    <div id="ynfeed_business_feed_{$aFeed.feed_id}" class="ynfeed_post_business">
        <!--Maps-->
        {if isset($aFeed.aBusiness) && isset($aFeed.aBusiness.location_latitude) && $aFeed.aBusiness.location_latitude !== '' && isset($aFeed.aBusiness.location_longitude) && $aFeed.aBusiness.location_longitude !== ''}
        <div id="ynfeed_map_canvas_{$aFeed.feed_id}" class="ynfeed_map_canvas"
             data-lat="{$aFeed.aBusiness.location_latitude}" data-lng="{$aFeed.aBusiness.location_longitude}"></div>
        {/if}
        <!--Profile-->
        <div class="ynfeed_business_profile">
            <span class="ynfeed_background_img" style="background-image: url('{$aFeed.aBusiness.url_image}')"></span>
            <div class="ynfeed_business_info">
               <a href="{url link=$aFeed.aBusiness.url}" class="ynfeed_business_title">{$aFeed.aBusiness.name}</a>

               <div class="ynfeed_business_categories_location">
                  <span class="ynfeed_business_categories">
                     {$aFeed.aBusiness.category_title}
                  </span>

                  {if isset($aFeed.aBusiness.location_title) && $aFeed.aBusiness.location_title != ''}
                  <span class="ynfeed_business_location">
                     {$aFeed.aBusiness.location_title}
                  </span>
                  {/if}
               </div>

               <div class="ynfeed_business_checkinfo">{$aFeed.aBusiness.sCheckinsInfo}</div>
            </div>
        </div>
    </div>
    {/if}
    {/if}
    <!--End business-->

    <div class="activity_feed_content_link">
        {if $aFeed.type_id == 'friend' && isset($aFeed.more_feed_rows) && is_array($aFeed.more_feed_rows) &&
        count($aFeed.more_feed_rows)}
        {foreach from=$aFeed.more_feed_rows item=aFriends}
        {$aFriends.feed_image}
        {/foreach}
        {$aFeed.feed_image}
        {else}

        {if !empty($aFeed.feed_image)}
        <div class="activity_feed_content_image" {if isset($aFeed.feed_custom_width)}
             style="width:{$aFeed.feed_custom_width};" {
        /if}>
        {if is_array($aFeed.feed_image)}
        <div class="activity_feed_multiple_image feed-img-stage-{$aFeed.total_image}">
            {foreach from=$aFeed.feed_image item=sFeedImage name=image}
            <div class="img-{$phpfox.iteration.image}">
                {$sFeedImage}
            </div>
            {/foreach}
        </div>
        <div class="clear"></div>
        {else}
        <a href="{if isset($aFeed.feed_link_actual)}{$aFeed.feed_link_actual}{else}{$aFeed.feed_link}{/if}" {if
           !isset($aFeed.no_target_blank)} target="_blank" {/if} class="{if isset($aFeed.custom_css)}
        {$aFeed.custom_css} {/if}{if !empty($aFeed.feed_image_onclick)}{if
        !isset($aFeed.feed_image_onclick_no_image)}play_link {/if} no_ajax_link{/if}"{if
        !empty($aFeed.feed_image_onclick)} onclick="{$aFeed.feed_image_onclick}"{/if}{if !empty($aFeed.custom_rel)}
        rel="{$aFeed.custom_rel}"{/if}{if isset($aFeed.custom_js)} {$aFeed.custom_js} {/if}{if
        Phpfox::getParam('core.no_follow_on_external_links')} rel="nofollow"{/if}>{if
        !empty($aFeed.feed_image_onclick)}{if !isset($aFeed.feed_image_onclick_no_image)}<span class="play_link_img">{_p var='play'}</span>{/if}{/if}{$aFeed.feed_image}</a>
        {/if}

    </div>
    {/if}

    {if isset($aFeed.feed_image_banner)}
    <div class="feed_banner">
        {$aFeed.feed_image_banner}
        {/if}

        {if isset($aFeed.load_block)}
            {module name=$aFeed.load_block this_feed_id=$aFeed.feed_id sponsor_feed_id=$iSponsorFeedId}
        {else}

        <div
            class="feed_block_title_content {if (!empty($aFeed.feed_content) || !empty($aFeed.feed_custom_html)) && empty($aFeed.feed_image) && empty($aFeed.feed_image_banner)} activity_feed_content_no_image{/if}{if !empty($aFeed.feed_image)} activity_feed_content_float{/if}"
            {if isset($aFeed.feed_custom_width)} style="margin-left:{$aFeed.feed_custom_width};" {
        /if}>
        {if !empty($aFeed.feed_title) || $aFeed.type_id == 'link'}
        {if isset($aFeed.feed_title_sub)}
        <span class="user_profile_link_span" id="js_user_name_link_{$aFeed.feed_title_sub|clean}">
					{/if}
					<a href="{if isset($aFeed.feed_link_actual)}{$aFeed.feed_link_actual}{else}{$aFeed.feed_link}{/if}"
                       class="activity_feed_content_link_title" {if isset($aFeed.feed_title_extra_link)} target="_blank"
                       {/if}{if Phpfox::getParam('core.no_follow_on_external_links')} rel="nofollow"{/if}>{$aFeed.feed_title|clean|split:30}</a>
            {if isset($aFeed.feed_title_sub)}
						</span>
        {/if}


        {if !empty($aFeed.feed_title_extra)}
        <div class="activity_feed_content_link_title_link">
            <a href="{$aFeed.feed_title_extra_link}" target="_blank" {if Phpfox::getParam('core.no_follow_on_external_links')}
            rel="nofollow"{/if}>{$aFeed.feed_title_extra|clean}</a>
        </div>
        {/if}



        {/if}
        {if !empty($aFeed.feed_content)}
        <div class="activity_feed_content_display">
            {if strpos($aFeed.feed_content, '<br/>') >= 200}
            {$aFeed.feed_content|feed_strip|shorten:200:'feed.view_more':true|split:55|max_line}
            {else}
            {$aFeed.feed_content|feed_strip|split:55|max_line|shorten:200:'feed.view_more':true}
            {/if}
        </div>
        {/if}
        {if !empty($aFeed.feed_custom_html)}
        <div class="activity_feed_content_display_custom">
            {$aFeed.feed_custom_html}
        </div>
        {/if}

        {if !empty($aFeed.app_content)}
        {$aFeed.app_content}
        {/if}

        {if !empty($aFeed.parent_module_id)}
        {module name='ynfeed.mini' parent_feed_id=$aFeed.parent_feed_id parent_module_id=$aFeed.parent_module_id}
        {/if}

        {if (isset($aFeed.parent_is_app)) && empty($aFeed.parent_module_id)}
        <div class="feed_is_child" style="display: block">
            <div class="feed_stream" data-feed-url="{url link='feed.stream' id=$aFeed.parent_is_app}"></div>
        </div>
        {/if}

    </div>

    {/if}

    {if isset($aFeed.feed_image_banner)}
</div>
{/if}

{if !empty($aFeed.feed_image)}
<div class="clear"></div>
{/if}
{/if}
</div>
</div>
{else}
<div class="activity_feed_content_text empty_content"></div>
{/if}
