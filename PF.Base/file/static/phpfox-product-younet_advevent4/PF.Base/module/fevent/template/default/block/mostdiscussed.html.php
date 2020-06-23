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
{foreach from=$aMostDiscussed item=aMDEvent name=Mostdiscussed}
	<div class="ynfevent-block-item">
		<div class="ynfevent-block-item-top">
			<a title="{$aMDEvent.title|clean}" href="{permalink module='fevent' id=$aMDEvent.event_id title=$aMDEvent.title}" class="ynfevent-block-item-photo" style="background-image: url('{$aMDEvent.image_path}');" title="{$aMDEvent.title|clean}"></a>
		</div>
		<div class="ynfevent-block-item-top-info">
			<a href="{permalink module='fevent' id=$aMDEvent.event_id title=$aMDEvent.title}" class="ynfevent-block-item-title" title="{$aMDEvent.title|clean}">{$aMDEvent.title|clean|shorten:50:'...'|split:20}</a>
			<div class="space-left text-gray-dark ynfevent-block-item-owner mt-1 fz-12"><i class="ico ico-user1-o"></i>{_p var='fevent.by'} {$aMDEvent|user}</div>
			<time class="mt-h1 d-block space-left">
				<i class="ico ico-calendar-star-o text-gray-dark"></i><span class="fw-bold fz-12">{$aMDEvent.d_start_time}&nbsp;-&nbsp;{$aMDEvent.short_start_time}</span>
			</time>
		</div>
	</div>
{/foreach}

