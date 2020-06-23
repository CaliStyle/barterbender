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
{foreach from=$aPast item=aPEvent name=Past}
	<div class="ynfevent-block-item">
		<div class="ynfevent-block-item-top">
			<a href="{permalink module='fevent' id=$aPEvent.event_id title=$aPEvent.title}" class="ynfevent-block-item-photo" title="{$aPEvent.title|clean}" style="background-image: url('{$aPEvent.image_path}');"></a>
		</div>
		<div class="ynfevent-block-item-top-info">
			<a href="{permalink module='fevent' id=$aPEvent.event_id title=$aPEvent.title}" class="ynfevent-block-item-title" title="{$aPEvent.title|clean}">{$aPEvent.title|clean|shorten:50:'...'|split:20}</a>
			<time class="mt-h1 d-block space-left">
				<i class="ico ico-calendar-star-o text-gray-dark"></i><span class="fw-bold fz-12">{$aPEvent.d_end_time_past}&nbsp;-&nbsp;{$aPEvent.short_end_time}</span>
			</time>
			<p class="ynfevent-block-item-location text-gray-dark mt-h1 space-left mb-0"><i class="ico ico-checkin-o"></i>{$aPEvent.location|clean|shorten:50:'...'}</p>
		</div>
	</div>
{/foreach}

