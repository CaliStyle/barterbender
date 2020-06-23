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

<div class="p-fevent-calendar-header">
	<div class="item-header-outer">
		<div class="item-top-wrapper">
			<div class="item-checkbox-group">
				<div class="checkbox p-checkbox-custom p-fevent-checkbox-my-event">
					<label>
						<input type="checkbox" checked><i class="ico ico-square-o mr-1"></i>
					</label>
					<div class="dropdown">
						<a class="dropdown-toggle" type="button" aria-haspopup="true" aria-expanded="true">
							{_p var='my_all_events'} <i class="ico ico-angle-down"></i>
						</a>
						<ul class="dropdown-menu dropdown-menu-right" id="">
							<li><a class="dropdown-item active" data-type="attending">{_p var='attending'}</a></li>
							<li><a class="dropdown-item active" data-type="maybe_attending">{_p var='maybe_attending'}</a></li>
							<li><a class="dropdown-item active" data-type="my">{_p var='my_events'}</a></li>
						</ul>
					</div>
				</div>
				<div class="checkbox p-checkbox-custom p-fevent-checkbox-other-events">
					<label>
						<input type="checkbox"><i class="ico ico-square-o mr-1"></i> {_p var='other_events'}
					</label>
				</div>
			</div>
			<div class="item-note-group">
				<div class="item-note">
					<span class="p-fevent-dot-status bg-success"></span> {_p var='ongoing'}
				</div>
				<div class="item-note">
					<span class="p-fevent-dot-status bg-primary"></span> {_p var='upcoming'}
				</div>
				<div class="item-note">
					<span class="p-fevent-dot-status bg-gray"></span> {_p var='ended'}
				</div>
			</div>
		</div>
		<div class="item-action-wrapper p-fevent-calendar-nav">
			<div class="item-time-title"></div>
			<div class="item-nav-group">
				<button class="btn btn-xs btn-default p-text-capitalize" data-calendar-nav="prev"><i class="ico ico-angle-left mr-1"></i>{_p var='fevent.prev'}</button>
				<button class="btn btn-xs btn-primary p-text-capitalize" data-calendar-nav="today">{_p var='today'}</button>
				<button class="btn btn-xs btn-default p-text-capitalize" data-calendar-nav="next">{_p var='next'}<i class="ico ico-angle-right ml-1"></i></button>
			</div>
			<div class="item-time-group">
				<button class="item-time-btn" data-calendar-view="year">{_p var='year'}</button>
				<button class="item-time-btn active" data-calendar-view="month">{_p var='month'}</button>
				<button class="item-time-btn" data-calendar-view="week">{_p var='fevent.week'}</button>
				<button class="item-time-btn" data-calendar-view="day">{_p var='day'}</button>
			</div>
		</div>
	</div>
</div>
<div id="p_fevent_calendar_page" class="p-fevent-calendar-container"></div>
<div class="modal hide fade" id="events-modal" aria-hidden="true" style="display: none;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3>Event</h3>
	</div>
	<div class="modal-body" style="height: 400px"><iframe width="100%" frameborder="0" src="http://www.example.com/" style="height: 370px;"></iframe></div>
	<div class="modal-footer">
		<a href="#" data-dismiss="modal" class="btn">Close</a>
	</div>
</div>