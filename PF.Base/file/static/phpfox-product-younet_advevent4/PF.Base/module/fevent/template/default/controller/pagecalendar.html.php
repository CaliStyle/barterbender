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

{module name='fevent.search'}
<div id="calendar" class="ynfevent-page-calendar">
	<div class="ynfevent-page-calendar-nav mb-2">
		<div class="ynfevent-page-calendar-nav__view">
			<a class="ynfevent-page-calendar-nav__today fz-12" href="{url link='fevent.pagecalendar' view='pagecalendar' month=$aTime.current_month year=$aTime.current_year}">{_p var='fevent.today'}</a>
			<div class="ynfevent-page-calendar-nav__time ml-2">
				<a class="ynfevent-page-calendar-nav__button double-left" href="{url link='fevent.pagecalendar' view='pagecalendar' month=$aTime.prev_year_month year=$aTime.prev_year}"><i class="ico ico-angle-double-left"></i></a>
				<a class="ynfevent-page-calendar-nav__button angle-left" href="{url link='fevent.pagecalendar' view='pagecalendar' month=$aTime.prev_month year=$aTime.prev_month_year}"><i class="ico ico-angle-left"></i></a>
				<p class="text-primary mb-0 mx-h1">{$aTime.monthText} / { $aTime.year}</p>
				<a class="ynfevent-page-calendar-nav__button angle-right" href="{url link='fevent.pagecalendar' view='pagecalendar' month=$aTime.next_month year=$aTime.next_month_year}"><i class="ico ico-angle-right"></i></a>
				<a class="ynfevent-page-calendar-nav__button double-right" href="{url link='fevent.pagecalendar' view='pagecalendar' month=$aTime.next_year_month year=$aTime.next_year}"><i class="ico ico-angle-double-right"></i></a>
			</div>
		</div>
		<div class="ynfevent-page-calendar-nav__note">
			<span class="text-gray-dark pl-2 one-time">{_p var ='one_time_event'}</span>
			<span class="text-gray-dark pl-2 repeat ml-3">{_p var = 'repeat_event'}</span>
			<span class="text-gray-dark pl-2 birthday ml-3">{_p var = 'birthday'}</span>
		</div>
	</div>
	<div class="ynfevent_table table-responsive">
		{$aCalendars}
	</div>
</div>
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?v=3.exp&key={$apiKey}&libraries=places"></script>