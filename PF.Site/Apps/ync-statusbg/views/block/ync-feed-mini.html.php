<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if !empty($aParentFeed)}
<div class="feed_share_holder feed_share_{$aParentFeed.type_id}">
    {if empty($aParentFeed.empty_content) }
    <div class="activity_feed_content_text{if isset($aParentFeed.comment_type_id) && $aParentFeed.comment_type_id == 'poll'} js_parent_module_feed_{$aParentFeed.comment_type_id}{/if}">
        {if !empty($aParentFeed.feed_mini_content)}
        <div class="activity_feed_content_status">
            <div class="activity_feed_content_status_left">
                <img src="{$aParentFeed.feed_icon}" alt="" class="v_middle"/> {$aParentFeed.feed_mini_content}
            </div>
            <div class="activity_feed_content_status_right">
                {template file='feed.block.link'}
            </div>
            <div class="clear"></div>
        </div>
        {/if}
        <!--Old extra info here-->

        {if Phpfox::getParam('feed.enable_check_in') && Phpfox::getParam('core.google_api_key') != '' &&
        isset($aParentFeed.location_name) && isset($aParentFeed.location_latlng.latitude)}
        {if $aParentFeed.type_id == 'user_status' || substr($aParentFeed.type_id, -8) == '_comment'}
        <div id="ynfeed_map_canvas_{$aParentFeed.feed_id}" class="ynfeed_map_canvas"
             data-lat="{$aParentFeed.location_latlng.latitude}" data-lng="{$aParentFeed.location_latlng.longitude}"></div>
        {/if}
        {/if}
        <!--End checkin location-->

        <!--Business-->
        {if isset($aParentFeed.business_id) && isset($aParentFeed.aBusiness) && $aParentFeed.aBusiness.business_id}
        <!-- Business template, only show map with user status, other types will display it's main content, ex: photo-->
        {if $aParentFeed.type_id == 'user_status' || substr($aParentFeed.type_id, -8) == '_comment'}
        <div id="ynfeed_business_feed_{$aParentFeed.feed_id}" class="ynfeed_post_business">
            <!--Maps-->
            {if isset($aParentFeed.aBusiness) && isset($aParentFeed.aBusiness.location_latitude) && $aParentFeed.aBusiness.location_latitude !== '' && isset($aParentFeed.aBusiness.location_longitude) && $aParentFeed.aBusiness.location_longitude !== ''}
            <div id="ynfeed_map_canvas_{$aParentFeed.feed_id}" class="ynfeed_map_canvas"
                 data-lat="{$aParentFeed.aBusiness.location_latitude}" data-lng="{$aParentFeed.aBusiness.location_longitude}"></div>
            {/if}
            <!--Profile-->
            <div class="ynfeed_business_profile">
                <span class="ynfeed_background_img" style="background-image: url('{$aParentFeed.aBusiness.url_image}')"></span>
                <div class="ynfeed_business_info">
                    <a href="{url link=$aParentFeed.aBusiness.url}" class="ynfeed_business_title">{$aParentFeed.aBusiness.name}</a>

                    <div class="ynfeed_business_categories_location">
                      <span class="ynfeed_business_categories">
                         {_p var=$aParentFeed.aBusiness.category_title}
                      </span>

                        {if isset($aParentFeed.aBusiness.location_title) && $aParentFeed.aBusiness.location_title != ''}
                        <span class="ynfeed_business_location">
                         {$aParentFeed.aBusiness.location_title}
                      </span>
                        {/if}
                    </div>

                    <div class="ynfeed_business_checkinfo">{$aParentFeed.aBusiness.sCheckinsInfo}</div>
                </div>
            </div>
        </div>
        {/if}
        {/if}
        <!--End business-->

        <div class="activity_feed_content_link">
            {if $aParentFeed.type_id == 'friend' && isset($aParentFeed.more_feed_rows) && is_array($aParentFeed.more_feed_rows) &&
            count($aParentFeed.more_feed_rows)}
            {foreach from=$aParentFeed.more_feed_rows item=aFriends}
            {$aFriends.feed_image}
            {/foreach}
            {$aParentFeed.feed_image}
            {else}

            {if !empty($aParentFeed.feed_image) && !isset($aParentFeed.load_block)}
            <div class="activity_feed_content_image" {if isset($aParentFeed.feed_custom_width)}
                 style="width:{$aParentFeed.feed_custom_width};" {
            /if}>
            {if is_array($aParentFeed.feed_image)}
            <div class="activity_feed_multiple_image feed-img-stage-{$aParentFeed.total_image}">
                {foreach from=$aParentFeed.feed_image item=sFeedImage name=image}
                <div class="img-{$phpfox.iteration.image}">
                    {$sFeedImage}
                </div>
                {/foreach}
            </div>
            <div class="clear"></div>
            {else}
            <a href="{if isset($aParentFeed.feed_link_actual)}{$aParentFeed.feed_link_actual}{else}{$aParentFeed.feed_link}{/if}" {if
               !isset($aParentFeed.no_target_blank)} target="_blank" {/if} class="{if isset($aParentFeed.custom_css)}
            {$aParentFeed.custom_css} {/if}{if !empty($aParentFeed.feed_image_onclick)}{if
            !isset($aParentFeed.feed_image_onclick_no_image)}play_link {/if} no_ajax_link{/if}"{if
            !empty($aParentFeed.feed_image_onclick)} onclick="{$aParentFeed.feed_image_onclick}"{/if}{if !empty($aParentFeed.custom_rel)}
            rel="{$aParentFeed.custom_rel}"{/if}{if isset($aParentFeed.custom_js)} {$aParentFeed.custom_js} {/if}{if
            Phpfox::getParam('core.no_follow_on_external_links')} rel="nofollow"{/if}>{if
            !empty($aParentFeed.feed_image_onclick)}{if !isset($aParentFeed.feed_image_onclick_no_image)}<span class="play_link_img">{_p var='play'}</span>{/if}{/if}{$aParentFeed.feed_image}</a>
            {/if}

        </div>
        {/if}

        {if isset($aParentFeed.feed_image_banner)}
        <div class="feed_banner">
            {$aParentFeed.feed_image_banner}
            {/if}

            {if isset($aParentFeed.load_block)}
            {assign var=tempParentFeed value=$aParentFeed}
            {module name=$aParentFeed.load_block this_feed_id=$aParentFeed.feed_id}
            {if empty($aParentFeed) && !empty($tempParentFeed)}
            {assign var=aParentFeed value=$tempParentFeed}
            {/if}
            {else}

            <div
                class="feed_block_title_content {if (!empty($aParentFeed.feed_content) || !empty($aParentFeed.feed_custom_html)) && empty($aParentFeed.feed_image) && empty($aParentFeed.feed_image_banner)} activity_feed_content_no_image{/if}{if !empty($aParentFeed.feed_image)} activity_feed_content_float{/if}"
                {if isset($aParentFeed.feed_custom_width)} style="margin-left:{$aParentFeed.feed_custom_width};" {
            /if}>
            {if !empty($aParentFeed.feed_title) || $aParentFeed.type_id == 'link'}
            {if isset($aParentFeed.feed_title_sub)}
            <span class="user_profile_link_span" id="js_user_name_link_{$aParentFeed.feed_title_sub|clean}">
                        {/if}
                        <a href="{if isset($aParentFeed.feed_link_actual)}{$aParentFeed.feed_link_actual}{else}{$aParentFeed.feed_link}{/if}"
                           class="activity_feed_content_link_title" {if isset($aParentFeed.feed_title_extra_link)} target="_blank"
                           {/if}{if Phpfox::getParam('core.no_follow_on_external_links')} rel="nofollow"{/if}>{$aParentFeed.feed_title|clean|split:30}</a>
                {if isset($aParentFeed.feed_title_sub)}
                            </span>
            {/if}
            {if !empty($aParentFeed.feed_title_extra)}
            <div class="activity_feed_content_link_title_link">
                <a href="{$aParentFeed.feed_title_extra_link}" target="_blank" {if Phpfox::getParam('core.no_follow_on_external_links')}
                rel="nofollow"{/if}>{$aParentFeed.feed_title_extra|clean}</a>
            </div>
            {/if}
            {/if}
            {if !empty($aParentFeed.feed_content)}
            <div class="activity_feed_content_display">
                {if strpos($aParentFeed.feed_content, '<br/>') >= 200}
                {$aParentFeed.feed_content|feed_strip|shorten:200:'feed.view_more':true|split:55|max_line}
                {else}
                {$aParentFeed.feed_content|feed_strip|split:55|max_line|shorten:200:'feed.view_more':true}
                {/if}
            </div>
            {/if}
            {if !empty($aParentFeed.feed_custom_html)}
            <div class="activity_feed_content_display_custom">
                {$aParentFeed.feed_custom_html}
            </div>
            {/if}

            {if !empty($aParentFeed.app_content)}
            {$aParentFeed.app_content}
            {/if}

            {if (isset($aParentFeed.parent_is_app)) && empty($aParentFeed.parent_module_id)}
            <div class="feed_is_child" style="display: block">
                <div class="feed_stream" data-feed-url="{url link='feed.stream' id=$aParentFeed.parent_is_app}"></div>
            </div>
            {/if}

        </div>

        {/if}

        {if isset($aParentFeed.feed_image_banner)}
    </div>
    {/if}

    {if !empty($aParentFeed.feed_image)}
    <div class="clear"></div>
    {/if}
    {/if}
</div>
</div>
{else}
<div class="activity_feed_content_text empty_content"></div>
{/if}
{if !isset($aParentFeed.feed_mini)}
<div class="feed_share_header">
    <div class="activity_feed_header_info">
        {$aParentFeed|user:'':'':50}{if (!empty($aParentFeed.parent_module_id) || isset($aParentFeed.parent_is_app))} {_p var='shared'}{else}{if isset($aParentFeed.parent_user)} <span class="ico ico-caret-right"></span> {$aParentFeed.parent_user|user:'parent_':'':50} {/if}{if !empty($aParentFeed.feed_info)} <span class="feed_info"> {$aParentFeed.feed_info} </span>{/if}{/if}
        <!--Extra info-->
        {if (isset($aParentFeed.aFeeling) && !empty($aParentFeed.aFeeling)) || (isset($aParentFeed.sTagInfo) && $aParentFeed.sTagInfo) || (Phpfox::getParam('feed.enable_check_in') && Phpfox::getParam('core.google_api_key') != '' && isset($aParentFeed.location_name)  && isset($aParentFeed.location_latlng.latitude)) || (isset($aParentFeed.aBusiness) && $aParentFeed.aBusiness.business_id)}
        {if !empty($aParentFeed.feed_info) || isset($aParentFeed.parent_user)} - {else}{_p('was')}{/if}

        {if isset($aParentFeed.aFeeling) && !empty($aParentFeed.aFeeling)}
        <span>
                    <img src="{$aParentFeed.aFeeling.image}" class="ynfeed_feeling_icon"> {_p('feeling')}
                    <span>{$aParentFeed.aFeeling.title_translated}</span>
                </span>
        {/if}

        {if isset($aParentFeed.sTagInfo) && $aParentFeed.sTagInfo != ''}
        <span>{$aParentFeed.sTagInfo}</span>
        {/if}

        <!--Checkin location-->
        {if Phpfox::getParam('feed.enable_check_in') && Phpfox::getParam('core.google_api_key') != '' && isset($aParentFeed.location_name) && isset($aParentFeed.location_latlng.latitude) && isset($aParentFeed.location_latlng.longitude)}
        <span>
            <span class="js_location_name_hover" {if isset($aParentFeed.location_latlng) &&
                  isset($aParentFeed.location_latlng.latitude)}onmouseover="" {/if}>
            {_p var="at_lowercase"}
                <a href="{if Phpfox::getParam('core.force_https_secure_pages')}https://{else}http://{/if}maps.google.com/maps?daddr={$aParentFeed.location_latlng.latitude},{$aParentFeed.location_latlng.longitude}"
                   target="_blank">{$aParentFeed.location_name}</a>
            </span>
        </span>
        {/if}
        <!-- Map here, only show map with user status, other types will display it's main content, ex: photo-->

        {if isset($aParentFeed.business_id) && isset($aParentFeed.aBusiness) && $aParentFeed.aBusiness.business_id}
        <span>
                    {_p var="at_lowercase"}
                    <a href="{url link=$aParentFeed.aBusiness.url}" target="_blank">{$aParentFeed.aBusiness.name}</a>
                </span>
        {/if}
        {/if}
        <!--End extra info-->
        <div class="activity-feed-time-privacy-block">
            <time>
                <a href="{$aParentFeed.feed_link}" class="feed_permalink">{$aParentFeed.time_stamp|convert_time:'feed.feed_display_time_stamp'}</a>
                {if (isset($sponsor) && $sponsor) || (isset($aParentFeed.sponsored_feed) && $aParentFeed.sponsored_feed)}
                <span>
                        <b>{_p var='sponsored'}</b>
                    </span>
                {/if}
            </time>
            {if !empty($aParentFeed.privacy_icon_class)}
            <span class="{$aParentFeed.privacy_icon_class}"></span>
            {/if}
        </div>
        {if !empty($aParentFeed.status_background)}
        <div class="mt-1">
            <div class="activity_feed_content_text ync-statusbg-feed" style="background-image: url('{$aParentFeed.status_background}');">
        {/if}
                {if isset($aParentFeed.feed_status) && (!empty($aParentFeed.feed_status) || $aParentFeed.feed_status == '0')}
                <!-- dont break-line issue layout -->
                <div class="activity_feed_content_status">{if strpos($aParentFeed.feed_status, '<br/>') >= 200}
                    {$aParentFeed.feed_status|ynfeed_strip|shorten:200:'feed.view_more':true|split:55|max_line}
                    {else}
                    {$aParentFeed.feed_status|ynfeed_strip|split:55|max_line|shorten:200:'feed.view_more':true}
                    {/if}
                </div>
                {/if}
        {if !empty($aParentFeed.status_background)}
            </div>
        </div>
        {/if}
    </div>
</div>
{/if}
</div>
{else}
<div class="alert alert-warning m_bottom_0 mt-1" role="alert">
    <h4 class="alert-heading mb-1">{_p var='this_content_is_not_available_at_the_moment'}</h4>
    <p>{_p var='when_this_happens_its_usually_because_the_owner_only_shared_it_with_a_small_group_of_people_or_changed_who_can_see_it_or_its_been_deleted'}</p>
</div>
{/if}
