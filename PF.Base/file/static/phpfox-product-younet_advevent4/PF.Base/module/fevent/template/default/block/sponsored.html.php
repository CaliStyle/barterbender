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
<div class="ynfevent-invite-item-wapper">
	<div class="sticky-label-icon sticky-sponsored-icon">
	    <span class="flag-style-arrow"></span>
	    <i class="ico ico-sponsor"></i>
	</div>
	<div class="ynfevent-invite-item">
		<div class="ynfevent-invite-item-photo">
			<span class="ynfevent-photoSpan" style="background-image: url('{$aSponsorEvents.image_path}');"></span>
		</div>

		<div class="ynfevent-invite-item-content">
			<a class="ynfevent-invite-item-title" href="{url link='ad.sponsor' view=$aSponsorEvents.sponsor_id}" title="{$aSponsorEvents.title|clean}">	{$aSponsorEvents.title|clean}</a>
			<time class="d-block ynfevent-invite-item-time mt-h1 space-left">
				<i class="ico ico-calendar-star-o text-gray-dark"></i><span class="fw-bold fz-12"> {$aSponsorEvents.date_start_time} - {$aSponsorEvents.short_start_time}</span>
			</time>
			<div class="mt-h1 ynfevent-invite-item-location text-gray-dark space-left"><i class="ico ico-checkin-o"></i> {$aSponsorEvents.location|clean|shorten:50:'...'}</div>
		</div>
	</div>
</div>