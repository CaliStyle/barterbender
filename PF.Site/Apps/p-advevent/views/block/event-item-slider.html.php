<div class="item">
    <div class="p-item p-fevent-item">
        <div class="item-outer">
            <div class="p-item-media-wrapper p-margin-default p-fevent-item-media ">
                <a href="{if !empty($aItem.sponsor_id)}{url link='ad.sponsor' view=$aItem.sponsor_id}{else}{permalink module='fevent' id=$aItem.event_id title=$aItem.title}{/if}" class="item-media-link">
                    <span class="item-media-src" style="background-image: url('{$aItem.image_path}');"></span>
                </a>
                <div class="p-fevent-label-status-container">
                    {if !empty($aItem.d_repeat_time)}
                    <div class="item-repeat">
                        <i class="ico ico-repeat-alt"></i>
                        <div class="item-title-hover">
                            {_p var='repeat'}: {$aItem.d_repeat_time}
                        </div>
                    </div>
                    {/if}
                </div>
            </div>
            <div class="item-inner">
                <div class="item-inner-wrapper">
                    <div class="p-fevent-timer-component">
                        <span class="item-month">{$aItem.M_start_time|shorten:3}</span>
                        <span class="item-date">{$aItem.d_start_time}</span>
                        <span class="item-time">{$aItem.short_start_time}</span>
                    </div>
                    <div class="item-inner-info">
                        <h2 class="p-item-title ">
                            <a href="{if !empty($aItem.sponsor_id)}{url link='ad.sponsor' view=$aItem.sponsor_id}{else}{permalink module='fevent' id=$aItem.event_id title=$aItem.title}{/if}" class="" >
                                <span>{$aItem.title}</span>
                            </a>
                        </h2>
                        <div class="item-info-wrapper">
                            <div class="item-side-info">
                                <div class="item-statistic-wrapper p-item-statistic p-seperate-dot-wrapper">
                                    <span class="p-seperate-dot-item item-guest p-fevent-slider-hide-on-grid">
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
                                    </span>
                                    {if $aItem.total_like}
                                    <span class="p-seperate-dot-item item-like p-fevent-slider-hide-on-grid">
                                        <span>{$aItem.total_like}</span>
                                        <span class="p-text-lowercase">
                                            {if $aItem.total_like > 1}
                                                {_p var='likes'}
                                            {else}
                                                {_p var='like'}
                                            {/if}
                                        </span>
                                    </span>
                                    {/if}
                                    {if (int)$aItem.total_view > 0}
                                    <span class="p-seperate-dot-item item-view">
                                        <span>{$aItem.total_view}</span>
                                        <span class="p-text-lowercase">{if (int)$aItem.total_view == 1}{_p var='view'}{else}{_p var='views'}{/if}</span>
                                    </span>
                                    {/if}

                                    {if $aItem.has_ticket}
                                    <span class="p-seperate-dot-item item-ticket p-fevent-slider-hide-on-list">
                                        <span class="item-ticket-title">{_p var='fevent.ticket_price'}:</span>
                                        <span class="item-ticket-number">
                                            {if $aItem.ticket_type == 'free'}
                                                <span class="p-text-success fw-bold">{_p var='free'}</span>
                                            {else}
                                                {$aItem.ticket_price}
                                            {/if}
                                        </span>
                                    </span>
                                    {/if}
                                </div>
                                <div class="item-time-wrapper p-fevent-slider-hide-on-grid">
                                    {if $isSlider}
                                    <div class="item-start p-text-info">
                                        {$aItem.start_time_basic_information_time}
                                    </div>
                                    <div class="item-end">
                                        {if !in_array($aItem.d_type, array('past', 'ongoing'))}{_p var='end'}: {/if}{$aItem.end_time_basic_information_time}
                                    </div>
                                    {else}
                                        {if in_array($dataSource, array('ongoing'))}
                                        <div class="item-end">
                                            {_p var='end'}: {$aItem.end_time_basic_information_time}
                                        </div>
                                        {else}
                                        <div class="item-start p-text-info">
                                            {$aItem.start_time_basic_information_time}
                                        </div>
                                        {/if}
                                    {/if}

                                </div>
                                <div class="item-ticket-price-listview p-fevent-slider-hide-on-grid">
                                    <div class="item-price">
                                        {if $aItem.has_ticket}
                                            {if $aItem.ticket_type == 'free'}
                                                <span class="p-text-success fw-bold">{_p var='free'}/{_p var='ticket'}</span>
                                            {else}
                                                <span class="p-text-warning fw-bold">{$aItem.ticket_price}/{_p var='ticket'}</span>
                                            {/if}
                                        {/if}
                                    </div>
                                </div>
                                <div class="item-author-wrapper">
                                    <div class="item-author-image">
                                        {img user=$aItem suffix='_50_square'}
                                    </div>
                                    <div class="item-author-info">
                                        <span class="item-author">
                                            <span class="p-text-capitalize">{_p var='fevent.by'}</span> {$aItem|user}
                                        </span>
                                        <span class="p-item-minor-info item-info">{$aItem.location}{if $aItem.address} {$aItem.address}{/if}{if $aItem.city} - {$aItem.city}{/if}</span>
                                    </div>
                                </div>
                                <div class="item-description p-fevent-slider-hide-on-grid item_view_content">
                                    {$aItem.description_parsed|striptag|clean|shorten:200:'...'}
                                </div>
                            </div>
                            <div class="item-side-action">
                                <div class="p-fevent-member-list-component">
                                    {if !empty($aItem.attending_statistic.people)}
                                    {foreach from=$aItem.attending_statistic.people item=attending_person}
                                    <div class="item-member">
                                        {img user=$attending_person suffix='_200_square'}
                                    </div>
                                    {/foreach}
                                    {/if}
                                    {if !empty($aItem.attending_statistic.other_people)}
                                    <div class="item-more">
                                        <a href="javascript:void(0);" onclick="P_AdvEvent.showTabAttendingPeople(this); return false;" data-event-id="{$aItem.event_id}" data-text="{_p var='fevent.friend_list'}" data-statistic="1">+{$aItem.attending_statistic.other_people}</a>
                                    </div>
                                    {/if}
                                </div>
                                <div class="p-fevent-action-btn js_rsvp_action_list_{$aItem.event_id}"">
                                    {template file='fevent.block.rsvp-action'}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>