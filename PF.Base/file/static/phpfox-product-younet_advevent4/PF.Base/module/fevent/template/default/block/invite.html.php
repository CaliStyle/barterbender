<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
?>
<ul class="ynfevent-invite-lists">
{foreach from=$aEventInvites item=aEventInvite}
	<li class="ynfevent-invite-item">
		<div class="ynfevent-invite-item-photo">
			<span class="ynfevent-photoSpan" style="background-image: url('{$aEventInvite.image_path}');"></span>
		</div>

		<div class="ynfevent-invite-item-content">
			<a href="{permalink module='fevent' id=$aEventInvite.event_id title=$aEventInvite.title}" class="ynfevent-invite-item-title">{$aEventInvite.title|clean}</a>
			<time class="d-block ynfevent-invite-item-time mt-h1 space-left">
				<i class="ico ico-calendar-star-o text-gray-dark"></i><span class="fw-bold fz-12"> {$aEventInvite.start_time_phrase} - {$aEventInvite.start_time_phrase_stamp}</span>
			</time>
			<div class="mt-h1 ynfevent-invite-item-location text-gray-dark space-left"><i class="ico ico-checkin-o"></i> {$aEventInvite.location|clean|shorten:50:'...'}</div>
			<div class="dropdown mt-1" id="ynfevent-invite-option">
				<span data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="text-js" class="text dropdown-toggle fz-12">{_p var='fevent.yes'} <i class="ico ico-caret-down ml-1"></i></span>
				<ul class="dropdown-menu">
					<li id="ynfevent-invite-option_1" class="fz-12 ynfevent-invite-option-item ynfevent-invite-option-item-js"><a href="javascript:void(0)" onclick="$(this).parents('#ynfevent-invite-option').hide(); $('#js_event_rsvp_invite_image_{$aEventInvite.event_id}').show(); $.ajaxCall('fevent.addRsvp', 'id={$aEventInvite.event_id}&amp;rsvp=1&amp;inline=1'); return false;">{_p var='fevent.yes'}</a></li>
					<li id="ynfevent-invite-option_2" class="fz-12 ynfevent-invite-option-item ynfevent-invite-option-item-js"><a href="javascript:void(0)" onclick="$(this).parents('#ynfevent-invite-option').hide(); $('#js_event_rsvp_invite_image_{$aEventInvite.event_id}').show(); $.ajaxCall('fevent.addRsvp', 'id={$aEventInvite.event_id}&amp;rsvp=2&amp;inline=1'); return false;">{_p var='fevent.maybe'}</a></li>
                    <li id="ynfevent-invite-option_3" class="fz-12 ynfevent-invite-option-item ynfevent-invite-option-item-js"><a href="javascript:void(0)" onclick="$(this).parents('#ynfevent-invite-option').hide(); $('#js_event_rsvp_invite_image_{$aEventInvite.event_id}').show(); $.ajaxCall('fevent.addRsvp', 'id={$aEventInvite.event_id}&amp;rsvp=3&amp;inline=1'); return false;">{_p var='fevent.no'}</a></li>
                </ul>
			</div>
		</div>		
	</li>
{/foreach}
</ul>