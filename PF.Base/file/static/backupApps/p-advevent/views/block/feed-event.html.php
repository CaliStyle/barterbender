<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<article class="p-fevent-app core-feed-item" data-url="{$link}">
    <div class="item-media-banner">
        <a class="item-media" href="{$link}">
            <span class="item-media-src" style="background-image: url('{if !empty($aItem.image_path)}{img return_url=true server_id=$aItem.server_id title=$aItem.title path='event.url_image' file=$aItem.image_path}{else}{$defaultImage}{/if}');"  alt="{$aItem.title}"></span>
        </a>
    </div>
    <div class="item-outer">
        <div class="item-calendar">
            <div class="item-date">{$aItem.d_day}</div>
            <div class="item-month">{$aItem.d_month}</div>
        </div>
        <div class="item-inner">
            <div class="item-title">
                <a href="{$link}" class="core-feed-title line-1">{$aItem.title}</a>
            </div>
            <div class="item-wrapper-info">
                <div class="item-side-left">
                    {if !empty($location)}
                    <div class="item-location core-feed-description line-1">
                        {$location}
                    </div>
                    {/if}
                    <div class="item-info core-feed-description">
                        <span class="item-time">
                            {$aItem.date_formatted}
                        </span>
                        <span class="item-total-guest">
                            <a href="javascript:void(0);" data-event-id="{$aItem.event_id}" data-text="{_p var='guest_list'}" onclick="P_AdvEvent.showTabAttendingPeople(this); return false;">
                            {if empty($aItem.attending_statistic.total_friend_attending)}
                                <span>{$aItem.attending_statistic.total_attending}</span>
                                <span class="p-text-lowercase">
                                    {if $aItem.attending_statistic.total_attending == 1}
                                        {_p var='fevent.person'}
                                    {else}
                                        {_p var='fevent.people'}
                                    {/if}
                                </span>
                            {else}
                                <span>{$aItem.attending_statistic.total_friend_attending}</span>
                                <span class="p-text-lowercase">
                                    {if $aItem.attending_statistic.total_friend_attending == 1}
                                        {_p var='fevent_friend'}
                                    {else}
                                    {_p var='fevent_friends'}
                                    {/if}
                                </span>
                                {if !empty($aItem.attending_statistic.total_other_people_attending)}
                                {_p var='and'}<span> {$aItem.attending_statistic.total_other_people_attending}</span>
                                <span class="p-text-lowercase">
                                    {if $aItem.attending_statistic.total_other_people_attending == 1}
                                        {_p var='fevent_other'}
                                    {else}
                                        {_p var='fevent_others'}
                                    {/if}
                                </span>
                                {/if}
                            {/if}
                            </a>
                        </span>
                    </div>
                </div>
                <div class="item-side-right">
                    <div class="item-action js_rsvp_action_list_{$aItem.event_id}">
                        {template file='fevent.block.rsvp-action'}
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>
