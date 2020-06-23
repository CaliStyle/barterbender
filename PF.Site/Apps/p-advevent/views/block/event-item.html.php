<div class="p-item p-fevent-item {if !empty($aItem.attending_statistic.people)}has-list-member{/if}">
    <div class="item-outer">
        {if !empty($canDoModeration)}
        <div class="moderation_row">
            <label class="item-checkbox">
                <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aItem.event_id}" id="check{$aItem.event_id}" />
                <i class="ico ico-square-o"></i>
            </label>
        </div>
        {/if}
        <div class="p-item-media-wrapper p-margin-default p-fevent-item-media ">
            <a href="{if !empty($aItem.sponsor_id)}{url link='ad.sponsor' view=$aItem.sponsor_id}{else}{permalink module='fevent' id=$aItem.event_id title=$aItem.title}{/if}" class="item-media-link">
                <span class="item-media-src" style="background-image: url('{$aItem.image_path}');"></span>
                {if !$hideStatus}
                <div class="p-fevent-label-status-container">
                    {if $aItem.d_type == 'past'}
                    <span class="p-label-status solid danger ">
                            <span class="p-text-uppercase">{_p var='end'}</span>
                        </span>
                    {/if}
                    {if $aItem.d_type == 'ongoing'}
                    <span class="p-label-status solid success ">
                            <span class="p-text-uppercase">{_p var='ongoing'}</span>
                         </span>
                    {/if}
                    {if $aItem.d_type == 'upcoming' && false}
                    <span class="p-label-status solid warning ">
                            <span class="p-text-uppercase">{_p var='upcoming'}</span>
                        </span>
                    {/if}
                </div>
                {/if}


                <div class="p-item-flag-wrapper js_status_icon_{$aItem.event_id}">
                    {template file='fevent.block.status-icon'}
                </div>

            </a>
        </div>
        <div class="item-inner">
        	<div class="p-fevent-item-time-listing">
        		<div class="item-start p-text-info">
                    {$aItem.date_formatted}
                </div>
        	</div>
        	<div class="p-fevent-item-title-wrapper">
        		<h4 class="p-item-title truncate-2">
                    <a href="{if !empty($aItem.sponsor_id)}{url link='ad.sponsor' view=$aItem.sponsor_id}{else}{permalink module='fevent' id=$aItem.event_id title=$aItem.title}{/if}" class="">
                        <span>{$aItem.title}</span>
                    </a>
                </h4>
        		<div class="item-side-action p-fevent-item-option-wrapper">
                    <div class="p-fevent-action-member-wrapper">
	                    <div class="p-fevent-action-btn js_rsvp_action_list_{$aItem.event_id}">
	                        {template file='fevent.block.rsvp-action'}
	                    </div>
	                    <div class="p-fevent-member-list-component p-fevent-listing-hidden-on-grid p-hidden-side-block">
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
	                </div>
                    {if $aItem.can_do_action }
                    <div class="dropdown p-hidden-side-block">
                        <span class="p-option-button dropdown-toggle" data-toggle="dropdown">
                            <i class="ico ico-gear-o"></i>
                        </span>
                        <ul class="dropdown-menu dropdown-menu-right">
                            {template file='fevent.block.action-link'}
                        </ul>
                    </div>
                    {/if}
                </div>
        	</div>

            <div class="p-fevent-item-info-wrapper p-seperate-dot-wrapper-inline p-item-minor-info">
                {if $aItem.has_ticket}
                <span class="p-seperate-dot-item-inline item-ticket ">
                    <span class="item-ticket-title p-text-capitalize">{_p var='fevent.ticket_price'}:</span>
                    <span class="item-ticket-number">
                        {if $aItem.ticket_type == 'free'}
                            {_p var='free'}
                        {else}
                            {$aItem.ticket_price}
                        {/if}
                    </span>
                </span>
                {/if}
                <span class="p-seperate-dot-item-inline p-seperate-dot-wrapper-inline item-wrapper-truncate">
	            	<span class="p-seperate-dot-item-inline item-member p-item-statistic">
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
                    {if !empty($aItem.location_parsed)}
                    <span class="p-seperate-dot-item-inline item-info p-fevent-listing-hidden-on-list">
	                    <span class="item-info-location">{$aItem.location_parsed}</span>
                    </span>
                    {/if}
	            </span>
            </div>

            <!-- //duplicate info for responsive layout -->
            <div class="p-item-description p-fevent-listing-hidden-on-grid p-fevent-item-description truncate-2 ">
                {if !empty($aItem.location_parsed)}
                <span class="item-info-location">{$aItem.location_parsed}</span>
                {/if}
                {if !empty($aItem.description_parsed)}
                <span class="item-info-desc item_view_content"> - {$aItem.description_parsed|striptag|clean|shorten:200:'...'}</span>
                {/if}
            </div>
            <div class="item-author-wrapper p-item-minor-info p-hidden-side-block">
                <div class="item-author-info p-seperate-dot-wrapper">
                    <span class="item-author p-seperate-dot-item">
	                    <span class="p-text-capitalize">{_p var='fevent.by'}</span> {$aItem|user}
                    </span>
                    {if (int)$aItem.isrepeat != -1}
                    <span class="item-repeat-status p-seperate-dot-item">
                    	{_p var='repeat'}: {$aItem.repeat_title}
                    </span>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>
